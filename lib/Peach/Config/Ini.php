<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Config
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
     * @throws Peach_Config_Exception
     */
    public function __construct($filename, $section = null, Array $options = array())
    {
        if (empty($filename)) {
            throw new Peach_Config_Exception('Filename can not be empty');
        }
        
        // set options
        $this->setOptions($options);
        
        $iniArray = $this->_loadIniFile($filename);

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
            parent::__construct($dataArray, $options);
        } else {
            // Load one or more sections
            if (!is_array($section)) {
                $section = array($section);
            }
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($iniArray[$sectionName])) {
                    throw new Peach_Config_Exception("Section '$sectionName' cannot be found in $filename");
                }
                $dataArray = $this->_arrayMergeRecursive($this->_processSection($iniArray, $sectionName), $dataArray);

            }
            parent::__construct($dataArray, $options);
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
        set_error_handler(array($this, '_loadFileErrorHandler'));
        $iniArray = parse_ini_file($filename, true); // Warnings and errors are suppressed
        restore_error_handler();

        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            throw new Peach_Config_Exception($this->_loadFileErrorStr);
        }

        return $iniArray;
    }

    /**
     * Load the ini file and preprocess the section separator (':' in the
     * section name (that is used for section extension) so that the resultant
     * array has the correct section names and the extension information is
     * stored in a sub-key called ';extends'. We use ';extends' as this can
     * never be a valid key name in an INI file that has been loaded using
     * parse_ini_file().
     *
     * @param string $filename
     * @return array
     * @throws Peach_Config_Exception
     */
    protected function _loadIniFile($filename)
    {
        $loaded = $this->_parseIniFile($filename);
        $iniArray = array();
        foreach ($loaded as $key => $data) {
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