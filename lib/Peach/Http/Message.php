<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP message implementation
 */
abstract class Peach_Http_Message
{
    /*
     * Available headers (only the headers used by the client are listed)
     */
    const HEADER_HOST = 'Host';
    const HEADER_LOCATION = 'Location';
    const HEADER_TRANSFER_ENCODING = 'Transfer-Encoding';
    const HEADER_CONTENT_ENCODING = 'Content-Encoding';
    const HEADER_ACCEPT_ENCODING = 'Accept-encoding';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_CONTENT_LENGTH = 'Content-Length';
    const HEADER_CONNECTION = 'Connection';
    const HEADER_USER_AGENT = 'User-Agent';
    
    /*
     * Available parts
     */
    const PART_HEADERS = 'headers';
    const PART_BODY = 'body';
    
    /**
     * Response parts
     * 
     * @var array
     */
    protected $_parts = array(
        self::PART_HEADERS => array(),
        self::PART_BODY => null
    );
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
    );
    
    /**
     * Raw data
     * 
     * @var string
     */
    protected $_rawData;
    
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
     * Set parts
     * 
     * @param array $parts
     * @return void
     */
    public function setParts(Array $parts)
    {
        if (isset($parts[self::PART_HEADERS])) {
            // format headers if provided
            $parts[self::PART_HEADERS] = $this->_formatHeaders($parts[self::PART_HEADERS]);
        }
        
        $this->_parts = array_merge($this->_parts, $parts);
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
     * Get body
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->_parts[self::PART_BODY];
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