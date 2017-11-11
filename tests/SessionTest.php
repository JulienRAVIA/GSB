<?php 	
session_start();
use PHPUnit\Framework\TestCase as TestCase;
use App\Utils\Session;

/**
 * Test de la classe utilitaire Session
 */
class SessionTest extends TestCase
{
    public function testIsConnected()
    {
        Session::set('idVisiteur', 15);
        $this->assertTrue(Session::isConnected());
    }

    public function testIsNotConnected()
    {
        Session::destroy('idVisiteur');
        $this->assertFalse(Session::isConnected());
    }

    public function testSessionKeyExist() {
        Session::set('idVisiteur', 15);
        $this->assertEquals(15, Session::get('idVisiteur'));
    }
}