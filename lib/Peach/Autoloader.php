<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Autoloader
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Autoloader implementation
 */
class Peach_Autoloader
{
    /*
     * Available options
     */
    const OPT_CHECK_NAMESPACES = 'check_namespaces';
    
    /**
     * Instance object
     * 
     * @var Peach_Autoloader
     */
    protected static $_instance;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_CHECK_NAMESPACES => true
    );
    
    /**
     * Namespaces to load
     * 
     * @var array
     */
    protected $_namespaces = array(
        'Peach_'
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
     * @param array|Peach_Config $options Options
     * @return Peach_Autoloader
     */
    public static function getInstance($options = array())
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Peach_Autoloader();
        }
        
        // set options
        self::$_instance->setOptions($options);

        return self::$_instance;
    }
    
    /**
     * Set options
     * 
     * @param array|Peach_Config $options Options
     * @return void
     * @throws Peach_Exception
     */
    public function setOptions($options = array())
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Autoload callback for loading class files.
     *
     * @param string $class Class to load
     * @return void
     */
    public function load($class)
    {
        if ($this->_options[self::OPT_CHECK_NAMESPACES]) {
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