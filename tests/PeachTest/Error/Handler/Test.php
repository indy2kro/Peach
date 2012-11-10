<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Registry tests
 */
class PeachTest_Error_Handler_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        // reset environment
        Peach_Error_Handler::reset();
    }
    
    public function testSimple()
    {
        Peach_Error_Handler::start();
        
        file_get_contents(''); 
        
        $error = Peach_Error_Handler::stop();
        
        $this->assertInstanceOf('Peach_Error_Exception', $error);
    }
    
    public function testSimpleException()
    {
        Peach_Error_Handler::start();
        
        file_get_contents(''); 
        
        $this->setExpectedException('Peach_Exception');
        
        Peach_Error_Handler::stop(true);
    }
    
    public function testStartException()
    {
        $this->setExpectedException('Peach_Exception');
        
        Peach_Error_Handler::start();
        Peach_Error_Handler::start();
    }
    
    public function testStopException()
    {
        $this->setExpectedException('Peach_Exception');
        
        Peach_Error_Handler::stop();
    }
    
    public function testStopExceptionDouble()
    {
        $this->setExpectedException('Peach_Exception');
        
        Peach_Error_Handler::start();
        Peach_Error_Handler::stop();
        Peach_Error_Handler::stop();
    }
    
    public function testNoException()
    {
        Peach_Error_Handler::start();
        $error = Peach_Error_Handler::stop();
        
        $this->assertNull($error);
    }
    
    public function testCustomLevel()
    {
        Peach_Error_Handler::start(E_WARNING);
        
        file_get_contents(''); 
        
        $error = Peach_Error_Handler::stop();
        
        $this->assertInstanceOf('Peach_Error_Exception', $error);
    }    
}

/* EOF */