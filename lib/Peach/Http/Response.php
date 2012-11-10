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
class Peach_Http_Response
{
    /*
     * Available parts
     */
    const PART_HTTP_VERSION = 'http_version';
    const PART_STATUS_CODE = 'status_code';
    const PART_STATUS_STRING = 'status_string';
    const PART_HEADERS = 'headers';
    const PART_BODY = 'body';
    
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
     * Raw response
     * 
     * @var string
     */
    protected $_rawResponse;

    /**
     * Constructor
     * 
     * @param array $parts
     * @return void
     */
    public function __construct(Array $parts = array())
    {
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
     * Set parts
     * 
     * @param array $parts
     * @return void
     */
    public function setParts(Array $parts)
    {
        if (isset($parts[self::PART_HEADERS])) {
            // format headers is provided
            $parts[self::PART_HEADERS] = $this->_formatHeaders($parts[self::PART_HEADERS]);
        }
        
        $this->_parts = array_merge($this->_parts, $parts);
    }
    
    /**
     * Set raw response
     * 
     * @param string $rawResponse
     * @return void
     */
    public function setRawResponse($rawResponse)
    {
        $this->_rawResponse = $rawResponse;
        
        // parse response
        $this->_parseRawResponse();
    }
    
    /**
     * Get raw response
     * 
     * @return string|null
     */
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }
    
    /**
     * Get headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->_parts[self::PART_HEADERS];
    }
    
    /**
     * Get header value
     * 
     * @param string $header
     * @return string|null
     */
    public function getHeader($header)
    {
        // all headers are stored lowercase
        $header = strtolower($header);
        
        if (array_key_exists($header, $this->_parts[self::PART_HEADERS])) {
            return $this->_parts[self::PART_HEADERS][$header];
        }
        
        return null;
    }
    
    /**
     * Parse raw response
     */
    protected function _parseRawResponse()
    {
        $lines = explode("\r\n", $this->_rawResponse);
        
        if (!is_array($lines) || 1 == count($lines)) {
            $lines = explode("\n", $this->_rawResponse);
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
        
        $this->_parts[self::PART_HEADERS] = $headers;
        $this->_parts[self::PART_BODY] = $body;
    }
    
    /**
     * Format headers
     * 
     * @param array $headers
     * @return array
     */
    protected function _formatHeaders(Array $headers)
    {
        $formatted = array();
        
        foreach ($headers as $headerKey => $headerValue) {
            $headerKey = strtolower(trim($headerKey));
            
            $formatted[$headerKey] = $headerValue;
        }
        
        return $formatted;
    }
}

/* EOF */