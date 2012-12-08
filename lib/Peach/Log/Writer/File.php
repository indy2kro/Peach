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
 * File log writer implementation
 */
class Peach_Log_Writer_File extends Peach_Log_Writer_Abstract
{
    /*
     * Available options
     */
    const OPT_FILE_LOCKING = 'file_locking';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_FILE_LOCKING => true
    );
    
    /**
     * File stream resource
     *
     * @var resource
     */
    protected $_stream;

    /**
     * Constructor
     *
     * @param string $file File name
     * @param string $mode Type of writing mode
     */
    public function __construct($file = '', $mode = 'a')
    {
        if ($file) {
            $this->setFile($file, $mode);
        }
    }

    /**
     * Set the file used for logging
     *
     * @param string $file File name
     * @param string $mode Type of writing mode
     * @return void
     * @throws Peach_Log_Exception
     */
    public function setFile($file, $mode = 'a')
    {
        // closes previous log file
        $this->shutdown();
        
        // open log file
        if (!$this->_stream = @fopen($file, $mode)) {
            throw new Peach_Log_Exception("File '" . $file . "' cannot be opened for writing using mode '" . $mode . "'");
        }
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function shutdown()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }

    /**
     * Write a message to the log.
     *
     * @param Peach_Log_Event $event Event
     * @return void
     * @throws Peach_Log_Exception
     */
    protected function _write(Peach_Log_Event $event)
    {
        // get string
        $line = $event->toString();
        
        if (!is_resource($this->_stream)) {
            // @codeCoverageIgnoreStart
            throw new Peach_Log_Exception('Invalid file resource');
            // @codeCoverageIgnoreEnd
        }

        // if locking is active try to get an exclusive lock on the file
        if ($this->_options[self::OPT_FILE_LOCKING]) {
            $result = flock($this->_stream, LOCK_EX);
            
            if (!$result) {
                // @codeCoverageIgnoreStart
                throw new Peach_Log_Exception('Failed to lock the log file');
            // @codeCoverageIgnoreEnd
            }
        }

        if (false === fwrite($this->_stream, $line)) {
            // @codeCoverageIgnoreStart
            throw new Peach_Log_Exception('Failed to write to stream');
            // @codeCoverageIgnoreEnd
        }
        
        // unlock the file
        if ($this->_options[self::OPT_FILE_LOCKING]) {
            $result = flock($this->_stream, LOCK_UN);
            
            if (!$result) {
                // @codeCoverageIgnoreStart
                throw new Peach_Log_Exception('Failed to unlock the log file');
                // @codeCoverageIgnoreEnd
            }
        }
    }
}

/* EOF */