<?php
require_once(__DIR__.'/php/dex.php');
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
$l1modellen=JSON_EMontParser::geefL1modellen();
$l2cases=JSON_EMontParser::geefL2cases();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Modelselectie</title>
	<meta charset='utf-8'>
	<!-- Stylesheets van DeltaExpertise-->
	<!-- Aangepaste kopie van <link rel="stylesheet" href="http://195.93.238.49/wiki/deltaexpertise/wiki/load.php?debug=false&amp;lang=nl&amp;modules=mediawiki.legacy.commonPrint%2Cshared%7Cskins.deltaskin&amp;only=styles&amp;skin=deltaskin&amp;*" />
		Vervangen bij integreren in DeltaExpertise-->
	<link rel="stylesheet" href="css/dex1.css"/>
	<link rel="stylesheet" href="http://195.93.238.49/wiki/deltaexpertise/wiki/extensions/HeaderTabs/skins/ext.headertabs.large.css" />

</head>
<body>
<?php toonDexPrePagina(); ?>
<h1>Modellen</h1>
<h2 id="practices">Practices (L1)</h2>
<p>Kies een practice om weer te geven:</p>
<ul>
<?php
foreach ($l1modellen as $l1model)
{
	echo '<li><a href="visualisatie.php?context='.urlencode($l1model->getUri()).'">'.Uri::SMWuriNaarLeesbareTitel($l1model->getUri()).'</a></li>';
}
?>
</ul>
<h2 id="experiences">Experiences (L2, cases)</h2>
<p>Kies een experience om weer te geven:</p>
<ul>
<?php
foreach($l2cases as $l2case)
{
	echo '<li><a href="visualisatie.php?context='.urlencode($l2case->getUri()).'">'.Uri::SMWuriNaarLeesbareTitel($l2case->getUri()).'</a></li>';
}
?>
</ul>
<form>
	<p>Nieuwe experience aanmaken:</p>
</form>
<?php toonDexPostPagina(); ?>
</body>
</html>