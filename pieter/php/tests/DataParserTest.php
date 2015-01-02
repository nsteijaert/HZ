<?php
include_once (__DIR__ . '/../DataParser.class.php');

class DataParserTest extends PHPUnit_Framework_TestCase {

	public function testParseRDF() {
		// Arrange
		$a = new DataParser(json_decode('
			{
			  "@graph": [
			    {
			      "@id": "uri:TZW-3Ahoofd",
			      "@type": [
			        "uri:Categorie-3ASKOS_Concept",
			        "http://semantic-mediawiki.org/swivt/1.0#Subject"
			      ],
			      "Eigenschap-3AIntentional_Element_type": "uri:SKOS_Concept",
			      "Eigenschap-3ASkosem-3Abroader": "uri:TZW-3Amenselijk_lichaam",
			      "Eigenschap-3ASkosem-3Anarrower": [
			        "uri:TZW-3Aschedel",
			        "uri:TZW-3Agezicht",
			        "uri:TZW-3Aoren"
			      ],
			      "uri:Eigenschap-3AWijzigingsdatum-23aux": 2456945.0848727,
			      "label": "TZW:hoofd"
			    }
			  ],
			  "@context": {
			    "page": {
			      "@id": "http://semantic-mediawiki.org/swivt/1.0#page",
			      "@type": "@id"
			    }
			  }
			}
		', true));

		// Assert
		$this -> assertEquals(1, count($a -> parseDataRDF()));
	}

	public function testParseRDFWithEmptyArray() {
		// Arrange
		$a = new DataParser(json_decode('
			{
			  "@graph": [
			  ],
			  "@context": {
			    "page": {
			      "@id": "http://semantic-mediawiki.org/swivt/1.0#page",
			      "@type": "@id"
			    }
			  }
			}
		', true));

		// Assert
		$this -> assertEquals(0, count($a -> parseDataRDF()));
	}

}
?>