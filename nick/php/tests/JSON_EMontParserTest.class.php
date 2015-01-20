<?php
require_once 'PHPUnit/Autoload.php';
require_once(__DIR__.'/../php-emont/JSON_EMontParser.class.php');

class JSON_EMontParserTest extends PHPUnit_Framework_TestCase
{
	public function testIsSituatie()
	{
		$situatie_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kust");
		$geen_situatie_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kus");
		
		$this->assertEquals(true,JSON_EMontParser::isSituatie($situatie_uri));
		$this->assertEquals(false,JSON_EMontParser::isSituatie($geen_situatie_uri));
	}
	
	public function testZoekSubrollen()
	{
		$zoek_subrollen_hardcode=array("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies",
		"http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Oesterriffen_als_interventie",
		"http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Vooroeversuppleties",
		"http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Menselijk-2D_en_ecosysteem");

		$this->assertEquals($zoek_subrollen_hardcode, JSON_EMontParser::zoekSubrollen("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem"));
	}
}
?>