<?php
/**
 * PHP-testpagina. Bedoeld om te kunnen testen zonder de rest van het systeem te beïnvloeden
 * @author: Michael Steenbeek
 */
 $geselecteerde_context='<http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Brede_groene_dijk_met_voorland>';
 
 $lijst_van_ies_in_context='DESCRIBE ?ie WHERE { ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> '.$geselecteerde_context.'}';
 $query=urlencode($lijst_van_ies_in_context);
 //echo '<pre>'.$query.'</pre>';
 $result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.$query);
 echo 'Lijstje van IEs in context "Brede groene dijk met voorland":<br />';
 echo '<pre>'.$result.'</pre>';
 
 
 require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
 $parse=JSON_EMontParser::parse($result);
 echo 'Geparsed:<br />'; 
 echo '<pre>';
 var_dump($parse);
 echo '</pre>';
 
 
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