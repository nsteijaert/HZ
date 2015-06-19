<?php
/**
 * Visualisatiepagina
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');
require_once(__DIR__.'/Visualisatie.class.php');
require_once(__DIR__.'/SPARQLConnection.class.php');

/* Manipulatiecode */
if($_POST)
{
	$type=$_GET['type'];
	$actie=$_GET['actie'];
	$naamprefix=Uri::SMWuriNaarLeesbareTitel($context_uri);

	if($type=='context')
	{
		if($actie=='nieuw')
		{
			$naam=$naamprefix.' '.$_POST['naam-nieuwe-context'];
			$supercontext_uri=$_POST['supercontext'];

			Model::nieuweContext($naam);
			Model::nieuweVN($naam.' VN','Context',$naam);
			Model::extraSupercontext($naam,$supercontext_uri);
		}
		elseif($actie=='extrasupercontext')
		{
			$context=$_POST['context'];
			$supercontext=$_POST['supercontext'];

			if($context!=$supercontext)
			{
				Model::extraSupercontext($context,$supercontext);
			}
		}
		elseif($actie=='supercontextverwijderen')
		{
			list($context,$supercontext)=explode('|',$_POST['verwijder-supercontexten']);

			Model::supercontextVerwijderen($context,$supercontext);
		}
	}
	elseif($type=='ie')
	{
		if($actie=='contexttoevoegen')
		{
			$ie=$_POST['ie'];
			$context=$_POST['context'];

			Model::contextToevoegenAanIE($ie,$context);
		}
		elseif($actie=='nieuw')
		{
			$naam=$naamprefix.' '.$_POST['titel'];
			Model::nieuwIE($_POST['ie'],$_POST['context'],$naam);
			Model::nieuweVN($naam.' VN','Intentional Element',$naam);
		}
		elseif($actie=='maakverband')
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
		elseif($actie=='verwijderverband')
		{
			$waardes=explode('|',$_POST['verwijder-verband']);
			Model::verwijderVerband($waardes[0],$waardes[2],$waardes[1]);
		}
	}
}

/* Einde manipulatiecode */

