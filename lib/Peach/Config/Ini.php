<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Config
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Config INI implementation
 */
class Peach_Config_Ini extends Peach_Config
{
    /*
     * Available options
     */
    const OPT_EXTEND_SEPARATOR = 'extend_separator';
    const OPT_NEST_SEPARATOR = 'nest_separator';
    const OPT_EXTENDS_KEYWORD = 'extends_keyword';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_READ_ONLY => true,
        self::OPT_SKIP_EXTENDS => false,
        self::OPT_EXTEND_SEPARATOR => ':',
        self::OPT_NEST_SEPARATOR => '.',
        self::OPT_EXTENDS_KEYWORD => ';extends'
    );

    /**
     * Constructor
     * 
     * @param string $filename
     * @param string $section
     * @param arrray $options
     * @return void
     */
    public function __construct($filename = null, $section = null, Array $options = array())
    {
        // set options
        $this->setOptions($options);

        if (!is_null($filename)) {
            $this->loadFile($filename, $section);
        }
    }
    
    /**
     * Load file
     * 
     * @param string $filename
     * @param string $section
     * @throws Peach_Config_Exception
     */
    public function loadFile($filename, $section = null)
    {
        if (empty($filename)) {
            throw new Peach_Config_Exception('Filename can not be empty');
        }

        // load INI file into array
        $iniArray = $this->_loadIniFile($filename);

        // load INI array
        $this->_loadIniArray($iniArray, $section);
    }
    
    /**
     * Load string
     * 
     * @param string $string
     * @param string $section
     * @throws Peach_Config_Exception
     */
    public function loadString($string, $section = null)
    {
        if (empty($string)) {
            throw new Peach_Config_Exception('String can not be empty');
        }

        // load INI string into array
        $iniArray = $this->_loadIniString($string);
        
        // load INI array
        $this->_loadIniArray($iniArray, $section);
    }
    
    /**
     * Load INI array
     * 
     * @param array  $iniArray
     * @param string $section
     * @throws Peach_Config_Exception
     */
    protected function _loadIniArray(Array $iniArray, $section = null)
    {
        if (is_null($section)) {
            // Load entire file
            $dataArray = array();
            foreach ($iniArray as $sectionName => $sectionData) {
                if (!is_array($sectionData)) {
                    $dataArray = $this->_arrayMergeRecursive($dataArray, $this->_processKey(array(), $sectionName, $sectionData));
                } else {
                    $dataArray[$sectionName] = $this->_processSection($iniArray, $sectionName);
                }
            }
            $this->loadArray($dataArray);
        } else {
            // Load one or more sections
            if (!is_array($section)) {
                $section = array($section);
            }
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($iniArray[$sectionName])) {
                    throw new Peach_Config_Exception("Section '" . $sectionName . "' cannot be found");
                }
                $dataArray = $this->_arrayMergeRecursive($this->_processSection($iniArray, $sectionName), $dataArray);

            }
            $this->loadArray($dataArray);
        }
    }
    
    /**
     * Load the INI file from disk using parse_ini_file(). Use a private error
     * handler to convert any loading errors into a Peach_Config_Exception
     *
     * @param string $filename
     * @return array
     * @throws Peach_Config_Exception
     */
    protected function _parseIniFile($filename)
    {
        set_error_handler(array($this, '_loadErrorHandler'));
        $iniArray = parse_ini_file($filename, true); // Warnings and errors are suppressed
        restore_error_handler();

        // Check if there was a error while loading file
        if (!is_null($this->_loadErrorStr)) {
            throw new Peach_Config_Exception($this->_loadErrorStr);
        }

        return $iniArray;
    }

    /**
     * Load the INI string using parse_ini_string(). Use a private error
     * handler to convert any loading errors into a Peach_Config_Exception
     *
     * @param string $string
     * @return array
     * @throws Peach_Config_Exception
     */
    protected function _parseIniString($string)
    {
        set_error_handler(array($this, '_loadErrorHandler'));
        $iniArray = parse_ini_string($string, true); // Warnings and errors are suppressed
        restore_error_handler();

        // Check if there was a error while loading string
        if (!is_null($this->_loadErrorStr)) {
            throw new Peach_Config_Exception($this->_loadErrorStr);
        }

        return $iniArray;
    }

    /**
     * Load the ini file
     *
     * @param string $filename
     * @return array
     */
    protected function _loadIniFile($filename)
    {
        // parse ini file
        $loaded = $this->_parseIniFile($filename);
        
        // process extends sections
        $iniArray = $this->_processExtends($loaded);

        return $iniArray;
    }

    /**
     * Load the ini string
     *
     * @param string $string
     * @return array
     */
    protected function _loadIniString($string)
    {
        // parse INI string
        $loaded = $this->_parseIniString($string);
        
        // process extends sections
        $iniArray = $this->_processExtends($loaded);

        return $iniArray;
    }
    
    /**
     * Process extends
     * 
     * @param array $array
     * @return array
     */
    protected function _processExtends(Array $array)
    {
        $iniArray = array();
        foreach ($array as $key => $data) {
            // get all items
            $extends = explode($this->_options[self::OPT_EXTEND_SEPARATOR], $key);
            // trim all section names
            $extends = array_map('trim', $extends);
            
            // get the section name
            $thisSection = array_shift($extends);
            
            $iniArray[$thisSection] = $data;
            
            // extends 
            if (!empty($extends)) {
                $iniArray[$thisSection][$this->_options[self::OPT_EXTENDS_KEYWORD]] = $extends;
            }
        }

        return $iniArray;
    }

    /**
     * Process each element in the section and handle the ";extends" inheritance
     * key. Passes control to _processKey() to handle the nest separator
     * sub-property syntax that may be used within the key name.
     *
     * @param array  $iniArray
     * @param string $section
     * @param array  $config
     * @throws Peach_Config_Exception
     * @return array
     */
    protected function _processSection(Array $iniArray, $section, Array $config = array())
    {
        $thisSection = $iniArray[$section];

        foreach ($thisSection as $key => $value) {
            if ($key == $this->_options[self::OPT_EXTENDS_KEYWORD] && !$this->_options[self::OPT_SKIP_EXTENDS]) {
                foreach ($value as $extended) {
                    if (!isset($iniArray[$extended])) {
                        throw new Peach_Config_Exception("Parent section '$extended' cannot be found");
                    }
                    
                    // check if the extend is valid - prevent circular extends
                    $this->_assertValidExtend($section, $extended);

                    $config = $this->_processSection($iniArray, $extended, $config);
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * Assign the key's value to the property list. Handles the
     * nest separator for sub-properties.
     *
     * @param array  $config
     * @param string $key
     * @param string $value
     * @return array
     * @throws Peach_Config_Exception
     */
    protected function _processKey(Array $config, $key, $value)
    {
        if (strpos($key, $this->_options[self::OPT_NEST_SEPARATOR]) !== false) {
            $pieces = explode($this->_options[self::OPT_NEST_SEPARATOR], $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    $config[$pieces[0]] = array();
                } elseif (!is_array($config[$pieces[0]])) {
                    throw new Peach_Config_Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                throw new Peach_Config_Exception("Invalid key '$key'");
            }
        } else {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }
        return $config;
    }
}

/* EOF */