<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service list transactions command
 */
class Peach_Service_Buxfer_Command_Transactions extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Parameters
     */
    const PARAM_ACCOUNT_ID = 'accountId';
    const PARAM_ACCOUNT_NAME = 'accountName';
    const PARAM_TAG_ID = 'tagId';
    const PARAM_TAG_NAME = 'tagName';
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_MONTH = 'month';
    const PARAM_BUDGET_ID = 'budgetId';
    const PARAM_BUDGET_NAME = 'budgetName';
    const PARAM_CONTACT_ID = 'contactId';
    const PARAM_CONTACT_NAME = 'contactName';
    const PARAM_GROUP_ID = 'groupId';
    const PARAM_GROUP_NAME = 'groupName';
    const PARAM_PAGE = 'page';
    
    /*
     * Expense types
     */
    const TYPE_EXPENSE = 'expense';
    const TYPE_INCOME = 'income';
    const TYPE_TRANSFER = 'transfer';
    
    /*
     * Status types
     * 
     * Note: When a transaction is in cleared state, the status element is not returned
     */
    const STATUS_CLEARED = 'Cleared';
    const STATUS_PENDING = 'Pending';
    const STATUS_RECONCILED = 'Reconciled';
    
    /*
     * Response constants
     */
    const RESPONSE_NUM_TRANSACTIONS = 'numTransactions';
    const RESPONSE_TRANSACTIONS = 'transactions';
    const RESPONSE_TRANSACTION_ITEM = 'transaction';
    
    /*
     * Response transaction constants
     */
    const RESPONSE_TRANSACTION_ID = 'id';
    const RESPONSE_TRANSACTION_DESCRIPTION = 'description';
    const RESPONSE_TRANSACTION_DATE = 'date';
    const RESPONSE_TRANSACTION_TYPE = 'type';
    const RESPONSE_TRANSACTION_AMOUNT = 'amount';
    const RESPONSE_TRANSACTION_ACCOUNT_ID = 'accountId';
    const RESPONSE_TRANSACTION_TAGS = 'tags';
    const RESPONSE_TRANSACTION_EXTRA_INFO = 'extraInfo';
    const RESPONSE_TRANSACTION_STATUS = 'status';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_TRANSACTIONS;
    
    /**
     * Last count
     * 
     * @var integer
     */
    protected $_lastCount;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (isset($response[self::RESPONSE_NUM_TRANSACTIONS])) {
            $this->_lastCount = $response[self::RESPONSE_NUM_TRANSACTIONS];
        }
        
        if (!isset($response[self::RESPONSE_TRANSACTIONS]) || !isset($response[self::RESPONSE_TRANSACTIONS][self::RESPONSE_TRANSACTION_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_TRANSACTIONS][self::RESPONSE_TRANSACTION_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
    
    /**
     * Get last count
     * 
     * @return integer
     */
    public function getLastCount()
    {
        return $this->_lastCount;
    }
}

/* EOF */