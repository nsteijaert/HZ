<?php
/**
 * Testpagina voor EMont-parser.
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Testpagina voor EMont-parser</title>
	<meta charset='utf-8'>
</head>
<body>
<?php
$context='Menselijk-2D_en_ecosysteem';
$context_uri='<http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'.$context.'>';
 
$query_inhoud_context='DESCRIBE ?ie WHERE { ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> '.$context_uri.'}';
  
$result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.urlencode($query_inhoud_context));
echo 'Lijstje van IEs in context "'.JSON_EMontParser::decodeerSMWNaam($context).'":<br />';
echo '<a href="#geparset">Naar de geparsete gegevens</a><br />';
echo '<pre>'.$result.'</pre>';

$parse=JSON_EMontParser::parse($result);

echo '<a name="geparset">Geparset:</a><br />'; 
echo '<pre>';
var_dump($parse);
echo '</pre>';
 
?>
</body>
</html>