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
    public function testEmptyIniFileName()
    {
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini('');
    }
    
    public function testInvalidIniFile()
    {
        $invalidFile = dirname(__FILE__) . '/_files/invalid.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    public function testInvalidNonExistentExtend()
    {
        $invalidFile = dirname(__FILE__) . '/_files/nonexistent.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
    public function testInvalidnesting()
    {
        $invalidFile = dirname(__FILE__) . '/_files/invalidnest.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($invalidFile);
    }
    
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
    
    public function testLoadSection()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $config = new Peach_Config_Ini($validFile, 'section1');
        $this->assertInstanceOf('Peach_Config_Ini', $config);
        $this->assertEquals($config->key11, 'value11');
    }
    
    public function testLoadNonExistentSection()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($validFile, 'NonExistent');
    }
    
    public function testLoadAlreadyExistingNest()
    {
        $validFile = dirname(__FILE__) . '/_files/invalidnest2.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($validFile);
    }
    
    public function testNestedSections()
    {
        $validFile = dirname(__FILE__) . '/_files/valid.ini';
        
        $config = new Peach_Config_Ini($validFile);
        $this->assertInstanceOf('Peach_Config', $config->nested1);
        $this->assertInstanceOf('Peach_Config', $config->nested2);
        $this->assertEquals($config->nested1->key31, 'value31');
        $this->assertEquals($config->nested1->key11, 'value11');
    }
    
    public function testMultipleExtends()
    {
        $validFile = dirname(__FILE__) . '/_files/multiple.ini';
        
        $config = new Peach_Config_Ini($validFile);
        
        $this->assertEquals($config->nested1->key31, 'value31');
        $this->assertEquals($config->nested1->key11, 'value21');
        $this->assertEquals($config->nested1->key22, 'value22');
    }
    
    public function testMultipleExtendsSection()
    {
        $validFile = dirname(__FILE__) . '/_files/multiple.ini';
        
        $config = new Peach_Config_Ini($validFile, 'nested1');
        
        $this->assertEquals($config->key31, 'value31');
        $this->assertEquals($config->key11, 'value21');
        $this->assertEquals($config->key22, 'value22');
    }
    
    public function testCircularExtend()
    {
        $validFile = dirname(__FILE__) . '/_files/invalidcircular.ini';
        
        $this->setExpectedException('Peach_Config_Exception');
        new Peach_Config_Ini($validFile);
    }
}

/* EOF */