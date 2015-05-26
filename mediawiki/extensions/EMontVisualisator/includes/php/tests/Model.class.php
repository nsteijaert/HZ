<?php
require_once 'PHPUnit/Autoload.php';
require_once(__DIR__.'/../php-emont/Model.class.php');

class ModelTest extends PHPUnit_Framework_TestCase
{
	public function testIsPractice()
	{
		$practice_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kust_practice");
		$geen_practice_uri=("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kus_practice");

		$this->assertEquals(true,Model::ModelisPractice($practice_uri));
		$this->assertEquals(false,Model::ModelisPractice($geen_practice_uri));
	}

	public function testZoekSubcontexten()
	{
		$zoek_subcontexten_hardcode=array("wiki:Building_with_Nature-2Dinterventies",
		"wiki:Oesterriffen_als_interventie",
		"wiki:Vooroeversuppleties",
		"wiki:Menselijk-2D_en_ecosysteem",
		"wiki:Sedimentatieprocessen_en_habitat_van_oesters");

		$this->assertEquals($zoek_subcontexten_hardcode, Model::zoekSubcontexten("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem"));
	}
}
?>