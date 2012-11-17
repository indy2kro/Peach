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
 * Peach_Config tests
 */
class PeachTest_Version_Test extends PeachTest_TestCase
{
    public function testVersion()
    {
        $this->assertNotNull(Peach_Version::VERSION);
    }
}

/* EOF */