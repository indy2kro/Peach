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
 * Buxfer service response
 */
class Peach_Service_Buxfer_Response
{
    /**
     * Adapter
     * 
     * @var Peach_Service_Buxfer_Response_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * Constructor
     * 
     * @param Peach_Http_Response $httpResponse HTTP response object
     * @return void
     */
    public function __construct(Peach_Http_Response $httpResponse)
    {
        // only XML adapter is supported for now
        $this->_adapter = new Peach_Service_Buxfer_Response_Adapter_Xml($httpResponse);
    }
    
    /**
     * Return response formatted as array
     * 
     * @throws Peach_Service_Buxfer_Response_Exception
     * @return array
     */
    public function toArray()
    {
        // convert to array
        $response = $this->_adapter->toArray();

        // check if error member exists
        if (isset($response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ERROR])) {
            throw new Peach_Service_Buxfer_Response_Exception($response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ERROR]['message']);
        }
        
        // check if response status exists
        if (!isset($response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ITEM]) || !isset($response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ITEM]['status'])) {
            throw new Peach_Service_Buxfer_Response_Exception('Invalid response received.');
        }
        
        // if response status is not OK, throw exception
        if ('OK' != $response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ITEM]['status']) {
            throw new Peach_Service_Buxfer_Response_Exception('Request failed: ' . $response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ITEM]['status']);
        }

        return $response[Peach_Service_Buxfer_Command_Abstract::RESPONSE_ITEM];
    }
}
        
/* EOF */