<?php

require('../lib/EasyRdf.php');
require('../lib/html_tag_helpers.php');

EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

$sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');

$result = $sparql->query($_POST['query']);

var_dump($result);

echo '{
	"title": "Example Schema",
	"type": "object",
	"properties": {
		"firstName": {
			"type": "string"
		},
		"lastName": {
			"type": "string"
		},
		"age": {
			"description": "Age in years",
			"type": "integer",
			"minimum": 0
		}
	},
	"required": ["firstName", "lastName"]
}';
?>