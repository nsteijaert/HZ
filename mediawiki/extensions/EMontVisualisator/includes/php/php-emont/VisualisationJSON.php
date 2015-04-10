<?php
require_once(__DIR__.'/VisualisationVisitor.class.php');
require_once(__DIR__.'/JSON_EMontParser.class.php');
require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Context.class.php');

$connectie=new SPARQLConnection();
$context_uri=$_POST['context_uri'];

$situatieparser=new JSON_EMontParser($context_uri);
$result=$situatieparser->geefElementenInSituatie();

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

$post['nodes']=$nodes;

$post['links']=array();
$post['constraints']=array();
foreach($links as $link)
{
	$post['links'][]=array('source'=>$indices[$link['source']],'target'=>$indices[$link['target']],'type'=>$link['type'],'extraInfo'=>$link['extraInfo'],'note'=>$link['note']);
	if($link['type']!='partOf')
	{
		$post['constraints'][]=array('gap'=>30,'axis'=>'x', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
	}
	else
	{
		$post['constraints'][]=array('gap'=>30,'axis'=>'y', 'left'=>$indices[$link['target']],'right'=>$indices[$link['source']]);
	}
}

$contextindex=array();

foreach($ies_contexten as $context=>$ies)
{
	$leaves=array();
	foreach ($ies as $ie)
	{
		$index=array_search($ie,$nodeindex);
		$leaves[]=$index;	
	}
	$post['groups'][]['leaves']=$leaves;
	$contextindex[]=$context;
}

// Ontbrekende contexten
foreach($contexten as $uri=>$description)
{
	if(!array_search($uri,$contextindex))
	{
		$contextindex[]=$uri;
	}
}

foreach($contextLinks as $contextLink)
{
	$context=$contextLink['context'];
	$supercontext=$contextLink['supercontext'];
	$contextnr=array_search($context,$contextindex);
	$supercontextnr=array_search($supercontext,$contextindex);

	if($contextnr!==FALSE && $supercontextnr!==FALSE && !empty($post['groups'][$contextnr]))
	{
		$post['groups'][$supercontextnr]['groups'][]=$contextnr;
	}
}
// Kan waarschijnlijk efficiënter
foreach ($post['groups'] as $index=>$inhoud)
{
	// Gebruik de uri om een titel toe te voegen aan de context. Deze komen uit een array, en moeten daarna worden omgezet in
	// een string om ze vervolgens om te zetten in een leesbare titel.
	$post['groups'][$index]['titel']=Uri::SMWuriNaarLeesbareTitel(implode("",array_slice($contextindex,$index,1)));
}

// Teken groepen met meer nodes eerst
// Uitgecomment omdat de subgroepid's niet worden meeveranderd
//usort($post['groups'], 'subgroupsizecmp'); 

function subgroupsizecmp($a,$b)
{
	if(count($a['groups'])>count($b['groups']))
	{
		return 1;
	}
	return -1;
}

echo strtr(json_encode($post),array('<\/'=>'</','<sub>'=>'','<\/sub>'=>''));