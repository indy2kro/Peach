<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Simple log writer formatter implementation
 */
class Peach_Log_Writer_Formatter_Simple extends Peach_Log_Writer_Formatter_Abstract
{
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_FORMAT => '%message%',
        self::OPT_DATE_FORMAT => null
    );
    
    /**
     * Format event
     * 
     * @param Peach_Log_Event $event
     * @return void
     */
    public function format(Peach_Log_Event $event)
    {
        // replace keywords based on pattern
        $formattedMessage = $this->_replaceKeywords($event, $this->_options[self::OPT_FORMAT]);
        
        // add end line
        $formattedMessage .= PHP_EOL;
        
        // set formatted message
        $event->setString($formattedMessage);
    }
}

/* EOF */