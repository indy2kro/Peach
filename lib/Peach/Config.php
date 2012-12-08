<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Config
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Config implementation
 */
class Peach_Config extends ArrayObject
{
    /*
     * Available options
     */
    const OPT_READ_ONLY = 'read_only';
    const OPT_SKIP_EXTENDS = 'skip_extends';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_READ_ONLY => true,
        self::OPT_SKIP_EXTENDS => false
    );

    /**
     * This is used to track section inheritance. The keys are names of sections that
     * extend other sections, and the values are the extended sections.
     *
     * @var array
     */
    protected $_extends = array();

    /**
     * Load file error string.
     *
     * @var string
     */
    protected $_loadErrorStr;
    /**
     * Constructor
     * 
     * @param array $array   Values array
     * @param array $options Options
     * @return void
     */
    public function __construct(Array $array = array(), Array $options = array())
    {
        // set options
        $this->setOptions($options);
        
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = new static($value, $options);
            }
        }
        
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }
    
    /**
     * Set options array
     * 
     * @param array $options Options
     * @return void
     */
    public function setOptions(Array $options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Get an offset
     * 
     * @param string $offset
     * @return mixed
     * @throws Peach_Config_Exception
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Peach_Config_Exception("Index '" . $offset . "' not found in config object");
        }
        
        return parent::offsetGet($offset);
    }
    
    /**
     * Set an offset
     * 
     * @param string $offset
     * @param mixed $value
     * @return void
     * @throws Peach_Config_Exception
     */
    public function offsetSet($offset, $value)
    {
        if ($this->_options[self::OPT_READ_ONLY]) {
            throw new Peach_Config_Exception('Config object is read only');
        }
        
        if (is_array($value)) {
            $value = new self($value, $this->_options);
        }
        
        parent::offsetSet($offset, $value);
    }
    
    /**
     * Unset offset
     * 
     * @param string $offset
     * @return void
     * @throws Peach_Config_Exception
     */
    public function offsetUnset($offset)
    {
        if ($this->_options[self::OPT_READ_ONLY]) {
            throw new Peach_Config_Exception('Config object is read only');
        }
        
        if (!$this->offsetExists($offset)) {
            throw new Peach_Config_Exception("Index '" . $offset . "' not found in config object");
        }
        
        parent::offsetUnset($offset);
    }
    
    /**
     * Retrieve a value
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->offsetGet($name);
    }
    
    /**
     * Retrieve a value
     *
     * @param string $name
     * @return mixed
     */
    public function getSafe($name, $default = null)
    {
        $result = $default;
        
        if ($this->offsetExists($name)) {
            $result = parent::offsetGet($name);
        }
        
        return $result;
    }
    
    /**
     * Get options array
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Convert structure to array
     * 
     * @return array
     */
    public function toArray()
    {
        $array = (array)$this;

        foreach ($array as &$value) {
            if ($value instanceof self) {
                $value = $value->toArray();
            }
        }
        
        return $array;
    }
    
    /**
     * Get the current extends
     *
     * @return array
     */
    public function getExtends()
    {
        return $this->_extends;
    }

    /**
     * Set an extend
     *
     * @param string $extendingSection
     * @param string $extendedSection
     * @return void
     */
    public function setExtend($extendingSection, $extendedSection = null)
    {
        if (is_null($extendedSection) && isset($this->_extends[$extendingSection])) {
            unset($this->_extends[$extendingSection]);
        } elseif (!is_null($extendedSection)) {
            $this->_extends[$extendingSection] = $extendedSection;
        }
    }
    
    /**
     * Load array
     * 
     * @param array $array
     * @return void
     */
    protected function _loadArray(Array $array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = new self($value, $this->_options);
            }
        }
        
        $this->exchangeArray($array);
    }
    
    /**
     * Handle any errors from simplexml_load_file, parse_ini_file, parse_ini_string
     *
     * @param integer $errno
     * @param string  $errstr
     * @param string  $errfile
     * @param integer $errline
     */
    protected function _loadErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (is_null($this->_loadErrorStr)) {
            $this->_loadErrorStr = $errstr;
        } else {
            $this->_loadErrorStr .= (PHP_EOL . $errstr);
        }
    }
    
    /**
     * Throws an exception if $extendingSection may not extend $extendedSection,
     * and tracks the section extension if it is valid.
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @throws Peach_Config_Exception
     * @return void
     */
    protected function _assertValidExtend($extendingSection, $extendedSection)
    {   
        // detect circular section inheritance
        $extendedSectionCurrent = $extendedSection;
        while (array_key_exists($extendedSectionCurrent, $this->_extends)) {
            foreach ($this->_extends[$extendedSectionCurrent] as $extendedItem) {
                if ($extendedItem == $extendingSection) {
                    throw new Peach_Config_Exception('Illegal circular inheritance detected');
                }
                
                $extendedSectionCurrent = $extendedItem;
            }
        }
        
        // remember that this section extends another section
        if (!isset($this->_extends[$extendingSection])) {
            $this->_extends[$extendingSection] = array($extendedSection);
        } else {
            if (!in_array($extendedSection, $this->_extends[$extendingSection])) {
                $this->_extends[$extendingSection][] = $extendedSection;
            }
        }
    }
    
    /**
     * Merge two arrays recursively, overwriting keys of the same name
     * in $firstArray with the value in $secondArray.
     *
     * @param  mixed $firstArray  First array
     * @param  mixed $secondArray Second array to merge into first array
     * @return array
     */
    protected function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key])) {
                    $firstArray[$key] = $this->_arrayMergeRecursive($firstArray[$key], $value);
                } else {
                    $firstArray[$key] = $value;
                }
            }
        } else {
            $firstArray = $secondArray;
        }

        return $firstArray;
    }
}

/* EOF */