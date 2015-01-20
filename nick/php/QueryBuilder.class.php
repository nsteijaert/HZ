<?php

class QueryBuilder {

	private $depth;
	private $concept;

	function __construct($depth, $concept) {
		$this -> depth = $depth;
		$this -> concept = $concept;
	}

	function generateQuery($relations = "", $depth = "", $concept = "") {
		$relations = $relations == "" ? "true,true" : $relations;
		$depth = $depth == "" ? $this -> depth : $depth;
		$concept = $concept == "" ? $this -> concept : $concept;

		$relation = "";
		$relations = explode(",", $relations);

		if (($relations[0] === 'true') && ($relations[1] === 'true')) {
			$relation .= "<>|!<>";
		} else if (($relations[0] === 'true')) {
			$relation .= "skosem:broader";
		} else if (($relations[1] === 'true')) {
			$relation .= "skosem:narrower";
		} else {
			$relation .= "<>|!<>";
		}

		return sprintf('
			PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
			PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
			PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			
			construct { ?s ?p ?o }
			where {
			  ?c rdfs:label "%s" .
			  ?c (%s){,%d} ?s .
			  ?s ?p ?o
			  FILTER(EXISTS { ?s a uri:Categorie-3ASKOS_Concept } )
			}
		', $concept, $relation, $depth);
	}

}
?>