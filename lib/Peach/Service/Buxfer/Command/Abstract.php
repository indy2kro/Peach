<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service abstract command
 */
abstract class Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Parameters
     */
    const PARAM_TOKEN = 'token';
    
    /*
     * Response constants
     */
    const RESPONSE_STATUS = 'status';
    const RESPONSE_ERROR = 'error';
    const RESPONSE_ITEM = 'response';
    
    /*
     * Available commands
     */
    const CMD_ADD_TRANSACTION = 'add_transaction';
    const CMD_UPLOAD_STATEMENT = 'upload_statement';
    const CMD_TRANSACTIONS = 'transactions';
    const CMD_REPORTS = 'reports';
    const CMD_LOANS = 'loans';
    const CMD_TAGS = 'tags';
    const CMD_REMINDERS = 'reminders';
    const CMD_GROUPS = 'groups';
    const CMD_CONTACTS = 'contacts';
    const CMD_LOGIN = 'login';
    const CMD_BUDGETS = 'budgets';
    const CMD_ACCOUNTS = 'accounts';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd;
    
    /**
     * Http request method
     * 
     * @var string
     */
    protected $_httpMethod = Peach_Http_Request::METHOD_GET;
    
    /**
     * Get HTTP method
     * 
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }
    
    /**
     * Get command string
     * 
     * @return string
     */
    public function getCmd()
    {
        return $this->_cmd;
    }
    
    /**
     * Format the response structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        // default formatter
        return $response;
    }
}

/* EOF */