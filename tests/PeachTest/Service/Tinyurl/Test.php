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
 * Peach_Service_Tinyurl tests
 */
class PeachTest_Service_Tinyurl_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        $options = array(
            Peach_Service_Tinyurl::OPT_TIMEOUT => 1
        );
        $optionsObj = new Peach_Config($options);
        
        new Peach_Service_Tinyurl();
        new Peach_Service_Tinyurl($options);
        $tinyUrl = new Peach_Service_Tinyurl($optionsObj);
        
        $this->assertInstanceOf('Peach_Http_Client', $tinyUrl->getHttpClient());
        
        $httpClient = new Peach_Http_Client();
        $tinyUrl = new Peach_Service_Tinyurl(array(), $httpClient);
        
        $this->assertInstanceOf('Peach_Http_Client', $tinyUrl->getHttpClient());
    }
    
    public function testGetVersion()
    {
        $tinyUrl = new Peach_Service_Tinyurl();
        $this->assertNotNull($tinyUrl->getVersion());
    }
    
    public function testCreate()
    {
        $tinyUrl = new Peach_Service_Tinyurl();
        
        $url = 'https://www.github.com';
        
        $result = $tinyUrl->create($url);
        
        $this->assertInternalType('string', $result);
        $this->assertStringStartsWith('http://tinyurl.com/', $result);
    }
}

/* EOF */