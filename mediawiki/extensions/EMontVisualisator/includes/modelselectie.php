<?php
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
require_once(__DIR__.'/php/php-emont/Model.class.php');
require_once(__DIR__.'/php/Uri.class.php');

// Notices worden voornamelijk bij niet-gedefinieerde eigenschappen gegeven. In productie uitzetten.
error_reporting(E_ALL & ~E_NOTICE);


$l1modellen=Model::geefL1modellen();
$l2cases=Model::geefL2cases();

if($_POST['titel']!=null)
{
	$l1model=Uri::decodeerSMWnaam($_POST['l1model']);
	$titel=$_POST['titel'];
	
	$contextTitle = Title::newFromText($titel);
	$contextArticle = new Article($contextTitle);

	if($contextArticle)
	{
		$contextArticleContents='{{Context}}
{{Heading
|Heading nl='.$titel.'
}}
{{Context query}}';
		$contextArticle->doEdit($contextArticleContents, 'Pagina aangemaakt via EMontVisualisator.');
	}
	
	$experienceTitle = Title::newFromText($titel.' experience');
	$experienceArticle = new Article($experienceTitle);

	if($experienceArticle)
	{
		$experienceArticleContents='{{Practice
|Context='.$titel.'
|Practice type=Experience
}}
{{Paragraphs show}}
{{Heading
|Heading nl='.$titel.' experience
}}

{{Practice links
|Part of='.$l1model.'
}}
{{Practice query}}';
		
		$experienceArticle->doEdit($experienceArticleContents, 'Pagina aangemaakt via EMontVisualisator.');
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
<ul>
<?php
foreach ($l1modellen as $l1uri => $l1beschrijving)
{
	echo '<li><a href="Speciaal%3AEMontVisualisator/toon/'.Uri::stripSMWuriPadEnPrefixes($l1uri).'">'.$l1beschrijving.'</a></li>';
}
?>
</ul>
<h2 id="experiences">Experiences (L2, cases)</h2>
<ul>
<?php
foreach($l2cases as $l2uri => $l2beschrijving)
{
	echo '<li><a href="Speciaal%3AEMontVisualisator/toon/'.Uri::stripSMWuriPadEnPrefixes($l2uri).'">'.$l2beschrijving.'</a></li>';
}
?>
</ul>
<h2>Nieuwe experience aanmaken</h2>
<form method="post">
	<table>
		<tr><td style="width: 150px;">Gebaseerd op:</td><td> 
	<select style="width:350px;" name="l1model">
	<?php
	foreach ($l1modellen as $l1uri => $l1beschrijving)
	{
		echo '<option value="'.Uri::stripSMWuriPadEnPrefixes($l1uri).'">'.$l1beschrijving.'</option>';
	}
	?>
	</select></td></tr>
	<tr><td>Naam:</td><td><input style="width:342px;" type="text" name="titel" /></td></tr>
	<tr><td colspan="100%"><input type="submit" value="Aanmaken" /></td></tr>
	</table>
</form>
</body>
</html>