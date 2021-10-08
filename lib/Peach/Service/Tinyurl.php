<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Service_Tinyurl
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 * @see        https://tinyurl.com/
 */

/**
 * Tinyurl API service
 */
class Peach_Service_Tinyurl
{
    /*
     * API version implementation
     */
    const VERSION = '1.0';
    
    /*
     * API url
     */
    const API_URL = 'https://tinyurl.com/api-create.php';
    
    /*
     * Available parameters
     */
    const PARAM_URL = 'url';
    
    /*
     * Available options
     */
    const OPT_TIMEOUT = 'timeout';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_TIMEOUT => 10
    );
    
    /**
     * Http client
     * 
     * @var Peach_Http_Client
     */
    protected $_httpClient;
    
    /**
     * Constructor
     *  
     * @param array|Peach_Config $options
     * @param Peach_Http_Client  $httpClient
     * @return void
     */
    public function __construct($options = array(), Peach_Http_Client $httpClient = null)
    {
        // set options
        $this->setOptions($options);
        
        if (!is_null($httpClient)) {
            // use provided http client
            $this->_httpClient = $httpClient;
        } else {
            // create new http client
            $this->_httpClient = new Peach_Http_Client();
            
        }
    }
    
    /**
     * Merge options with incoming values
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function setOptions($options)
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Get API version
     * 
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
    
    /**
     * Get HTTP client
     * 
     * @return Peach_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }
    
    /**
     * Create a tiny URL
     * 
     * @param string $url
     * @return array
     */
    public function create($url)
    {
        // set http options
        $httpOptions = array(
            Peach_Http_Client::OPT_TIMEOUT => $this->_options[self::OPT_TIMEOUT]
        );
        $this->_httpClient->setOptions($httpOptions);
        
        // set url
        $this->_httpClient->setUri(self::API_URL);
        
        $params = array(
            self::PARAM_URL => $url
        );
        
        //set parameters
        $this->_httpClient->setQueryParameters($params);
        
        // perform request
        $response = $this->_httpClient->request();
        
        // response is the tiny url
        return $response->getBody();
    }
}

/* EOF */
