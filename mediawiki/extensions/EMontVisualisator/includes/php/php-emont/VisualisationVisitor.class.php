<?php
/**
 * Haalt de elementen uit een bepaalde Situatie op en geeft ze terug als JSON voor de visualisatie.
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/PHPEMontVisitor.interface.php');
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Activity.class.php');
require_once(__DIR__.'/Context.class.php');
require_once(__DIR__.'/../Uri.class.php');

class VisualisationVisitor implements PHPEMontVisitor
{
	function __construct() {}

	function visit($visitee)
	{
		if ($visitee instanceof IntentionalElement)
		{
			$node=array();
			$links=array();
			$ies_contexten=array();

			$uri=$visitee->getUri();
			$node['uri']=$uri;
			$node['type']=get_class($visitee);
			$node['name']=Uri::SMWuriNaarLeesbareTitel($visitee->getUri());
			$node['heading']=$visitee->getHeading();
			$node['decompositionType']=$visitee->getDecompositionType();
			$node['instanceOf']=$visitee->getInstanceOf();

			foreach($visitee->getPartOf() as $link)
			{
				$links[]=array('source'=>$uri,'type'=>'partOf','target'=>$link->getUri());
			}
			foreach($visitee->getContributes() as $link)
			{
				$links[]=array('source'=>$uri,'type'=>'contributes','target'=>$link->getLink()->getUri(),'note'=>$link->getLinkNote(),'extraInfo'=>': '.$link->getContributionValue());
			}
			foreach($visitee->getDepends() as $link)
			{
				$links[]=array('source'=>$uri,'type'=>'depends','target'=>$link->getLink()->getUri(),'note'=>$link->getLinkNote(),'extraInfo'=>'');
			}
			foreach($visitee->getContext() as $link)
			{
				$index=$link->getUri();
				$ies_contexten[$index][]=$uri;
			}
			if($visitee instanceOf Activity)
			{
				foreach($visitee->getConnects() as $link)
				{
					$links[]=array('source'=>$uri,'type'=>'connects','target'=>$link->getLink()->getUri(),'note'=>$link->getLinkNote(),'extraInfo'=>': '.$link->getConnectionType().' '.$link->getLinkCondition());
				}
			}

			$return['node']=$node;
			$return['links']=$links;
			$return['ies_contexten']=$ies_contexten;
			return $return;
		}
		elseif($visitee instanceof Context)
		{
			$context=array();
			$uri=$visitee->getUri();
			$context['uri']=$visitee->getUri();
			$context['description']=$visitee->getDescription();
			$contextLinks=array();
			foreach($visitee->getSupercontext() as $link)
			{
				$contextLinks[]=array('context'=>$uri,'supercontext'=>$link->getUri());
			}
			$return['context']=$context;
			$return['contextLinks']=$contextLinks;
			return $return;
		}
	}
}