<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Filter_Suppress tests
 */
class PeachTest_Log_Filter_Suppress_Test extends PeachTest_TestCase
{
    public function testValid()
    {
        $filter = new Peach_Log_Filter_Suppress();
        
        $event = new Peach_Log_Event('test', 3);
        $this->assertTrue($filter->accept($event));
        
        $filter->suppress(true);
        
        $event = new Peach_Log_Event('test', 4);
        $this->assertFalse($filter->accept($event));
        
        $filter->suppress(false);
        
        $event = new Peach_Log_Event('test', 5);
        $this->assertTrue($filter->accept($event));
    }
}

/* EOF */