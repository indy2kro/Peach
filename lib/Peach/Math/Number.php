<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Math
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Class to store large numbers
 */
class Peach_Math_Number
{
    /**
     * GMP value
     * 
     * @var resource 
     */
    protected $_resource;
    
    /**
     * Constructor
     * 
     * @param mixed $number
     * @throws Exception
     */
    public function __construct($number = 0)
    {
        if (!extension_loaded('gmp')) {
            throw new Peach_Math_Exception('Extension gmp must be loaded in order to use Peach_Math_Number');
        }
        
        $this->setValue($number);
    }
    
    /**
     * Set value
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function setValue($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_init($number);
        
        return $this;
    }
    
    /**
     * Generate random number
     * 
     * @param mixed $limiter
     * @return Peach_Math_Number
     */
    public function random($limiter = 20)
    {
        $this->_resource = gmp_random($limiter);
        
        return $this;
    }
    
    /**
     * Get absolute value
     * 
     * @return Peach_Math_Number
     */
    public function abs()
    {
        $this->_resource = gmp_abs($this->_resource);
        
        return $this;
    }
    
    /**
     * Get negative value
     * 
     * @return Peach_Math_Number
     */
    public function neg()
    {
        $this->_resource = gmp_neg($this->_resource);
        
        return $this;
    }
    
    /**
     * Get next prime number greater than current value
     * 
     * @return Peach_Math_Number
     */
    public function nextprime()
    {
        $this->_resource = gmp_nextprime($this->_resource);
        
        return $this;
    }
    
    /**
     * Add two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function add($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_add($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Substract two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function sub($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_sub($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Test if the value is a perfect square
     * 
     * @return boolean
     */
    public function perfectSquare()
    {
        return gmp_perfect_square($this->_resource);
    }
    
    /**
     * Compute bitwise AND between two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function bitAnd($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_and($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Compute bitwise OR between two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function bitOr($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_or($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Compute bitwise XOR between two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function bitXor($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_xor($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Test if a bit is set
     * 
     * @param integer $bit
     * @return boolean
     */
    public function bitTest($bit)
    {
        return gmp_testbit($this->_resource, $bit);
    }
    
    /**
     * Set a bit to a specific value
     * 
     * @param integer $bit
     * @param integer $value
     * @return Peach_Math_Number
     */
    public function bitSet($bit, $value = 1)
    {
        gmp_setbit($this->_resource, $bit, $value);
        
        return $this;
    }
    
    /**
     * Multiply two numbers
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function mul($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_mul($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Raise the value to a specific power
     * 
     * @param mixed $power
     * @return Peach_Math_Number
     */
    public function pow($power)
    {
        $power = $this->_formatInput($power);
        
        $this->_resource = gmp_pow($this->_resource, $power);
        
        return $this;
    }
    
    /**
     * Raise the value to a specific power with modulo
     * 
     * @param mixed $power
     * @param mixed $modulo
     * @return Peach_Math_Number
     */
    public function powm($power, $modulo)
    {
        $power = $this->_formatInput($power);
        $modulo = $this->_formatInput($modulo);
        
        $this->_resource = gmp_powm($this->_resource, $power, $modulo);
        
        return $this;
    }
    
    /**
     * Shift a number to the left
     * 
     * @param integer $position Number of bits to shift
     * @return Peach_Math_Number
     */
    public function shiftLeft($position = 1)
    {
        $this->mul(gmp_pow(2, $position));
        
        return $this;
    }
    
    /**
     * Shift a number to the right
     * 
     * @param integer $position Number of bits to shift
     * @return Peach_Math_Number
     */
    public function shiftRight($position = 1)
    {
        $this->div(gmp_pow(2, $position));
        
        return $this;
    }
    
    /**
     * Compare two numbers
     * 
     * @param mixed $number
     * @return integer
     */
    public function cmp($number)
    {
        $number = $this->_formatInput($number);
        
        return gmp_cmp($this->_resource, $number);
    }
    
    /**
     * Mod by a number
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function mod($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_mod($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Divide by a number
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function div($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_div($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Calculate greatest common divisor
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function gcd($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_gcd($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Calculate Hamming distance
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function hamdist($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_hamdist($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Calculate Jacobi symbol
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function jacobi($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_jacobi($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Calculate Legendre symbol
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function legendre($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_legendre($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Inverse by modulo
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public function invert($number)
    {
        $number = $this->_formatInput($number);
        
        $this->_resource = gmp_invert($this->_resource, $number);
        
        return $this;
    }
    
    /**
     * Get the sign of the number
     * 
     * @return integer
     */
    public function sign()
    {
        return gmp_sign($this->_resource);
    }
    
    /**
     * Compute square value
     * 
     * @return Peach_Math_Number
     */
    public function sqrt()
    {
        $this->_resource = gmp_sqrt($this->_resource);
        
        return $this;
    }
    
    /**
     * Compute number complement
     * 
     * @return Peach_Math_Number
     */
    public function com()
    {
        $this->_resource = gmp_com($this->_resource);
        
        return $this;
    }
    
    /**
     * Compute number factorial
     * 
     * @return Peach_Math_Number
     */
    public function fact()
    {
        $this->_resource = gmp_fact($this->_resource);
        
        return $this;
    }
    
    /**
     * Compute population count
     * 
     * @return integer
     */
    public function popcount()
    {
        return gmp_popcount($this->_resource);
    }
    
    /**
     * Convert value to string
     * 
     * @return string
     */
    public function toString()
    {
        return gmp_strval($this->_resource);
    }
    
    /**
     * Get string representation
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    
    /**
     * Format input
     * 
     * @param mixed $number
     * @return string
     */
    protected function _formatInput($number)
    {
        if ($number instanceof Peach_Math_Number) {
            $number = $number->toString();
        }
        
        return $number;
    }
}

/* EOF */