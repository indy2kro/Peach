<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Http mock server response
 */
abstract class PeachTest_Mock_Http_Server_Response
{
    protected $_httpProto = 'HTTP/1.1';
    protected $_statusCode = 200;
    protected $_statusMessage = 'OK';
    protected $_headers = array();
    protected $_body;
    
    public function __construct($body = '')
    {
        $this->_body = $body;
    }
    
    public function setStatusCode($code)
    {
        $this->_statusCode = $code;
        return $this;
    }
    
    public function getStatusCode()
    {
        return $this->_statusCode;
    }
    
    public function setStatusMessage($message)
    {
        $this->_statusMessage = $message;
        return $this;
    }
    
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }
    
    public function setHeader($name, $value)
    {
        $this->_headers[$name] = $value;
        return $this;
    }
    
    public function getHeader($name)
    {
        return (isset($this->_headers[$name]) ? $this->_headers[$name] : null);
    }
    
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    public function render()
    {
        $output = $this->_httpProto.' '.$this->getStatusCode().' '.$this->getStatusMessage()."\r\n";
        foreach ($this->_headers as $name => $value) {
            $output .= "{$name}: {$value}\r\n";
        }
        $output .= "\r\n".$this->getBody();
        return $output;
    }
}

/* EOF */