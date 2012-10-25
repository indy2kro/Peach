<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Db
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Database implementation
 */
class Peach_Db
{
    /*
     * Available adapters
     */
    const ADAPTER_MYSQLI = 'Mysqli';
    
    /*
     * Available options
     */
    const OPT_ADAPTER_NAMESPACE = 'adapter_namespace';
    const OPT_FORMAT_ADAPTER_NAME = 'format_adapter_name';
    
    /**
     * Default options
     * 
     * @var array
     */
    protected static $_defaultOptions = array(
        self::OPT_ADAPTER_NAMESPACE => 'Peach_Db_Adapter',
        self::OPT_FORMAT_ADAPTER_NAME => true
    );
    
    /**
     * Factory method
     * 
     * @param string             $adapterName
     * @param array|Peach_Config $options
     * @param array|Peach_Config $adapterOptions
     * @return Peach_Db_Adapter_Abstract
     * @throws Peach_Db_Exception
     */
    public static function factory($adapterName, $options = array(), $adapterOptions = array())
    {
        // convert options to array if they are provided as config object
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        // validate options format
        if (!is_array($options)) {
            throw new Peach_Db_Exception('Options must be provided as an array or Peach_Config object');
        }
        
        // convert adapter options to array if they are provided as config object
        if ($adapterOptions instanceof Peach_Config) {
            $adapterOptions = $adapterOptions->toArray();
        }
        
        // validate adapter options format
        if (!is_array($adapterOptions)) {
            throw new Peach_Db_Exception('Adapter options must be provided as an array or Peach_Config object');
        }
        
        // validate adapter name
        if (!is_string($adapterName) || empty($adapterName)) {
            throw new Peach_Db_Exception('Adapter name must be provided as a string');
        }

        // merge default options with provided values
        $options = array_merge(self::$_defaultOptions, $options);
        
        // format adapter name if needed
        if ($options[self::OPT_FORMAT_ADAPTER_NAME]) {
            $adapterName = self::_formatAdapterName($adapterName);
        }
        
        // build adapter class name
        $adapterClassName = $options[self::OPT_ADAPTER_NAMESPACE] . '_' . $adapterName;
        
        // create new adapter object
        $adapter = new $adapterClassName($adapterOptions);
        
        // adapter must extend the abstract
        if (! $adapter instanceof Peach_Db_Adapter_Abstract) {
            throw new Peach_Db_Exception("Adapter class '" . $adapterClassName . "' does not extend Peach_Db_Adapter_Abstract");
        }
        
        return $adapter;
    }
    
    /**
     * Format adapter name
     * 
     * @param string $adapterName
     * @return string
     */
    protected static function _formatAdapterName($adapterName)
    {
        // split in words
        $adapterName = str_replace('_', ' ', $adapterName);
        
        // make first letter uppercase for all words
        $adapterName = ucwords($adapterName);
        
        // replace back the space with underscore
        $formattedAdapterName = str_replace(' ', '_', $adapterName);
        
        return $formattedAdapterName;
    }
}

/* EOF */