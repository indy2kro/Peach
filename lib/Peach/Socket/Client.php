<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Socket
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Socket client
 */
class Peach_Socket_Client
{
    /*
     * Available options
     */
    const OPT_CONNECT_TIMEOUT = 'connect_timeout';
    const OPT_TIMEOUT = 'timeout';
    const OPT_PERSISTENT = 'persistent';
    const OPT_ASYNC = 'async';
    const OPT_CRYPTO_ENABLED = 'crypto_enabled';
    const OPT_CRYPTO_TYPE = 'crypto_type';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_CONNECT_TIMEOUT => 10,
        self::OPT_TIMEOUT => 10,
        self::OPT_PERSISTENT => false,
        self::OPT_ASYNC => false,
        self::OPT_CRYPTO_ENABLED => false,
        self::OPT_CRYPTO_TYPE => null
    );
    
    /**
     * Socket resource
     * 
     * @var resource
     */
    protected $_socket;
    
    /**
     * Constructor
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * Set options
     *
     * @param array|Peach_Config $options
     * @return void
     */
    public function setOptions($options = array())
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Open file
     * 
     * @param string $filename
     * @param string $mode
     * @param resource $context
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function open($filename, $mode = 'r', $context = null)
    {
        Peach_Error_Handler::start();
        if (!is_null($context)) {
            $this->_socket = fopen($filename, $mode, false, $context);
        } else {
            $this->_socket = fopen($filename, $mode);
        }
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Socket_Client_Exception('Failed to open ' . $filename);
        }
    }
    
    /**
     * Connect
     * 
     * @param string   $socketUrl
     * @param resource $context
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function connect($socketUrl, $context = null)
    {
        // set connect flags
        $flags = STREAM_CLIENT_CONNECT;
        
        // set persistent if needed
        if ($this->_options[self::OPT_PERSISTENT]) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }
        
        // set async if needed
        if ($this->_options[self::OPT_ASYNC]) {
            $flags |= STREAM_CLIENT_ASYNC_CONNECT;
        }
        
        // if no context is provided, create a new one
        if (is_null($context)) {
            $context = stream_context_create();
        }

        Peach_Error_Handler::start();
        $this->_socket = stream_socket_client($socketUrl, $errno, $errstr, (int) $this->_options[self::OPT_CONNECT_TIMEOUT], $flags, $context);
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Socket_Client_Exception('Unable to Connect to ' . $socketUrl . '. Error #' . $errno . ': ' . $errstr);
        }
        
        // set command timeout
        $this->setTimeout((int) $this->_options[self::OPT_TIMEOUT]);
        
        // enable crypto if needed
        if ($this->_options[self::OPT_CRYPTO_ENABLED]) {
            $this->enableCrypto();
        }
    }
    
    /**
     * Set command timeout
     * 
     * @param integer $seconds
     * @param integer $microseconds
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function setTimeout($seconds, $microseconds = 0)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        // Set the stream timeout
        if (!stream_set_timeout($this->_socket, (int)$seconds, (int)$microseconds)) {
            throw new Peach_Socket_Client_Exception('Unable to set the connection timeout');
        }
    }
    
    /**
     * Get socket resource
     * 
     * @return resource
     * @throws Peach_Socket_Client_Exception
     */
    public function getSocket()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        return $this->_socket;
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
    }
    
    /**
     * Enable encryption
     * 
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function enableCrypto()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (is_null($this->_options[self::OPT_CRYPTO_TYPE])) {
            throw new Peach_Socket_Client_Exception('Crypto type must be set in order to enable encryption');
        }

        
        Peach_Error_Handler::start();
        $result = stream_socket_enable_crypto($this->_socket, true, $this->_options[self::OPT_CRYPTO_TYPE]);
        $error = Peach_Error_Handler::stop();

        if (!$result || $error) {
            $errorString = '';
            
            if (0 === $result) {
                $errorString .= ': not enough data, please try again';
            }
            
            if ($error) {
                $errorString .= ': ' . $error->getMessage();
            }
            while (($sslError = openssl_error_string()) != false) {
                $errorString .= '; SSL error: ' . $sslError;
            }
            
            // close socket
            $this->close();
            
            throw new Peach_Socket_Client_Exception('Unable to enable crypto on TCP connection' . $errorString);
        }
    }
    
    /**
     * Disable encryption
     * 
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function disableCrypto()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        Peach_Error_Handler::start();
        $result = stream_socket_enable_crypto($this->_socket, false);
        $error = Peach_Error_Handler::stop();

        if (!$result || $error) {
            $errorString = '';
            while (($sslError = openssl_error_string()) != false) {
                $errorString .= '; SSL error: ' . $sslError;
            }
            
            // close socket
            $this->close();
            
            throw new Peach_Socket_Client_Exception('Unable to disable crypto on TCP connection' . $errorString);
        }
    }
    
    /**
     * Get socket metadata
     * 
     * @return array
     * @throws Peach_Socket_Client_Exception
     */
    public function getMetadata()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        // get stream metadata
        $metadata = stream_get_meta_data($this->_socket);
        
        return $metadata;
    }
    
    /**
     * Set blocking mode
     * 
     * @param boolean $mode
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function setBlocking($mode = true)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!stream_set_blocking($this->_socket, (int)$mode)) {
            throw new Peach_Socket_Client_Exception('Failed to change socket blocking state');
        }
    }
    
    /**
     * Set read buffer size. If buffer is 0 then read operations are unbuffered.
     * 
     * @param integer $buffer
     * @throws Peach_Socket_Client_Exception
     */
    public function setReadBuffer($buffer = 0)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (0 !== stream_set_read_buffer($this->_socket, (int)$buffer)) {
            throw new Peach_Socket_Client_Exception('Failed to set read buffer');
        }
    }
    
    /**
     * Set write buffer size. If buffer is 0 then write operations are unbuffered.
     * 
     * @param integer $buffer
     * @throws Peach_Socket_Client_Exception
     */
    public function setWriteBuffer($buffer = 0)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (0 !== stream_set_write_buffer($this->_socket, (int)$buffer)) {
            throw new Peach_Socket_Client_Exception('Failed to set write buffer');
        }
    }
    
    /**
     * Reads remainder of a stream into a string
     * 
     * @param integer $maxlength
     * @param integer $offset
     * @return string
     * @throws Peach_Socket_Client_Exception
     */
    public function getContents($maxlength = -1, $offset = -1)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        $contents = stream_get_contents($this->_socket, $maxlength, $offset);
        
        return $contents;
    }
    
    /**
     * Read for specified length or until EOF is reached.
     * 
     * @param integer $length
     * @return string
     * @throws Peach_Socket_Client_Exception
     */
    public function read($length)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        $content = fread($this->_socket, $length);
        
        if (false === $content && !feof($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Unexpected EOF while reading from socket');
        }
        
        return $content;
    }
    
    /**
     * Write a string to socket
     * 
     * @param string  $string
     * @param integer $length
     * @return integer
     * @throws Peach_Socket_Client_Exception
     */
    public function write($string, $length = null)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!is_null($length)) {
            $lengthWritten = fwrite($this->_socket, $string, $length);
        } else {
            $lengthWritten = fwrite($this->_socket, $string);
        }
        
        if (false === $lengthWritten) {
            throw new Peach_Socket_Client_Exception('Failed to write to socket');
        }
        
        return $lengthWritten;
    }
    
    /**
     * Tests for end-of-file
     * 
     * @return boolean
     * @throws Peach_Socket_Client_Exception
     */
    public function eof()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        return feof($this->_socket);
    }
    
    /**
     * Flushes the output to a file
     * 
     * @return boolean
     * @throws Peach_Socket_Client_Exception
     */
    public function flush()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        return fflush($this->_socket);
    }
    
    /**
     * Gets a line from file pointer including end line delimiter.
     * 
     * @param integer $length
     * @return string|false
     * @throws Peach_Socket_Client_Exception
     */
    public function gets($length = null)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!is_null($length)) {
            $content = fgets($this->_socket, $length);
        } else {
            $content = fgets($this->_socket);
        }
        
        if (false === $content && !feof($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Unexpected EOF while reading line from socket');
        }
        
        return $content;
    }
    
    /**
     * Gets a line from file pointer without end line delimiter.
     * 
     * @param integer $length
     * @param string  $ending
     * @return string
     * @throws Peach_Socket_Client_Exception
     */
    public function getLine($length, $ending = null)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!is_null($ending)) {
            $content = stream_get_line($this->_socket, $length, $ending);
        } else {
            $content = stream_get_line($this->_socket, $length);
        }
        
        if (false === $content && !feof($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Unexpected EOF while reading line from socket');
        }
        
        return $content;
    }
    
    /**
     * Returns the current position of the file read/write pointer
     * 
     * @return integer
     * @throws Peach_Socket_Client_Exception
     */
    public function tell()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        $position = ftell($this->_socket);
        
        if (false === $position) {
            throw new Peach_Socket_Client_Exception('Unexpected ftell failure from socket');
        }
        
        return $position;
    }
    
    /**
     * Seeks on a file pointer
     * 
     * @param integer $offset
     * @param integer $whence
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        $result = fseek($this->_socket, $offset, $whence);
        
        if (-1 === $result) {
            throw new Peach_Socket_Client_Exception('Failed to seek to specified offset');
        }
    }
    
    /**
     * Truncates a file to a given length
     * 
     * @param integer $size 
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function truncate($size)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!ftruncate($this->_socket, $size)) {
            throw new Peach_Socket_Client_Exception('Failed to truncate file');
        }
    }
    
    /**
     * Lock a resource file
     * 
     * @param boolean $exclusive
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function lock($exclusive = false)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if ($exclusive) {
            $operation = LOCK_EX;
        } else {
            $operation = LOCK_SH;
        }
        
        if (!flock($this->_socket, $operation)) {
            throw new Peach_Socket_Client_Exception('Lock operation failed');
        }
    }
    
    /**
     * Unlock a resource file
     * 
     * @return void
     * @throws Peach_Socket_Client_Exception
     */
    public function unlock()
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        if (!flock($this->_socket, LOCK_UN)) {
            throw new Peach_Socket_Client_Exception('Unlock operation failed');
        }
    }
    
    /**
     * Get local socket name
     * 
     * @return string
     */
    public function getLocalSocketName()
    {
        return $this->_getSocketName(false);
    }
    
    /**
     * Get remote socket name
     * 
     * @return string
     */
    public function getRemoteSocketName()
    {
        return $this->_getSocketName(true);
    }
    
    /**
     * Get socket name
     * 
     * @param boolean $wantPeer
     * @return string
     * @throws Peach_Socket_Client_Exception
     */
    protected function _getSocketName($wantPeer = false)
    {
        if (is_null($this->_socket)) {
            throw new Peach_Socket_Client_Exception('Socket is not connected');
        }
        
        return stream_socket_get_name($this->_socket, $wantPeer);
    }
}

/* EOF */