<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Cache
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Cache abstract adapter
 */
abstract class Peach_Cache_Adapter_Abstract
{
    /*
     * Available methods to be used for tokens
     */
    const TOKEN_METHOD_NONE = 'none';
    const TOKEN_METHOD_CLEAN_FILE = 'clean_file';
    const TOKEN_METHOD_MD5 = 'md5';
    const TOKEN_METHOD_CRC32 = 'crc32';
    const TOKEN_METHOD_ADLER32 = 'adler32';
    
    /*
     * Available options
     */
    const OPT_LIFETIME = 'lifetime';
    const OPT_TOKEN_METHOD = 'token_method';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_LIFETIME => 3600,
        self::OPT_TOKEN_METHOD => self::TOKEN_METHOD_MD5
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
     * Load a cache item
     *
     * @param string $id Cache id
     * @return string|null Cached data
     */
    abstract public function load($id);

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return boolean
     */
    abstract public function test($id);

    /**
     * Save some string data into a cache record
     *
     * @param string $data Data to cache
     * @param string $id   Cache id
     * @param array  $tags Array of strings, the cache record will be tagged by each string entry
     * @return boolean
     */
    abstract public function save($data, $id, Array $tags = array());

    /**
     * Remove a cache record
     *
     * @param string $id Cache id
     * @return boolean True if no problem
     */
    abstract public function remove($id);

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
     * @param string $mode Clean mode
     * @param array  $tags Array of tags
     * @return boolean true if no problem
     */
    abstract public function clean($mode = Peach_Cache::CLEANING_MODE_ALL, Array $tags = array());
    
    /**
     * Get token based on id
     * 
     * @param string $id
     * @return string
     * @throws Peach_Cache_Exception
     */
    protected function _getToken($id)
    {
        switch ($this->_options[self::OPT_TOKEN_METHOD]) {
            case self::TOKEN_METHOD_NONE:
                $token = $id;
                break;
            
            case self::TOKEN_METHOD_MD5:
                $token = md5($id);
                break;
            
            case self::TOKEN_METHOD_CRC32:
                $token = crc32($id);
                break;
            
            case self::TOKEN_METHOD_ADLER32:
                $token = hash('adler32', $id);
                break;
            
            case self::TOKEN_METHOD_CLEAN_FILE:
                $token = preg_replace('/[^a-z0-9_]+/i', '_', strtolower($id));
                break;
            
            default:
                throw new Peach_Cache_Exception('Invalid token method "' . $method . '"');
                break;
        }
        
        return $token;
    }
}

/* EOF */