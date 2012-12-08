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
 * HTTP client abstract adapter
 */
abstract class Peach_Http_Client_Adapter_Abstract
{
    /*
     * SSL types
     */
    const SSL_CRYPTO_V23 = STREAM_CRYPTO_METHOD_SSLv23_CLIENT;
    const SSL_CRYPTO_V2 = STREAM_CRYPTO_METHOD_SSLv2_CLIENT;
    const SSL_CRYPTO_V3 = STREAM_CRYPTO_METHOD_SSLv3_CLIENT;
    const SSL_CRYPTO_TLS = STREAM_CRYPTO_METHOD_TLS_CLIENT;
    
    /*
     * Available options
     */
    const OPT_PERSISTENT = 'persistent';
    const OPT_HTTP_VERSION = 'http_version';
    const OPT_TIMEOUT = 'timeout';
    const OPT_KEEP_ALIVE = 'keep_alive';
    const OPT_BUFFER_SIZE = 'buffer_size';
    const OPT_SSL_ENABLED = 'ssl_enabled';
    const OPT_SSL_TRANSPORT = 'ssl_transport';
    
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
        self::OPT_BUFFER_SIZE => 8192,
        self::OPT_SSL_ENABLED => false,
        self::OPT_SSL_TRANSPORT => self::SSL_CRYPTO_V23
    );
    
    /*
     * Map SSL transport wrappers to stream crypto method constants
     *
     * @var array
     */
    protected $_sslTransportTypes = array(
        self::SSL_CRYPTO_V23, self::SSL_CRYPTO_V2,
        self::SSL_CRYPTO_V3, self::SSL_CRYPTO_TLS
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
     * @return boolean
     */
    abstract public function connect($host, $port = 80);

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