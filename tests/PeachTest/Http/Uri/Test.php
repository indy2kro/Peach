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
    
    public function testEmpty()
    {
        $this->setExpectedException('Peach_Http_Uri_Exception');
        new Peach_Http_Uri();
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
}

/* EOF */