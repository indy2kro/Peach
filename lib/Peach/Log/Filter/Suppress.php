<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Suppress log filter implementation
 */
class Peach_Log_Filter_Suppress extends Peach_Log_Filter_Abstract
{
    /**
     * @var boolean
     */
    protected $_accept = true;

    /**
     * This is a simple boolean filter.
     *
     * Call suppress(true) to suppress all log events.
     * Call suppress(false) to accept all log events.
     *
     * @param boolean $suppress Should all log events be suppressed?
     * @return void
     */
    public function suppress($suppress)
    {
        $this->_accept = (! $suppress);
    }

    /**
     * Accept method
     * 
     * @param Peach_Log_Event $event
     * @return boolean
     */
    public function accept(Peach_Log_Event $event)
    {
        return $this->_accept;
    }
}

/* EOF */