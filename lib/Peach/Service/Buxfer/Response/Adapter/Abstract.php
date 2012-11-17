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
 * Buxfer service response abstract adapter
 */
abstract class Peach_Service_Buxfer_Response_Adapter_Abstract
{
    /**
     * HTTP response
     * 
     * @var Peach_Http_Response 
     */
    protected $_httpResponse;
    
    /**
     * Structure
     * 
     * @var array
     */
    protected $_structure;
    
    /**
     * Constructor
     * 
     * @param Peach_Http_Response $httpResponse HTTP response object
     */
    public function __construct(Peach_Http_Response $httpResponse)
    {
        $this->_httpResponse = $httpResponse;
    }
    
    /**
     * Return response as an array
     * 
     * @return array
     */
    public function toArray()
    {
        // convert input to array
        $this->_toArray();
        
        return $this->_structure;
    }
    
    /**
     * Convert input string to array
     */
    abstract protected function _toArray();
}

/* EOF */