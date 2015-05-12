<?php
/**
 * Visualisatiepagina
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');
require_once(__DIR__.'/Visualisatie.class.php');

/* Manipulatiecode */
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
/* Einde manipulatiecode */

class Visualisatiepagina
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		if(!$model_uri)
			$model_uri='wiki:Building_with_Nature-2Dinterventies_op_het_systeem_practice';

		$this->inhoud.='<h2 id="visualisatiekop">Visualisatie</h2>
		<p>U kunt elementen verslepen om het overzicht te verbeteren. Dubbelklik op een element om de wikipagina ervan weer te geven.</p>';

		$visualisatie=new Visualisatie($model_uri);
		$this->inhoud.=$visualisatie->geefInhoud();

		if(Model::modelIsExperience($model_uri))
		{
			$l1model=Model::geefL1modelVanCase($model_uri);
			$l1hoofdcontext=Model::geefContextVanModel($l1model);

			$data=Model::geefElementenUitContextEnSubcontexten($l1hoofdcontext);

			$this->inhoud.= '<h2>Nieuw element</h2>';
			$this->inhoud.= '<form method="post">Beschikbare IE\'s (afkomstig van L1-model "'.Uri::SMWuriNaarLeesbareTitel($l1model).'"):<br /><select name="ie">';

			foreach($data['@graph'] as $item)
			{
				$this->inhoud.= '<option value="'.$item['@id'].'">'.$item['label'].'</option>';
			}

			$this->inhoud.= '</select><br />Naam: <input type="text" style="width: 300px;" name="titel"/><input type="submit" value="Aanmaken"/></form>';

			$context_uri=Model::geefContextVanModel($model_uri);
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

			$this->inhoud.= '<h2>Nieuw verband aanbrengen</h2>';
			$this->inhoud.= '<form method="post"><table>';
			$this->inhoud.= '<tr><th>Van:</th><th>Type:</th><th>Naar:</th><tr>';
			$this->inhoud.= '<tr><td><select name="van">'.$ie_lijst.'</select></td>';
			$this->inhoud.= '<td><select name="type"><option value="Contributes">Contributes</option><option value="Depends">Depends</option><option value="Connects">Connects</option><option value="Produces">Produces</option><option value="Consumes">Consumes</option></select></td>';
			$this->inhoud.= '<td><select name="naar">'.$ie_lijst.'</select></td></tr>';
			$this->inhoud.= '<tr><td></td><td>Notitie:</td><td><input name="notitie" type="text" style="width:300px;"></td></tr>';
			$this->inhoud.= '<tr><td></td><td>CV/CT:</td><td><input name="subtype" type="text" style="width:300px;"/></td></tr>';
			$this->inhoud.= '</table><input type="submit" value="Aanmaken" /></form>';

			$this->inhoud.= '<h2>Verband verwijderen</h2>';
			$this->inhoud.= '<form method="post"><table>';
			foreach($verbandenlijst as $verband)
			{
				$this->inhoud.= '<tr><td><input type="radio" name="verwijder-verband" value="'.$verband['van'].'|'.$verband['type'].'|'.$verband['naar'].'"/>&nbsp;</td><td>'.$verband['van'].'&nbsp;</td><td>'.$verband['type'].'&nbsp;</td><td>'.$verband['naar'].'</td></tr>';
			}
			$this->inhoud.= '</table><input type="submit" value="Verwijderen"></form>';

			$this->inhoud.= '<h2>L1-model: '.Uri::SMWuriNaarLeesbareTitel($l1hoofdcontext).'</h2>';
			$this->inhoud.= '<iframe width="100%" height="800" src="/mediawiki/extensions/EMontVisualisator/includes/php/Visualisatie.class.php?echo=true&amp;model_uri='.urlencode($l1model).'"></iframe>';
		}
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}