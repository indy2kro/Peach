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
 * Buxfer service add transaction command
 */
class Peach_Service_Buxfer_Command_AddTransaction extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Parameters
     */
    const PARAM_FORMAT = 'format';
    const PARAM_TEXT = 'text';
    
    /*
     * Response constants
     */
    const RESPONSE_TRANSACTION_ADDED = 'transactionAdded';
    const RESPONSE_PARSE_STATUS = 'parseStatus';
    
    /**
     * Available format values
     */
    const FORMAT_SMS = 'sms';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_ADD_TRANSACTION;
    
    /**
     * Http request method
     * 
     * @var string
     */
    protected $_httpMethod = Peach_Http_Request::METHOD_POST;
    
    /**
     * Format the response structure
     * 
     * @param array $response Response structure
     * @return boolean
     */
    public function format(Array $response)
    {
        if (!isset($response[self::RESPONSE_PARSE_STATUS]) || !$response[self::RESPONSE_PARSE_STATUS]) {
            return false;
        }
        
        if (!isset($response[self::RESPONSE_TRANSACTION_ADDED]) || !$response[self::RESPONSE_TRANSACTION_ADDED]) {
            return false;
        }
        
        return true;
    }
}

/* EOF */