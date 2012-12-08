<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Cache
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Cache Snappy filter
 */
class Peach_Cache_Filter_Snappy extends Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    public function filterInput($input)
    {
        if (!function_exists('snappy_compress')) {
            throw new Peach_Cache_Exception('Snappy extension is required to use this function');
        }
        
        return snappy_compress($input);
    }
    
    /**
     * Filter method for output
     * 
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        if (!function_exists('snappy_uncompress')) {
            throw new Peach_Cache_Exception('Snappy extension is required to use this function');
        }
        
        return snappy_uncompress($output);
    }
}

/* EOF */