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
 * Buxfer service login command
 */
class Peach_Service_Buxfer_Command_Login extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Parameters
     */
    const PARAM_USER_ID = 'userid';
    const PARAM_PASSWORD = 'password';
    
    /*
     * Response constants
     */
    const RESPONSE_TOKEN = 'token';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_LOGIN;
    
    /**
     * Last token
     * 
     * @var string
     */
    protected $_lastToken;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return boolean
     */
    public function format(Array $response)
    {
        if (!isset($response[self::RESPONSE_TOKEN])) {
            return false;
        }
        
        // store last token
        $this->_lastToken = $response[self::RESPONSE_TOKEN];
        
        return true;
    }
    
    /**
     * Get last token
     * 
     * @return string
     */
    public function getLastToken()
    {
        return $this->_lastToken;
    }
}

/* EOF */