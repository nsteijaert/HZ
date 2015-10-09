<?php
/**
 * Visualisatiepagina
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');
require_once(__DIR__.'/SPARQLConnection.class.php');

class Visualisatiepagina
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		if(!$model_uri)
			$model_uri='wiki:Building_with_Nature-2Dinterventies_op_het_systeem_practice';

		$url = (explode('?',rtrim($_SERVER['REQUEST_URI'],'/')));
		$hoofdvisualisatie_id = 'visualisatie-'.Uri::stripSMWuriPadEnPrefixes($model_uri);

		if($_POST)
		{
			$type = $_GET['type'];
			$actie = $_GET['actie'];

			if ($type == 'context')
			{
				if ($actie == 'extrasupercontext')
				{
					$context = $_POST['context'];
					$supercontext = $_POST['supercontext'];

					if ($context != $supercontext)
					{
						Model::extraSupercontext($context, $supercontext);
					}
				}
				elseif ($actie == 'supercontextverwijderen')
				{
					list($context, $supercontext) = explode('|', $_POST['verwijder-supercontexten']);

					Model::supercontextVerwijderen($context, $supercontext);
				}
			}
			elseif ($type == 'ie')
			{
				if ($actie == 'contexttoevoegen')
				{
					$ie = $_POST['ie'];
					$context = $_POST['context'];

					Model::contextToevoegenAanIE($ie, $context);
				}
				elseif ($actie == 'verwijderverband')
				{
					$waardes = explode('|', $_POST['verwijder-verband']);
					Model::verwijderVerband($waardes[0], $waardes[2], $waardes[1]);
				}
			}
		}

		$this->inhoud.=sprintf('
			<p>U kunt elementen verslepen om het overzicht te verbeteren. Dubbelklik op een element om de wikipagina ervan weer te geven.</p>

			<button title="Terug naar modellenoverzicht" onclick="window.location=\'%1$s/../../\'">⮤</button>
			<button title="Herladen" onclick="window.location=\'%1$s\';">⟳</button>
			<button title="Naar hoofdcontext scrollen" onclick="adjustScrollbars(visualisatieId,true,0);">⯐</button>',$url[0]);

		if(Model::modelIsExperience($model_uri)) {
			$this->inhoud.=' <button title="Nieuw Intentional Element" onclick="toggleL1modelDiv(true);">➕ IE</button>';
			$this->inhoud.=' <button title="Nieuwe Context" onclick="nieuweContextPopup();">➕ Context</button>';
			$this->inhoud.=' <button title="Nieuwe verband" onclick="nieuwVerbandPopup();">➕ Verband</button>';
		}

		$this->inhoud.=sprintf('
		<div id="visualisatiepaginacontainer">
			<div id="div-%1$s"></div>
		</div>
			<script type="text/javascript">

				$("#visualisatiepaginacontainer").height($("#mw-content-text").width()*0.75);

				var domeinprefix = "%2$s";

				var contextUri = "%3$s";
				var visualisatieId = "%1$s";

				startVisualisatie(visualisatieId, contextUri);
			</script>',$hoofdvisualisatie_id,Uri::geefDomeinPrefix(),Model::geefContextVanModel($model_uri));

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

			$sec_visualisatie_id='visualisatie-'.Uri::stripSMWuriPadEnPrefixes($l1model);

			////
			$this->inhoud.='<h2>Verband verwijderen</h2>';
			$this->inhoud.='<form method="post" action="?actie=verwijderverband&amp;type=ie"><table>';
			foreach($verbandenlijst as $verband)
			{
				$this->inhoud.='<tr><td><input type="radio" name="verwijder-verband" value="'.$verband['van'].'|'.$verband['type'].'|'.$verband['naar'].'"/>&nbsp;</td><td>'.$verband['van'].'&nbsp;</td><td>'.$verband['type'].'&nbsp;</td><td>'.$verband['naar'].'</td></tr>';
			}
			$this->inhoud.='</table><input type="submit" value="Verwijderen"></form>';

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

			$this->inhoud.='
				<script type="text/javascript">
					var secContextUri = "'.Model::geefContextVanModel($l1model).'";
					var secVisualisatieId = "'.$sec_visualisatie_id.'";

					createL1hoverPopup(secVisualisatieId);
					startVisualisatie(secVisualisatieId, secContextUri);
				</script>';
		}
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}