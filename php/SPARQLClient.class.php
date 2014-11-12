<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient.class.php is used to communicatie with an RDF endpoint through SPARQL queries.
 */

// Required libraries
require ('../lib/EasyRdf.php');

class SPARQLclient {

	// Global variables
	private $serialiserType = null;
	private $serialiser = null;

	// Initialise a SPARQL Client with a endpoint URL
	public function __construct($endpoint) {
		$sparql = new EasyRdf_Sparql_Client($endpoint);
	}

	// Set the serialiser to be used if the result graph needs to be converted
	public function setSerialiser($serialiser_user) {
		switch ($serialiser_user) {
			case "Json" :
				$serialiser = new EasyRdf_Serializer_Json();
				break;
			case "GraphViz" :
				$serialiser = new EasyRdf_Serializer_GraphzViz();
				break;
			case "JsonLd" :
				$serialiser_local = new EasyRdf_Serializer_JsonLd();
				break;
			case "Ntriples" :
				$serialiser = new EasyRdf_Serializer_Ntriples();
				break;
			case "Rapper" :
				$serialiser = new EasyRdf_Serializer_Rapper();
				break;
			case "RdfPhp" :
				$serialiser = new EasyRdf_Serializer_RdfPhp();
				break;
			case "RdfXml" :
				$serialiser = new EasyRdf_Serializer_RdfXml();
				break;
			case "Turtle" :
				$serialiser = new EasyRdf_Serializer_Turtle();
				break;
			default :
				$serialiser = new EasyRdf_Serializer_Json();
		}
		$this -> serialiserType = $serialiser_user;
	}

	// Set any general purpose SPARQL prefixes to enable smaller queries
	public function setPredefinedSparqlPrefixs($prefix, $URL) {
		EasyRdf_Namespace::set($prefix, $URL);
	}

	// Execute a query with a non-serialised result (a.k.a. EasyRDF graph as result)
	public function executeQuery($query) {
		$result = $sparql -> query($query);
		return $result;
	}

	// Execute a query with a serialised result (set the serialiser first!)
	public function executeSerialisedQuery($query) {
		$result = $sparql -> query($query);
		$serializedData = $serializer -> serialise($result, $serialiserType);
		return $serialisedResult;
	}

}
?>