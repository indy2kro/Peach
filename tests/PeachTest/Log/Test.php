<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log tests
 */
class PeachTest_Log_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Log file
     * 
     * @var string
     */
    protected $_logFile;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        // set log file
        $this->_logFile = dirname(__FILE__) . '/_files/test.log';
    }
        
    public function tearDown()
    {
        parent::tearDown();
        
        if (file_exists($this->_logFile)) {
            unlink($this->_logFile);
        }
    }
    
    public function testConstructor()
    {
        // default options
        $log = new Peach_Log();
        
        $this->assertInstanceOf('Peach_Log', $log);
        
        // custom array options
        $options = array(
            Peach_Log::OPT_TRACK_MEMORY_USAGE => false
        );
        
        $log = new Peach_Log($options);
        $this->assertInstanceOf('Peach_Log', $log);
        
        // custom ini config options
        $configFile = dirname(__FILE__) . '/_files/config.ini';
        $iniOptions = new Peach_Config_Ini($configFile);
        
        $log = new Peach_Log($iniOptions);
        $this->assertInstanceOf('Peach_Log', $log);
    }
    
    public function testFileSimple()
    {
        $writer = new Peach_Log_Writer_File($this->_logFile);
        
        $filter = new Peach_Log_Filter_Priority(Peach_Log::INFO);
        $writer->addFilter($filter);
        
        $formatter = new Peach_Log_Writer_Formatter_Simple();
        $writer->setFormatter($formatter);
        
        // default options
        $log = new Peach_Log();
        $log->addWriter($writer);
        
        $log->info('Test message');
    }
    
    public function testFileStandard()
    {
        $writer = new Peach_Log_Writer_File($this->_logFile);
        
        $filter = new Peach_Log_Filter_Priority(Peach_Log::INFO);
        $writer->addFilter($filter);
        
        $formatter = new Peach_Log_Writer_Formatter_Standard();
        $writer->setFormatter($formatter);
        
        // custom array options
        $options = array(
            Peach_Log::OPT_TRACK_MEMORY_USAGE => true,
            Peach_Log::OPT_COMPUTE_MICROSECONDS => true,
            Peach_Log::OPT_TRACK_DURATION => true
        );
        
        // default options
        $log = new Peach_Log($options);
        $log->addWriter($writer);
        
        $log->debug('Test message1');
        $log->info('Test message2');
        $log->notice('Test message3');
        $log->warning('Test message4');
        $log->error('Test message5');
        $log->critical('Test message6');
        $log->alert('Test message7');
        $log->emergency('Test message8');
        
        $log->setEventItem('extra_item', 'extra_value');
        
        $memory = $log->getMemoryStatistics();
        $this->assertArrayHasKey('min', $memory);
        $this->assertArrayHasKey('max', $memory);
    }
    
    public function testFileWrongPriority()
    {
        // default options
        $log = new Peach_Log();
        
        $this->setExpectedException('Peach_Log_Exception');
        $log->log('test message', 999);
    }
}

/* EOF */