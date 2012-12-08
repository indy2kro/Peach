<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Error
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Error handler
 */
class Peach_Error_Handler
{
    /**
     * Flag to mark started
     *
     * @var boolean
     */
    protected static $_started = false;
    
    /**
     * Error exception
     * 
     * @var Peach_Error_Exception
     */
    protected static $_errorException;
    
    // @codeCoverageIgnoreStart
    /**
     * Constructor
     */
    private function __construct()
    {
        // do not allow an instance
    }
    // @codeCoverageIgnoreEnd

    /**
     * If the error handler has been started.
     *
     * @return boolean
     */
    public static function isStarted()
    {
        return self::$_started;
    }

    /**
     * Starting the error handler
     *
     * @param integer $errorLevel Error level to handle
     * @return void
     * @throws Peach_Exception
     */
    public static function start($errorLevel = E_ALL)
    {
        if (self::isStarted()) {
            throw new Peach_Exception('Error handler is already started');
        }
        
        self::$_started = true;
        self::$_errorException = null;
        
        set_error_handler(array('Peach_Error_Handler', 'addError'), $errorLevel);
    }

    /**
     * Stopping the error handler
     *
     * @param  boolean $throw Throw the Peach_Error_Exception if any was caught
     * @return null|Peach_Error_Exception
     * @throws Peach_Exception If not started before
     * @throws Peach_Error_Exception If an error has been catched and $throw is true
     */
    public static function stop($throw = false)
    {
        if (!self::isStarted()) {
            throw new Peach_Exception('Error handler is not started');
        }
        
        $errorException = self::$_errorException;

        // reset error handler
        self::reset();

        if ($throw && !is_null($errorException)) {
            throw $errorException;
        }

        return $errorException;
    }
    
    /**
     * Reset error handler
     * 
     * @return void
     */
    public static function reset()
    {
        self::$_started = false;
        self::$_errorException = null;
        restore_error_handler();
    }

    /**
     * Add an error to the stack.
     *
     * @param integer $errno
     * @param string  $errstr
     * @param string  $errfile
     * @param integer $errline
     * @return void
     */
    public static function addError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        self::$_errorException = new Peach_Error_Exception($errstr, $errno, self::$_errorException);
    }
}

/* EOF */