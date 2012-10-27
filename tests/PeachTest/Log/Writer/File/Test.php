<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Writer_File tests
 */
class PeachTest_Log_Writer_File_Test extends PHPUnit_Framework_TestCase
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
    
    public function testValid()
    {
        // default options
        $log = new Peach_Log();
        
        $writer = new Peach_Log_Writer_File($this->_logFile);
        $log->addWriter($writer);
        
        $log->debug('test');
        
        $options = array(
            Peach_Log_Writer_File::OPT_FILE_LOCKING => true
        );
        
        $writer->setOptions($options);
        
        $log->debug('test2');
    }
    
    public function testInvalidPath()
    {
        $this->setExpectedException('Peach_Log_Exception');
        new Peach_Log_Writer_File('invalid://path');
    }
}

/* EOF */