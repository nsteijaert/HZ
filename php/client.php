<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * Client.php is used to communicatie with an RDF endpoint through SPARQL queries.
 */
 
// Required libraries
require('../lib/EasyRdf.php');

// Global variables
$serialiserType = null;
$serialiser = null;
 
// Initialise a SPARQL Client with a endpoint URL 
function initialise($endpoint, $serialiser)
{
	$sparql =  new EasyRdf_Sparql_Client($endpoint);
}

// Set the serialiser to be used if the result graph needs to be converted
function setSerialiser($serialiser_user){
	switch ($serialiser_user) {
    case "Json":
        $serialiser = new EasyRdf_Serializer_Json();
		$serialiserType = $serialiser_user;
        break;
    case "GraphViz":
        $serialiser = new EasyRdf_Serializer_GraphzViz();
		$serialiserType = $serialiser_user;
        break;
    case "JsonLd":
        $serialiser_local = new EasyRdf_Serializer_JsonLd();
		$serialiserType = $serialiser_user;
        break;
	case "Ntriples":
        $serialiser = new EasyRdf_Serializer_Ntriples();
		$serialiserType = $serialiser_user;
        break;
	case "Rapper":
        $serialiser = new EasyRdf_Serializer_Rapper();
		$serialiserType = $serialiser_user;
        break;
	case "RdfPhp":
        $serialiser = new EasyRdf_Serializer_RdfPhp();
		$serialiserType = $serialiser_user;
        break;
	case "RdfXml":
        $serialiser = new EasyRdf_Serializer_RdfXmlp();
		$serialiserType = $serialiser_user;
        break;
	case "Turtle":
        $serialiser = new EasyRdf_Serializer_Turtle();
		$serialiserType = $serialiser_user;
        break;
	default:
		$serialiser = new EasyRdf_Serializer_Json();
		$serialiserType = $serialiser_user;
		}
}

// Set any general purpose SPARQL prefixes to enable smaller queries
function setPredefinedSparqlPrefixs($prefix, $URL)
{
  	EasyRdf_Namespace::set($prefix, $URL);
}

// Execute a query with a non-serialised result (a.k.a. EasyRDF graph as result)
function executeQuery($Query)
{
	$result = $sparql->query($_POST['query']);
	return $result;
}

// Execute a query with a serialised result (set the serialiser first!)
function executeSerialisedQuery($query)
{
	$result = $sparql->query($_POST['query']);
	$serializedData = $serializer->serialise($result, $serialiserType);
	return $serialisedResult;
	
}
?>