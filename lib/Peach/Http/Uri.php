<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP uri
 */
class Peach_Http_Uri
{
    /*
     * Available schemes
     */
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';
    
    /*
     * Available part items
     */
    const PART_SCHEME = 'scheme';
    const PART_HOST = 'host';
    const PART_PORT = 'port';
    const PART_PATH = 'path';
    const PART_QUERY = 'query';
    const PART_FRAGMENT = 'fragment';
    const PART_AUTH_USERNAME = 'auth_username';
    const PART_AUTH_PASSWORD = 'auth_password';
    
    /**
     * Uri parts
     * 
     * @var array
     */
    protected $_parts = array(
        self::PART_SCHEME => self::SCHEME_HTTP,
        self::PART_HOST => null,
        self::PART_PORT => null,
        self::PART_PATH => null,
        self::PART_QUERY => null,
        self::PART_FRAGMENT => null,
        self::PART_AUTH_USERNAME => null,
        self::PART_AUTH_PASSWORD => null
    );
    
    /**
     * Allowed schemes
     * 
     * @var array
     */
    protected $_allowedSchemes = array(
        self::SCHEME_HTTP, self::SCHEME_HTTPS
    );
    
    /**
     * Constructor
     * 
     * @param string $uri
     * @return void
     */
    public function __construct($uri = null)
    {
        if (!is_null($uri)) {
            $this->_parseString($uri);
        }
    }
    
    /**
     * Get uri parts
     * 
     * @return array
     */
    public function getParts()
    {
        return $this->_parts;
    }
    
    /**
     * Get uri part
     * 
     * @param string $part
     * @return array
     * @throws Peach_Http_Uri_Exception
     */
    public function getPart($part)
    {
        if (!isset($this->_parts[$part])) {
            throw new Peach_Http_Uri_Exception('Invalid part provided');
        }
        
        return $this->_parts[$part];
    }
    
    /**
     * Parse uri string
     * 
     * @param string $uri
     * @return void
     * @throws Peach_Http_Uri_Exception
     */
    protected function _parseString($uri)
    {
        $parts = parse_url($uri);
        
        if (is_null($parts) || empty($parts)) {
            throw new Peach_Http_Uri_Exception('Internal error: scheme-specific decomposition failed');
        }
        
        if (isset($parts['scheme'])) {
            $this->_parts[self::PART_SCHEME] = $parts['scheme'];
        } else {
            // scheme not provided, use HTTP as fallback
            $this->_parts[self::PART_SCHEME] = self::SCHEME_HTTP;
        }
        
        if (!in_array($this->_parts[self::PART_SCHEME], $this->_allowedSchemes)) {
            throw new Peach_Http_Uri_Exception("Invalid scheme provided: '" . $this->_parts[self::PART_SCHEME] . "'");
        }
        
        if (isset($parts['host'])) {
            $this->_parts[self::PART_HOST] = $parts['host'];
        }
        
        if (isset($parts['port'])) {
            $this->_parts[self::PART_PORT] = $parts['port'];
        }
        
        if (isset($parts['user'])) {
            $this->_parts[self::PART_AUTH_USERNAME] = $parts['user'];
        }
        
        if (isset($parts['pass'])) {
            $this->_parts[self::PART_AUTH_PASSWORD] = $parts['pass'];
        }
        
        if (isset($parts['path'])) {
            $this->_parts[self::PART_PATH] = $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $this->_parts[self::PART_QUERY] = $parts['query'];
        }
        
        if (isset($parts['fragment'])) {
            $this->_parts[self::PART_FRAGMENT] = $parts['fragment'];
        }
    }
}

/* EOF */