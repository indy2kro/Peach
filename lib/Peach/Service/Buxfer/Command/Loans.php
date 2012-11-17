<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service loans command
 */
class Peach_Service_Buxfer_Command_Loans extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_LOANS = 'loans';
    const RESPONSE_LOAN_ITEM = 'loan';
    
    /*
     * Response loan item constants
     */
    const RESPONSE_LOAN_ENTITY = 'entity';
    const RESPONSE_LOAN_TYPE = 'type';
    const RESPONSE_LOAN_BALANCE = 'balance';
    const RESPONSE_LOAN_DESCRIPTION = 'description';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_LOANS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_LOANS]) || !isset($response[self::RESPONSE_LOANS][self::RESPONSE_LOAN_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_LOANS][self::RESPONSE_LOAN_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */