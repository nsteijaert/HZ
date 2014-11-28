<?php
$depth = $_POST['depth'];
$concept = $_POST['concept'];

$query = sprintf('
		PREFIX uri: <http://192.168.238.133/index.php/Speciaal:URIResolver/>
		PREFIX skos: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkos-3A>
		PREFIX skosem: <http://192.168.238.133/index.php/Speciaal:URIResolver/Eigenschap-3ASkosem-3A>
		
		SELECT ?concept ?relation ?value WHERE
		    {
		        uri:%s ?relation ?value .
		
		        FILTER(regex(str(?relation), "Eigenschap-3ASkos-3A") 
		        || regex(str(?relation), "Eigenschap-3ASkosem-3A")) .
		    }
	', $concept);

// For loop to add 
for($i = 0; $i < $depth; $i++) {
	$query .= '';
}

echo $query;
?>