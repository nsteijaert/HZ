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
		$tojson = array();
		foreach ($objects as $object) {
			$array = $object -> accept($visitor);
			$tojson = is_array($array) ? array_merge($tojson, $array) : $tojson;
		}
		echo json_encode($tojson);
		break;
	default :
		exit ;
}
?>