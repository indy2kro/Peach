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
 * Buxfer service upload statement command
 */
class Peach_Service_Buxfer_Command_UploadStatement extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Parameters
     */
    const PARAM_ACCOUNT_ID = 'accountId';
    const PARAM_STATEMENT = 'statement';
    const PARAM_DATE_FORMAT = 'dateFormat';
    
    /*
     * Response constants
     */
    const RESPONSE_UPLOADED = 'uploaded';
    const RESPONSE_BALANCE = 'balance';
    
    /*
     * Date formats
     */
    const DATE_FORMAT_MM_DD_YYYY = 'MM/DD/YYYY';
    const DATE_FORMAT_DD_MM_YYYY = 'DD/MM/YYYY';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_UPLOAD_STATEMENT;
    
    /**
     * Http request method
     * 
     * @var string
     */
    protected $_httpMethod = Peach_Http_Request::METHOD_POST;
    
    /**
     * Last balance
     * 
     * @var integer
     */
    protected $_lastBalance;
    
    /**
     * Format the response structure
     * 
     * @param array $response Response structure
     * @return boolean
     */
    public function format(Array $response)
    {
        if (!isset($response[self::RESPONSE_UPLOADED]) || !$response[self::RESPONSE_UPLOADED]) {
            return false;
        }
        
        // store last balance
        $this->_lastBalance = $response[self::RESPONSE_BALANCE];
        
        return true;
    }
    
    /**
     * Get last balance
     * 
     * @return integer
     */
    public function getLastBalance()
    {
        return $this->_lastBalance;
    }
}

/* EOF */