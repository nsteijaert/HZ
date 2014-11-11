<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/');
require_once "EasyRdf.php";
require_once "html_tag_helpers.php";

$sparql = new EasyRdf_Sparql_Client('http://192.168.238.133:3030/ds/');
$query = $_POST['query']

//add sparql query from post
$result = $sparql->query(
	$query
	);

foreach ($result as $row) {
}
?>

