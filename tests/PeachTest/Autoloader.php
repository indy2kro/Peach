<?php
/**
 * Peach Framework
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Autoloader implementation
 */
class PeachTest_Autoloader
{
    /**
     * Instance object
     * 
     * @var PeachTest_Autoloader
     */
    protected static $_instance;
    
    /**
     * Namespaces to load
     * 
     * @var array
     */
    protected $_namespaces = array(
        'Peach_', 'PeachTest_'
    );
    
    /**
     * Constructor
     */
    protected function __construct()
    {
        // singleton  
    }
    
    /**
     * Get instance
     * 
     * @return PeachTest_Autoloader
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new PeachTest_Autoloader();
        }
        
        return self::$_instance;
    }
    
    /**
     * Autoload callback for loading class files.
     *
     * @param string $class Class to load
     * @return void
     */
    public function load($class)
    {
        $found = false;

        foreach ($this->_namespaces as $namespace) {
            if (false !== strpos($class, $namespace)) {
                $found = true;
            }
        }

        if (!$found) {
            // namespace not found, do not load
            return null;
        }
        
        // build path
        $path = str_replace('_', '/', $class) . '.php';
        
        // load path
        require_once $path;
    }
    
    /**
     * Register namespace
     * 
     * @param string $namespace 
     * @return void
     */
    public function registerNamespace($namespace)
    {
        if (in_array($namespace, $this->_namespaces)) {
            return null;
        }
        
        $this->_namespaces[] = $namespace;
    }

    /**
     * Unregister namespace
     * 
     * @param string $namespace 
     * @return void
     */
    public function unregisterNamespace($namespace)
    {
        $key = array_search($namespace, $this->_namespaces);
        
        if (false === $key) {
            // key not found
            return null;
        }

        unset($this->_namespaces[$key]);
    }

    /**
     * Registers this class as an autoloader.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'load'));
    }

    /**
     * Unregisters this class as an autoloader.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load'));
    }
}

/* EOF */