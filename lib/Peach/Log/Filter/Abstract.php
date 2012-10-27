<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
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