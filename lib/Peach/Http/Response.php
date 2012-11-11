<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP response implementation
 */
class Peach_Http_Response extends Peach_Http_Message
{
    /*
     * Available parts
     */
    const PART_HTTP_VERSION = 'http_version';
    const PART_STATUS_CODE = 'status_code';
    const PART_STATUS_STRING = 'status_string';
    
    /**
     * Response parts
     * 
     * @var array
     */
    protected $_parts = array(
        self::PART_HTTP_VERSION => Peach_Http_Client::HTTP_VERSION_11,
        self::PART_STATUS_CODE => null,
        self::PART_STATUS_STRING => null,
        self::PART_HEADERS => array(),
        self::PART_BODY => null
    );
    
    /**
     * Constructor
     * 
     * @param array              $parts
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct(Array $parts = array(), $options = array())
    {
        parent::__construct($options);
        
        $this->setParts($parts);
    }
    
    /**
     * Get status string
     * 
     * @return string
     */
    public function getStatusString()
    {
        if (is_null($this->_parts[self::PART_STATUS_STRING])) {
            $translator = new Peach_Http_Response_Translator();
            $this->_parts[self::PART_STATUS_STRING] = $translator->translate($this->_parts[self::PART_STATUS_CODE]);
        }
        
        return $this->_parts[self::PART_STATUS_STRING];
    }
    
    /**
     * Get status code
     * 
     * @return integer
     */
    public function getStatusCode()
    {
        return (int)$this->_parts[self::PART_STATUS_CODE];
    }
    
    /**
     * Does the status code indicate a client error?
     *
     * @return boolean
     */
    public function isClientError()
    {
        $code = $this->getStatusCode();
        return ($code >= 400 && $code < 500);
    }

    /**
     * Is the request forbidden due to ACLs?
     *
     * @return boolean
     */
    public function isForbidden()
    {
        return (403 == $this->getStatusCode());
    }

    /**
     * Is the current status "informational"?
     *
     * @return boolean
     */
    public function isInformational()
    {
        $code = $this->getStatusCode();
        return ($code >= 100 && $code < 200);
    }

    /**
     * Does the status code indicate the resource is not found?
     *
     * @return boolean
     */
    public function isNotFound()
    {
        return (404 === $this->getStatusCode());
    }

    /**
     * Do we have a normal, OK response?
     *
     * @return boolean
     */
    public function isOk()
    {
        return (200 === $this->getStatusCode());
    }

    /**
     * Does the status code reflect a server error?
     *
     * @return boolean
     */
    public function isServerError()
    {
        $code = $this->getStatusCode();
        return (500 <= $code && 600 > $code);
    }

    /**
     * Do we have a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        return (300 <= $code && 400 > $code);
    }

    /**
     * Was the response successful?
     *
     * @return boolean
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return (200 <= $code && 300 > $code);
    }

    /**
     * Set raw response
     * 
     * @param string  $rawResponse
     * @param boolean $parseResponse
     * @return void
     */
    public function setRawResponse($rawResponse, $parseResponse = true)
    {
        $this->_rawData = $rawResponse;
        
        // parse response
        if ($parseResponse) {
            $this->_parseRawResponse();
        }
    }
    
    /**
     * Get raw response
     * 
     * @return string|null
     */
    public function getRawResponse()
    {
        return $this->_rawData;
    }
    
    /**
     * Parse raw response
     */
    protected function _parseRawResponse()
    {
        $lines = explode("\r\n", $this->_rawData);
        
        if (!is_array($lines) || 1 == count($lines)) {
            $lines = explode("\n", $this->_rawData);
        }
        
        $firstLine = array_shift($lines);

        $matches = array();
        
        if (!preg_match('/^HTTP\/(?P<version>1\.[01]) (?P<status>\d{3})(?:[ ]+(?P<reason>.*))?$/', $firstLine, $matches)) {
            throw new Peach_Http_Response_Exception('A valid response status line was not found in the provided string');
        }

        $this->_parts[self::PART_HTTP_VERSION] = $matches['version'];
        $this->_parts[self::PART_STATUS_CODE] = $matches['status'];
        $this->_parts[self::PART_STATUS_STRING] = (isset($matches['reason']) ? $matches['reason'] : '');
        
        if (0 == count($lines)) {
            return null;
        }
        
        $headers = array();
        $bodyLines = array();
        
        $isHeader = true;
        foreach ($lines as $line) {
            if ($isHeader && empty($line)) {
                $isHeader = false;
                continue;
            }
            
            if ($isHeader) {
                $headers[] = $line;
            } else {
                $bodyLines[] = $line;
            }
        }

        $body = implode("\r\n", $bodyLines);
        
        // decode body if needed
        $decodedHeaders = $this->_decodeHeaders($headers);
        $this->_parts[self::PART_HEADERS] = $decodedHeaders;
        
        if (!empty($body)) {
            // decode body if needed
            $decodedBody = $this->_decodeBody($body);

            $this->_parts[self::PART_BODY] = $decodedBody;
        }
    }
    
