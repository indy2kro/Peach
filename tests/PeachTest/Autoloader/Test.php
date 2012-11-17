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
 * Peach_Autoloader tests
 */
class PeachTest_Autoloader_Test extends PeachTest_TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        // unregister unit tests autoloader
        $autoloader = PeachTest_Autoloader::getInstance();
        $autoloader->unregister();
        
        // include Peach autoloader
        require_once 'Peach/Autoloader.php';
        
        // set include path
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/_files/');
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        // register unit tests autoloader
        $autoloader = PeachTest_Autoloader::getInstance();
        $autoloader->register();
    }
    
    public function testRegister()
    {
        $autoloader = Peach_Autoloader::getInstance();
        $autoloader->register();
    }
    
    public function testSetOptions()
    {
        $options = array(
            Peach_Autoloader::OPT_CHECK_NAMESPACES => true
        );
        
        $autoloader = Peach_Autoloader::getInstance();
        $autoloader->setOptions($options);
        
        $configFile = dirname(__FILE__) . '/_files/config.ini';
        
        $iniOptions = new Peach_Config_Ini($configFile);
        $autoloader->setOptions($iniOptions);
    }
    
    public function testLoadRegisteredNamespace()
    {
        $autoloader = Peach_Autoloader::getInstance();
        $autoloader->registerNamespace('Test1_');
        
        $options = array(
            Peach_Autoloader::OPT_CHECK_NAMESPACES => true
        );
        $autoloader->setOptions($options);
        
        $autoloader->register();
        
        $class1 = new Test1_Class1();
        $class2 = new Test1_Class2();
        
        $this->assertInstanceOf('Test1_Class1', $class1);
        $this->assertInstanceOf('Test1_Class2', $class2);
    }
    
    public function testLoadUnregisteredNamespace()
    {
        $autoloader = Peach_Autoloader::getInstance();
        $autoloader->registerNamespace('Test1_');
        
        $options = array(
            Peach_Autoloader::OPT_CHECK_NAMESPACES => true
        );
        $autoloader->setOptions($options);
        $autoloader->register();
        
        $this->assertFalse(class_exists('Test2_Class1'));
        
        $autoloader->registerNamespace('Test2_');
        $this->assertTrue(class_exists('Test2_Class1'));
    }
    
    public function testUnregisterNamespace()
    {
        $autoloader = Peach_Autoloader::getInstance();
        
        $autoloader->unregisterNamespace('NonExistent_');
        
        $autoloader->unregisterNamespace('Test1_');
        $autoloader->registerNamespace('Test1_');
        $autoloader->unregisterNamespace('Test1_');
    }
    
    public function testUnregister()
    {
        $autoloader = Peach_Autoloader::getInstance();
        
        $autoloader->unregister();
    }
}

/* EOF */