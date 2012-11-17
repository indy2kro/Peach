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
 * Peach_Config tests
 */
class PeachTest_Config_Test extends PeachTest_TestCase
{
    /**
     * Config object
     * 
     * @var Peach_Config
     */
    protected $_config;
    
    public function setUp()
    {
        parent::setUp();
        
        $subData = array(
            'sub11' => 'value11',
            'sub12' => 'value12'
        );
        
        $subObj = new Peach_Config($subData);
        
        $data = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'subkey1' => array(
                'sub1' => 'value1',
                'sub2' => 'value2'
            ),
            'subkey2' => $subObj
        );
        
        // initialize config
        $this->_config = new Peach_Config($data);
        
        // set default options
        $options = array(
            Peach_Config::OPT_READ_ONLY => true
        );
        $this->_config->setOptions($options);
    }
    
    public function testClone()
    {
        $config = clone $this->_config;
        
        $this->assertInstanceOf('Peach_Config', $config);
    }
    
    public function testIsset()
    {
        $this->assertTrue(isset($this->_config->key1));
        $this->assertFalse(isset($this->_config->nokey1));
    }
    
    public function testUnsetException()
    {
        $this->setExpectedException('Peach_Config_Exception');
        unset($this->_config->key1);
    }
    
    public function testUnset()
    {
        $this->assertTrue(isset($this->_config->key1));
        
        $options = array(
            Peach_Config::OPT_READ_ONLY => false
        );
        $this->_config->setOptions($options);
        unset($this->_config->key1);
        
        $this->assertFalse(isset($this->_config->key1));
    }
    
    public function testSetException()
    {
        $this->setExpectedException('Peach_Config_Exception');
        $this->_config->key1 = 'valuex';
    }
    
    public function testSet()
    {
        $this->assertEquals('value1', $this->_config->key1);
        
        $options = array(
            Peach_Config::OPT_READ_ONLY => false
        );
        $this->_config->setOptions($options);
        
        $this->_config->key1 = 'valuex';
        $this->assertEquals('valuex', $this->_config->key1);
        
        $this->_config->key1 = array(
            'newkey1' => 'newvalue1'
        );
        $this->assertEquals('newvalue1', $this->_config->key1->newkey1);
    }
    
    public function testGetException()
    {
        $this->setExpectedException('Peach_Config_Exception');
        $this->_config->get('NonExistent');
    }
    
    public function testGet()
    {
        $this->assertEquals('value1', $this->_config->get('key1'));
    }
    
    public function testGetSafe()
    {
        $this->assertEquals('value1', $this->_config->getSafe('key1'));
        $this->assertNull($this->_config->getSafe('NonExistent'));
        $this->assertEquals('safevalue', $this->_config->getSafe('NonExistent', 'safevalue'));
    }
    
    public function testGetOptions()
    {
        $options = $this->_config->getOptions();
        
        $this->assertTrue($options[Peach_Config::OPT_READ_ONLY]);
    }
    
    public function testToArray()
    {
        $options = $this->_config->toArray();
        
        $this->assertEquals('value1', $options['key1']);
        $this->assertEquals('value1', $options['subkey1']['sub1']);
        $this->assertEquals('value11', $options['subkey2']['sub11']);
    }
    
    public function testCount()
    {
        $count = $this->_config->count();
        
        $this->assertEquals(4, $count);
    }
    
    public function testIterator()
    {
        foreach ($this->_config as $item) {
            $currentItem = $this->_config->current();
            $currentKey = $this->_config->key();
            
            $this->assertNotNull($currentItem);
            $this->assertNotNull($currentKey);
        }
    }
    
    public function testExtends()
    {
        $extends = $this->_config->getExtends();
        $this->assertEmpty($extends);
        
        $this->_config->setExtend('extending', 'extended');
        
        $newExtends = $this->_config->getExtends();
        $this->assertNotEmpty($newExtends);
        
        $this->_config->setExtend('extending');
        
        $finalExtends = $this->_config->getExtends();
        $this->assertEmpty($finalExtends);
    }
}

/* EOF */