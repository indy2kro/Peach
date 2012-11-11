<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP client cURL adapter
 */
class Peach_Http_Client_Adapter_Curl extends Peach_Http_Client_Adapter_Abstract
{
    /**
     * The resource for server connection
     *
     * @var resource
     */
    protected $_resource;
    
    /**
     * Connected host
     * 
     * @var string|null
     */
    protected $_connectedHost;
    
    /**
     * Connected port
     * 
     * @var integer|null
     */
    protected $_connectedPort;
    
    /**
     * Response string
     * 
     * @var string
     */
    protected $_response;
    
    /**
     * Contructor
     * 
     * @param array|Peach_Config $options
     * @return void
     * @throws Peach_Http_Client_Adapter_Exception
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('curl')) {
            throw new Peach_Http_Client_Adapter_Exception('cURL extension has to be loaded to use this adapter');
        }
        
        parent::__construct($options);
    }
    
    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param integer $port
     * @param boolean $secure
     * @return void
     * @throws Peach_Http_Client_Adapter_Exception
     */
    public function connect($host, $port = 80, $secure = false)
    {
        if ($this->_connectedHost != $host || $this->_connectedPort != $port) {
            $this->close();
        }
        
        // already connected
        if (!is_null($this->_resource)) {
            return null;
        }
        
        $this->_resource = curl_init();
        
        if (80 != $port) {
            curl_setopt($this->_resource, CURLOPT_PORT, (int)$port);
        }
        
        if (!empty($this->_options[self::OPT_TIMEOUT])) {
            // Set timeout
            curl_setopt($this->_resource, CURLOPT_CONNECTTIMEOUT, $this->_options[self::OPT_TIMEOUT]);
        }

        if (!$this->_resource) {
            $this->close();

            throw new Peach_Http_Client_Adapter_Exception('Failed to initialize cURL adapter');
        }

        $this->_connectedHost = $host;
        $this->_connectedPort = $port;
    }

    /**
     * Send request to the remote server
     *
     * @param string         $method
     * @param Peach_Http_Uri $url
     * @param array          $headers
     * @param string         $body
     * @return string Request as text
     */
    public function write($method, Peach_Http_Uri $uri, Array $headers = array(), $body = '')
    {
        if (is_null($this->_resource)) {
            throw new Peach_Http_Client_Adapter_Exception('Trying to write to socket, but not connected');
        }
        
        // set URL
        curl_setopt($this->_resource, CURLOPT_URL, $uri->toString());

        $curlMethod = null;
        $curlValue = null;
        
        switch ($method) {
            case Peach_Http_Request::METHOD_GET:
                $curlMethod = CURLOPT_HTTPGET;
                break;
            
            case Peach_Http_Request::METHOD_POST:
                $curlMethod = CURLOPT_POST;
                break;
            
            default:
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = $method;
                break;
        }
        
        // get http version to use
        $curlHttpVersion = ($this->_options[self::OPT_HTTP_VERSION] == Peach_Http_Client::HTTP_VERSION_11) ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0;

        // mark as HTTP request and set HTTP method
        curl_setopt($this->_resource, $curlHttpVersion, true);
        curl_setopt($this->_resource, $curlMethod, $curlValue);

        
        // ensure headers are also returned
        curl_setopt($this->_resource, CURLOPT_HEADER, true);

        // ensure actual response is returned
        curl_setopt($this->_resource, CURLOPT_RETURNTRANSFER, true);
        
        $curlHeaders = array();
        foreach ($headers as $key => $value) {
            $curlHeaders[] = $key . ': ' . $value;
        }
        
        curl_setopt($this->_resource, CURLOPT_HTTPHEADER, $curlHeaders);
        
        if (Peach_Http_Request::METHOD_POST == $method) {
            curl_setopt($this->_resource, CURLOPT_POSTFIELDS, $body);
        }
        
        // send the request
        $this->_response = curl_exec($this->_resource);

        if (empty($this->_response)) {
            throw new Peach_Http_Client_Adapter_Exception("Error in cURL request: " . curl_error($this->_resource));
        }

        $request  = curl_getinfo($this->_resource, CURLINFO_HEADER_OUT);
        $request .= $body;

        return $request;
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        return $this->_response;
    }

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->_resource)) {
            curl_close($this->_resource);
        }
        
        $this->_resource = null;
        $this->_connectedHost = null;
        $this->_connectedPort = null;
    }
}

/* EOF */