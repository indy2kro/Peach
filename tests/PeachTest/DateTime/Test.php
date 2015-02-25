<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2015 Peach Library
 */

/**
 * Peach_Config tests
 */
class PeachTest_DateTime_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        new Peach_DateTime();
        new Peach_DateTime('2015-01-11');
        new Peach_DateTime('2015-01-20 10:00:02');
        new Peach_DateTime('10/03/2015');
        new Peach_DateTime('2015-01-11', 'Europe/Paris');
        new Peach_DateTime('2015-01-20 10:00:02', 'Europe/Bucharest');
        new Peach_DateTime('10/03/2015', 'America/New_York');
        new Peach_DateTime('10/03/2015', new DateTimeZone('America/New_York'));
    }
    
    public function testToString()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02');
        $this->assertEquals('2015-01-20 10:00:02', $dateTimeObj->toString());
        $this->assertEquals('2015-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $this->assertEquals('2015-01-20', $dateTimeObj->toString(Peach_DateTime::DAY_FORMAT));
    }
    
    public function testSetTimezone()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02', 'Europe/Paris');
        $dateTimeObj->setTimezone('Europe/Bucharest');
        $this->assertEquals('2015-01-20 11:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }
    
    public function testSetTimezoneObj()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02', 'Europe/Paris');
        $timezoneObj = new DateTimeZone('Europe/Bucharest');
        $dateTimeObj->setTimezone($timezoneObj);
        $this->assertEquals('2015-01-20 11:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }
    
    public function testGetTimezone()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02', 'Europe/Paris');
        $timezoneObj = new DateTimeZone('Europe/Paris');
        $this->assertInstanceOf('DateTimeZone', $dateTimeObj->getTimezone());
        $this->assertEquals($timezoneObj, $dateTimeObj->getTimezone());
    }
    
    public function testGetTimezoneName()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02', 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $dateTimeObj->getTimezoneName());
    }
    
    public function testIsToday()
    {
        $dateTimeObj = new Peach_DateTime();
        $this->assertTrue($dateTimeObj->isToday());
    }
    
    public function testIsYesterday()
    {
        $dateTimeObj = new Peach_DateTime();
        $dateTimeObj->subDay(1);
        $this->assertTrue($dateTimeObj->isYesterday());
    }
    
    public function testAddDay()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02');
        $currentObj = clone $dateTimeObj;
        $dateTimeObj->addDay(1);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-21 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->addDay(4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-25 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->addDay(-4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-21 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }

    public function testSubDay()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02');
        $currentObj = clone $dateTimeObj;
        $dateTimeObj->subDay(1);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-19 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->subDay(4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-15 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->subDay(-4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2015-01-19 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }

    public function testAddYear()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02');
        $currentObj = clone $dateTimeObj;
        $dateTimeObj->addYear(1);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2016-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->addYear(4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2020-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->addYear(-4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2016-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }

    public function testSubYear()
    {
        $dateTimeObj = new Peach_DateTime('2015-01-20 10:00:02');
        $currentObj = clone $dateTimeObj;
        $dateTimeObj->subYear(1);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2014-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->subYear(4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2010-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
        $dateTimeObj->subYear(-4);
        $this->assertNotEquals($currentObj, $dateTimeObj);
        $this->assertEquals('2014-01-20 10:00:02', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }

    public function testCompare()
    {
        $dateTimeObj = new Peach_DateTime();
        $currentObj = clone $dateTimeObj;
        $pastObj = clone $dateTimeObj;
        $pastObj->subDay(3);
        $futureObj = clone $dateTimeObj;
        $futureObj->addDay(5);
        
        $this->assertTrue($dateTimeObj->compare($currentObj) == 0);
        $this->assertTrue($dateTimeObj->compare($pastObj) > 0);
        $this->assertTrue($pastObj->compare($dateTimeObj) < 0);
        $this->assertTrue($dateTimeObj->compare($futureObj) < 0);
        $this->assertTrue($futureObj->compare($dateTimeObj) > 0);
    }
    
    public function testGetGmtOffset()
    {
        $dateTimeObjFr = new Peach_DateTime('2010-01-20 10:00:02', 'Europe/Paris');
        $this->assertEquals(3600, $dateTimeObjFr->getGmtOffset());    // GMT+1
        $dateTimeObjRo = new Peach_DateTime('2010-01-20 10:00:02', 'Europe/Bucharest');
        $this->assertEquals(7200, $dateTimeObjRo->getGmtOffset());    // GMT+2
    }
    
    public function testGetTimestamp()
    {
        $dateTimeObjFr = new Peach_DateTime('2015-01-20 10:00:00', 'Europe/Paris');
        $this->assertEquals(1421744400, $dateTimeObjFr->getTimestamp());
        $dateTimeObjRo = new Peach_DateTime('2015-01-20 10:00:00', 'Europe/Bucharest');
        $this->assertEquals(1421740800, $dateTimeObjRo->getTimestamp());
    }

    public function testSetTimestamp()
    {
        $dateTimeObj = new Peach_DateTime('2010-01-20 10:00:02', 'Europe/Paris');
        $dateTimeObj->setTimestamp(1421740020);
        $this->assertEquals(1421740020, $dateTimeObj->getTimestamp());
        $this->assertEquals('2015-01-20 08:47:00', $dateTimeObj->toString(Peach_DateTime::DEFAULT_FORMAT));
    }
    
}

/* EOF */