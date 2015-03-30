<?php


abstract class Visitor {
	abstract function visit(SKOSConcept $type);
	
	abstract function getUsableJSON();
}
?>