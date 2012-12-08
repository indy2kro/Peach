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
 * Cache serialize filter
 */
class Peach_Cache_Filter_Serialize extends Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    public function filterInput($input)
    {
        return serialize($input);
    }
    
    /**
     * Filter method for output
     * 
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        return unserialize($output);
    }
}

/* EOF */