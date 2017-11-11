<?php 	

use PHPUnit\Framework\TestCase as TestCase;
use App\Utils\Form;

/**
 * Test de la classe Form
 */
class FormTest extends TestCase
{
    public function testIsPassword()
    {
    	$this->assertEquals("s#Am6je8", Form::isPassword("s#Am6je8"));
    }

    public function testIsInt()
    {
        $this->assertEquals(1, Form::isInt("1"));
    }

    public function testIsStringWithLess5Caracters() {
    	$this->assertEquals("Oxford", Form::isString("Oxford", 2));
    }

    public function testIsStringWithMore5Caracters() {
    	$this->assertEquals("Oxford", Form::isString("Oxford"));
    }

    public function testIsMailAddress() {
    	$this->assertEquals('btssio@gmail.com', Form::isMail('btssio@gmail.com'));
    }
}