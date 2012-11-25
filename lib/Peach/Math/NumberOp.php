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
 * Class to handle large number operations
 */
class Peach_Math_NumberOp
{
    /**
     * Generate random number
     * 
     * @param mixed $limiter
     * @return Peach_Math_Number
     */
    public static function random($limiter = 20)
    {
        // format input
        $limiter = self::_formatInput($limiter);
        
        $result = new Peach_Math_Number();
        
        // perform operation
        $result->random($limiter);
        
        return $result;
    }
    
    /**
     * Get absolute value
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public static function abs($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->abs();
        
        return $result;
    }
    
    /**
     * Get negative value
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public static function neg($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->neg();
        
        return $result;
    }
    
    /**
     * Compute number complement
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public static function com($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->com();
        
        return $result;
    }
    
    /**
     * Get the sign of the number
     * 
     * @param mixed $number
     * @return integer
     */
    public static function sign($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        // perform operation
        $result = $number->sign();
        
        return $result;
    }
    
    /**
     * Test if a number is perfect square
     * 
     * @param mixed $number
     * @return integer
     */
    public static function perfectSquare($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        // perform operation
        $result = $number->perfectSquare();
        
        return $result;
    }
    
    /**
     * Get next prime number greater than current value
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public static function nextprime($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->nextprime();
        
        return $result;
    }
    
    /**
     * Compute square value
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    public static function sqrt($number)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->sqrt();
        
        return $result;
    }
    
    /**
     * Compare two numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return integer
     */
    public static function cmp($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        // perform operation
        $result = $number1->cmp($number2);
        
        return $result;
    }
    
    /**
     * Add 2 numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function add($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->add($number2);
        
        return $result;
    }
    
    /**
     * Substract 2 numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function sub($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->sub($number2);
        
        return $result;
    }
    
    /**
     * Multiply two numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function mul($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->mul($number2);
        
        return $result;
    }
    
    /**
     * Mod by a number
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function mod($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->mod($number2);
        
        return $result;
    }
    
    /**
     * Divide by a number
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function div($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->div($number2);
        
        return $result;
    }
    
    /**
     * Calculate greatest common divisor
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function gcd($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->gcd($number2);
        
        return $result;
    }
    
    /**
     * Calculate Hamming distance
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function hamdist($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->hamdist($number2);
        
        return $result;
    }
    
    /**
     * Calculate Jacobi symbol
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function jacobi($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->jacobi($number2);
        
        return $result;
    }
    
    /**
     * Test if a number is perfect square
     * 
     * @param mixed $number
     * @return integer
     */
    public static function popcount($number)
    {
        // format input
        $number1 = self::_formatInput($number1);
        
        // perform operation
        $result = $number->popcount();
        
        return $result;
    }
    
    /**
     * Calculate Legendre symbol
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function legendre($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->legendre($number2);
        
        return $result;
    }
    
    /**
     * Inverse by modulo
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function invert($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->invert($number2);
        
        return $result;
    }
    
    /**
     * Raise the value to a specific power
     * 
     * @param mixed $number
     * @param mixed $power
     * @return Peach_Math_Number
     */
    public static function pow($number, $power)
    {
        // format input
        $number = self::_formatInput($number);
        $power = self::_formatInput($power);
        
        $result = clone $number;
        
        // perform operation
        $result->pow($power);
        
        return $result;
    }
    
    /**
     * Raise the value to a specific power with modulo
     * 
     * @param mixed $number
     * @param mixed $power
     * @param mixed $modulo
     * @return Peach_Math_Number
     */
    public static function powm($number, $power, $modulo)
    {
        // format input
        $number = self::_formatInput($number);
        $power = self::_formatInput($power);
        $modulo = self::_formatInput($modulo);
        
        $result = clone $number;
        
        // perform operation
        $result->powm($power, $modulo);
        
        return $result;
    }
    
    /**
     * Compute bitwise AND between two numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function bitAnd($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->bitAnd($number2);
        
        return $result;
    }
    
    /**
     * Compute bitwise OR between two numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function bitOr($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->bitOr($number2);
        
        return $result;
    }
    
    /**
     * Compute bitwise XOR between two numbers
     * 
     * @param mixed $number1
     * @param mixed $number2
     * @return Peach_Math_Number
     */
    public static function bitXor($number1, $number2)
    {
        // format input
        $number1 = self::_formatInput($number1);
        $number2 = self::_formatInput($number2);
        
        $result = clone $number1;
        
        // perform operation
        $result->bitXor($number2);
        
        return $result;
    }
    
    /**
     * Test if a bit is set
     * 
     * @param mixed   $number
     * @param integer $bit
     * @return boolean
     */
    public static function bitTest($number, $bit)
    {
        // format input
        $number = self::_formatInput($number);
        
        // perform operation
        $result = $number->bitTest($bit);
        
        return $result;
    }
    
    /**
     * Set a bit to a specific value
     * 
     * @param mixed   $number
     * @param integer $bit
     * @param integer $value
     * @return void
     */
    public static function bitSet($number, $bit, $value = 1)
    {
        // format input
        $number = self::_formatInput($number);
        
        // perform operation
        $number->bitSet($bit, $value);
    }
    
    /**
     * Shift a number to the left
     * 
     * @param mixed   $number
     * @param integer $position
     * @return Peach_Math_Number
     */
    public static function shiftLeft($number, $position)
    {
        // format input
        $number = self::_formatInput($number);
        
        $result = clone $number;
        
        // perform operation
        $result->shiftLeft($position);
        
        return $result;
    }
    
    /**
     * Shift a number to the right
     * 
     * @param mixed   $number
     * @param integer $position
     * @return Peach_Math_Number
     */
    public static function shiftRight($number, $position)
    {
        // format input
        $number = self::_formatInput($number);
                
        $result = clone $number;
        
        // perform operation
        $result->shiftRight($position);
        
        return $result;
    }
    
    /**
     * Format input
     * 
     * @param mixed $number
     * @return Peach_Math_Number
     */
    protected static function _formatInput($number)
    {
        if ($number instanceof Peach_Math_Number) {
            return $number;
        }
        
        $numberObj = new Peach_Math_Number($number);
        
        return $numberObj;
    }
}

/* EOF */