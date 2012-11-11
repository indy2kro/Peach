<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Http_Response_Translator tests
 */
class PeachTest_Http_Response_Translator_Test extends PeachTest_TestCase
{
    public function testOk()
    {
        $translator = new Peach_Http_Response_Translator();
        $this->assertEquals('OK', $translator->translate(200));
    }
    
    public function testNotFound()
    {
        $translator = new Peach_Http_Response_Translator();
        $this->assertNull($translator->translate(-1));
    }
}

/* EOF */