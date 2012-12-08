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
     * Extra items
     */
    const EXTRA_MEMORY = 'memory';
    const EXTRA_MICROTIME = 'microtime';
    const EXTRA_DURATION = 'duration';

    /*
     * Available options
     */
    const OPT_TRACK_MEMORY_USAGE = 'track_memory_usage';
    const OPT_TRACK_DURATION = 'track_duration';
    const OPT_COMPUTE_MICROSECONDS = 'compute_microseconds';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_TRACK_MEMORY_USAGE => false,
        self::OPT_COMPUTE_MICROSECONDS => false,
        self::OPT_TRACK_DURATION => false
    );
    
    /**
     * List of priorities
     * 
     * @var array
     */
    protected $_priorities = array(
        self::EMERGENCY, self::ALERT, self::CRITICAL,
        self::ERROR, self::WARNING, self::NOTICE,
        self::INFO, self::DEBUG
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
    protected $_extras = array(
        self::EXTRA_MEMORY => null,
        self::EXTRA_MICROTIME => null,
        self::EXTRA_DURATION => null
    );
    
    /**
     * Minumum memory used
     * 
     * @var integer
     */
    protected $_memoryMin;

    /**
     * Maximum memory used
     * 
     * @var integer
     */
    protected $_memoryMax;

    /**
     * Initial timestamp
     * 
     * @var float
     */
    protected $_startTimestamp;
    
    /**
     * Last timestamp
     * 
     * @var float
     */
    protected $_lastTimestamp;

    /**
     * Constructor
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        // set options
        $this->setOptions($options);
        
        // set a start
        $this->_startTimestamp = $this->_lastTimestamp = $this->_getMicrotime();

        if ($this->_options[self::OPT_TRACK_MEMORY_USAGE]) {
            // set the min and max memory to the current value
            $this->_memoryMin = $this->_memoryMax = $this->_getMemoryUsage();
        }
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
        if (!in_array($priority, $this->_priorities)) {
            throw new Peach_Log_Exception('Invalid log priority provided: ' . $priority);
        }
        
        // add extras if provided
        $extras = array_merge($this->_extras, $extras);
        
        // add microseconds
        if ($this->_options[self::OPT_COMPUTE_MICROSECONDS]) {
            // get current time in microseconds
            $currentMicrotime = $this->_getMicrotime();

            // set current time in seconds
            $currentSeconds = (int)$currentMicrotime;

            // compute number of microseconds
            $microseconds = (int)( ( $currentMicrotime - $currentSeconds ) * 1000000 );
            
            // set extra item
            $extras[self::EXTRA_MICROTIME] = $microseconds;
        }
        
        // add memory usage
        if ($this->_options[self::OPT_TRACK_MEMORY_USAGE]) {
            // compute memory - it will be used later also
            $memoryUsage = $this->_getMemoryUsage();
            
            // set extra item
            $extras[self::EXTRA_MEMORY] = $memoryUsage;
        }
        
        // track duration
        if ($this->_options[self::OPT_TRACK_DURATION]) {
            $extras[self::EXTRA_DURATION] = $this->getElapsedTime();
        }
        
        // build event
        $event = new Peach_Log_Event($message, $priority, $extras);
        
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }
    
    /**
     * Log message on DEBUG level
     *
     * @param string $message Message to log
     * @return void
     */
    public function debug($message)
    {
        $this->log($message, self::DEBUG);
    }
    
    /**
     * Log message on INFO level
     *
     * @param string $message Message to log
     * @return void
     */
    public function info($message)
    {
        $this->log($message, self::INFO);
    }
    
    /**
     * Log message on NOTICE level
     *
     * @param string $message Message to log
     * @return void
     */
    public function notice($message)
    {
        $this->log($message, self::NOTICE);
    }
    
    /**
     * Log message on WARNING level
     *
     * @param string $message Message to log
     * @return void
     */
    public function warning($message)
    {
        $this->log($message, self::WARNING);
    }
    
    /**
     * Log message on ERROR level
     *
     * @param string $message Message to log
     * @return void
     */
    public function error($message)
    {
        $this->log($message, self::ERROR);
    }
    
    /**
     * Log message on CRITICAL level
     *
     * @param string $message Message to log
     * @return void
     */
    public function critical($message)
    {
        $this->log($message, self::CRITICAL);
    }
    
    /**
     * Log message on ALERT level
     *
     * @param string $message Message to log
     * @return void
     */
    public function alert($message)
    {
        $this->log($message, self::ALERT);
    }
    
    /**
     * Log message on EMERGENCY level
     *
     * @param string $message Message to log
     * @return void
     */
    public function emergency($message)
    {
        $this->log($message, self::EMERGENCY);
    }
    
    /**
     * Set an extra item to pass to the log writers.
     *
     * @param $name  Name of the field
     * @param $value Value of the field
     * @return void
     */
    public function setEventItem($name, $value)
    {
        $this->_extras = array_merge($this->_extras, array($name => $value));
    }
    
    /**
     * Returns the elapsed time from the logger initialization
     *
     * @return float
     */
    public function getElapsedTime()
    {
        $total = $this->_lastTimestamp - $this->_startTimestamp;

        return sprintf('%05f', $total);
    }
    
    /**
     * Get memory usage statistics
     * 
     * @return array
     */
    public function getMemoryStatistics()
    {
        $result = array(
            'min' => $this->_memoryMin,
            'max' => $this->_memoryMax
        );
        
        return $result;
    }
    
    /**
     * Current microtime
     * 
     * @return float
     */
    protected function _getMicrotime()
    {
        return gettimeofday(true);
    }
    
    /**
     * Get memory usage
     * 
     * @return integer
     */
    protected function _getMemoryUsage()
    {
        return memory_get_usage();
    }
}

/* EOF */