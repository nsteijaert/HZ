<?php
/**
 * PHP-testpagina. Bedoeld om te kunnen testen zonder de rest van het systeem te beïnvloeden
 * @author: Michael Steenbeek
 */
 
 $lijst_van_contexten='DESCRIBE ?context WHERE { ?context <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AContext>
} LIMIT 2';
 $query=urlencode($lijst_van_contexten);
 //echo '<pre>'.$query.'</pre>';
 $result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.$query);
 echo 'Lijstje van contexten:<br />';
 echo '<pre>'.$result.'</pre>';
 
 require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
 $contexten=new JSON_EMontParser($result);
 
 
 
 /* Ouwe meuk
 
 require_once('php/SPARQLClient.class.php');
 $client = new SPARQLClient('http://localhost:3030/ds/query');
 // Default
 $client->setSerialiser();
 
 $query="SELECT DISTINCT * WHERE { ?s ?p ?o } LIMIT 1";
 echo 'abc';
 $json=$client->parseQuery($query);
 echo 'def';
 $idata=$client->parseJSON($json);
 echo 'ghi';
 echo '<pre>';
 var_dump($json);
 echo "\n\n\n\n\n\n\n\n";
 var_dump($idata);
 echo '</pre>';
  * 
  */
?>