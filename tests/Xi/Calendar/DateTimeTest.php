<?php
namespace Xi\Calendar;

use PHPUnit_Framework_TestCase,
    DateTimeZone;

/**
 * Note: This test case does _not_ test for the peculiarities of PHP's date
 * handling, but serves only to validate that the interface to those handling
 * functions works as intended. For most purposes, this is simply a smoke test.
 */
class DateTimeTest extends PHPUnit_Framework_TestCase
{
    const SAMPLE_DATE_FORMAT = DateTime::ISO8601;
    const SAMPLE_DATE = '2011-02-03T13:37:00+0200';
    const SAMPLE_TIME_OFFSET = 7200;
    const SAMPLE_TIMEZONE = 'Europe/Helsinki';
    
    const SAMPLE_DATE_DAY_OF_MONTH = 3;
    const SAMPLE_DATE_DAYS_IN_MONTH = 28;
    const SAMPLE_DATE_DAY_OF_YEAR = 34;
    const SAMPLE_DATE_WEEK = 5;
    const SAMPLE_DATE_DAY_OF_WEEK = 4;
    
    const SAMPLE_DATE_YEAR = 2011;
    const SAMPLE_DATE_MONTH = 2;
    const SAMPLE_DATE_HOUR = 13;
    const SAMPLE_DATE_MINUTE = 37;
    const SAMPLE_DATE_SECOND = 0;
    
    /**
     * @return DateTime
     */
    private function getSampleDatetime()
    {
        return DateTime::createFromFormat(self::SAMPLE_DATE_FORMAT, self::SAMPLE_DATE);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCreate()
    {
        $this->assertTrue(DateTime::create() instanceof DateTime);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCreateWithImplicitCurrentTime()
    {
        $this->assertEquals(time(), DateTime::create()->getTimestamp());
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCreateWithExplicitTime()
    {
        $time = time() - 3600;
        $this->assertEquals($time, DateTime::create($time)->getTimestamp());
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCreateFromFormat()
    {
        $this->assertTrue(DateTime::createFromFormat(self::SAMPLE_DATE_FORMAT, self::SAMPLE_DATE) instanceof DateTime);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCreateFromFormatWithTimezone()
    {
        $this->assertTrue(DateTime::createFromFormat(self::SAMPLE_DATE_FORMAT, self::SAMPLE_DATE, new DateTimeZone('Europe/Helsinki')) instanceof DateTime);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToFormatAsString()
    {
        $this->assertEquals($this->getSampleDatetime()->format(self::SAMPLE_DATE_FORMAT), self::SAMPLE_DATE);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToCastAsString()
    {
        $this->assertEquals((string) $this->getSampleDatetime(), self::SAMPLE_DATE);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetTimeOffset()
    {
        $this->assertEquals($this->getSampleDatetime()->getOffset(), self::SAMPLE_TIME_OFFSET);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetTimeZone()
    {
        $this->assertEquals($this->getSampleDatetime()->getTimezone(), new DateTimeZone(self::SAMPLE_TIMEZONE));
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetDayOfMonth()
    {
        $this->assertEquals($this->getSampleDatetime()->getDayOfMonth(), self::SAMPLE_DATE_DAY_OF_MONTH);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetDaysInMonth()
    {
        $this->assertEquals($this->getSampleDatetime()->getDaysInMonth(), self::SAMPLE_DATE_DAYS_IN_MONTH);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetDayOfYear()
    {
        $this->assertEquals($this->getSampleDateTime()->getDayOfYear(), self::SAMPLE_DATE_DAY_OF_YEAR);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWeek()
    {
        $this->assertEquals($this->getSampleDateTime()->getWeek(), self::SAMPLE_DATE_WEEK);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetDayOfWeek()
    {
        $this->assertEquals($this->getSampleDateTime()->getDayOfWeek(), self::SAMPLE_DATE_DAY_OF_WEEK);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetYear()
    {
        $this->assertEquals($this->getSampleDateTime()->getYear(), self::SAMPLE_DATE_YEAR);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetMonth()
    {
        $this->assertEquals($this->getSampleDateTime()->getMonth(), self::SAMPLE_DATE_MONTH);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetHour()
    {
        $this->assertEquals($this->getSampleDateTime()->getHour(), self::SAMPLE_DATE_HOUR);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetMinute()
    {
        $this->assertEquals($this->getSampleDateTime()->getMinute(), self::SAMPLE_DATE_MINUTE);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetSecond()
    {
        $this->assertEquals($this->getSampleDateTime()->getSecond(), self::SAMPLE_DATE_SECOND);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithModifiedDate()
    {
        $datetime = $this->getSampleDatetime()->withDate(2011, 1, 2);
        $this->assertEquals($datetime->getYear(), 2011);
        $this->assertEquals($datetime->getMonth(), 1);
        $this->assertEquals($datetime->getDayOfMonth(), 2);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithModifiedISODate()
    {
        $datetime = $this->getSampleDatetime()->withISODate(2011, 1, 1);
        $this->assertEquals($datetime->getYear(), 2011);
        $this->assertEquals($datetime->getWeek(), 1);
        $this->assertEquals($datetime->getDayOfWeek(), 1);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithModifiedTime()
    {
        $datetime = $this->getSampleDatetime()->withTime(11, 12, 13);
        $this->assertEquals($datetime->getHour(), 11);
        $this->assertEquals($datetime->getMinute(), 12);
        $this->assertEquals($datetime->getSecond(), 13);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithIntegerSubtraction()
    {
        $datetime = $this->getSampleDatetime()->withTime(11, 12, 13)->withSubtraction(10);
        $this->assertEquals($datetime->getHour(), 11);
        $this->assertEquals($datetime->getMinute(), 12);
        $this->assertEquals($datetime->getSecond(), 3);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithStringSubtraction()
    {
        $datetime = $this->getSampleDatetime()->withTime(11, 12, 13)->withSubtraction('1 hour 2 minutes 3 seconds');
        $this->assertEquals($datetime->getHour(), 10);
        $this->assertEquals($datetime->getMinute(), 10);
        $this->assertEquals($datetime->getSecond(), 10);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithIntegerAddition()
    {
        $datetime = $this->getSampleDatetime()->withTime(11, 12, 13)->withAddition(10);
        $this->assertEquals($datetime->getHour(), 11);
        $this->assertEquals($datetime->getMinute(), 12);
        $this->assertEquals($datetime->getSecond(), 23);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithStringAddition()
    {
        $datetime = $this->getSampleDatetime()->withTime(11, 12, 13)->withAddition('1 hour 2 minutes 3 seconds');
        $this->assertEquals($datetime->getHour(), 12);
        $this->assertEquals($datetime->getMinute(), 14);
        $this->assertEquals($datetime->getSecond(), 16);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetWithModification()
    {
        $datetime = $this->getSampleDatetime()->withDate(2011, 1, 1)->modify('+1 year, 2 months, 3 days');
        $this->assertEquals($datetime->getYear(), 2012);
        $this->assertEquals($datetime->getMonth(), 3);
        $this->assertEquals($datetime->getDayOfMonth(), 4);
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToGetDifference()
    {
        $left = $this->getSampleDatetime();
        $right = $this->getSampleDatetime()->modify('+1 year, 1 month, 1 days');
        $diff = $left->diff($right);
        $this->assertEquals(1, $diff->y);
        $this->assertEquals(1, $diff->m);
        $this->assertEquals(1, $diff->d);
    }
}