<?php
// Het uitspugen van een simpele notice of warning is al genoeg om de visualisatie op zijn gat te gooien.
ini_set('display_errors','0');

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
$extragroups=array();
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
	$newlink=array('source'=>$indices[$link['source']],'target'=>$indices[$link['target']],'type'=>$link['type'],'extraInfo'=>$link['extraInfo'],'note'=>$link['note']);

	if($link['type']=='connects')
	{
		$newlink['connectionType']=$link['connectionType'];
		$newlink['linkCondition']=$link['linkCondition'];

		if($link['connectionType']=='seq')
		{
			$post['constraints'][]=array('gap'=>120,'axis'=>'x', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
		//	$post['constraints'][]=array('gap'=>0,'axis'=>'y', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
		}
	}
	elseif($link['type']=='contributes')
	{
		$newlink['contributionValue']=$link['contributionValue'];
	}
	elseif($link['type']!='partOf')
	{
		$post['constraints'][]=array('gap'=>120,'axis'=>'x', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
		//$post['constraints'][]=array('gap'=>0,'axis'=>'y', 'left'=>$indices[$link['source']],'right'=>$indices[$link['target']]);
	}
	else
	{
		$post['constraints'][]=array('gap'=>50,'axis'=>'y', 'left'=>$indices[$link['target']],'right'=>$indices[$link['source']]);
	}
	$post['links'][]=$newlink;
}

$contextindex=array();
$gebruikteLeaves=array();

foreach($ies_contexten as $context=>$ies)
{
	$leaves=array();
	foreach ($ies as $ie)
	{
		$index=array_search($ie,$nodeindex);

		// IE's gaan spacen als de visualisatie ze in twee contexten tegelijkertijd moet tekenen. Dit helpt, maar moet wat verfijnder geïmplementeerd worden.
		if($gebruikteLeaves[$index]==false)
		{
			$leaves[]=$index;
			$gebruikteLeaves[$index]=true;
		}
	}

	if(!empty($leaves))
		$post['groups'][]=array('leaves' => $leaves);
	$contextindex[]=$context;
}

// Ontbrekende contexten
foreach($contexten as $uri=>$description)
{
	if(!array_search($uri,$contextindex))
	{
		/*$post['nodes'][]=array('uri'=>'dummy','name'=>'','heading'=>'dummy','type'=>'dummy');*/
		$post['groups'][]=array(); //'leaves'=>array(count($post['nodes'])-1));
		$contextindex[]=$uri;
	}
}
$gebruikteSubcontexten=array();

foreach($contextLinks as $contextLink)
{
	$context=$contextLink['context'];
	$supercontext=$contextLink['supercontext'];
	$contextnr=array_search($context,$contextindex);
	$supercontextnr=array_search($supercontext,$contextindex);

	if($contextnr!==FALSE && $supercontextnr!==FALSE && !$gebruikteSubcontexten[$contextnr])
	{
		if(!empty($post['groups'][$contextnr])) {
			$post['groups'][$supercontextnr]['groups'][]=$contextnr;
		}
		else {
			$extragroups[$supercontextnr][]=$contextnr;
		}

	}
	if(!$gebruikteSubcontexten[$contextnr])
		$gebruikteSubcontexten[$contextnr]=array();

	$gebruikteSubcontexten[$contextnr][]=$supercontextnr;
}

// Kan waarschijnlijk efficiënter
foreach ($post['groups'] as $index=>$inhoud)
{
	$post['groups'][$index]['uri']=$contextindex[$index];

	// Gebruik de uri om een titel toe te voegen aan de context. Deze komen uit een array, en moeten daarna worden omgezet in
	// een string om ze vervolgens om te zetten in een leesbare titel.
	$post['groups'][$index]['titel']=Uri::SMWuriNaarLeesbareTitel(implode("",array_slice($contextindex,$index,1)));
	$post['groups'][$index]['langbijschrift']=array();

	// Gewoon bijschrift wordt bovenaan de context getoond, lang bijschrift als tooltip.
	if(isset($gebruikteSubcontexten[$index]))
	{
		foreach($gebruikteSubcontexten[$index] as $supercontextindex)
		{
			$post['groups'][$index]['langbijschrift'][]=Uri::SMWuriNaarLeesbareTitel(implode("",array_slice($contextindex,$supercontextindex,1))).': '.$post['groups'][$index]['titel'];
		}

		$post['groups'][$index]['bijschrift']=implode(', ', $post['groups'][$index]['langbijschrift']);

		if(strlen($post['groups'][$index]['bijschrift'])>50)
		{
			$post['groups'][$index]['bijschrift']="";
			foreach($gebruikteSubcontexten[$index] as $supercontextindex)
			{
				if($post['groups'][$index]['bijschrift'])
					$post['groups'][$index]['bijschrift'].=", ";
				$supercontextvermelding=geefInitialen(Uri::SMWuriNaarLeesbareTitel(implode("",array_slice($contextindex,$supercontextindex,1))));
				$post['groups'][$index]['bijschrift'].=$supercontextvermelding.': '.$post['groups'][$index]['titel'];
			}
		}
	}
	//Als de context nooit als subcontext voorkomt is het waarschijnlijk de hoofdcontext, die geen supercontextvermelding heeft (om begrijpelijke redenen).
	else
	{
		$post['groups'][$index]['langbijschrift'][]=$post['groups'][$index]['titel'];
		$post['groups'][$index]['bijschrift']=$post['groups'][$index]['titel'];
	}

	$post['groups'][$index]['tooltip']=implode('<br />',$post['groups'][$index]['langbijschrift']);
}

$groups=$post['groups'];
$post['groups']=array();
$teller=0;

foreach ($groups as $group)
{
	if($extragroups[$teller]) {
		$grouptemp=$group;
		if($grouptemp['groups'])
			$grouptemp['groups']=array_merge($group['groups'],$extragroups[$teller]);
		else
			$grouptemp['groups']=$extragroups[$teller];

		$post['allgroups'][]=$grouptemp;
	}else {
		$post['allgroups'][]=$group;
	}

	if(count($group['leaves'])>0 || count($group['groups'])>0)
		$post['groups'][]=$group;

	$teller++;
}

// Teken groepen met meer nodes eerst
// Uitgecomment omdat de subgroepid's niet worden meeveranderd
/*usort($post['groups'], 'subgroupsizecmp');

function subgroupsizecmp($a,$b)
{
	if(count($a['groups'])>count($b['groups']))
	{
		return 1;
	}
	return -1;
}*/

function geefInitialen($string)
{
	$words = explode(" ", $string);
	$acronym = "";

	foreach ($words as $w) {
 		$acronym .= $w[0];
	}
	return $acronym;
}

$out=json_encode($post);
$out=strtr($out,array('<\/'=>'</'));
$out=strtr($out,array('<sub>2</sub>'=>'₂'));
$out=strtr($out,array('<sub>'=>'','<\/sub>'=>''));
$out=strtr($out,array('CO2'=>'CO₂'));
echo $out;

//error_log(var_export($contexten,true));
