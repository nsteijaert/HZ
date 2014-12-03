<?php
$depth = $_POST['depth'];
$concept = $_POST['concept'];

$query = sprintf('
		PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
		PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
		PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
		PREFIX rdf: <http://www.w3.org/2000/01/rdf-schema#>
		
		PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
		PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
		PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
		PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		
		SELECT DISTINCT ?s ?p ?o {
		  ?c rdfs:label "%s" .
		  ?c (<>|!<>){,%d} ?s .
		  ?s ?p ?o .
		}
	', $concept, $depth);

// For loop to add 
for($i = 0; $i < $depth; $i++) {
	$query .= '';
}

echo $query;
?>