<?php
require_once(__DIR__.'/VisualisationVisitor.class.php');
require_once(__DIR__.'/JSON_EMontParser.class.php');
require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Context.class.php');

$connectie=new SPARQLConnection();
$context_uri='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem';

$situatieparser=new JSON_EMontParser($context_uri);
$result=$situatieparser->geefElementenInSituatie();

$visualisationItems=array();

$visitor=new VisualisationVisitor();

$nodes=array();
$links=array();
$ies_contexten=array();
$contexten=array();
$contextLinks=array();

foreach($result as $uri =>$object)
{
	if($object instanceOf IntentionalElement)
	{
		$result=($object->accepts($visitor));
		$nodes[$uri]=$result['node'];
		$links=array_merge($links,$result['links']);
		$ies_contexten=array_merge($ies_contexten,$result['ies_contexten']);
	}
	elseif($object instanceOf Context)
	{
		$result=($object->accepts($visitor));
		$contexten[$uri]=$result['context'];
		$contextLinks=array_merge($contextLinks,$result['contextLinks']);
	}
}
echo '<pre>';
var_dump($nodes);
var_dump($links);
var_dump($ies_contexten);
echo '</pre>';