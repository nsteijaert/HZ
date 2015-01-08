<?php
include_once (__DIR__ . '/QueryBuilder.class.php');
include_once (__DIR__ . '/DataParser.class.php');
include_once (__DIR__ . '/visitor/NodeMapVisitor.class.php');

// Load data
$querybuilder = new QueryBuilder($_POST["depth"], $_POST["concept"]);
$query = $querybuilder -> generateQuery($_POST["relations"]);
$result = file_get_contents('http://localhost:3030/ds/query?output=json&query=' . urlencode($query));

// Parse data
$parser = new DataParser(json_decode($result, true));
$objects = $parser -> parseDataRDF();

// Handle data
$visitor = new NodeMapVisitor();
foreach ($objects as $object) {
	$object -> accept($visitor);
}

// Return JSON
echo $visitor -> getUsableJSON();
?>