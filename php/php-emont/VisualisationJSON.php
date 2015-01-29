<?php
require_once(__DIR__.'/VisualisationVisitor.class.php');
require_once(__DIR__.'/JSON_EMontParser.class.php');
require_once(__DIR__.'/../SPARQLConnection.class.php');

$connectie=new SPARQLConnection();
$context_uri='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem';

$situatieparser=new JSON_EMontParser($context_uri);
$result=$situatieparser->geefElementenInSituatie();

$visualisationItems=array();

$visitor=new VisualisationVisitor();
foreach($result as $key =>$object)
{
	if(is_object($object))
	{
		echo $key." ";
		$object->accepts($visitor);
	}
}
