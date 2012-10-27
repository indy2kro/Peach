<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Null log writer implementation
 */
class Peach_Log_Writer_Null extends Peach_Log_Writer_Abstract
{
    /**
     * Write a message to the log.
     *
     * @param Peach_Log_Event $event Event
     * @return void
     */
    protected function _write(Peach_Log_Event $event)
    {
        // nothing to do
    }
}

/* EOF */