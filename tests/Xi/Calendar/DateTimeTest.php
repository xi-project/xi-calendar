<?php
namespace Xi\Calendar;

/**
 * Note: This test case does _not_ test for the peculiarities of PHP's date
 * handling, but serves only to validate that the interface to those handling
 * functions works as intended.
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
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
}