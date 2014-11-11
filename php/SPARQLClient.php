<?php

require('../lib/EasyRdf.php');
require('../lib/html_tag_helpers.php');

EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

$sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');
$serializer = new EasyRdf_Serialiser_Json();

$result = $sparql->query($_POST['query']);

$serializedData = $serializer->serialise($result, "json");

echo $serializedData;

?>