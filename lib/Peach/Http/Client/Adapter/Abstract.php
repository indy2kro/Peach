<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP client abstract adapter
 */
abstract class Peach_Http_Client_Adapter_Abstract
{
    /*
     * Available options
     */
    const OPT_PERSISTENT = 'persistent';
    const OPT_HTTP_VERSION = 'http_version';
    const OPT_TIMEOUT = 'timeout';
    const OPT_KEEP_ALIVE = 'keep_alive';
    const OPT_BUFFER_SIZE = 'buffer_size';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_PERSISTENT => false,
        self::OPT_HTTP_VERSION => Peach_Http_Client::HTTP_VERSION_11,
        self::OPT_TIMEOUT => 10,
        self::OPT_KEEP_ALIVE => false,
        self::OPT_BUFFER_SIZE => 8192
    );
    
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
     * Destructor: close connection if needed
     */
    public function __destruct()
    {
        if (!$this->_options[self::OPT_PERSISTENT]) {
            $this->close();
        }
    }
    
    /**
     * Set options
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
     * Connect to the remote server
     *
     * @param string  $host
     * @param integer $port
     * @param boolean $secure
     * @return boolean
     */
    abstract public function connect($host, $port = 80, $secure = false);

    /**
     * Send request to the remote server
     *
     * @param string         $method
     * @param Peach_Http_Uri $url
     * @param array          $headers
     * @param string         $body
     * @return string Request as text
     */
    abstract public function write($method, Peach_Http_Uri $uri, Array $headers = array(), $body = '');

    /**
     * Read response from server
     *
     * @return string
     */
    abstract public function read();

    /**
     * Close the connection to the server
     *
     * @return void
     */
    abstract public function close();
}

/* EOF */