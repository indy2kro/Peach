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
 * Http mock server socket client
 */
class PeachTest_Mock_Http_Server_Socket_Client extends PeachTest_Mock_Http_Server_Socket
{
    public function getRemoteAddress()
    {
        $address = 0;
        if (socket_getpeername($this->getSock(), $address) === false) {
            throw new PeachTest_Mock_Http_Server_Exception("socket_accept failed: ".$this->getError());
        }
        return $address;
    }
}

/* EOF */