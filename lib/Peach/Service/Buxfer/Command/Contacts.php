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
 * Buxfer service contacts command
 */
class Peach_Service_Buxfer_Command_Contacts extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_CONTACTS = 'contacts';
    const RESPONSE_CONTACT_ITEM = 'contact';
    
    /*
     * Response contact item constants
     */
    const RESPONSE_CONTACT_ID = 'id';
    const RESPONSE_CONTACT_NAME = 'name';
    const RESPONSE_CONTACT_EMAIL = 'email';
    const RESPONSE_CONTACT_BALANCE = 'balance';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_CONTACTS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_CONTACTS]) || !isset($response[self::RESPONSE_CONTACTS][self::RESPONSE_CONTACT_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_CONTACTS][self::RESPONSE_CONTACT_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */