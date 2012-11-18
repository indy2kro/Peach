<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_SemRush
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * SemRush service error translator
 */
class Peach_Service_SemRush_ErrorTranslator
{
    /**
     * Errors list
     * 
     * @var array
     */
    protected static $_errors = array(
        30 => 'LIMIT EXCEEDED',
        40 => 'MANDATORY PARAMETER "action" NOT SET OR EMPTY',
        41 => 'MANDATORY PARAMETER "type" NOT SET OR EMPTY',
        42 => 'MANDATORY PARAMETER "domain" NOT SET OR EMPTY',
        43 => 'MANDATORY PARAMETER "phrase" NOT SET OR EMPTY',
        44 => 'MANDATORY PARAMETER "url" NOT SET OR EMPTY',
        45 => 'MANDATORY PARAMETER "vs_domain" NOT SET OR EMPTY',
        50 => 'NOTHING FOUND',
        70 => 'API KEY HASH FAILURE',
        120 => 'WRONG KEY - ID PAIR',
        121 => 'WRONG FORMAT OR EMPTY HASH',
        122 => 'WRONG FORMAT OR EMPTY KEY',
        130 => 'API DISABLED',
        131 => 'LIMIT EXCEEDED',
        132 => 'API UNITS BALANCE IS ZERO',
        133 => 'DB ACCESS DENIED',
        134 => 'TOTAL LIMIT EXCEEDED',
        135 => 'API REPORT TYPE DISABLED'
    );
    
    /**
     * Translate error code
     * 
     * @param integer $errorCode
     * @return string|null
     */
    public static function translate($errorCode)
    {
        if (isset(self::$_errors[$errorCode])) {
            return self::$_errors[$errorCode];
        }
        
        return null;
    }
}

/* EOF */