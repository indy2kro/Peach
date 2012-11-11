<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach_Log_Formatter_Simple tests
 */
class PeachTest_Log_Formatter_SimpleTest extends PeachTest_TestCase
{
    public function testValid()
    {
        $formatter = new Peach_Log_Writer_Formatter_Simple();
        
        $options = array(
            Peach_Log_Writer_Formatter_Simple::OPT_DATE_FORMAT => 'd.M.Y'
        );
        
        $formatter->setOptions($options);
    }
}

/* EOF */