<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Math
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Fast Fibonacci implementation
 */
class Peach_Math_FastFibo
{
    /**
     * Fibonacci cache table
     * 
     * @var Peach_Math_FastFibo_Table 
     */
    protected $_table;
    
    /**
     * First value
     * 
     * @var Math_Number
     */
    protected $_value1;
    
    /**
     * Second value
     * 
     * @var Math_Number
     */
    protected $_value2;
    
    /**
     * Limit
     * 
     * @var integer
     */
    protected $_n;
    
    /**
     * Fibonacci series
     * 
     * @var array
     */
    protected $_series = array();
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_table = new Peach_Math_FastFibo_Table();
    }
    
    /**
     * Compute Fibonacci number
     * 
     * @param string $n
     * @return string
     */
    public function compute($n)
    {
        // check cache table first
        $value = $this->_table->get($n);
        
        if (!is_null($value)) {
            return $value;
        }
        
        // get starting marker
        $startFrom = $this->_table->getMarker($n);
        
        $computeResult = $this->_computeFibo($n, $startFrom);
        
        return $computeResult;
    }
    
    /**
     * Compute Fibonacci series
     * 
     * @param integer $start
     * @param integer $end
     * @return array
     */
    public function series($start, $end)
    {
        if ($start > $end) {
            throw new Peach_Math_Exception('Start value must be lower than end value.');
        }
        
        $this->_series = array();
        
        if ($end > $start + 2) {
            $firstValue = $this->compute($start);
            $secondValue = $this->compute($start+1);
            
            $this->_seriesCallback($start, $firstValue);
            $this->_seriesCallback($start+1, $secondValue);
            
            $this->_value1->setValue($firstValue);
            $this->_value2->setValue($secondValue);

            $this->_n = $end + 1;

            $this->_runFibo($start+2, array($this, '_seriesCallback'));
            
        } else {
            for ($i = $start; $i <= $end; $i++) {
                $this->_seriesCallback($i, $this->compute($i));
            }
        }
        
        return $this->_series;
    }
    
    /**
     * Compute Fibonacci starting from cached value
     * 
     * @param integer $n
     * @param integer $startFrom
     * @return string
     */
    protected function _computeFibo($n, $startFrom)
    {
        $this->_n = $n;
        
        $this->_value1 = new Math_Number($this->_table->get($startFrom - 1));
        $this->_value2 = new Math_Number($this->_table->get($startFrom));
        
        $this->_runFibo($startFrom);
        
        return $this->_value2->toString();
    }
    
    /**
     * Run iterative Fibonacci algorithm
     * 
     * @param integer  $iteration
     * @param callback $callback
     */
    protected function _runFibo($iteration, $callback = null)
    {
        if ($iteration >= $this->_n) {
            return null;
        }
        
        // store old value
        $temp = clone $this->_value2;
        
        // add previous 2 numbers
        $this->_value2->add($this->_value1);
        
        // store previous value
        $this->_value1 = $temp;
        
        if (!is_null($callback)) {
            call_user_func_array($callback, array($iteration, $this->_value2));
        }

        // run the next iteration
        $this->_runFibo($iteration + 1, $callback);
    }
    
    /**
     * Callback function for series
     * 
     * @param integer            $iteration
     * @param Math_Number|string $value
     */
    protected function _seriesCallback($iteration, $value)
    {
        if ($value instanceof Math_Number) {
            $valueFormatted = $value->toString();
        } else {
            $valueFormatted = $value;
        }
        
        $this->_series[$iteration] = $valueFormatted;
    }
}

/* EOF */