<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Http_Uri tests
 */
class PeachTest_Http_Uri_Test extends PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        $uriList = array(
            'http://www.test.com',
            'http://www.test.com:80/test',
            'http://username:password@hostname/path?arg=value#anchor'
        );
        
        foreach ($uriList as $uri) {
            new Peach_Http_Uri($uri);
        }
    }
    
    public function testGetParts()
    {
        $uri = 'http://username:password@hostname/path?arg=value#anchor';
        
        $uriObj = new Peach_Http_Uri($uri);
        $parts = $uriObj->getParts();
                
        $this->assertEquals('http', $parts[Peach_Http_Uri::PART_SCHEME]);
        $this->assertEquals('hostname', $parts[Peach_Http_Uri::PART_HOST]);
        $this->assertEquals('/path', $parts[Peach_Http_Uri::PART_PATH]);
        $this->assertEquals('arg=value', $parts[Peach_Http_Uri::PART_QUERY]);
        $this->assertEquals('anchor', $parts[Peach_Http_Uri::PART_FRAGMENT]);
        $this->assertEquals('username', $parts[Peach_Http_Uri::PART_AUTH_USERNAME]);
        $this->assertEquals('password', $parts[Peach_Http_Uri::PART_AUTH_PASSWORD]);
    }
    
    public function testGetPart()
    {
        $uri = 'http://username:password@hostname/path?arg=value#anchor';
        
        $uriObj = new Peach_Http_Uri($uri);
                
        $this->assertEquals('http', $uriObj->getPart(Peach_Http_Uri::PART_SCHEME));
        $this->assertEquals('hostname', $uriObj->getPart(Peach_Http_Uri::PART_HOST));
        $this->assertEquals('/path', $uriObj->getPart(Peach_Http_Uri::PART_PATH));
        $this->assertEquals('arg=value', $uriObj->getPart(Peach_Http_Uri::PART_QUERY));
        $this->assertEquals('anchor', $uriObj->getPart(Peach_Http_Uri::PART_FRAGMENT));
        $this->assertEquals('username', $uriObj->getPart(Peach_Http_Uri::PART_AUTH_USERNAME));
        $this->assertEquals('password', $uriObj->getPart(Peach_Http_Uri::PART_AUTH_PASSWORD));
    }
    
    public function testGetPartException()
    {
        $uri = 'http://username:password@hostname/path?arg=value#anchor';
        
        $uriObj = new Peach_Http_Uri($uri);

        $this->setExpectedException('Peach_Http_Uri_Exception');
        $uriObj->getPart('invalid');
        
    }
    
    public function testEmpty()
    {
        $uri = new Peach_Http_Uri();
        $this->assertInstanceOf('Peach_Http_Uri', $uri);
    }
    
    public function testNoScheme()
    {
        $uri = new Peach_Http_Uri('generic.com');
        $this->assertInstanceOf('Peach_Http_Uri', $uri);
        
        $scheme = $uri->getPart(Peach_Http_Uri::PART_SCHEME);
        $this->assertEquals(Peach_Http_Uri::SCHEME_HTTP, $scheme);
    }
    
    public function testInvalidUri()
    {
        $this->setExpectedException('Peach_Http_Uri_Exception');
        new Peach_Http_Uri('http://username:password@hostname:port/path?arg=value#anchor');
    }
    
    public function testInvalidScheme()
    {
        $this->setExpectedException('Peach_Http_Uri_Exception');
        new Peach_Http_Uri('invalid://www.test.com');
    }
    
    public function testMemoryLeak()
    {
        $uri = 'http://username:password@hostname/path?arg=value#anchor';
        $iterations = 10;
        
        $uriObj = null;
        $memoryUsed = null;
        $previousMemoryUsed = null;
        
        for ($counter = 0; $counter < $iterations; $counter++) {
            $uriObj = new Peach_Http_Uri($uri);
            
            $previousMemoryUsed = $memoryUsed;
            $memoryUsed = memory_get_usage();
            
            if (!is_null($previousMemoryUsed) && $previousMemoryUsed != $memoryUsed) {
                $this->fail('Memory leak detected!');
            }
        }
    }
}

/* EOF */