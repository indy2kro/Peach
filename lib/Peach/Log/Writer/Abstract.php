<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Log
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Abstract log writer implementation
 */
abstract class Peach_Log_Writer_Abstract
{
    /**
     * Formatter attached
     * 
     * @var array
     */
    protected $_formatter;

    /**
     * Filters attached
     * 
     * @var array
     */
    protected $_filters = array();

    /**
     * Add formatter to log
     * 
     * @param Peach_Log_Formatter_Abstract $formatter
     * @return void
     */
    public function setormatter(Peach_Log_Formatter_Abstract $formatter)
    {
        $this->_formatter = $formatter;
    }
    
    /**
     * Add filter to log
     * 
     * @param Peach_Log_Filter_Abstract $filter
     * @return void
     */
    public function addFilter(Peach_Log_Filter_Abstract $filter)
    {
        $this->_filters[] = $filter;
    }
    
    /**
     * Shutdown method
     */
    public function shutdown()
    {
        // default shutdown method, nothing to do
    }
    
    /**
     * Write event information
     * 
     * @param Peach_Log_Event $event
     * @return boolean
     */
    public function write(Peach_Log_Event $event)
    {
        // check if all filters are passed
        foreach ($this->_filters as $filter) {
            if (!$filter->accept($event)) {
                return false;
            }
        }
        
        // format event
        $this->_formatter->format($event);
        
        // write event information
        $this->_write($event);
        
        return true;
    }
    
    /**
     * Write method
     * 
     * @param Peach_Log_Event $event
     * @return void
     */
    abstract protected function _write(Peach_Log_Event $event);
}

/* EOF */