<?php
require_once 'PHPUnit/Autoload.php';
require_once(__DIR__.'/../php-emont/Model.class.php');

class ModelTest extends PHPUnit_Framework_TestCase
{
	public function testIsSituatie()
	{
		$situatie_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kust");
		$geen_situatie_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kus");
		
		$this->assertEquals(true,Model::isSituatie($situatie_uri));
		$this->assertEquals(false,Model::isSituatie($geen_situatie_uri));
	}
	
	public function testZoekSubrollen()
	{
		$zoek_subrollen_hardcode=array("wiki:Building_with_Nature-2Dinterventies",
		"wiki:Oesterriffen_als_interventie",
		"wiki:Vooroeversuppleties",
		"wiki:Menselijk-2D_en_ecosysteem");

		$this->assertEquals($zoek_subrollen_hardcode, Model::zoekSubrollen("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem"));
	}
}
?>