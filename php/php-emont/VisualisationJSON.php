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
$nodeindex=array();
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
		$nodeindex[]=$uri;
		$indices[$uri]=$teller;
		$teller++;
		$links=array_merge($links,$result['links']);
		foreach ($result['ies_contexten'] as $context => $ies)
		{
			foreach($ies as $ie)
			{
				$ies_contexten[$context][]=$ie;
			}
		}
	}
	elseif($object instanceOf Context)
	{
		$result=($object->accepts($visitor));
		$contexten[$uri]=$result['context'];
		$contextLinks=array_merge($contextLinks,$result['contextLinks']);
	}
}
//var_dump($ies_contexten);

$post['nodes']=$nodes;

foreach($links as $link)
{
	$post['links'][]=array('source'=>$indices[$link['source']],'target'=>$indices[$link['target']]);
	$post['constraints'][]=array('gap'=>120,'axis'=>'x', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
}

//$post['ies_contexten']=$ies_contexten;
$post['contexten']=$contexten;
//var_dump($ies_contexten);

foreach($ies_contexten as $context=>$ies)
{
	//echo "\n\n\n\<br /><br />".$context."\n\n\n\<br /><br />";
	$leaves=array();
	foreach ($ies as $ie)
	{
		$index=array_search($ie,$nodeindex);
		$leaves[]=$index;	
	}
	$post['groups'][]['leaves']=$leaves;
}

$post['contextLinks']=$contextLinks;
echo strtr(json_encode($post),array('<\/'=>'</'));