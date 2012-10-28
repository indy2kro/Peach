<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Config
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Config implementation
 */
class Peach_Config implements Countable, Iterator
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
     * Contains array of configuration data
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Counter
     * 
     * @var integer
     */
    protected $_count = 0;
    
    /**
     * Current index
     * 
     * @var integer
     */
    protected $_index = 0;
    
    /**
     * Load file error string.
     *
     * Is null if there was no error while file loading
     *
     * @var string
     */
    protected $_loadErrorStr = null;
    
    /**
     * This is used to track section inheritance. The keys are names of sections that
     * extend other sections, and the values are the extended sections.
     *
     * @var array
     */
    protected $_extends = array();

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
        
        // load array
        $this->loadArray($array);
    }
    
    /**
     * Deep clone of this instance to ensure that nested Peach_Config objects are also cloned
     *
     * @return void
     */
    public function __clone()
    {
      $array = array();
      
      foreach ($this->_data as $key => $value) {
          if ($value instanceof Peach_Config) {
              $array[$key] = clone $value;
          } else {
              $array[$key] = $value;
          }
      }
      
      $this->_data = $array;
    }

    /**
     * Support isset() overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Support unset() overloading
     *
     * @param string $name
     * @throws Peach_Config_Exception
     * @return void
     */
    public function __unset($name)
    {
        if ($this->_options[self::OPT_READ_ONLY]) {
            throw new Peach_Config_Exception('Config object is read only');
        }
        
        unset($this->_data[$name]);
        $this->_count = count($this->_data);
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Peach_Config_Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if ($this->_options[self::OPT_READ_ONLY]) {
            throw new Peach_Config_Exception('Config object is read only');
        }
        
        if (is_array($value)) {
            $this->_data[$name] = new self($value, $this->_options);
        } else {
            $this->_data[$name] = $value;
        }
        
        // update counter
        $this->_count = count($this->_data);
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
     * Load array
     * 
     * @param array $array
     * @return void
     */
    public function loadArray(Array $array)
    {
        $this->_data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value, $this->_options);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    /**
     * Retrieve a value
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Peach_Config_Exception("Index '" . $name . "' not found in config object");
        }
        return $this->_data[$name];
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    public function getSafe($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
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
        $array = array();
        $data = $this->_data;
        
        foreach ($data as $key => $value) {
            if ($value instanceof Peach_Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        
        return $array;
    }
    
    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next()
    {
        next($this->_data);
        $this->_index++;
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind()
    {
        reset($this->_data);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->_count;
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