<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Http_Response tests
 */
class PeachTest_Http_Response_Test extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new Peach_Http_Response();
        
        $parts = array(
            Peach_Http_Response::PART_HTTP_VERSION => Peach_Http_Client::HTTP_VERSION_11,
            Peach_Http_Response::PART_STATUS_CODE => 200,
            Peach_Http_Response::PART_STATUS_STRING => 'OK',
            Peach_Http_Response::PART_HEADERS => array(
                'Content-Type' => 'text/html'
            ),
            Peach_Http_Response::PART_BODY => 'empty'
        );
        
        new Peach_Http_Response($parts);
    }
    
    public function testStatusCode()
    {
        $statusCode = 200;
        $statusString = 'OK Custom';
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => $statusCode,
            Peach_Http_Response::PART_STATUS_STRING => $statusString
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertEquals($statusCode, $response->getStatusCode());
    }
    
    public function testGetCustomStatusString()
    {
        $statusCode = 200;
        $string = 'OK Custom';
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => $statusCode,
            Peach_Http_Response::PART_STATUS_STRING => $string
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertEquals($string, $response->getStatusString());
    }
    
    public function testTranslateStatusString()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertEquals('OK', $response->getStatusString());
    }
    
    public function testIsClientError()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isClientError());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 404
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isClientError());
    }
    
    public function testIsForbidden()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 404
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isForbidden());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 403
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isForbidden());
    }
    
    public function testIsInformational()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isInformational());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 101
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isInformational());
    }
    
    public function testIsNotFound()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isNotFound());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 404
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isNotFound());
    }
    
    public function testIsOk()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 404
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isOk());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isOk());
    }
    
    public function testIsServerError()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 404
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isServerError());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 500
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isServerError());
    }
    
    public function testIsRedirect()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 200
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isRedirect());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 302
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isRedirect());
    }
    
    public function testIsSuccess()
    {
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 302
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertFalse($response->isSuccess());
        
        $parts = array(
            Peach_Http_Response::PART_STATUS_CODE => 202
        );
        
        $response = new Peach_Http_Response($parts);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testRawResponse()
    {
        $rawResponse = '';
        
        $response = new Peach_Http_Response();
        $response->setRawResponse($rawResponse);
        
        $this->assertEquals($rawResponse, $response->getRawResponse());
    }
    
    public function testGetHeaders()
    {
        $headerName = 'Content-Type';
        $headerValue = 'text/html';
        
        $parts = array(
            Peach_Http_Response::PART_HEADERS => array(
                $headerName => $headerValue
            )
        );
        
        $response = new Peach_Http_Response($parts);
        
        $headers = $response->getHeaders();
        $this->assertCount(1, $headers);
        
        $this->assertEquals($headerValue, $response->getHeader($headerName));
        $this->assertNull($response->getHeader('NonExistent'));
        
    }
}

/* EOF */