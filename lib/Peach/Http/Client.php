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
     * Available transfer encodings
     */
    const TRANSFER_ENCODING_CHUNKED = 'chunked';
    const TRANSFER_ENCODING_COMPRESS = 'compress';
    const TRANSFER_ENCODING_DEFLATE = 'deflate';
    const TRANSFER_ENCODING_GZIP = 'gzip';
    const TRANSFER_ENCODING_IDENTITY = 'identity';
    
    /*
     * Encode types
     */
    const ENC_FORM_DATA = 'multipart/form-data';
    const ENC_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    
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
     * @var Peach_Http_Client_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * Constructor
     *
     * @param Peach_Http_Uri|string $uri
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
     * Set method
     * 
     * @param string $method
     * @return void
     */
    public function setMethod($method)
    {
        $this->getRequest()->setMethod($method);
    }

    /**
     * Set GET parameter
     *
     * @param string $name
     * @return void
     */
    public function setQueryParameter($name, $value = null)
    {
        $this->getRequest()->setQueryParameter($name, $value);
    }

    /**
     * Set the POST parameters
     *
     * @param array $post
     * @return Client
     */
    public function setPostParameter($name, $value = null)
    {
        $this->getRequest()->setPostParameter($name, $value);
    }
    
    /**
     * Set adapter
     * 
     * @param Peach_Http_Client_Adapter_Abstract|string $adapter
     * @param array                                     $adapterOptions
     * @return void
     * @throws Peach_Http_Client_Exception
     */
    public function setAdapter($adapter, Array $adapterOptions = array())
    {
        // set the base options
        $baseOptions = array(
            Peach_Http_Client_Adapter_Abstract::OPT_HTTP_VERSION => $this->_options[self::OPT_HTTP_VERSION]
        );
        
        // create final adapter options
        $adapterOptions = array_merge($baseOptions, $adapterOptions);
        
        if ($adapter instanceof Peach_Http_Client_Adapter_Abstract) {
            $this->_adapter = $adapter;
            $this->_adapter->setOptions($adapterOptions);
        } else {
            $this->_adapter = new $adapter($adapterOptions);
            
            if (!$this->_adapter instanceof Peach_Http_Client_Adapter_Abstract) {
                throw new Peach_Http_Client_Exception('Adapter must be an instance of Peach_Http_Client_Adapter_Abstract');
            }
        }
    }
    
    /**
     * Get adapter
     * 
     * @return Peach_Http_Client_Adapter_Abstract
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
        
        // get request object
        $request = $this->getRequest();

        // get adapter
        $this->_adapter = $this->getAdapter();
        
        $redirectCounter = 0;
        
        // uri
        $uri = clone $this->getUri();
        
        do {
            $response = $this->_doRequest($uri, $request);

            if (!$response->isRedirect()) {
                break;
            }
            
            $uri->setUri($response->getHeader(self::HEADER_LOCATION));
            $request->setUri($uri);
            
            $redirectCounter++;
        } while ($redirectCounter < $this->_options[self::OPT_MAX_REDIRECTS]);
        
        return $response;
    }
    
    /**
     * Do request
     * 
     * @param Peach_Http_Uri     $uri
     * @param Peach_Http_Request $request
     * @return Peach_Http_Response
     */
    protected function _doRequest(Peach_Http_Uri $uri, Peach_Http_Request $request)
    {
        $host = $uri->getPart(Peach_Http_Uri::PART_HOST);
        $port = (int)$uri->getPart(Peach_Http_Uri::PART_PORT);
        
        if (empty($port)) {
            $port = 80;
        }
        
        $secure = ($uri->getPart(Peach_Http_Uri::PART_SCHEME) == Peach_Http_Uri::SCHEME_HTTPS) ? true : false;
        
        // connect to the remote host
        $this->_adapter->connect($host, $port, $secure);
        
        // prepare body
        $body = $this->_prepareBody($request);
        
        // prepare headers
        $headers = $this->_prepareHeaders($uri, $body);

        // get method
        $method = $request->getMethod();
        
        // write request
        $rawRequest = $this->_adapter->write($method, $uri, $headers, $body);
        $request->setRawRequest($rawRequest);
        
        // read response
        $rawResponse = $this->_adapter->read();
        
        // build response object
        $this->_response = new Peach_Http_Response();
        $this->_response->setRawResponse($rawResponse);
        
        return $this->_response;
    }
    
    /**
     * Prepare body content
     * 
     * @param Peach_Http_Request $request
     * @return string
     * @throws Peach_Http_Client_Exception
     */
    protected function _prepareBody(Peach_Http_Request $request)
    {
        $body = '';
        
        // get method
        $method = $request->getMethod();
        
        // TRACE requests do not have a body
        if (Peach_Http_Request::METHOD_TRACE == $method) {
            return $body;
        }
        
        // get post parameters
        $postParams = $request->getPostParameters();
        
        if (count($postParams) > 0) {
            if (is_null($this->_options[self::OPT_ENC_TYPE])) {
                $this->_options[self::OPT_ENC_TYPE] = self::ENC_FORM_URLENCODED;
            }
            
            switch ($this->_options[self::OPT_ENC_TYPE]) {
                case self::ENC_FORM_DATA:
                    $body = $this->_encFormData($postParams);
                    break;
                
                case self::ENC_FORM_URLENCODED:
                    $body = $this->_encUrlEncode($postParams);
                    break;
                
                default:
                    throw new Peach_Http_Client_Exception("Cannot handle specified content type: '" . $this->_options[self::OPT_ENC_TYPE] . "'");
                    break;
            }
        }
        
        return $body;
    }
    
    /**
     * Encode body as multipart/form-data
     * 
     * @param array $postParams
     * @return string
     */
    protected function _encFormData(Array $postParams)
    {
        $encoded = '';
        
        // TODO
        
        return $encoded;
    }
    
    /**
     * Encode body as application/x-www-form-urlencoded
     * 
     * @param array $postParams
     * @return type
     */
    protected function _encUrlEncode(Array $postParams)
    {
        $encoded = http_build_query($postParams, '', '&');
        
        return $encoded;
    }
    
    /**
     * Prepare headers
     * 
     * @param Peach_Http_Uri $uri
     * @param string         $body
     * @return array
     */
    protected function _prepareHeaders(Peach_Http_Uri $uri, $body)
    {
        $headers = array();
        
        // set host header
        if (self::HTTP_VERSION_11 == $this->_options[self::OPT_HTTP_VERSION]) {
            $host = $uri->getPart(Peach_Http_Uri::PART_HOST);
            
            // use the scheme to determine if port is needed
            $scheme = $uri->getPart(Peach_Http_Uri::PART_SCHEME);
            
            // add the port if needed
            $port = $uri->getPart(Peach_Http_Uri::PART_PORT);
            
            // don't add the port if the value is default one
            if (!empty($port) && ! ((Peach_Http_Uri::SCHEME_HTTP == $scheme && 80 == $port) || (Peach_Http_Uri::SCHEME_HTTPS == $scheme && 443 == $port))) {
                $host .= ':' . $port;
            }
            
            $headers[self::HEADER_HOST] = $host;
        }
        
        // add keep-alive header
        if (!$this->_options[self::OPT_KEEP_ALIVE]) {
            $headers[self::HEADER_CONNECTION] = 'close';
        }
        
        // add user agent
        if (!empty($this->_options[self::OPT_USER_AGENT])) {
            $headers[self::HEADER_USER_AGENT] = $this->_options[self::OPT_USER_AGENT];
        }
        
        // check if zlib library is available in order to accept compressed encoding
        if (function_exists('gzinflate')) {
            $headers[self::HEADER_ACCEPT_ENCODING] = 'gzip, deflate';
        } else {
            $headers[self::HEADER_ACCEPT_ENCODING] = 'identity';
        }
        
        if (!is_null($this->_options[self::OPT_ENC_TYPE])) {
            $headers[self::HEADER_CONTENT_TYPE] = $this->_options[self::OPT_ENC_TYPE];
        }
        
        // set content length
        if (!empty($body)) {
            $headers[self::HEADER_CONTENT_LENGTH] = strlen($body);
        }
        
        return $headers;
    }
}

/* EOF */