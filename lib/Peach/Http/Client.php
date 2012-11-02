<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP client implementation
 */
class Peach_Http_Client
{
    /*
     * Available HTTP protocol versions
     */
    const HTTP_VERSION_10 = '1.0';
    const HTTP_VERSION_11 = '1.1';
    
    /*
     * Available options
     */
    const OPT_ADAPTER_TYPE = 'adapter_type';
    const OPT_USER_AGENT = 'user_agent';
    const OPT_HTTP_VERSION = 'http_version';
    const OPT_TIMEOUT = 'timeout';
    const OPT_MAX_REDIRECTS = 'max_redirects';
    const OPT_KEEP_ALIVE = 'keep_alive';
    const OPT_ENC_TYPE = 'enc_type';

    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_ADAPTER_TYPE => 'Peach_Http_Client_Adapter_Socket',
        self::OPT_USER_AGENT => 'Peach_Http_Client',
        self::OPT_HTTP_VERSION => self::HTTP_VERSION_11,
        self::OPT_TIMEOUT => 10,
        self::OPT_MAX_REDIRECTS => 5,
        self::OPT_KEEP_ALIVE => false,
        self::OPT_ENC_TYPE => null
    );
    
    /**
     * Http request
     * 
     * @var Peach_Http_Request
     */
    protected $_request;
    
    /**
     * Http response
     * 
     * @var Peach_Http_Response
     */
    protected $_response;
    
    /**
     * Adapter
     * 
     * @var Peach_Http_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * Last raw request
     * 
     * @var string
     */
    protected $_lastRawRequest;
    
    /**
     * Last raw response
     * 
     * @var string
     */
    protected $_lastRawResponse;
    
    /**
     * Constructor
     *
     * @param Peach_Http_Uri|string uri
     * @param array|Peach_Config    $options
     * @return void
     */
    public function __construct($uri = null, $options = array())
    {
        if (!is_null($uri)) {
            $this->setUri($uri);
        }
        
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
     * Set uri
     * 
     * @param Peach_Http_Uri|string $uri
     * @return void
     */
    public function setUri($uri)
    {
        $this->getRequest()->setUri($uri);
    }
    
    /**
     * Get uri
     * 
     * @return Peach_Http_Uri
     */
    public function getUri()
    {
        return $this->getRequest()->getUri();
    }
    
    /**
     * Get request
     * 
     * @return Peach_Http_Request
     */
    public function getRequest()
    {
        if (is_null($this->_request)) {
            $this->_request = new Peach_Http_Request();
        }
        
        return $this->_request;
    }
    
    /**
     * Get response
     * 
     * @return Peach_Http_Response
     */
    public function getResponse()
    {
        if (is_null($this->_response)) {
            $this->_response = new Peach_Http_Response();
        }
        
        return $this->_response;
    }
    
    /**
     * Set request
     * 
     * @param Peach_Http_Request $request
     * @return void
     */
    public function setRequest(Peach_Http_Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Set response
     * 
     * @param Peach_Http_Response $response
     * @return void
     */
    public function setResponse(Peach_Http_Response $response)
    {
        $this->_response = $response;
    }

    /**
     * Get the last request (as a string)
     *
     * @return string
     */
    public function getLastRawRequest()
    {
        return $this->_lastRawRequest;
    }

    /**
     * Get the last response (as a string)
     *
     * @return string
     */
    public function getLastRawResponse()
    {
        return $this->_lastRawResponse;
    }

    /**
     * Set GET parameter
     *
     * @param string $name
     * @return void
     */
    public function setQueryParameter($name, $value = null)
    {
        $this->_request->setQueryParameter($name, $value);
    }

    /**
     * Set the POST parameters
     *
     * @param array $post
     * @return Client
     */
    public function setPostParameter($name, $value = null)
    {
        $this->_request->setPostParameter($name, $value);
    }
    
    /**
     * Set adapter
     * 
     * @param Peach_Http_Adapter_Abstract|string $adapter
     * @return void
     * @throws Peach_Http_Client_Exception
     */
    public function setAdapter($adapter)
    {
        if ($adapter instanceof Peach_Http_Adapter_Abstract) {
            $this->_adapter = $adapter;
        } else {
            $this->_adapter = new $adapter();
            
            if (!$this->_adapter instanceof Peach_Http_Adapter_Abstract) {
                throw new Peach_Http_Client_Exception('Adapter must be an instance of Peach_Http_Adapter_Abstract');
            }
        }
    }
    
    /**
     * Get adapter
     * 
     * @return Peach_Http_Adapter_Abstract
     */
    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            $this->setAdapter($this->_options[self::OPT_ADAPTER_TYPE]);
        }
        
        return $this->_adapter;
    }

    /**
     * Send request
     * 
     * @param Peach_Http_Request $request
     * @returns Peach_Http_Response
     */
    public function send(Peach_Http_Request $request = null)
    {
        // set request if provided
        if (!is_null($request)) {
            $this->setRequest($request);
        }

        // get adapter
        $adapter = $this->getAdapter();
        
        $redirectCounter = 0;
        
        do {
            // uri
            $uri = $this->getUri();

            // method
            $method = $this->getRequest()->getMethod();

            // body
            $body = $this->_prepareBody();

            // headers
            $headers = $this->_prepareHeaders();
            
            $response = $this->_doRequest($uri, $method, $body, $headers);

            if (!$response->isRedirect()) {
                break;
            }
            
            // TODO
            
            $redirectCounter++;
        } while ($redirectCounter < $this->_options[self::OPT_MAX_REDIRECTS]);
    }
    
    /**
     * Prepare body
     */
    protected function _prepareBody()
    {
        // TODO
    }
    
    /**
     * Prepare headers
     */
    protected function _prepareHeaders()
    {
        // TODO
    }
    
    /**
     * Do request
     * 
     * @param Peach_Http_Uri $uri
     * @param string         $method
     * @param string         $body
     * @param array          $headers
     * @return Peach_Http_Response
     */
    protected function _doRequest(Peach_Http_Uri $uri, $method, $body, Array $headers = array())
    {
        // TODO
    }
}

/* EOF */