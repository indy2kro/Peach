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
 * Buxfer service groups command
 */
class Peach_Service_Buxfer_Command_Groups extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_GROUPS = 'groups';
    const RESPONSE_GROUP_ITEM = 'group';
    
    /*
     * Response group item constants
     */
    const RESPONSE_GROUP_ID = 'id';
    const RESPONSE_GROUP_NAME = 'name';
    const RESPONSE_GROUP_CONSOLIDATED = 'consolidated';
    const RESPONSE_GROUP_MEMBERS = 'members';
    
    /*
     * Response group member constants
     */
    const RESPONSE_GROUP_MEMBER_ITEM = 'member';
    const RESPONSE_GROUP_MEMBER_ID = 'id';
    const RESPONSE_GROUP_MEMBER_NAME = 'name';
    const RESPONSE_GROUP_MEMBER_EMAIL = 'email';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_GROUPS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_GROUPS]) || !isset($response[self::RESPONSE_GROUPS][self::RESPONSE_GROUP_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_GROUPS][self::RESPONSE_GROUP_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */