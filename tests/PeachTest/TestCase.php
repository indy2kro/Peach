<?php
/**
 * Peach Framework
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Abstract test case
 */
abstract class PeachTest_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Format memory usage
     * 
     * @param integer $size
     * @param integer $digits
     * @return string
     */
    protected function _friendlySize($size, $digits = 2)
    {
        $bytes = array('KB', 'KB', 'MB', 'GB', 'TB');

        // less than 1 KB, display in bytes
        if ($size < 1024) {
            return $size . ' bytes';
        }

        for ($i = 0; $size >= 1024; $i++) {
            $size /= 1024;
        }

        $formattedSize = round($size, $digits) . ' ' . $bytes[$i];

        return $formattedSize;
    }
}

/* EOF */