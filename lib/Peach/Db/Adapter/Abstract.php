<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Db
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Abstract database adapter
 */
abstract class Peach_Db_Adapter_Abstract
{
    /*
     * Available options
     */
    const OPT_CASE_FOLDING = 'case_folding';
    const OPT_DRIVER_OPTIONS = 'driver_options';
    const OPT_FETCH_MODE = 'fetch_mode';
    
    /*
     * Case folding options
     */
    const CASE_DEFAULT = 'default';
    const CASE_UPPER = 'upper';
    const CASE_LOWER = 'lower';
    
    /**
     * Fetch modes
     */
    const FETCH_ASSOC = 'assoc';
    const FETCH_ARRAY = 'array';
    
    /**
     * Query profiler object
     *
     * @var Peach_Db_Profiler
     */
    protected $_profiler;
    
    /**
     * Default class name for a DB statement
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Peach_Db_Statement';

    /**
     * Default class name for the profiler object
     *
     * @var string
     */
    protected $_defaultProfilerClass = 'Peach_Db_Profiler';

    /**
     * Database connection
     *
     * @var object|resource|null
     */
    protected $_connection = null;

    /**
     * Adapter options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_CASE_FOLDING => self::CASE_DEFAULT,
        self::OPT_FETCH_MODE => self::FETCH_ASSOC
    );
    
    // TODO
}

/* EOF */