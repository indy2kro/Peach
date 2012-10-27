<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Config_Ini tests
 */
class PeachTest_Config_Ini_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Test invalid file
     */
    public function testEmptyIniFileName()
    {
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini('');
    }
    
    /**
     * Test invalid file
     */
    public function testInvalidIniFile()
    {
        $invalidFile = dirname(__FILE__) . '/_files/invalid.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    /**
     * Test invalid file
     */
    public function testInvalidMultipleExtends()
    {
        $invalidFile = dirname(__FILE__) . '/_files/multiple.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    /**
     * Test invalid file
     */
    public function testInvalidNonExistentExtend()
    {
        $invalidFile = dirname(__FILE__) . '/_files/nonexistent.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    /**
     * Test invalid file
     */
    public function testInvalidnesting()
    {
        $invalidFile = dirname(__FILE__) . '/_files/invalidnest.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    /**
     * Test valid file
     */
    public function testValidIniFile()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $config = new Peach_Config_Ini($validFile);
        $this->assertInstanceOf('Peach_Config_Ini', $config);
        $this->assertInstanceOf('Peach_Config', $config->section1);
        $this->assertEquals($config->out1, 'value1');
        $this->assertEquals($config->section1->key11, 'value11');
        $this->assertEquals($config->section2->key21, 'value21');
        $this->assertEquals($config->section2->key22, 'value22');
    }
    
    /**
     * Test section load
     */
    public function testLoadSection()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $config = new Peach_Config_Ini($validFile, 'section1');
        $this->assertInstanceOf('Peach_Config_Ini', $config);
        $this->assertEquals($config->key11, 'value11');
    }
    
    /**
     * Test section load
     */
    public function testLoadNonExistentSection()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($validFile, 'NonExistent');
    }
    
    /**
     * Test section load
     */
    public function testLoadAlreadyExistingNest()
    {
        $validFile = dirname(__FILE__) . '/_files/invalidnest2.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($validFile);
    }
    
    /**
     * Test nested sections
     */
    public function testNestedSections()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $config = new Peach_Config_Ini($validFile);
        $this->assertInstanceOf('Peach_Config', $config->nested1);
        $this->assertInstanceOf('Peach_Config', $config->nested2);
        $this->assertEquals($config->nested1->key31, 'value31');
        $this->assertEquals($config->nested1->key11, 'value11');
    }
}

/* EOF */