<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Standard log writer formatter implementation
 */
class Peach_Log_Writer_Formatter_Standard extends Peach_Log_Writer_Formatter_Abstract
{
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_FORMAT => '%timestamp%.%milliseconds%|%pid%|%memory%|%priorityName%|%message%',
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