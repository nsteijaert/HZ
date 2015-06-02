<?php
require_once 'PHPUnit/Autoload.php';
require_once(__DIR__.'/../Uri.class.php');

class UriTest extends PHPUnit_Framework_TestCase
{
	public function testSMWuriNaarLeesbareTitel()
	{
		$uri='wiki:Effectief_met_sediment_suppleren-3A_Vooroeversuppletie';
		$correcte_titel='Effectief met sediment suppleren: Vooroeversuppletie';

		// Test het correct decoderen van speciale tekens en verwijderen prefix
		$this->assertEquals($correcte_titel,Uri::SMWuriNaarLeesbareTitel($uri));
	}
}
?>