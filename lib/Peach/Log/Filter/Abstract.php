<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Log
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Abstract log filter implementation
 */
abstract class Peach_Log_Filter_Abstract
{
    /**
     * Accept method
     * 
     * @param Peach_Log_Event $event
     * @return boolean
     */
    abstract public function accept(Peach_Log_Event $event);
}

/* EOF */