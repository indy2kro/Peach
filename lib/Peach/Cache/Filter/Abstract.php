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
 * Cache abstract filter
 */
abstract class Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    abstract public function filterInput($input);
    
    /**
     * Filter method for output
     * 
     * @param mixed $output
     * @return string
     */
    abstract public function filterOutput($output);
}

/* EOF */