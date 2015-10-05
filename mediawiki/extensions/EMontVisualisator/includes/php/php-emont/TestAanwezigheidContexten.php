<?php
require_once(__DIR__.'/JSON_EMontParser.class.php');
$parser=new JSON_EMontParser('wiki:De_Oosterschelde_beschermen_met_oesterriffen_Oosterschelde');
$objecten=$parser->geefElementenInSituatie();

var_dump($objecten);
?>