class Visualisatiepagina
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		if(!$model_uri)
			$model_uri='wiki:Building_with_Nature-2Dinterventies_op_het_systeem_practice';

		$this->inhoud.='<script type="text/javascript">
		function toggleL1modelframe()
		{
			var l1modelframe=document.getElementById(\'l1modelframe\');
			if (l1modelframe.style.display==\'none\')
			{
				l1modelframe.style.display=\'block\';
			}
			else
			{
				l1modelframe.style.display=\'none\';
			}
		}
		</script>';
		$url=(explode('?',rtrim($_SERVER['REQUEST_URI'],'/')));
		$this->inhoud.='<a href="'.$url[0].'/../../">Terug naar het modellenoverzicht</a>';
		$this->inhoud.='<h2 id="visualisatiekop">Visualisatie</h2>
		<p><a href="'.$url[0].'">Herladen</a></p>
		<p>U kunt elementen verslepen om het overzicht te verbeteren. Dubbelklik op een element om de wikipagina ervan weer te geven.</p>';

		$visualisatie=new Visualisatie($model_uri);
		$this->inhoud.=$visualisatie->geefInhoud();

		if(Model::modelIsExperience($model_uri))
		{
			$l1model=Model::geefL1modelVanCase($model_uri);
			$l1hoofdcontext=Model::geefContextVanModel($l1model);

			$context_uri=Model::geefContextVanModel($model_uri);
			$contexten=Model::geefUrisVanContextEnSubcontexten($context_uri);

			$contextenlijst='';
			foreach($contexten as $context)
			{
				$contextenlijst.='<option value="'.$context.'">'.Uri::SMWuriNaarLeesbareTitel($context).'</option>';
			}

			$data=Model::geefElementenUitContextEnSubcontexten($context_uri);

			$ie_lijst='';
			$verbandenlijst=array();
			$ie_contexten=array();

			foreach($data['@graph'] as $item)
			{
				if($item['@id'])
				{
					$ie_lijst.='<option value="'.$item['@id'].'">'.$item['label'].'</option>';
					$elementen=Model::elementenNaarArrays(Model::geefArtikelTekst($item['@id']));

					foreach($elementen as $element)
					{
						if($element['Element link'])
						{
							$verbandenlijst[]=array('van'=>$item['label'],'type'=>$element['type'],'naar'=>$element['Element link']);
						}
						elseif($element['type']=='Intentional Element links' || $element['type']=='Activity links')
						{
							$linksverbanden=array();

							if($element['Part of'])
							{
								$linksverbanden=explode(',',$element['Part of']);
								foreach($linksverbanden as $linksverband)
								{
									if(trim($linksverband))
										$verbandenlijst[]=array('van'=>$item['label'],'type'=>'Part of','naar'=>trim($linksverband));
								}
							}
							if($element['Consumes'])
							{
								$linksverbanden=explode(',',$element['Consumes']);
								foreach($linksverbanden as $linksverband)
								{
									if(trim($linksverband))
										$verbandenlijst[]=array('van'=>$item['label'],'type'=>'Consumes','naar'=>trim($linksverband));
								}
							}
							if($element['Produces'])
							{
								$linksverbanden=explode(',',$element['Produces']);
								foreach($linksverbanden as $linksverband)
								{
									if(trim($linksverband))
										$verbandenlijst[]=array('van'=>$item['label'],'type'=>'Produces','naar'=>trim($linksverband));
								}
							}
						}
					}
					if($item['Eigenschap-3AContext'])
					{
						if(is_array($item['Eigenschap-3AContext']))
						{
							foreach($item['Eigenschap-3AContext'] as $ie_context)
							{
								$ie_contexten[$item['@id']][]=$ie_context;
							}
						}
						else
						{
							$ie_contexten[$item['@id']][]=$item['Eigenschap-3AContext'];
						}
					}
				}
			}

			$this->inhoud.='<h2>L1-model: '.Uri::SMWuriNaarLeesbareTitel($l1hoofdcontext).'</h2>';
			$this->inhoud.='<button onclick="toggleL1modelframe();">Open/dichtklappen</button>';
			$this->inhoud.='<iframe style="display:none;" id="l1modelframe" width="100%" height="800" src="/mediawiki/extensions/EMontVisualisator/includes/php/Visualisatie.class.php?echo=true&amp;model_uri='.urlencode($l1model).'"></iframe>';

			////
			$this->inhoud.='<h2>Nieuw Intentional Element</h2>';
			$this->inhoud.='<form action="?actie=nieuw&amp;type=ie" method="post"><table>';
			$this->inhoud.='<tr><td>Naam: '.Uri::SMWuriNaarLeesbareTitel($context_uri).'</td><td><input type="text" style="width: 300px;" name="titel"/></td></tr>';
			$this->inhoud.='<tr><td>Instance of:</td><td><select name="ie">';

			$data=Model::geefElementenUitContextEnSubcontexten($l1hoofdcontext);
			if(isset($data['@graph']))
			{
				foreach($data['@graph'] as $item)
				{
					$this->inhoud.= '<option value="'.$item['@id'].'">'.$item['label'].'</option>';
				}
			}
			$this->inhoud.='</select></td></tr>';

			$this->inhoud.='<tr><td>Context:</td><td><select name="context">'.$contextenlijst.'</select></td></tr>';
			$this->inhoud.='</table><input type="submit" value="Aanmaken"/></form>';

			////
			$this->inhoud.='<h2>Nieuw verband aanbrengen</h2>';
			$this->inhoud.='<form method="post" action="?actie=maakverband&amp;type=ie"><table>';
			$this->inhoud.='<tr><th>Van:</th><th>Type:</th><th>Naar:</th><tr>';
			$this->inhoud.='<tr><td><select name="van">'.$ie_lijst.'</select></td>';
			$this->inhoud.='<td><select name="type"><option value="Contributes">Contributes</option><option value="Depends">Depends</option><option value="Connects">Connects</option><option value="Produces">Produces</option><option value="Consumes">Consumes</option><option value="Part of">Part of</option></select></td>';
			$this->inhoud.='<td><select name="naar">'.$ie_lijst.'</select></td></tr>';
			$this->inhoud.='<tr><td></td><td>Notitie:</td><td><input name="notitie" type="text" style="width:300px;"></td></tr>';
			$this->inhoud.='<tr><td></td><td>CV/CT:</td><td><input name="subtype" type="text" style="width:300px;"/></td></tr>';
			$this->inhoud.='</table><input type="submit" value="Aanmaken" /></form>';

			////
			$this->inhoud.='<h2>Verband verwijderen</h2>';
			$this->inhoud.='<form method="post" action="?actie=verwijderverband&amp;type=ie"><table>';
			foreach($verbandenlijst as $verband)
			{
				$this->inhoud.='<tr><td><input type="radio" name="verwijder-verband" value="'.$verband['van'].'|'.$verband['type'].'|'.$verband['naar'].'"/>&nbsp;</td><td>'.$verband['van'].'&nbsp;</td><td>'.$verband['type'].'&nbsp;</td><td>'.$verband['naar'].'</td></tr>';
			}
			$this->inhoud.='</table><input type="submit" value="Verwijderen"></form>';

			////
			$this->inhoud.='<h2>Nieuwe context</h2>';
			$this->inhoud.='<form method="post" action="?actie=nieuw&amp;type=context">';
			$this->inhoud.='Naam: '.Uri::SMWuriNaarLeesbareTitel($context_uri).'<input type="text" name="naam-nieuwe-context" /><br />';
			$this->inhoud.='Supercontext: <select name="supercontext">'.$contextenlijst.'</select>';
			$this->inhoud.='<input type="submit" value="Aanmaken"></form>';

			////
			$this->inhoud.='<h2>Context verwijderen</h2>';

			////
			$this->inhoud.='<h2>Supercontext toevoegen aan context</h2>';
			$this->inhoud.='<form method="post" action="?actie=extrasupercontext&amp;type=context">';
			$this->inhoud.='Context: <select name="context">'.$contextenlijst.'</select><br />';
			$this->inhoud.='Supercontext: <select name="supercontext">'.$contextenlijst.'</select><br />';
			$this->inhoud.='<input type="submit" value="Toevoegen"></form>';

			////
			$this->inhoud.='<h2>Supercontext verwijderen van context</h2>';
			$this->inhoud.='<form method="post" action="?actie=supercontextverwijderen&amp;type=context"><table><tr><th></th><th>Context</th><th>Supercontext</th></tr>';

			foreach($contexten as $context)
			{
				$supercontexten=Model::zoekSupercontexten($context);
				foreach($supercontexten as $supercontext)
				{
					$this->inhoud.='<tr><td><input type="radio" name="verwijder-supercontexten" value="'.$context.'|'.$supercontext.'">&nbsp;</td><td>'.Uri::SMWuriNaarLeesbareTitel($context).'&nbsp;</td><td>'.Uri::SMWuriNaarLeesbareTitel($supercontext).'</td></tr>';
				}
			}

			$this->inhoud.='</table><input type="submit" value="Verwijderen" /></form>';

			////
			$this->inhoud.='<h2>Context aan Intentional Element toevoegen</h2>';
			$this->inhoud.='<form method="post" action="?actie=contexttoevoegen&amp;type=ie">';
			$this->inhoud.='<table><tr><td>Intentional Element</td><td><select name="ie">';
			$this->inhoud.=$ie_lijst;
			$this->inhoud.='</select></td></tr><tr><td>Context</td><td><select name="context">';
			$this->inhoud.=$contextenlijst;
			$this->inhoud.='</select></td></tr></table><input type="submit" value="Toevoegen" /></form>';

			////
			$this->inhoud.='<h2>Context van Intentional Element verwijderen</h2>';

			////
			$this->inhoud.='<h2>Context van Intentional Element vervangen</h2>';
			$this->inhoud.='<table>';
			$this->inhoud.='<tr><th>IE</th><th>Context</th><th>Vervangen door</th></tr>';
			foreach($ie_contexten as $ie=>$contextlijst)
			{
				foreach($contextlijst as $context)
				{
					$this->inhoud.='<form method="post" action="?actie=contextvervangen&amp;type=ie&amp;ie='.$ie.'&amp;context='.$context.'">';
					$this->inhoud.='<tr><td>'.Uri::SMWuriNaarLeesbareTitel($ie).'</td><td>'.Uri::SMWuriNaarLeesbareTitel($context).'</td><td><select name="nieuwecontext">';
					foreach($contexten as $beschikbarecontext)
					{
						if($beschikbarecontext!=$context)
						{
							$this->inhoud.='<option value="'.$beschikbarecontext.'">'.Uri::SMWuriNaarLeesbareTitel($beschikbarecontext).'</option>';
						}
					}

					$this->inhoud.='</select></td><td><input type="submit" value="Vervangen"/></td></tr></form>';
				}
			}
			$this->inhoud.='</table>';

		}
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}