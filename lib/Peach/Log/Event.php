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
 * Peach Log Event
 */
class Peach_Log_Event
{
    /*
     * Event members
     */
    const EVENT_MESSAGE = 'message';
    const EVENT_PRIORITY = 'priority';
    const EVENT_EXTRAS = 'extras';
    const EVENT_STRING = 'string';
    const EVENT_TIMESTAMP = 'timestamp';
    
    /*
     * Priority names
     */
    const PRIORITY_EMERGENCY = 'EMERG';
    const PRIORITY_ALERT = 'ALERT';
    const PRIORITY_CRITICAL = 'CRIT';
    const PRIORITY_ERROR = 'ERROR';
    const PRIORITY_WARNING = 'WARN';
    const PRIORITY_NOTICE = 'NOTICE';
    const PRIORITY_INFO = 'INFO';
    const PRIORITY_DEBUG = 'DEBUG';
    
    /**
     * Priority mapping
     * 
     * @var array
     */
    protected $_priorityMap = array(
        Peach_Log::EMERGENCY => self::PRIORITY_EMERGENCY,
        Peach_Log::ALERT => self::PRIORITY_ALERT,
        Peach_Log::CRITICAL => self::PRIORITY_CRITICAL,
        Peach_Log::ERROR => self::PRIORITY_ERROR,
        Peach_Log::WARNING => self::PRIORITY_WARNING,
        Peach_Log::NOTICE => self::PRIORITY_NOTICE,
        Peach_Log::INFO => self::PRIORITY_INFO,
        Peach_Log::DEBUG => self::PRIORITY_DEBUG,
    );

    /**
     * Event information
     * 
     * @var array
     */
    protected $_event = array(
        self::EVENT_MESSAGE => null,
        self::EVENT_PRIORITY => null,
        self::EVENT_EXTRAS => array(),
        self::EVENT_STRING => null,
        self::EVENT_TIMESTAMP => null
    );
    
    /**
     * Constructor
     * 
     * @param string  $message
     * @param integer $priority
     * @param array   $extras
     * @return void
     * @throws Peach_Log_Exception
     */
    public function __construct($message, $priority, Array $extras = array())
    {
        // validate priority
        if (!isset($this->_priorityMap[$priority])) {
            throw new Peach_Log_Exception('Invalid priority received: ' . $priority);
        }
        
        $this->_event[self::EVENT_MESSAGE] = $message;
        $this->_event[self::EVENT_PRIORITY] = $priority;
        $this->_event[self::EVENT_EXTRAS] = $extras;
        $this->_event[self::EVENT_TIMESTAMP] = time();
    }
    
    /**
     * Get message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->_event[self::EVENT_MESSAGE];
    }
    
    /**
     * Set message
     * 
     * @param string $message
     * @return string
     */
    public function setMessage($message)
    {
        $this->_event[self::EVENT_MESSAGE] = (string)$message;
    }
    
    /**
     * Get priority
     * 
     * @return integer
     */
    public function getPriority()
    {
        return $this->_event[self::EVENT_PRIORITY];
    }
    
    /**
     * Get priority name
     * 
     * @return string
     */
    public function getPriorityName()
    {
        // get event priority
        $priority = $this->_event[self::EVENT_PRIORITY];
        
        // get priority name
        $priorityName = $this->_priorityMap[$priority];
        
        return $priorityName;
    }
    
    /**
     * Get extra value
     * 
     * @param string $key
     * @return mixed
     */
    public function getExtra($key)
    {
        // make sure the key is a string
        $key = (string)$key;
        
        return isset($this->_event[self::EVENT_EXTRAS][$key]) ? $this->_event[self::EVENT_EXTRAS][$key] : null;
    }
    
    /**
     * Set message
     * 
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setExtra($key, $value)
    {
        $this->_event[self::EVENT_EXTRAS][$key] = $value;
    }
    
    /**
     * Set string
     * 
     * @param string $string
     * @return void
     */
    public function setString($string)
    {
        $this->_event[self::EVENT_STRING] = (string)$string;
    }
    
    /**
     * Get event timestamp
     * 
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->_event[self::EVENT_TIMESTAMP];
    }
    
    /**
     * Get event in string format
     * 
     * @return string
     */
    public function toString()
    {
        return (string)$this->_event[self::EVENT_STRING];
    }
}

/* EOF */