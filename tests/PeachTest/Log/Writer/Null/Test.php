<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Writer_Null tests
 */
class PeachTest_Log_Writer_Null_Test extends PeachTest_TestCase
{
    public function testValid()
    {
        // default options
        $log = new Peach_Log();
        
        $writer = new Peach_Log_Writer_Null();
        $log->addWriter($writer);
        
        $log->debug('test');
    }
}

/* EOF */