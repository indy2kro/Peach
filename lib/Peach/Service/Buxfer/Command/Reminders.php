<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service reminders command
 */
class Peach_Service_Buxfer_Command_Reminders extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_REMINDERS = 'reminders';
    const RESPONSE_REMINDER_ITEM = 'reminder';
    
    /*
     * Response reminder item constants
     */
    const RESPONSE_REMINDER_ID = 'id';
    const RESPONSE_REMINDER_DESCRIPTION = 'description';
    const RESPONSE_REMINDER_START_DATE = 'startDate';
    const RESPONSE_REMINDER_NOTIFICATION_TIME = 'notificationTime';
    const RESPONSE_REMINDER_PERIOD = 'period';
    const RESPONSE_REMINDER_AMOUNT = 'amount';
    const RESPONSE_REMINDER_ACCOUNT_ID = 'accountId';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_REMINDERS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_REMINDERS]) || !isset($response[self::RESPONSE_REMINDERS][self::RESPONSE_REMINDER_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_REMINDERS][self::RESPONSE_REMINDER_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */