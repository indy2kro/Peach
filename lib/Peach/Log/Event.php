<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Log
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
    
    /**
     * Event information
     * 
     * @var array
     */
    protected $_event = array(
        self::EVENT_MESSAGE => null,
        self::EVENT_PRIORITY => null,
        self::EVENT_EXTRAS => array(),
        self::EVENT_STRING => null
    );
    
    /**
     * Constructor
     * 
     * @param string  $message
     * @param integer $priority
     * @param array   $extras
     * @return void
     */
    public function __construct($message, $priority, Array $extras = array())
    {
        $this->_event[self::EVENT_MESSAGE] = $message;
        $this->_event[self::EVENT_PRIORITY] = $priority;
        $this->_event[self::EVENT_EXTRAS] = $extras;
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