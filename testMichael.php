<?php
/**
 * PHP-testpagina. Bedoeld om te kunnen testen zonder de rest van het systeem te beïnvloeden
 * @author: Michael Steenbeek
 */
 require_once('php/SPARQLClient.class.php');
 $client=new SPARQLClient();
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
?>