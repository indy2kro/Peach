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
 * Http mock server socket server
 */
class PeachTest_Mock_Http_Server_Socket_Server extends PeachTest_Mock_Http_Server_Socket
{
    public function create($domain, $type, $protocol)
    {
        $sock = socket_create($domain, $type, $protocol);
        if ($sock === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_create failed: ".$this->getError());
        }
        return $this->setSock($sock);
    }
    
    public function bind($address, $port)
    {
        if (socket_bind($this->getSock(), $address, $port) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_bind failed: ".$this->getError());
        }
        return $this;
    }
    
    public function listen($backlog = 0)
    {
        if (socket_listen($this->getSock(), $backlog) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_listen failed: ".$this->getError());
        }
        return $this;
    }
    
    public function accept()
    {
        $sock = @socket_accept($this->getSock());
        if ($sock === false) {
            return false;
        }
        return new PeachTest_Mock_Http_Server_Socket_Client($sock);
    }
}

/* EOF */