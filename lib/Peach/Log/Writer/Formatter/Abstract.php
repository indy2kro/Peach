<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Abstract log writer formatter implementation
 */
abstract class Peach_Log_Writer_Formatter_Abstract
{
    /**
     * Format event
     * 
     * @param Peach_Log_Event $event
     * @return void
     */
    abstract public function format(Peach_Log_Event $event);
}

/* EOF */