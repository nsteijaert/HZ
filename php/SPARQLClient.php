<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient is used to communicatie with an RDF endpoint through SPARQL query's, it returns data in the JSON format.
 */
//Required libraries
require('../lib/EasyRdf.php');
require('../lib/html_tag_helpers.php');

//Pred-defined SPARQL prefixews
EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

//Initialize a SPARQL client and JSON serialiser
$sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');
$serializer = new EasyRdf_Serialiser_Json();

//Run query and store result in $result
$result = $sparql->query($_POST['query']);

//Serialise data to JSON
$serializedData = $serializer->serialise($result, "json");

// Echo serialised data to the page
echo $serializedData;

?>