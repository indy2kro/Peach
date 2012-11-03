<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Http
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * HTTP client socket adapter
 */
class Peach_Http_Client_Adapter_Socket extends Peach_Http_Client_Adapter_Abstract
{
    
    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param integer $port
     * @param boolean $secure
     * @return boolean
     */
    public function connect($host, $port = 80, $secure = false)
    {
        // TODO
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
        // TODO
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        // TODO
    }

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close()
    {
        // TODO
    }
}

/* EOF */