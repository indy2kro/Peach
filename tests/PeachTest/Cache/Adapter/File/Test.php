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
 * Peach_Cache tests
 */
class PeachTest_Cache_Adapter_File_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        new Peach_Cache_Adapter_File();
    }
    
    public function testSetOptions()
    {
        $cache = new Peach_Cache_Adapter_File();
        
        $options = array(
            Peach_Cache_Adapter_File::OPT_LIFETIME => 1000
        );
        $optionsObj = new Peach_Config($options);
        
        $cache->setOptions($options);
        $cache->setOptions($optionsObj);
    }
    
}

/* EOF */