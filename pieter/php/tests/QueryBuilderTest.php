<?php
include_once (__DIR__ . '/../QueryBuilder.class.php');

class QueryBuilderTest extends PHPUnit_Framework_TestCase {
	public function testCorrectQuery() {
		// Arrange
		$a = new QueryBuilder("1", "TZW:Test");

		// Assert
		$this -> assertEquals(preg_replace('/\s+/', ' ', '
            PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
			PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
			PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			
			construct { ?s ?p ?o }
			where {
			  ?c rdfs:label "TZW:Test" .
			  ?c (<>|!<>){,1} ?s .
			  ?s ?p ?o
			  FILTER(EXISTS { ?s a uri:Categorie-3ASKOS_Concept } )
			}
			'), preg_replace('/\s+/', ' ', $a -> generateQuery()));
	}

	public function testCorrectQueryWithInput() {
		// Arrange
		$a = new QueryBuilder("1", "TZW:Test");

		// Assert
		$this -> assertEquals(preg_replace('/\s+/', ' ', '
            PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
			PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
			PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			
			construct { ?s ?p ?o }
			where {
			  ?c rdfs:label "TZW:ThisTest" .
			  ?c (<>|!<>){,3} ?s .
			  ?s ?p ?o
			  FILTER(EXISTS { ?s a uri:Categorie-3ASKOS_Concept } )
			}
			'), preg_replace('/\s+/', ' ', $a -> generateQuery("", "3", "TZW:ThisTest")));
	}

	public function testCorrectQueryWithInputAndFilter() {
		// Arrange
		$a = new QueryBuilder("1", "TZW:Test");

		// Assert
		$this -> assertEquals(preg_replace('/\s+/', ' ', '
            PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
			PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
			PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			
			construct { ?s ?p ?o }
			where {
			  ?c rdfs:label "TZW:ThisTest" .
			  ?c (skosem:narrower){,3} ?s .
			  ?s ?p ?o
			  FILTER(EXISTS { ?s a uri:Categorie-3ASKOS_Concept } )
			}
			'), preg_replace('/\s+/', ' ', $a -> generateQuery("false,true", "3", "TZW:ThisTest")));
	}

}
