<?php
/**
 * Testpagina voor EMont-parser.
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
require_once(__DIR__.'/php/SPARQLConnection.class.php');
include_once(__DIR__.'/php/tests/JSON_EMontParserTest.class.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Testpagina voor EMont-parser</title>
	<meta charset='utf-8'>
</head>
<body>
<?php
$connectie=new SPARQLConnection();

//$context='Menselijk-2D_en_ecosysteem';
//$context="Building_with_Nature-2Dinterventies_op_het_systeem";
$context="B_en_O_Kust";
$context_uri='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'.$context;
 
echo 'Lijstje van IEs in context "'.JSON_EMontParser::decodeerSMWNaam($context);?>
<br /><a href="#geparset">Naar de geparsete gegevens</a><br />
<?php
$situatieparser=new JSON_EMontParser($context_uri);
$parse=$situatieparser->geefElementenInSituatie();
?>
<a name="geparset">Geparset:</a><br /> 
<pre>
<?php var_dump($parse); ?>
</pre>
<?php /*
Test van isSituatie: (moet 1.true en 2.false opleveren)
<pre>
<?php
var_dump(JSON_EMontParser::isSituatie("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kust"));
var_dump(JSON_EMontParser::isSituatie("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kus"));
?>
</pre>
Test van zoeken naar subrollen:
<pre>
<?php
var_dump(JSON_EMontParser::zoekSubrollen("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem"));
?>
</pre>
*/?>
</body>
</html>