    /**
     * Decode headers
     * 
     * @param array $headers
     * @return array
     * @throws Peach_Http_Response_Exception
     */
    protected function _decodeHeaders(Array $headers)
    {
        $decoded = array();
        
        $lastHeaderKey = null;
        
        foreach ($headers as $header) {
            $header = trim($header);
            
            // empty line means end of headers
            if (empty($header)) {
                break;
            }
            
            $matches = array();
            
            // check if a header name is present
            if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):\s*(?P<value>.*)$/', $header, $matches)) {
                $lastHeaderKey = strtolower($matches['name']);
                
                if (isset($decoded[$lastHeaderKey])) {
                    // multiple values provided, create array
                    if (is_array($decoded[$lastHeaderKey])) {
                        $decoded[$lastHeaderKey][] = $matches['value'];
                    } else {
                        $decoded[$lastHeaderKey] = array($matches['value']);
                    }
                } else {
                    $decoded[$lastHeaderKey] = $matches['value'];
                }
                
                continue;
            }
            
            // continuation of an existing header
            if (!is_null($lastHeaderKey)) {
                $decoded[$lastHeaderKey] .= $header;
            } else {
                // invalid header
                throw new Peach_Http_Response_Exception("Invalid header received: '" . $header . "'");
            }
        }
        
        return $decoded;
    }
    
    /**
     * Decode body
     * 
     * @param string $body
     * @return string
     */
    protected function _decodeBody($body)
    {
        $transferEncoding = strtolower($this->getHeader(Peach_Http_Message::HEADER_TRANSFER_ENCODING));

        // check transfer encoding
        if ('chunked' == $transferEncoding) {
            $body = $this->_decodeChunkedBody($body);
        }
        
        // check content encoding
        $contentEncoding = strtolower($this->getHeader(Peach_Http_Message::HEADER_CONTENT_ENCODING));

        switch ($contentEncoding) {
            case 'gzip':
                $body = $this->_decodeGzipBody($body);
                break;
            
            case 'deflate':
                $body = $this->_decodeDeflateBody($body);
                break;
            
            default:
                // nothing to do
                break;
        }

        return $body;
    }
    
    /**
     * Decode a chunked message
     *
     * @param string $body
     * @return string
     * @throws Peach_Http_Response_Exception
     */
    protected function _decodeChunkedBody($body)
    {
        $decBody = '';

        while (trim($body)) {
            $matches = array();
            
            if (!preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $body, $matches)) {
                throw new Peach_Http_Response_Exception("Error parsing body - invalid chunked message");
            }

            $length = hexdec(trim($matches[1]));
            $cut = strlen($matches[0]);
            $decBody .= substr($body, $cut, $length);
            $body = substr($body, $cut + $length + 2);
        }

        return $decBody;
    }
    
    /**
     * Decode a gzip encoded message
     *
     * @param string $body
     * @return string
     * @throws Peach_Http_Response_Exception
     */
    protected function _decodeGzipBody($body)
    {
        if (!function_exists('gzinflate')) {
            throw new Peach_Http_Response_Exception('The zlib extension is required in order to decode "gzip" encoding');
        }

        return gzinflate(substr($body, 10));
    }
    
    /**
     * Decode a zlib deflated message (when Content-encoding = deflate)
     *
     * @param string $body
     * @return string
     * @throws Peach_Http_Response_Exception
     */
    protected function _decodeDeflateBody($body)
    {
        if (!function_exists('gzuncompress')) {
            throw new Peach_Http_Response_Exception('The zlib extension is required in order to decode "deflate" encoding');
        }

        /**
         * Some servers (IIS ?) send a broken deflate response, without the
         * RFC-required zlib header.
         *
         * We try to detect the zlib header, and if it does not exsit we
         * teat the body is plain DEFLATE content.
         *
         * This method was adapted from PEAR HTTP_Request2 by (c) Alexey Borzov
         */
        $zlibHeader = unpack('n', substr($body, 0, 2));

        if ($zlibHeader[1] % 31 == 0) {
            return gzuncompress($body);
        }
        return gzinflate($body);
    }
}

/* EOF */