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
<h2 name="practices">Practices (L1)</h2>
<p>Kies een practice om weer te geven.</p>
<form method="post" action="visualisatie.php">
	<select size="10" name="context">';
	<?php
	foreach ($l1modellen as $l1model)
	{
		echo '<option ';
		if ($l1model->getUri()==$standaard_context_uri)
			echo 'selected="selected" ';
		echo 'value="'.$l1model->getUri().'">'.Uri::SMWuriNaarLeesbareTitel($l1model->getUri()).'</option>';
	}
	?>
	</select><br />
	<input type="submit" value="Deze practice opvragen" />
</form>
<h2 name="experiences">Experiences (L2, cases)</h2>
<p>Kies een experience om weer te geven.</p>
<form method="post" action="visualisatie.php">
	<select size="10" name="context">
	<?php
	foreach($l2cases as $l2case)
	{
		echo '<option value="'.$l2case->getUri().'">'.Uri::SMWuriNaarLeesbareTitel($l2case->getUri()).'</option>';
	}
	?>
	</select><br />
	<input type="submit" value="Deze experience opvragen" />
</form>
<?php toonDexPostPagina(); ?>
</body>
</html>