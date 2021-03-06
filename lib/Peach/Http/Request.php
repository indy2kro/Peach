<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Http
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP request implementation
 */
class Peach_Http_Request extends Peach_Http_Message
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
     *
     * @param Peach_Http_Uri|string $uri
     * @param array|Peach_Config    $options
     * @return void
     */
    public function __construct($uri = null, $options = array())
    {
        parent::__construct($options);
        
        if (!is_null($uri)) {
            $this->setUri($uri);
        }
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
     * Set method
     * 
     * @param string $method
     * @return string
     * @throws Peach_Http_Request_Exception
     */
    public function setMethod($method)
    {
        if (!in_array($method, $this->_availableMethods)) {
            throw new Peach_Http_Request_Exception("Invalid method provided: '" . $method . "'");
        }
        
        $this->_options[self::OPT_METHOD] = $method;
    }
    
    /**
     * Get query parameter
     * 
     * @param string $name
     * @return mixed
     */
    public function getQueryParameter($name)
    {
        if (!isset($this->_options[self::OPT_QUERY_PARAMS][$name])) {
            return null;
        }
        
        return $this->_options[self::OPT_QUERY_PARAMS][$name];
    }

    /**
     * Get query parameters
     * 
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->_options[self::OPT_QUERY_PARAMS];
    }

    /**
     * Get post parameter
     * 
     * @param string $name
     * @return mixed
     */
    public function getPostParameter($name)
    {
        if (!isset($this->_options[self::OPT_POST_PARAMS][$name])) {
            return null;
        }
        
        return $this->_options[self::OPT_POST_PARAMS][$name];
    }

    /**
     * Get post parameters
     * 
     * @return array
     */
    public function getPostParameters()
    {
        return $this->_options[self::OPT_POST_PARAMS];
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
    
    /**
     * Get uri
     * 
     * @return Peach_Http_Uri
     */
    public function getUri()
    {
        if (is_null($this->_options[self::OPT_URI])) {
            $this->_options[self::OPT_URI] = new Peach_Http_Uri();
        }
        
        if (!empty($this->_options[self::OPT_QUERY_PARAMS])) {
            // add query params to URI
            $query = http_build_query($this->_options[self::OPT_QUERY_PARAMS]);

            // set query part
            $this->_options[self::OPT_URI]->setPart(Peach_Http_Uri::PART_QUERY, $query);
        }
        
        return $this->_options[self::OPT_URI];
    }
    
    /**
     * Set raw request
     * 
     * @param string $rawRequest
     * @return void
     */
    public function setRawRequest($rawRequest)
    {
        $this->_rawData = $rawRequest;
    }
    
    /**
     * Get raw request
     * 
     * @return string|null
     */
    public function getRawRequest()
    {
        return $this->_rawData;
    }
}

/* EOF */