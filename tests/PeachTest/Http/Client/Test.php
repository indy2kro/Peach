<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Http_Client tests
 */
class PeachTest_Http_Client_Test extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $options = array(
            Peach_Http_Client::OPT_HTTP_VERSION => Peach_Http_Client::HTTP_VERSION_11
        );
        
        new Peach_Http_Client();
        new Peach_Http_Client('http://www.test.com');
        new Peach_Http_Client(new Peach_Http_Uri('http://www.test.com'));
        new Peach_Http_Client(null, $options);
        new Peach_Http_Client(null, new Peach_Config($options));
    }
    
    public function testGetUri()
    {
        $client = new Peach_Http_Client();
        $uri = $client->getUri();
        
        $this->assertInstanceOf('Peach_Http_Uri', $uri);
        
        $uriValue = 'http://www.test.com';
        
        $client = new Peach_Http_Client($uriValue);
        $uri = $client->getUri();
        
        $this->assertInstanceOf('Peach_Http_Uri', $uri);
        $this->assertEquals('www.test.com', $uri->getPart(Peach_Http_Uri::PART_HOST));
    }
    
    public function testGetResponse()
    {
        $client = new Peach_Http_Client();
        $response = $client->getResponse();
        
        $this->assertInstanceOf('Peach_Http_Response', $response);
        
        $response = new Peach_Http_Response();
        $client->setResponse($response);
        
        $response = $client->getResponse();
        $this->assertInstanceOf('Peach_Http_Response', $response);
    }
    
    public function testGetRequest()
    {
        $client = new Peach_Http_Client();
        $request = $client->getRequest();
        
        $this->assertInstanceOf('Peach_Http_Request', $request);
        
        $request = new Peach_Http_Request();
        $client->setRequest($request);
        
        $request = $client->getRequest();
        $this->assertInstanceOf('Peach_Http_Request', $request);
    }
    
    public function testSetParameter()
    {
        $client = new Peach_Http_Client();
        
        $nameQuery = 'name1';
        $valueQuery = 'value1';
        
        $namePost = 'name2';
        $valuePost = 'value2';
        
        $client->setQueryParameter($nameQuery, $valueQuery);
        $client->setPostParameter($namePost, $valuePost);
    }
    
    public function testGetAdapter()
    {
        $client = new Peach_Http_Client();
        
        $this->assertInstanceOf('Peach_Http_Client_Adapter_Abstract', $client->getAdapter());
    }
    
    public function testSetAdapter()
    {
        $client = new Peach_Http_Client();
        
        $client->setAdapter('Peach_Http_Client_Adapter_Socket');
        
        $this->assertInstanceOf('Peach_Http_Client_Adapter_Abstract', $client->getAdapter());
        
        $adapter = new Peach_Http_Client_Adapter_Socket();
        $client->setAdapter($adapter);
        
        $this->assertInstanceOf('Peach_Http_Client_Adapter_Abstract', $client->getAdapter());
    }
    
    public function testSetAdapterInvalid()
    {
        require_once dirname(__FILE__) . '/_files/DummyAdapter.php';
        $adapter = new DummyAdapter();
        
        $client = new Peach_Http_Client();
        
        $this->setExpectedException('Peach_Http_Client_Exception');
        $client->setAdapter($adapter);
    }
    
    public function testRequest()
    {
        $uri = 'http://www.cl.cam.ac.uk/~mgk25/ucs/examples/UTF-8-test.txt';
        
        $client = new Peach_Http_Client($uri);
        $response = $client->send();
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testRequestWithRedirect()
    {
        $uri = 'http://google.com';
        
        $client = new Peach_Http_Client($uri);
        $response = $client->send();
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testGzipRequest()
    {
        $uri = 'http://www.tools4noobs.com';
        
        $client = new Peach_Http_Client($uri);
        $response = $client->send();
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testSendCustom()
    {
        $uri = 'http://www.w3.org/2005/10/Process-20051014/';
        
        $client = new Peach_Http_Client();
        
        $request = new Peach_Http_Request($uri);
        $response = $client->send($request);
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testSendTrace()
    {
        $uri = 'http://google.com/';
        
        $client = new Peach_Http_Client();
        $client->setMethod(Peach_Http_Request::METHOD_TRACE);
        $response = $client->send();
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testSetMethod()
    {
        $uri = 'http://www.w3.org';
        
        $client = new Peach_Http_Client($uri);
        $client->setMethod(Peach_Http_Request::METHOD_POST);
        $response = $client->send();
        
        $body = $response->getBody();
        $this->assertNotNull($body);
    }
    
    public function testMemoryLeak()
    {
        $uri = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec1.html';
        $iterations = 10;
        
        $client = null;
        $memoryUsed = null;
        $previousMemoryUsed = null;
        
        for ($counter = 0; $counter < $iterations; $counter++) {
            $client = new Peach_Http_Client($uri);
            $client->send();
            
            $previousMemoryUsed = $memoryUsed;
            $memoryUsed = memory_get_usage();
            
            if (!is_null($previousMemoryUsed) && $previousMemoryUsed != $memoryUsed) {
                $this->fail('Memory leak detected!');
            }
        }
    }
}

/* EOF */