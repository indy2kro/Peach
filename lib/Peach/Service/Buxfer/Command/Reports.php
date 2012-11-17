<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service reports command
 */
class Peach_Service_Buxfer_Command_Reports extends Peach_Service_Buxfer_Command_Abstract
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
     * Response constants
     */
    const RESPONSE_ANALYSIS = 'analysis';
    const RESPONSE_ANALYSIS_RAW_DATA = 'rawData';
    const RESPONSE_ANALYSIS_IMAGE_URL = 'imageURL';
    const RESPONSE_ANALYSIS_BUXFER_IMAGE_URL = 'buxferImageURL';
    const RESPONSE_ANALYSIS_BREADCRUMB = 'breadCrumb';
    const RESPONSE_ANALYSIS_ITEM = 'item';
    
    /*
     * Response analysis item constants
     */
    const RESPONSE_ANALYSIS_TAG = 'tag';
    const RESPONSE_ANALYSIS_TAG_ID = 'tagId';
    const RESPONSE_ANALYSIS_COLOR = 'color';
    const RESPONSE_ANALYSIS_AMOUNT = 'amount';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_REPORTS;
    
    /**
     * Last image url
     * 
     * @var string
     */
    protected $_lastImageUrl;
    
    /**
     * Last buxfer image url
     * 
     * @var string
     */
    protected $_lastBuxferImageUrl;
    
    /**
     * Last breadcrumb
     * 
     * @var string
     */
    protected $_lastBreadcrumb;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_ANALYSIS]) || !isset($response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_RAW_DATA])) {
            return $result;
        }
        
        if (isset($response[self::RESPONSE_ANALYSIS]) && isset($response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_IMAGE_URL])) {
            $this->_lastImageUrl = $response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_IMAGE_URL];
        }
        
        if (isset($response[self::RESPONSE_ANALYSIS]) && isset($response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_BUXFER_IMAGE_URL])) {
            $this->_lastBuxferImageUrl = $response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_BUXFER_IMAGE_URL];
        }
        
        if (isset($response[self::RESPONSE_ANALYSIS]) && isset($response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_BREADCRUMB])) {
            $this->_lastBreadcrumb = $response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_BREADCRUMB];
        }
        
        foreach ($response[self::RESPONSE_ANALYSIS][self::RESPONSE_ANALYSIS_RAW_DATA][self::RESPONSE_ANALYSIS_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
    
    /**
     * Get last image url
     * 
     * @return string
     */
    public function getLastImageUrl()
    {
        return $this->_lastImageUrl;
    }
    
    /**
     * Get last buxfer image url
     * 
     * @return string
     */
    public function getLastBuxferImageUrl()
    {
        return $this->_lastBuxferImageUrl;
    }
    
    /**
     * Get last breadcrumb
     * 
     * @return string
     */
    public function getLastBreadcrumb()
    {
        return $this->_lastBreadcrumb;
    }
}

/* EOF */