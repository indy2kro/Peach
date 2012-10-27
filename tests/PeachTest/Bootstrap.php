<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Test bootstrap
 */
class PeachTest_Bootstrap
{
    /**
     * Initialize environment
     */
    public function init()
    {
        // set error reporting level
        $this->_setErrorReporting();
        
        // set timezone
        $this->_setTimezone();
        
        // set include path
        $this->_setIncludePath();
        
        // initialize autoloader
        $this->_initAutoloader();
    }
    
    /**
     * Set error reporting level
     */
    protected function _setErrorReporting()
    {
        error_reporting(E_ALL);
    }
    
    /**
     * Set include path
     */
    protected function _setIncludePath()
    {
        set_include_path(
                get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__))
                . PATH_SEPARATOR . dirname(dirname(dirname(__FILE__))) . '/lib/'
        );
    }
    
    /**
     * Set timezone
     */
    protected function _setTimezone()
    {
        date_default_timezone_set('UTC');
    }
    
    /**
     * Initialize autoloader
     */
    protected function _initAutoloader()
    {
        require_once dirname(__FILE__) . '/Autoloader.php';

        $autoloader = PeachTest_Autoloader::getInstance();
        $autoloader->register();
    }
}

/* EOF */