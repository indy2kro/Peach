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
class PeachTest_Registry_Test extends PeachTest_TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $registry = Peach_Registry::getInstance();
        $registry->unsetInstance();
    }
 
    public function testSimple()
    {
        $registry = Peach_Registry::getInstance();
        
        $index = 'test';
        $value = 'value';
        
        $this->assertFalse($registry->offsetExists($index));
        $registry->set($index, $value);
        $this->assertTrue($registry->offsetExists($index));
        $this->assertEquals($value, $registry->get($index));
        $this->assertTrue($registry->isRegistered($index));
        $registry->remove($index);
        $this->assertFalse($registry->isRegistered($index));
        $registry->unsetInstance();
        $this->assertFalse($registry->isRegistered($index));
    }
    
    public function testStatic()
    {
        $registry = Peach_Registry::getInstance();
        
        $index = 'test';
        $value = 'value';
        
        $this->assertFalse(Peach_Registry::isRegistered($index));
        Peach_Registry::set($index, $value);
        $this->assertTrue(Peach_Registry::isRegistered($index));
        Peach_Registry::remove($index);
        $this->assertFalse(Peach_Registry::isRegistered($index));
        $registry->unsetInstance();
        $this->assertFalse(Peach_Registry::isRegistered($index));
        Peach_Registry::remove($index);
    }
    
    public function testGetException()
    {
        $registry = Peach_Registry::getInstance();
        $this->setExpectedException('Peach_Exception');
        $registry->get('NonExisting');
    }
}

/* EOF */