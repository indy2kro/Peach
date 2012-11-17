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
 * Buxfer service budgets command
 */
class Peach_Service_Buxfer_Command_Budgets extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_BUDGETS = 'budgets';
    const RESPONSE_BUDGET_ITEM = 'budget';
    
    /*
     * Response budget item constants
     */
    const RESPONSE_BUDGET_ID = 'id';
    const RESPONSE_BUDGET_NAME = 'name';
    const RESPONSE_BUDGET_LIMIT = 'limit';
    const RESPONSE_BUDGET_BALANCE = 'balance';
    const RESPONSE_BUDGET_PERIOD = 'period';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_BUDGETS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_BUDGETS]) || !isset($response[self::RESPONSE_BUDGETS][self::RESPONSE_BUDGET_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_BUDGETS][self::RESPONSE_BUDGET_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */