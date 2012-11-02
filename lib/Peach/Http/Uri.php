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
    const SCHEME_FTP = 'ftp';
    
    /*
     * Available options
     */
    const OPT_SCHEME = 'scheme';
    const OPT_HOST = 'host';
    const OPT_PORT = 'port';
    const OPT_PATH = 'path';
    const OPT_QUERY = 'query';
    const OPT_FRAGMENT = 'fragment';
    const OPT_AUTH_USERNAME = 'auth_username';
    const OPT_AUTH_PASSWORD = 'auth_password';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_SCHEME => self::SCHEME_HTTP,
        self::OPT_HOST => null,
        self::OPT_PORT => null,
        self::OPT_PATH => null,
        self::OPT_QUERY => null,
        self::OPT_FRAGMENT => null,
        self::OPT_AUTH_USERNAME => null,
        self::OPT_AUTH_PASSWORD => null
    );
    
    /**
     * Allowed schemes
     * 
     * @var array
     */
    protected $_allowedSchemes = array(
        self::SCHEME_HTTP, self::SCHEME_HTTPS, self::SCHEME_FTP
    );
    
    /**
     * Constructor
     * 
     * @param string $uri
     * @return void
     */
    public function __construct($uri = null)
    {
        $this->_parseString($uri);
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
        
        if (is_null($parts)) {
            throw new Peach_Http_Uri_Exception('Provided uri is invalid');
        }
        
        if (isset($parts['scheme'])) {
            $this->_options[self::OPT_SCHEME] = $parts['scheme'];
        } else {
            throw new Peach_Http_Uri_Exception('Internal error: scheme-specific decomposition failed');
        }
        
        if (!in_array($this->_options[self::OPT_SCHEME])) {
            throw new Peach_Http_Uri_Exception("Invalid scheme provided: '" . $this->_options[self::OPT_SCHEME] . "'");
        }
        
        if (isset($parts['host'])) {
            $this->_options[self::OPT_HOST] = $parts['host'];
        }
        
        if (isset($parts['port'])) {
            $this->_options[self::OPT_PORT] = $parts['port'];
        }
        
        if (isset($parts['user'])) {
            $this->_options[self::OPT_AUTH_USERNAME] = $parts['user'];
        }
        
        if (isset($parts['pass'])) {
            $this->_options[self::OPT_AUTH_PASSWORD] = $parts['pass'];
        }
        
        if (isset($parts['path'])) {
            $this->_options[self::OPT_PATH] = $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $this->_options[self::OPT_QUERY] = $parts['query'];
        }
        
        if (isset($parts['fragment'])) {
            $this->_options[self::OPT_FRAGMENT] = $parts['fragment'];
        }
    }
}

/* EOF */