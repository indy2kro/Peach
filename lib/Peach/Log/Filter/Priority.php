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
 * Priority log filter implementation
 */
class Peach_Log_Filter_Priority extends Peach_Log_Filter_Abstract
{
    /**
     * Accepted minimum priority level
     * 
     * @var integer
     */
    protected $_priority;
    
    /**
     * Constructor
     * 
     * @param integer $priority
     * @return void
     */
    public function __construct($priority)
    {
        if (!is_integer($priority)) {
            throw new Peach_Log_Exception('Priority must be an integer');
        }
        
        $this->_priority = $priority;
    }
    
    /**
     * Accept method
     * 
     * @param Peach_Log_Event $event
     * @return boolean
     */
    public function accept(Peach_Log_Event $event)
    {
        $accepted = ($event->getPriority() <= $this->_priority);
        
        return $accepted;
    }
}

/* EOF */