<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient.class.php is used to communicatie with an RDF endpoint through SPARQL queries.
 */

// Required libraries
require ('../lib/EasyRdf.php');

class SPARQLclient {

	// Global variables
	var $serialiserType = null;
	var $serialiser = null;

	// Initialise a SPARQL Client with a endpoint URL
	public function __construct($endpoint) {
		$sparql = new EasyRdf_Sparql_Client($endpoint);
	}

	// Set the serialiser to be used if the result graph needs to be converted
	public function setSerialiser($serialiser_user) {
		switch ($serialiser_user) {
			case "Json" :
				$serialiser = new EasyRdf_Serializer_Json();
				$serialiserType = $serialiser_user;
				break;
			case "GraphViz" :
				$serialiser = new EasyRdf_Serializer_GraphzViz();
				$serialiserType = $serialiser_user;
				break;
			case "JsonLd" :
				$serialiser_local = new EasyRdf_Serializer_JsonLd();
				$serialiserType = $serialiser_user;
				break;
			case "Ntriples" :
				$serialiser = new EasyRdf_Serializer_Ntriples();
				$serialiserType = $serialiser_user;
				break;
			case "Rapper" :
				$serialiser = new EasyRdf_Serializer_Rapper();
				$serialiserType = $serialiser_user;
				break;
			case "RdfPhp" :
				$serialiser = new EasyRdf_Serializer_RdfPhp();
				$serialiserType = $serialiser_user;
				break;
			case "RdfXml" :
				$serialiser = new EasyRdf_Serializer_RdfXml();
				$serialiserType = $serialiser_user;
				break;
			case "Turtle" :
				$serialiser = new EasyRdf_Serializer_Turtle();
				$serialiserType = $serialiser_user;
				break;
			default :
				$serialiser = new EasyRdf_Serializer_Json();
				$serialiserType = $serialiser_user;
		}
	}

	// Set any general purpose SPARQL prefixes to enable smaller queries
	public function setPredefinedSparqlPrefixs($prefix, $URL) {
		EasyRdf_Namespace::set($prefix, $URL);
	}

	// Execute a query with a non-serialised result (a.k.a. EasyRDF graph as result)
	public function executeQuery($Query) {
		$result = $sparql -> query($_POST['query']);
		return $result;
	}

	// Execute a query with a serialised result (set the serialiser first!)
	public function executeSerialisedQuery($query) {
		$result = $sparql -> query($_POST['query']);
		$serializedData = $serializer -> serialise($result, $serialiserType);
		return $serialisedResult;
	}
}
?>