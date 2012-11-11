<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Filter_Priority tests
 */
class PeachTest_Log_Filter_Priority_Test extends PeachTest_TestCase
{
    public function testValid()
    {
        $filter = new Peach_Log_Filter_Priority(4);
        
        $event = new Peach_Log_Event('test', 3);
        $this->assertTrue($filter->accept($event));
        
        $event = new Peach_Log_Event('test', 4);
        $this->assertTrue($filter->accept($event));
        
        $event = new Peach_Log_Event('test', 5);
        $this->assertFalse($filter->accept($event));
    }
    
    public function testInvalidPriority()
    {
        $this->setExpectedException('Peach_Log_Exception');
        new Peach_Log_Filter_Priority('invalid');
    }
}

/* EOF */