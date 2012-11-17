<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service tags command
 */
class Peach_Service_Buxfer_Command_Tags extends Peach_Service_Buxfer_Command_Abstract
{
    /*
     * Response constants
     */
    const RESPONSE_TAGS = 'tags';
    const RESPONSE_TAG_ITEM = 'tag';
    
    /*
     * Response tag item constants
     */
    const RESPONSE_TAG_ID = 'id';
    const RESPONSE_TAG_NAME = 'name';
    const RESPONSE_TAG_PARENT_ID = 'parentId';
    const RESPONSE_TAG_KEYWORDS = 'keywords';
    
    /**
     * Command string
     * 
     * @var string
     */
    protected $_cmd = self::CMD_TAGS;
    
    /**
     * Format structure
     * 
     * @param array $response Response structure
     * @return array
     */
    public function format(Array $response)
    {
        $result = array();
        
        if (!isset($response[self::RESPONSE_TAGS]) || !isset($response[self::RESPONSE_TAGS][self::RESPONSE_TAG_ITEM])) {
            return $result;
        }
        
        foreach ($response[self::RESPONSE_TAGS][self::RESPONSE_TAG_ITEM] as $item) {
            $result[] = $item;
        }
        
        return $result;
    }
}

/* EOF */