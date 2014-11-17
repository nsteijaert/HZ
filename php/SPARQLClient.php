<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient is used to communicatie with an RDF endpoint through SPARQL query's, it returns data in the JSON format.
 */
//Required libraries
require('SPARQLClient.class.php');
//require('../lib/html_tag_helpers.php');

$SPARQLClient = new SPARQLClient('http://127.0.0.1:3030/ds/');

$SPARQLClient -> setSerialiser();

$SPARQLClient -> setPredefinedSparqlPrefixs('category', 'http://dbpedia.org/resource/Category:');
$SPARQLClient -> setPredefinedSparqlPrefixs('dbpedia', 'http://dbpedia.org/resource/');
$SPARQLClient -> setPredefinedSparqlPrefixs('dbo', 'http://dbpedia.org/ontology/');
$SPARQLClient -> setPredefinedSparqlPrefixs('dbp', 'http://dbpedia.org/property/');

echo $SPARQLClient -> executeSerialisedQuery($_POST['query']);

?>