<?php
/**
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/PHPEMontVisitor.interface.php');

interface PHPEmontVisitee
{
	function accepts(PHPEMontVisitor $v);
}