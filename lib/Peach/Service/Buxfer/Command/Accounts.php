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
 * Buxfer service accounts command
 */
class Peach_Service_Buxfer_Command_Accounts extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_ACCOUNTS = 'accounts';
    const RESPONSE_ACCOUNT_ITEM = 'account';
    
    /*
     * Response account item constants
     */
    const RESPONSE_ACCOUNT_ID = 'id';
    const RESPONSE_ACCOUNT_NAME = 'name';
    const RESPONSE_ACCOUNT_BANK = 'bank';
    const RESPONSE_ACCOUNT_BALANCE = 'balance';
    const RESPONSE_ACCOUNT_CURRENCY = 'currency';
    const RESPONSE_ACCOUNT_LAST_SYNCED = 'lastSynced';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_ACCOUNTS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_ACCOUNTS]) || !isset($response[self::RESPONSE_ACCOUNTS][self::RESPONSE_ACCOUNT_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_ACCOUNTS][self::RESPONSE_ACCOUNT_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */