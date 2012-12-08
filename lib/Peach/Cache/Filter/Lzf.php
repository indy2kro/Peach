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
 * Cache Lzf filter
 */
class Peach_Cache_Filter_Lzf extends Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    public function filterInput($input)
    {
        if (!function_exists('lzf_compress')) {
            throw new Peach_Cache_Exception('Lzf extension is required to use this function');
        }
        
        return lzf_compress($input);
    }
    
    /**
     * Filter method for output
     * 
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        if (!function_exists('lzf_decompress')) {
            throw new Peach_Cache_Exception('Lzf extension is required to use this function');
        }
        
        return lzf_decompress($output);
    }
}

/* EOF */