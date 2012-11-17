<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Registry
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Registry implementation
 */
class Peach_Registry extends ArrayObject
{
    /**
     * Registry object provides storage for shared objects
     * 
     * @var Peach_Registry
     */
    protected static $_registry;

    /**
     * Retrieves the registry instance
     *
     * @return Peach_Registry
     */
    public static function getInstance()
    {
        if (is_null(self::$_registry)) {
            self::_init();
        }

        return self::$_registry;
    }

    /**
     * Unset the default registry instance.
     * 
     * @returns void
     */
    public static function unsetInstance()
    {
        self::$_registry = null;
    }

    /**
     * getter method, basically same as offsetGet().
     *
     * This method can be called from an object of type Peach_Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index Index to get
     * @return mixed
     * @throws Peach_Exception
     */
    public static function get($index)
    {
        $instance = self::getInstance();

        if (!$instance->offsetExists($index)) {
            throw new Peach_Exception("No entry is registered for key '$index'");
        }

        return $instance->offsetGet($index);
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type Peach_Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index The location in which to store the value
     * @param mixed  $value The value to store
     * @return void
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->offsetSet($index, $value);
    }

    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param string $index
     * @return boolean
     */
    public static function isRegistered($index)
    {
        if (is_null(self::$_registry)) {
            return false;
        }
        
        return self::$_registry->offsetExists($index);
    }
    
    /**
     * Remove a value from the registry
     *
     * @param string $index
     * @return void
     */
    public static function remove($index)
    {
        if (is_null(self::$_registry)) {
            return null;
        }
        
        self::$_registry->offsetUnset($index);
    }
    
    /**
     * Initialize the registry instance
     *
     * @return void
     */
    protected static function _init()
    {
        self::$_registry = new self(array(), self::ARRAY_AS_PROPS);
    }
}

/* EOF */