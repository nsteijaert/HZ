<?php
/**
 * Visualisatiepagina
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php/php-emont/Model.class.php');
require_once(__DIR__.'/php/Uri.class.php');
require_once(__DIR__.'/php/Visualisatie.class.php');

$standaard_model_uri='wiki:Building_with_Nature-2Dinterventies_op_het_systeem_practice';

if($par) {
	$model_uri='wiki:'.$pars[1];
}
elseif(!empty($_GET['model'])) {
	$model_uri=urldecode($_GET['model']);
}
elseif(!empty($_POST['model'])) {
	$model_uri=$_POST['model'];
}
else {
	$model_uri=$standaard_model_uri;
}

if($_POST['titel'])
{
	Model::nieuwIE($_POST['ie'],$context_uri,$_POST['titel']);
}

if($_POST['van'])
{
	$eigenschappen=array();

	if($_POST['notitie'])
		$eigenschappen['Element link note']=$_POST['notitie'];
	if($_POST['type']=='Contributes')
		$eigenschappen['Element contribution value']=$_POST['subtype'];
	if($_POST['type']=='Connects')
		$eigenschappen['Element connection type']=$_POST['subtype'];

	Model::maakVerband($_POST['van'],$_POST['naar'],$_POST['type'],$eigenschappen);
}

if($_POST['verwijder-verband'])
{
	$waardes=explode('|',$_POST['verwijder-verband']);
	Model::verwijderVerband($waardes[0],$waardes[2],$waardes[1]);
}
?>

<h2 id="visualisatiekop">Visualisatie</h2>
<p>U kunt elementen verslepen om het overzicht te verbeteren. Dubbelklik op een element om de wikipagina ervan weer te geven.</p>

<?php
$visualisatie=new Visualisatie($model_uri);
echo $visualisatie->geefInhoud();

if(Model::modelIsExperience($model_uri))
{
	$context_uri=Model::geefContextVanModel($model_uri);
	
	$l1model=Model::geefL1modelVanCase($model_uri);
	$l1hoofdcontext=Model::geefContextVanModel($l1model);

	$data=Model::geefElementenUitContextEnSubcontexten($l1hoofdcontext);

	echo '<h2>Nieuw element</h2>';
	echo '<form method="post">Beschikbare IE\'s (afkomstig van L1-model "'.Uri::SMWuriNaarLeesbareTitel($l1model).'"):<br /><select name="ie">';

	foreach($data['@graph'] as $item)
	{
		echo '<option value="'.$item['@id'].'">'.$item['label'].'</option>';
	}

	echo '</select><br />Naam: <input type="text" style="width: 300px;" name="titel"/><input type="submit" value="Aanmaken"/></form>';

	$data=Model::geefElementenUitContextEnSubcontexten($context_uri);
	$ie_lijst='';
	$verbandenlijst=array();

	foreach($data['@graph'] as $item)
	{
		$ie_lijst.='<option value="'.$item['@id'].'">'.$item['label'].'</option>';
		$elementen=Model::elementenNaarArrays(Model::geefArtikelTekst($item['@id']));

		foreach($elementen as $element)
		{
			if($element['Element link'])
			{
				$verbandenlijst[]=array('van'=>$item['label'],'type'=>$element['type'],'naar'=>$element['Element link']);
			}
		}
	}

	echo '<h2>Nieuw verband aanbrengen</h2>';
	echo '<form method="post"><table>';
	echo '<tr><th>Van:</th><th>Type:</th><th>Naar:</th><tr>';
	echo '<tr><td><select name="van">'.$ie_lijst.'</select></td>';
	echo '<td><select name="type"><option value="Contributes">Contributes</option><option value="Depends">Depends</option><option value="Connects">Connects</option><option value="Produces">Produces</option><option value="Consumes">Consumes</option></select></td>';
	echo '<td><select name="naar">'.$ie_lijst.'</select></td></tr>';
	echo '<tr><td></td><td>Notitie:</td><td><input name="notitie" type="text" style="width:300px;"></td></tr>';
	echo '<tr><td></td><td>CV/CT:</td><td><input name="subtype" type="text" style="width:300px;"/></td></tr>';
	echo '</table><input type="submit" value="Aanmaken" /></form>';

	echo '<h2>Verband verwijderen</h2>';
	echo '<form method="post"><table>';
	foreach($verbandenlijst as $verband)
	{
		echo '<tr><td><input type="radio" name="verwijder-verband" value="'.$verband['van'].'|'.$verband['type'].'|'.$verband['naar'].'"/>&nbsp;</td><td>'.$verband['van'].'&nbsp;</td><td>'.$verband['type'].'&nbsp;</td><td>'.$verband['naar'].'</td></tr>';
	}
	echo '</table><input type="submit" value="Verwijderen"></form>';

	echo '<h2>L1-model</h2>';
	echo '<iframe width="100%" height="800" src="/mediawiki/extensions/EMontVisualisator/includes/php/Visualisatie.class.php?echo=true&amp;model_uri='.urlencode($l1model).'"></iframe>';
}