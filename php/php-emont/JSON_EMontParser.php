<?php
/**
 * Pagina om Ajax-aanroepen vanuit JSON te beantwoorden met JSON
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/JSON_EMontParser.class.php');
require_once(__DIR__.'/../SPARQLConnection.class.php');

$connectie=new SPARQLConnection();
$context_uri='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem';

$situatieparser=new JSON_EMontParser($context_uri);
$parse=$situatieparser->geefElementenInSituatie();
echo json_encode($parse);