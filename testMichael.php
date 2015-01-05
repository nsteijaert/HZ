<?php
/**
 * Testpagina voor EMont-parser.
 * @author: Michael Steenbeek
 */
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
 
$lijst_van_ies_in_context='DESCRIBE ?ie WHERE { ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> '.$context_uri.'}';
$query=urlencode($lijst_van_ies_in_context);
  
$result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.$query);
echo 'Lijstje van IEs in context "'.urldecode(strtr($context,'-_','% ')).'":<br />';
echo '<pre>'.$result.'</pre>';

require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
$parse=JSON_EMontParser::parse($result);
echo 'Geparsed:<br />'; 
echo '<pre>';
var_dump($parse);
echo '</pre>';
 
?>
</body>
</html>