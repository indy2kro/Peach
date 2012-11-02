<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP request implementation
 */
class Peach_Http_Request
{
    /*
     * Available methods
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_PATCH   = 'PATCH';
    
    /*
     * Available options
     */
    const OPT_METHOD = 'method';
    const OPT_URI = 'uri';
    const OPT_QUERY_PARAMS = 'query_params';
    const OPT_POST_PARAMS = 'post_params';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_METHOD => self::METHOD_GET,
        self::OPT_URI => null,
        self::OPT_QUERY_PARAMS => array(),
        self::OPT_POST_PARAMS => array(),
    );
    
    /**
     * Available methods
     * 
     * @var array
     */
    protected $_availableMethods = array(
        self::METHOD_OPTIONS,
        self::METHOD_GET, 
        self::METHOD_HEAD,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_DELETE,
        self::METHOD_TRACE,
        self::METHOD_CONNECT,
        self::METHOD_PATCH
    );
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // TODO
    }
    
    /**
     * Get method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_options[self::OPT_METHOD];
    }

    /**
     * Set query parameter
     * 
     * @param string $name
     * @param mixed  $value
     * @return void
     */
    public function setQueryParameter($name, $value = null)
    {
        $this->_options[self::OPT_QUERY_PARAMS][$name] = $value;
    }
    
    /**
     * Set post parameter
     * 
     * @param string $name
     * @param mixed  $value
     * @return void
     */
    public function setPostParameter($name, $value = null)
    {
        $this->_options[self::OPT_POST_PARAMS][$name] = $value;
    }
    
    /**
     * Set uri
     * 
     * @param Peach_Http_Uri|string $uri
     * @return void
     * @throws Peach_Http_Request_Exception
     */
    public function setUri($uri)
    {
        if (!$uri instanceof Peach_Http_Uri) {
            if (!is_string($uri)) {
                throw new Peach_Http_Request_Exception('Uri must be either string or Peach_Http_Uri instance');
            }
            
            $uri = new Peach_Http_Uri($uri);
        }
        
        $this->_options[self::OPT_URI] = $uri;
    }
}

/* EOF */