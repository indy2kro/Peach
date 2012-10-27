<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Log implementation
 */
class Peach_Log
{
    /*
     * Priority values
     */
    const EMERGENCY = 0;  // Emergency: system is unusable
    const ALERT     = 1;  // Alert: action must be taken immediately
    const CRITICAL  = 2;  // Critical: critical conditions
    const ERROR     = 3;  // Error: error conditions
    const WARNING   = 4;  // Warning: warning conditions
    const NOTICE    = 5;  // Notice: normal but significant condition
    const INFO      = 6;  // Informational: informational messages
    const DEBUG     = 7;  // Debug: debug messages
    
    /*
     * Priority names
     */
    const PRIORITY_EMERGENCY = 'EMERGENCY';
    const PRIORITY_ALERT = 'ALERT';
    const PRIORITY_CRITICAL = 'CRITICAL';
    const PRIORITY_ERROR = 'ERROR';
    const PRIORITY_WARNING = 'WARNING';
    const PRIORITY_NOTICE = 'NOTICE';
    const PRIORITY_INFO = 'INFO';
    const PRIORITY_DEBUG = 'DEBUG';

    /*
     * Available options
     */
    const OPT_TRACK_MEMORY_USAGE = 'track_memory_usage';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_TRACK_MEMORY_USAGE => false
    );
    
    /**
     * List of priorities
     * 
     * @var array
     */
    protected $_priorities = array(
        self::PRIORITY_EMERGENCY => self::EMERGENCY,
        self::PRIORITY_ALERT => self::ALERT,
        self::PRIORITY_CRITICAL => self::CRITICAL,
        self::PRIORITY_ERROR => self::ERROR,
        self::PRIORITY_WARNING => self::WARNING,
        self::PRIORITY_NOTICE => self::NOTICE,
        self::PRIORITY_INFO => self::INFO,
        self::PRIORITY_DEBUG => self::DEBUG
    );

    /**
     * Writers attached
     * 
     * @var array
     */
    protected $_writers = array();

    /**
     * Extra parameters for log events
     * 
     * @var array
     */
    protected $_extras = array();
    
    /**
     * Constructor
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * Class destructor.  Shutdown log writers
     *
     * @return void
     */
    public function __destruct()
    {
        foreach($this->_writers as $writer) {
            $writer->shutdown();
        }
    }

    /**
     * Set options
     * 
     * @param array $options
     * @return void
     */
    public function setOptions($options = array())
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Add writer to log
     * 
     * @param Peach_Log_Writer_Abstract $writer
     * @return void
     */
    public function addWriter(Peach_Log_Writer_Abstract $writer)
    {
        $this->_writers[] = $writer;
    }
    
    /**
     * Log message
     * 
     * @param string  $message
     * @param integer $priority
     * @param array   $extras
     * @return void
     * @throws Peach_Log_Exception
     */
    public function log($message, $priority, Array $extras = array())
    {
        if (!isset($this->_priorities[$priority])) {
            throw new Peach_Log_Exception('Invalid log priority provided: ' . $priority);
        }
        
        // build event
        $event = new Peach_Log_Event($message, $priority, $extras);
        
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }
}

/* EOF */