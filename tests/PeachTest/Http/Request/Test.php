<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Http_Request tests
 */
class PeachTest_Http_Request_Test extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $uri = 'http://www.example.com';
        $options = array(
            Peach_Http_Request::OPT_METHOD => Peach_Http_Request::METHOD_POST
        );
        $optionsConfig = new Peach_Config($options);
        
        new Peach_Http_Request();
        new Peach_Http_Request($uri);
        new Peach_Http_Request($uri, $options);
        new Peach_Http_Request($uri, $optionsConfig);
    }
    
    public function testGetMethod()
    {
        $request = new Peach_Http_Request();
        
        $method = Peach_Http_Request::METHOD_POST;
        
        $request->setMethod($method);
        $this->assertEquals($method, $request->getMethod());
    }
    
    public function testSetMethodException()
    {
        $request = new Peach_Http_Request();
        
        $method = 'invalid';
        
        $this->setExpectedException('Peach_Http_Request_Exception');
        $request->setMethod($method);
    }
    
    public function testSetQueryParameter()
    {
        $request = new Peach_Http_Request();
        
        $paramName = 'name';
        $paramValue = 'value';
        
        $request->setQueryParameter($paramName, $paramValue);
        $this->assertEquals($paramValue, $request->getQueryParameter($paramName));
        
        $request->setQueryParameter($paramName);
        $this->assertEquals(null, $request->getQueryParameter($paramName));
        
        $params = $request->getQueryParameters();
        $this->assertCount(1, $params);
    }
    
    public function testSetPostParameter()
    {
        $request = new Peach_Http_Request();
        
        $paramName = 'name';
        $paramValue = 'value';
        
        $request->setPostParameter($paramName, $paramValue);
        $this->assertEquals($paramValue, $request->getPostParameter($paramName));
        
        $request->setPostParameter($paramName);
        $this->assertEquals(null, $request->getPostParameter($paramName));
        
        $params = $request->getPostParameters();
        $this->assertCount(1, $params);
    }
    
    public function testSetUriException()
    {
        $request = new Peach_Http_Request();
        
        $uri = array('x');
        
        $this->setExpectedException('Peach_Http_Request_Exception');
        $request->setUri($uri);
    }
    
    public function testSetUri()
    {
        $request = new Peach_Http_Request();
        
        $uriObj = $request->getUri();
        $this->assertInstanceOf('Peach_Http_Uri', $uriObj);
        
        $uri = 'http://www.example.com';
        
        $request->setUri($uri);
        $uriObj = $request->getUri();
        
        $this->assertInstanceOf('Peach_Http_Uri', $uriObj);
        $this->assertEquals('www.example.com', $uriObj->getPart(Peach_Http_Uri::PART_HOST));
    }
}

/* EOF */