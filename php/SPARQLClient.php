<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient is used to communicatie with an RDF endpoint through SPARQL query's, it returns data in the JSON format.
 */
//Required libraries
require('SPARQLClient.class.php');
//require('../lib/html_tag_helpers.php');

//$SPARQLClient = new SPARQLClient('http://dbpedia.org/sparql');
$SPARQLClient = new SPARQLClient('http://localhost:3030/ds/query');

$SPARQLClient -> setSerialiser();

//$SPARQLClient -> setPredefinedSparqlPrefixs('category', 'http://dbpedia.org/resource/Category:');
//$SPARQLClient -> setPredefinedSparqlPrefixs('dbpedia', 'http://dbpedia.org/resource/');
//$SPARQLClient -> setPredefinedSparqlPrefixs('dbo', 'http://dbpedia.org/ontology/');
//$SPARQLClient -> setPredefinedSparqlPrefixs('dbp', 'http://dbpedia.org/property/');
$SPARQLClient -> setPredefinedSparqlPrefixs('dbwiki', 'http://localhost:3030/');

echo $SPARQLClient -> executeSerialisedQuery($_POST['query']);

?>