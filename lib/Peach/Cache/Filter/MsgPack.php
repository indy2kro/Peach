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
 * Cache Message Pack filter
 * 
 * @see https://github.com/msgpack/msgpack-php
 */
class Peach_Cache_Filter_MsgPack extends Peach_Cache_Filter_Abstract
{
    /**
     * Filter method for input
     * 
     * @param mixed $input
     * @return string
     */
    public function filterInput($input)
    {
        return msgpack_serialize($input);
    }
    
    /**
     * Filter method for output
     * 
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        return msgpack_unserialize($output);
    }
}

/* EOF */