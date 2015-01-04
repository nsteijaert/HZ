<?php
/**
 * PHP-testpagina. Bedoeld om te kunnen testen zonder de rest van het systeem te beïnvloeden
 * @author: Michael Steenbeek
 */
 $context='Menselijk-2D_en_ecosysteem';
 $context_uri='<http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'.$context.'>';
 
 $lijst_van_ies_in_context='DESCRIBE ?ie WHERE { ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> '.$context_uri.'}';
 $query=urlencode($lijst_van_ies_in_context);
  
 $result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.$query);
 echo 'Lijstje van IEs in context "'.$context.'":<br />';
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