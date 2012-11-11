<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Event tests
 */
class PeachTest_Log_Event_Test extends PeachTest_TestCase
{
    public function testValidEvent()
    {
        $message = 'message';
        $priority = Peach_Log::INFO;
        $priorityName = 'INFO';
        
        $event = new Peach_Log_Event($message, $priority);
        
        $this->assertEquals($message, $event->getMessage());
        $this->assertEquals($priority, $event->getPriority());
        $this->assertEquals($priorityName, $event->getPriorityName());
        
        $message2 = 'message2';
        $event->setMessage($message2);
        $this->assertEquals($message2, $event->getMessage());
    }
    
    public function testExtra()
    {
        $message = 'message';
        $priority = Peach_Log::INFO;
        $event = new Peach_Log_Event($message, $priority);
        
        $event->setExtra('extra1', 'extra_value');
    }
    
    public function testInvalidPriority()
    {
        $this->setExpectedException('Peach_Log_Exception');
        
        new Peach_Log_Event('message', 999);
    }
}

/* EOF */