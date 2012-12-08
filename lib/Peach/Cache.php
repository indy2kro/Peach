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
 * Cache implementation
 */
class Peach_Cache
{
    /*
     * Available adapters
     */
    const ADAPTER_FILE = 'file';
    
    /*
     * Cleaning modes
     */
    const CLEANING_MODE_ALL              = 'all';
    const CLEANING_MODE_OLD              = 'old';
    const CLEANING_MODE_MATCHING_TAG     = 'matching_tag';
    const CLEANING_MODE_NOT_MATCHING_TAG = 'not_matching_tag';
    const CLEANING_MODE_MATCHING_ANY_TAG = 'matching_any_tag';

    /*
     * Available options
     */
    const OPT_ENABLED = 'enabled';
    const OPT_AUTOMATIC_SERIALIZATION = 'automatic_serialization';
    const OPT_LOGGER = 'logger';
    const OPT_IGNORE_USER_ABORT = 'ignore_user_abort';
    const OPT_CACHE_PREFIX = 'cache_prefix';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_ENABLED => true,
        self::OPT_AUTOMATIC_SERIALIZATION => false,
        self::OPT_LOGGER => null,
        self::OPT_IGNORE_USER_ABORT => false,
        self::OPT_CACHE_PREFIX => ''
    );
    
    /**
     * Adapter for caching
     * 
     * @var Peach_Cache_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * Constructor
     * 
     * @param string             $adapter
     * @param array|Peach_Config $options
     * @param array|Peach_Config $adapterOptions
     * @return void
     */
    public function __construct($adapter = self::ADAPTER_FILE, $options = array(), $adapterOptions = array())
    {
        // set options
        $this->setOptions($options);
        
        // build adapter object
        $adapterClassName = 'Peach_Cache_Adapter_' . ucfirst($adapter);
        $this->_adapter = new $adapterClassName($adapterOptions);
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
     * Get adapter
     * 
     * @return Peach_Cache_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param string             $id                Cache id
     * @param array|Peach_Config $contextualOptions Contextual options
     * @return mixed|false Cached data
     */
    public function load($id, $contextualOptions = array())
    {
        // backup existing options
        $originalOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (!$this->_options[self::OPT_ENABLED]) {
            // restore original options
            $this->_options = $originalOptions;
            
            return false;
        }
        
        $id = $this->_formatId($id);
        
        // load data
        $data = $this->_adapter->load($id);
        
        if (false === $data) {
            // restore original options
            $this->_options = $originalOptions;
            
            // no cache available
            return false;
        }
        
        // unserialize the data if needed
        if ($this->_options[self::OPT_AUTOMATIC_SERIALIZATION]) {
            $data = unserialize($data);
        }
        
        // restore original options
        $this->_options = $originalOptions;
        return $data;
    }
    
    /**
     * Test if a cache is available for the given id
     *
     * @param string             $id                Cache id
     * @param array|Peach_Config $contextualOptions Contextual options
     * @return integer|false Last modified time of cache entry if it is available, false otherwise
     */
    public function test($id, $contextualOptions = array())
    {
        // backup existing options
        $originalOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (!$this->_options[self::OPT_ENABLED]) {
            // restore original options
            $this->_options = $originalOptions;
            
            return false;
        }
        
        // format id
        $id = $this->_formatId($id);
        
        $result = $this->_adapter->test($id);
        
        // restore original options
        $this->_options = $originalOptions;
        return $result;
    }

    /**
     * Save some data in a cache
     *
     * @param mixed              $data              Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param string             $id                Cache id (if not set, the last cache id will be used)
     * @param array              $tags              Cache tags
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Cache_Exception
     * @return boolean
     */
    public function save($data, $id = null, $tags = array(), $contextualOptions = array())
    {
        // backup existing options
        $originalOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (!$this->_options[self::OPT_ENABLED]) {
            // restore original options
            $this->_options = $originalOptions;
            
            return true;
        }
        
        // format id
        $id = $this->_formatId($id);
                
        if ($this->_options[self::OPT_AUTOMATIC_SERIALIZATION]) {
            $data = serialize($data);
        } else {
            if (!is_string($data)) {
                // restore original options
                $this->_options = $originalOptions;

                throw new Peach_Cache_Exception('Data must be provided as string or set automatic_serialization = true');
            }
        }
        
        if ($this->_options[self::OPT_IGNORE_USER_ABORT]) {
            $abort = ignore_user_abort(true);
        }
        
        // save to adapter
        $result = $this->_adapter->save($data, $id, $tags);
        
        if ($this->_options[self::OPT_IGNORE_USER_ABORT]) {
            ignore_user_abort($abort);
        }
        
        // restore original options
        $this->_options = $originalOptions;
        return $result;
    }

    /**
     * Remove a cache
     *
     * @param string             $id                Cache id
     * @param array|Peach_Config $contextualOptions Contextual options
     * @return boolean
     */
    public function remove($id, $contextualOptions = array())
    {
        // backup existing options
        $originalOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (!$this->_options[self::OPT_ENABLED]) {
            // restore original options
            $this->_options = $originalOptions;
            
            return true;
        }
        
        // format id
        $id = $this->_formatId($id);
                
        // save to adapter
        $result = $this->_adapter->remove($id);
        
        // restore original options
        $this->_options = $originalOptions;
        return $result;
    }

    /**
     * Format id
     * 
     * @param string $id
     * @return string
     */
    protected function _formatId($id)
    {
        $prefix = (string)$this->_options[self::OPT_CACHE_PREFIX];
        
        return $prefix . $id;
    }

}

/* EOF */