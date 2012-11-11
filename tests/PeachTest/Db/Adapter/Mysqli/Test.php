<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Db_Adapter_Mysqli tests
 */
class PeachTest_Db_Adapter_Mysqli_Test extends PeachTest_TestCase
{
    /**
     * Db connection
     * 
     * @var Peach_Db_Adapter_Mysqli
     */
    protected $_db;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_db = Peach_Db::factory(Peach_Db::ADAPTER_MYSQLI);
    }
    
    public function testConstructor()
    {
        $options = array();
        $adapterOptions = array();
        
        $adapter = Peach_Db::factory(Peach_Db::ADAPTER_MYSQLI, $options, $adapterOptions);
        
        $this->assertInstanceOf('Peach_Db_Adapter_Mysqli', $adapter);
       
        $optionsObj = new Peach_Config($options);
        $adapterOptionsObj = new Peach_Config($adapterOptions);
        
        $adapter = Peach_Db::factory(Peach_Db::ADAPTER_MYSQLI, $optionsObj, $adapterOptionsObj);
        
        $this->assertInstanceOf('Peach_Db_Adapter_Mysqli', $adapter);
    }
    
    public function testConstructorException()
    {
        $this->setExpectedException('Peach_Db_Exception');
        
        Peach_Db::factory(Peach_Db::ADAPTER_MYSQLI, 'invalid');
    }
    
    public function testConstructorAdapterOptionsException()
    {
        $this->setExpectedException('Peach_Db_Exception');
        
        Peach_Db::factory(Peach_Db::ADAPTER_MYSQLI, array(), 'invalid');
    }
    
    public function testConstructorAdapterNameException()
    {
        $this->setExpectedException('Peach_Db_Exception');
        
        Peach_Db::factory('');
    }
    
    public function testConstructorAdapterNotExtend()
    {
        require_once dirname(__FILE__) . '/_files/Dummy/Adapter.php';
        
        $this->setExpectedException('Peach_Db_Exception');
        
        $options = array(
            Peach_Db::OPT_ADAPTER_NAMESPACE => 'Dummy'
        );
        
        Peach_Db::factory('Adapter', $options);
    }
}

/* EOF */