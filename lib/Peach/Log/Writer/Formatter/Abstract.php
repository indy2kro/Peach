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
 * Abstract log writer formatter implementation
 */
abstract class Peach_Log_Writer_Formatter_Abstract
{
    /*
     * Available keywords
     */
    const KEYWORD_MESSAGE = 'message';
    const KEYWORD_TIMESTAMP = 'timestamp';
    const KEYWORD_MILLISECONDS = 'milliseconds';
    const KEYWORD_MICROSECONDS = 'microseconds';
    const KEYWORD_DURATION = 'duration';
    const KEYWORD_DATE = 'date';
    const KEYWORD_PRIORITY = 'priority';
    const KEYWORD_PRIORITY_NAME = 'priorityName';
    const KEYWORD_PID = 'pid';
    const KEYWORD_MEMORY = 'memory';
    
    /*
     * Available options
     */
    const OPT_FORMAT = 'format';
    const OPT_DATE_FORMAT = 'date_format';
    
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
     * Set options
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function setOptions($options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * Format event
     * 
     * @param Peach_Log_Event $event
     * @return void
     */
    abstract public function format(Peach_Log_Event $event);
    
    /**
     * Replace keywords
     * 
     * @param Peach_Log_Event $event
     * @param string          $pattern
     * @return string
     */
    protected function _replaceKeywords(Peach_Log_Event $event, $pattern)
    {
        // start with the format
        $formattedMessage = $pattern;
        
        $replaceMap = array(
            $this->_formatKeyword(self::KEYWORD_MESSAGE) => $event->getMessage(),
            $this->_formatKeyword(self::KEYWORD_PRIORITY_NAME) => $event->getPriorityName(),
            $this->_formatKeyword(self::KEYWORD_PRIORITY) => $event->getPriority(),
            $this->_formatKeyword(self::KEYWORD_TIMESTAMP) => $event->getTimestamp(),
            $this->_formatKeyword(self::KEYWORD_DURATION) => $event->getExtra(Peach_Log::EXTRA_DURATION),
            $this->_formatKeyword(self::KEYWORD_PID) => getmypid()
        );
        
        // get microtime
        $microtime = $event->getExtra(Peach_Log::EXTRA_MICROTIME);
        
        if (!is_null($microtime)) {
            $replaceMap[$this->_formatKeyword(self::KEYWORD_MICROSECONDS)] = sprintf('%06.6s', $microtime);
            $replaceMap[$this->_formatKeyword(self::KEYWORD_MILLISECONDS)] = sprintf('%03.3s', $microtime);
        }
        
        // get memory used
        $memory = $event->getExtra(Peach_Log::EXTRA_MEMORY);
        
        if (!is_null($memory)) {
            $replaceMap[$this->_formatKeyword(self::KEYWORD_MEMORY)] = sprintf( '%dkB', $memory / 1024 );
        }
        
        // replace keywords
        foreach ($replaceMap as $key => $value) {
            $formattedMessage = str_replace($key, $value, $formattedMessage);
        }
        
        return $formattedMessage;
    }
    
    /**
     * Format keyword
     * 
     * @param string $keyword
     * @return string
     */
    protected function _formatKeyword($keyword)
    {
        $formatted = '%' . $keyword . '%';
        
        return $formatted;
    }
}

/* EOF */