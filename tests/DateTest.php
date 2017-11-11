<?php 	

use PHPUnit\Framework\TestCase as TestCase;
use App\Utils\Date;
use App\Utils\ErrorLogger;

/**
 * Test de la classe Date
 */
class DateTest extends TestCase
{
    public function testEngToFrValid()
    {
        $this->assertEquals("10/11/2017", Date::EngToFr("2017-11-10"));
    }

    public function testFrToEngValid()
    {
        $this->assertEquals("2017-11-10", Date::FrToEng("10/11/2017"));
    }

    public function testEngToFrNotValid()
    {
    	$this->assertNotEquals("11/10/2017", Date::EngToFr("2017-11-10"));
    }

    public function testFrToEngNotValid()
    {
        $this->assertNotEquals("2017-10-11", Date::FrToEng("10/11/2017"));
    }

    public function testGetMoisValid()
    {
        $this->assertEquals("201711", Date::getMois("10/11/2017"));
    }

    public function testGetMoisNotValid()
    {
        $this->assertNotEquals("201710", Date::getMois("10/11/2017"));
    }

    public function testIsOutdated() {
        $this->assertTrue(Date::outdated('10/11/2016'));
    }

    public function testIsNotOutdated() {
        $this->assertFalse(Date::outdated('10/11/2017'));
    }

    public function testDateIsValid() {
        $this->assertTrue(Date::check('10/11/2017'));
    }
}