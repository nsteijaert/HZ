<?php
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
require_once(__DIR__.'/php/php-emont/Model.class.php');

$l1modellen=Model::geefL1modellen();
$l2cases=Model::geefL2cases();

if($_POST['titel']!=null)
{
	$titleObj = Title::newFromText($_POST['titel']);
	$articleObj = new Article($titleObj);

	if($articleObj)
	{
		$articleObj->doEdit( 'Hello world!', 'Pagina aangemaakt via EMontVisualisator.');
	}
}
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
<form method="post">
	<p>Nieuwe experience aanmaken:</p>
	<input type="text" name="titel" />
	<input type="submit" value="Aanmaken" />
</form>
</body>
</html>