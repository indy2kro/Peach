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
     * Initialize autoloader
     */
    protected function _initAutoloader()
    {
        require_once dirname(dirname(dirname(__FILE__))) . '/lib/Peach/Autoloader.php';

        $autoloader = Peach_Autoloader::getInstance();
        $autoloader->register();
        $autoloader->registerNamespace('PeachTest_');
    }
}

/* EOF */