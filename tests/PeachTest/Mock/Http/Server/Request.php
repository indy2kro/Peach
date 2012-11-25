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
 * Http mock server request
 */
class PeachTest_Mock_Http_Server_Request
{
    protected $_socket;
    protected $_method;
    protected $_uri;
    protected $_query;
    protected $_headers = array();
    protected $_body;
    
    public function __construct($input, $skipFirstLine = false)
    {
        $headerEnd = strpos($input, "\r\n\r\n");
        $headerLines = explode("\r\n", substr($input, 0, $headerEnd));
        
        $i = 0;
        if (!$skipFirstLine) {
            list($this->_method, $uri) = sscanf($headerLines[$i++], "%s %s");
            $queryPos = strpos($uri, '?');
            if ($queryPos !== false) {
                $this->_uri = substr($uri, 0, $queryPos);
                $this->_query = substr($uri, $queryPos + 1);
            } else {
                $this->_uri = $uri;
            }
        }
        
        for ($n = count($headerLines); $i < $n; $i++) {
            $p = strpos($headerLines[$i], ': ');
            $name = substr($headerLines[$i], 0, $p);
            $value = substr($headerLines[$i], $p + 2);
            $this->_headers[$name] = $value;
        }
        
        $this->_body = substr($input, $headerEnd + 4);
    }
    
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function getUri()
    {
        return $this->_uri;
    }
    
    public function getQuery()
    {
        return $this->_query;
    }
    
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    public function getHeader($name)
    {
        return (isset($this->_headers[$name]) ? $this->_headers[$name] : null);
    }
    
    public function getBody()
    {
        return $this->_body;
    }
}

/* EOF */