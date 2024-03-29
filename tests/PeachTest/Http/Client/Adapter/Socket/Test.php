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
 * Peach_Http_Client_Adapter_Socket tests
 */
class PeachTest_Http_Client_Adapter_Socket_Test extends PeachTest_TestCase
{
//    public function testConnect()
//    {
//        $host = 'yahoo.com';
//        $port = 80;
//        
//        $adapter = new Peach_Http_Client_Adapter_Socket();
//        $adapter->connect($host, $port);
//        $adapter->close();
//    }
//    
//    public function testConnectPersistent()
//    {
//        $host = 'yahoo.com';
//        $port = 80;
//        
//        $options = array(
//            Peach_Http_Client_Adapter_Socket::OPT_PERSISTENT => true
//        );
//        
//        $adapter = new Peach_Http_Client_Adapter_Socket($options);
//        $adapter->connect($host, $port);
//        $adapter->close();
//    }
//    
//    public function testAlreadyConnected()
//    {
//        $host = 'yahoo.com';
//        $port = 80;
//        
//        $adapter = new Peach_Http_Client_Adapter_Socket();
//        
//        $adapter->connect($host, $port);
//        $result = $adapter->connect($host, $port);
//        
//        $this->assertNull($result);
//        $adapter->close();
//    }
    
    public function testConnectFailed()
    {
        $host = 'no/such_host';
        $port = 80;
        
        $adapter = new Peach_Http_Client_Adapter_Socket();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $adapter->connect($host, $port);
        $adapter->close();
    }
    
//    public function testWrite()
//    {
//        $method = 'GET';
//        $uri = new Peach_Http_Uri('http://www.yahoo.com');
//        $host = $uri->getPart(Peach_Http_Uri::PART_HOST);
//        $port = 80;
//        
//        $adapter = new Peach_Http_Client_Adapter_Socket();
//        $adapter->connect($host, $port);
//        
//        $headers = array(
//            Peach_Http_Message::HEADER_ACCEPT_ENCODING => 'identity',
//            Peach_Http_Message::HEADER_CONNECTION => 'close'
//        );
//        
//        $request = $adapter->write($method, $uri, $headers);
//        
//        $this->assertNotEmpty($request);
//        $adapter->close();
//    }
//    
//    public function testRead()
//    {
//        $method = 'GET';
//        $uri = new Peach_Http_Uri('http://www.freesoft.org/CIE/RFC/2068/158.htm');
//        $host = $uri->getPart(Peach_Http_Uri::PART_HOST);
//        $port = 80;
//        
//        $adapter = new Peach_Http_Client_Adapter_Socket();
//        $adapter->connect($host, $port);
//        
//        $headers = array(
//            Peach_Http_Message::HEADER_HOST => $host,
//            Peach_Http_Message::HEADER_ACCEPT_ENCODING => 'identity',
//            Peach_Http_Message::HEADER_USER_AGENT => 'Peach_Http_Client'
//        );
//        
//        $adapter->write($method, $uri, $headers);
//        
//        $result = $adapter->read();
//        
//        $this->assertNotEmpty($result);
//        $adapter->close();
//    }
    
}

/* EOF */