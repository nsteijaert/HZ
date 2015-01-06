<?php
include_once (__DIR__ . '/QueryBuilder.class.php');
include_once (__DIR__ . '/DataParser.class.php');
include_once (__DIR__ . '/visitor/NodeMapVisitor.class.php');

switch ($_POST["do"]) {
	case "generate" :
		// Load data
		$querybuilder = new QueryBuilder($_POST["depth"], $_POST["concept"]);
		echo $query = $querybuilder -> generateQuery($_POST["relations"]);

		break;
	case "parse" :
		// Parse data
		$parser = new DataParser($_POST["data"]);
		$objects = $parser -> parseDataRDF();

		// Handle data
		$visitor = new NodeMapVisitor();
		foreach ($objects as $object) {
			$object -> accept($visitor);
		}
		echo $visitor -> getUsableJSON();
		break;
	default :
		exit ;
}
?>