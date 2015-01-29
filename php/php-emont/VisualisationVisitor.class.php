<?php
/**
 * Haalt de elementen uit een bepaalde Situatie op en geeft ze terug als JSON voor de visualisatie.
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/PHPEMontVisitor.interface.php');
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Context.class.php');

class VisualisationVisitor implements PHPEMontVisitor
{
	function __construct() {}
	
	function visit($visitee)
	{
		if ($visitee instanceof IntentionalElement)
		{
			echo get_class($visitee);
		}
		elseif($visitee instanceof Context)
		{
			echo get_class($visitee);
		}
		echo "<br />\n";
	}
}