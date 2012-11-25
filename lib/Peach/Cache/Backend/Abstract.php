<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Cache
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Cache abstract backend
 */
abstract class Peach_Cache_Backend_Abstract
{
    /*
     * Available options
     */
    const OPT_LIFETIME = 'lifetime';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_LIFETIME => 3600
    );
    
    /**
     * Constructor
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * Set options
     * 
     * @param array|Peach_Config $options
     * @return void
     * @throws Peach_Cache_Exception
     */
    public function setOptions($options = array())
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * Note : return value is always "string" (unserialization is done by the core not by the backend)
     *
     * @param  string  $id Cache id
     * @return string|false cached datas
     */
    public function load($id);

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id);

    /**
     * Save some string data into a cache record
     *
     * @param  string  $data     Data to cache
     * @param  string  $id       Cache id
     * @param  array   $tags     Array of strings, the cache record will be tagged by each string entry
     * @return boolean true if no problem
     */
    public function save($data, $id, Array $tags = array());

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id);

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Peach_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Peach_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Peach_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     * Peach_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not matching one of the given tags
     * Peach_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = self::CLEANING_MODE_ALL, Array $tags = array());
}

/* EOF */