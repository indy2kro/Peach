<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP client socket adapter
 */
class Peach_Http_Client_Adapter_Socket extends Peach_Http_Client_Adapter_Abstract
{
    /**
     * Line separator
     */
    const LINE_SEPARATOR = "\r\n";
    
    /**
     * The socket for server connection
     *
     * @var resource
     */
    protected $_socket;
    
    /**
     * Connected host
     * 
     * @var string|null
     */
    protected $_connectedHost;
    
    /**
     * Connected port
     * 
     * @var integer|null
     */
    protected $_connectedPort;
    
    /**
     * Method type
     * 
     * @var string
     */
    protected $_method;
    
    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param integer $port
     * @param boolean $secure
     * @return void
     * @throws Peach_Http_Client_Adapter_Exception
     */
    public function connect($host, $port = 80, $secure = false)
    {
        if ($this->_connectedHost != $host || $this->_connectedPort != $port) {
            $this->close();
        }
        
        // already connected
        if (!is_null($this->_socket)) {
            return null;
        }
        
        // set connect flags
        $flags = STREAM_CLIENT_CONNECT;
        if ($this->_options[self::OPT_PERSISTENT]) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        Peach_Error_Handler::start();
        $this->_socket = stream_socket_client($host . ':' . $port, $errno, $errstr, (int) $this->_options[self::OPT_TIMEOUT], $flags);
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            $this->close();
            
            throw new Peach_Http_Client_Adapter_Exception('Unable to Connect to ' . $host . ':' . $port . '. Error #' . $errno . ': ' . $errstr);
        }
        
        // Set the stream timeout
        if (!stream_set_timeout($this->_socket, (int) $this->_options[self::OPT_TIMEOUT])) {
            throw new Peach_Http_Client_Adapter_Exception('Unable to set the connection timeout');
        }

        $this->_connectedHost = $host;
        $this->_connectedPort = $port;
    }

    /**
     * Send request to the remote server
     *
     * @param string         $method
     * @param Peach_Http_Uri $url
     * @param array          $headers
     * @param string         $body
     * @return string Request as text
     */
    public function write($method, Peach_Http_Uri $uri, Array $headers = array(), $body = '')
    {
        if (is_null($this->_socket)) {
            throw new Peach_Http_Client_Adapter_Exception('Trying to write to socket, but not connected');
        }
        
        // store method type
        $this->_method = $method;
        
        // get path
        $path = $uri->getPart(Peach_Http_Uri::PART_PATH);
        
        if (empty($path)) {
            $path = '/';
        }
        
        // get query
        $query = $uri->getPart(Peach_Http_Uri::PART_QUERY);
        if (!empty($query)) {
            $path .= '?' . $query;
        }
        
        // add http header
        $request = $method . ' ' . $path . ' HTTP/' . $this->_options[self::OPT_HTTP_VERSION] . self::LINE_SEPARATOR;
        
        // add headers
        foreach ($headers as $headerKey => $headerValue) {
            if (is_string($headerKey)) {
                $headerValue = ucfirst($headerKey) . ': ' . $headerValue;
            }
            
            $request .= $headerValue . self::LINE_SEPARATOR;
        }
        
        // Add empty line
        $request .= self::LINE_SEPARATOR;
        
        // Add the request body
        $request .= $body;
        
        // Send the request
        Peach_Error_Handler::start();
        $writeResult = fwrite($this->_socket, $request);
        $error = Peach_Error_Handler::stop();
        
        if (!$writeResult || !is_null($error)) {
            throw new Peach_Http_Client_Adapter_Exception('Error writing request to server', 0, $error);
        }
        
        return $request;
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $response = '';
        
        // read headers
        $gotStatus = false;

        while (($line = fgets($this->_socket)) !== false) {
            $gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
            
            if ($gotStatus) {
                $response .= $line;

                // trim the line
                $line = trim($line);
                
                if (empty($line)) {
                    break;
                }
            }
        }
        
        // check if the socket was timed out
        $this->_checkSocketReadTimeout();

        // build response object
        $responseObj = new Peach_Http_Response();
        $responseObj->setRawResponse($response);
        
        // get status code
        $statusCode = $responseObj->getStatusCode();

        // Handle 100 and 101 responses internally by restarting the read again
        if ($statusCode == 100 || $statusCode == 101) {
            return $this->read();
        }
        
        // stop if the request was a HEAD or a 204/304 status was received
        if ($statusCode == 304 || $statusCode == 204 || $this->_method == Peach_Http_Request::METHOD_HEAD) {
            // close the connection if requested to do so by the server
            $connection = $responseObj->getHeader(Peach_Http_Message::HEADER_CONNECTION);
            
            if (strtolower($connection) == 'close') {
                $this->close();
            }
            
            return $response;
        }

        // check transfer encoding
        $contentLength = $responseObj->getHeader(Peach_Http_Message::HEADER_CONTENT_LENGTH);
        
        if (!is_null($contentLength)) {
            // read until end the length defined
            if (is_array($contentLength)) {
                $contentLength = array_pop($contentLength);
            }
            
            $chunk = '';
            $currentPos = ftell($this->_socket);

            for ($readTo = $currentPos + $contentLength; $readTo > $currentPos; $currentPos = ftell($this->_socket)) {
                $chunk = fread($this->_socket, $readTo - $currentPos);
                
                if ($chunk === false || strlen($chunk) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                }

                $response .= $chunk;

                // Break if the connection ended prematurely
                if (feof($this->_socket)) {
                    break;
                }
            }
        } else {
            // read until EOF
            do {
                $buffer = fread($this->_socket, $this->_options[self::OPT_BUFFER_SIZE]);
                
                if ($buffer === false || strlen($buffer) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                } else {
                    $response .= $buffer;
                }
            } while (feof($this->_socket) === false);

            $this->close();
        }
        
        // close the connection if requested to do so by the server
        $connection = $responseObj->getHeader(Peach_Http_Message::HEADER_CONNECTION);

        if (strtolower($connection) == 'close') {
            $this->close();
        }

        return $response;
    }

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->_socket)) {
            Peach_Error_Handler::start();
            fclose($this->_socket);
            Peach_Error_Handler::stop();
        }
        
        $this->_socket = null;
        $this->_connectedHost = null;
        $this->_connectedPort = null;
    }
    
    /**
     * Check if the socket has timed out - if so close connection and throw an exception
     *
     * @return void
     * @throws Peach_Http_Client_Adapter_Exception
     */
    protected function _checkSocketReadTimeout()
    {
        if (is_null($this->_socket)) {
            return null;
        }
        
        // get stream metadata
        $info = stream_get_meta_data($this->_socket);
        $timedout = $info['timed_out'];
        
        if (!$timedout) {
            return null;
        }
        
        // request timed out
        $this->close();
        throw new Peach_Http_Client_Adapter_Exception('Read timed out after ' . $this->_options[self::OPT_TIMEOUT] . ' seconds');
    }
}

/* EOF */