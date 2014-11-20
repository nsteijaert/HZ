<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLClient.class.php is used to communicatie with an RDF endpoint through SPARQL queries.
 */

// Required libraries
require ('../lib/EasyRdf.php');
require ('SPARQLObject.class.php');

class SPARQLClient {

    // Global variables
    private $serialiserType = null;
    private $serialiser = null;
    private $sparql = null;
    
    // Initialise a SPARQL Client with a endpoint URL
    public function __construct($endpoint = null) {
    	if ($endpoint != null)
        $this -> sparql = new EasyRdf_Sparql_Client($endpoint);
    }

    // Set the serialiser to be used if the result graph needs to be converted
    public function setSerialiser($serialiser_user = "json") {
        $this -> serialiserType = $serialiser_user;
        switch ($serialiser_user) {
            case "json" :
                $this -> serialiser = new EasyRdf_Serialiser_Json();
                break;
            case "GraphViz" :
                $this -> serialiser = new EasyRdf_Serialiser_GraphzViz();
                break;
            case "JsonLd" :
                $this -> serialiser_local = new EasyRdf_Serialiser_JsonLd();
                break;
            case "Ntriples" :
                $this -> serialiser = new EasyRdf_Serialiser_Ntriples();
                break;
            case "Rapper" :
                $this -> serialiser = new EasyRdf_Serialiser_Rapper();
                break;
            case "RdfPhp" :
                $this -> serialiser = new EasyRdf_Serialiser_RdfPhp();
                break;
            case "RdfXml" :
                $this -> serialiser = new EasyRdf_Serialiser_RdfXml();
                break;
            case "Turtle" :
                $this -> serialiser = new EasyRdf_Serialiser_Turtle();
                break;
            default :
                $this -> serialiser = new EasyRdf_Serialiser_Json();
                $this -> serialiserType = "json";
                break;
        }
    }

    // Set any general purpose SPARQL prefixes to enable smaller queries
    public function setPredefinedSparqlPrefixs($prefix, $URL) {
        EasyRdf_Namespace::set($prefix, $URL);
    }

    // Execute a query with a non-serialised result (a.k.a. EasyRDF graph as result)
    public function executeQuery($query) {
        $result = $this -> sparql -> query($query);
        return $result;
    }

    // Execute a query with a serialised result (set the serialiser first!)
    public function executeSerialisedQuery($query) {
        $result = $this -> sparql -> query($query);

        return $serializedData;
    }

    //Return the result as an array of PHP objects with properties in an array within the object
    public function executeParsedQuery($query) {
        $result = $this -> sparql -> query($query);
        $serializedData = $this -> serialiser -> serialise($result, $this -> serialiserType);
        $intermediate_data = array();

        $decoded_data = json_decode($serializedData);

        foreach ($decoded_data as $name => $value) {
            $object = new SPARQLObject($value);
            foreach ($property as $object_property) {
                $object -> setProperty($object_property);
            }
            $intermediate_data[$object];
        }
        return $intermediate_data;
    }
	
	//Return the result as an array of PHP objects with properties in an array within the object
    public function parseJSON($json) {
        $intermediate_data = array();

        $decoded_data = json_decode($json);

        foreach ($decoded_data as $name => $value) {
            $object = new SPARQLObject($value);
            foreach ($property as $object_property) {
                $object -> setProperty($object_property);
            }
            array_push($intermediate_data, $object);
        }
        return $intermediate_data;
    }

}
?>