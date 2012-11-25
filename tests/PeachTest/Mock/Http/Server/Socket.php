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
 * Http mock server socket
 */
abstract class PeachTest_Mock_Http_Server_Socket
{
    protected $_sock;
    
    public function __construct($sock = null)
    {
        $this->setSock($sock);
    }
    
    public function setSock($sock)
    {
        $this->_sock = $sock;
        return $this;
    }
    
    public function getSock()
    {
        return $this->_sock;
    }
    
    public function close()
    {
        if (socket_close($this->getSock()) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_close failed: ".$this->getError());
        }
        return $this;
    }
    
    public function write($buf, $length = null)
    {
        if (!isset($length)) {
            $length = strlen($buf);
        }
        if (socket_write($this->getSock(), $buf, $length) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_close failed: ".$this->getError());
        }    
        return $this;
    }
    
    public function read($length, $type = PHP_BINARY_READ)
    {
        $buf = socket_read($this->getSock(), $length, $type);
        if ($buf === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_read failed: ".$this->getError());
        }
        return $buf;
    }
    
    public function setNonBlock()
    {
        socket_set_nonblock($this->getSock());
        return $this;
    }
    
    public function setOption($level, $optname, $optval)
    {
        if (socket_set_option($this->getSock(), $level, $optname, $optval) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_set_option failed: ".$this->getError());
        }
        return $this;
    }
    
    public function getError()
    {
        return socket_strerror(socket_last_error($this->getSock()));
    }
}

/* EOF */