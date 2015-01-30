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
$indices=array();
$teller=0;
foreach($result as $uri =>$object)
{
	if($object instanceOf IntentionalElement)
	{
		$result=($object->accepts($visitor));
		$nodes[]=$result['node'];
		$indices[$uri]=$teller;
		$teller++;
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

$post['nodes']=$nodes;
foreach($links as $link)
{
	$post['links'][]=array('source'=>$indices[$link['source']],'target'=>$indices[$link['target']]);
}
$post['ies_contexten']=$ies_contexten;
$post['contexten']=$contexten;
$post['contextLinks']=$contextLinks;
echo strtr(json_encode($post),array('<\/'=>'</'));