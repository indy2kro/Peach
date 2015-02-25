<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_DateTime
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2015 Peach Library
 */

/**
 * Wrapper over DateTime class
 */
class Peach_DateTime
{
    /*
     * Avaible date formats
     */
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';
    const DAY_FORMAT = 'Y-m-d';
    const HOUR_FORMAT = 'H:i:s';
    
    /**
     * DateTime object
     * 
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * Constructor
     * 
     * @param string|null              $datetime The datetime string
     * @param string|DateTimeZone|null $timezone The timezone for date calculation
     * @return void
     */
    public function __construct($datetime = null, $timezone = null)
    {
        $timezoneObj = null;

        if (!is_null($timezone)) {
            if ($timezone instanceof DateTimeZone) {
                $timezoneObj = $timezone;
            } else {
                $timezoneObj = new DateTimeZone($timezone);
            }
        }

        $this->_dateTime = new DateTime($datetime, $timezoneObj);
    }
    
    /**
     * Clone method
     */
    public function __clone()
    {
        $this->_dateTime = clone $this->_dateTime;
    }
    
    /**
     * Returns a string representation of the date in specified format
     *
     * @param string $format Format for the date
     * @return string
     */
    public function toString($format = self::DEFAULT_FORMAT)
    {
        return $this->_dateTime->format($format);
    }    

    /**
     * Set the timezone for the Peach_DateTime object
     *
     * @param string|DateTimeZone $timezone The timezone name
     * @return void
     */    
    public function setTimezone($timezone)
    {
        if ($timezone instanceof DateTimeZone) {
            $timezoneObj = $timezone;
        } else {
            $timezoneObj = new DateTimeZone($timezone);
        }
        $this->_dateTime->setTimezone($timezoneObj);
    }
    
    /**
     * Get the timezone object
     *
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->_dateTime->getTimezone();
    }    
    
    /**
     * Get the timezone name
     *
     * @return string
     */
    public function getTimezoneName()
    {
        return $this->_dateTime->getTimezone()->getName();
    }    
    
    /**
     * Returns if the set date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $day = $this->toString(self::DAY_FORMAT);
        $todayObj = new Peach_DateTime('now', $this->getTimezone());
        $today = $todayObj->toString(self::DAY_FORMAT);
        
        return $today == $day;
    }

    /**
     * Returns if the set date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        $day = $this->toString(self::DAY_FORMAT);
        $yesterdayObj = new Peach_DateTime('now', $this->getTimezone());
        $yesterdayObj->subDay(1);
        $yesterday = $yesterdayObj->toString(self::DAY_FORMAT);        
        
        return $day == $yesterday;
    }
    
    /**
     * Compares a Peach_DateTime object with the existing one
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param Peach_DateTime $datetimeObj The Peach_DateTime object to comparare
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    public function compare(Peach_DateTime $datetimeObj)
    {
        if ($this->_dateTime > $datetimeObj->_dateTime) {
            return 1;
        } else if ($this->_dateTime < $datetimeObj->_dateTime) {
            return -1;
        }
        
        return 0;
    }    
    
    /**
     * Adds a number of years to the existing Peach_DateTime object
     *
     * @param integer $year Number of years to add
     * @return void
     */
    public function addYear($year)
    {
        if ($year >= 0) {
            $this->_dateTime->modify('+' . $year . ' year');
        } else {
            $this->subYear((-1) * $year);
        }
    }

    /**
     * Substracts a number of years to the existing Peach_DateTime object
     *
     * @param integer $year Number of years to add
     * @return void
     */
    public function subYear($year)
    {
        if ($year <= 0) {
            $this->addYear((-1) * $year);
        } else {
            $this->_dateTime->modify('-' . $year . ' year');
        }
    }

    /**
     * Add a number of days to the existing Peach_DateTime object
     *
     * @param integer $day Number of days to add
     * @return void
     */
    public function addDay($day)
    {
        if ($day >= 0) {
            $this->_dateTime->modify('+' . $day . ' day');
        } else {
            $this->subDay((-1) * $day);
        }
    }
    
    /**
     * Substract a number of days to the existing Peach_DateTime object
     *
     * @param integer $day Number of days to substract
     * @return void
     */
    public function subDay($day)
    {
        if ($day <= 0) {
            $this->addDay((-1) * $day);
        } else {
            $this->_dateTime->modify('-' . $day . ' day');
        }
    }
    
    /**
     * Get the timezone offset from the existing Peach_DateTime object
     * 
     * @return integer
     */
    public function getGmtOffset()
    {
        return $this->_dateTime->getOffset();
    }
    
    /**
     * Set the UNIX timestamp to the existing Peach_DateTime object
     * 
     * @param integer $timestamp Timestamp value
     * @return void
     */
    public function setTimestamp($timestamp)
    {
        $this->_dateTime->setTimestamp($timestamp);
    }
    
    /**
     * Get the UNIX timestamp from the existing Peach_DateTime object
     * 
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->_dateTime->getTimestamp();
    }
}

/* EOF */