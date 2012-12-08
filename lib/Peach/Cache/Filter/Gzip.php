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
 * Cache Gzip filter
 */
class Peach_Cache_Filter_Gzip extends Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    public function filterInput($input)
    {
        return gzcompress($input);
    }
    
    /**
     * Filter method for output
     * 
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        return gzuncompress($output);
    }
}

/* EOF */