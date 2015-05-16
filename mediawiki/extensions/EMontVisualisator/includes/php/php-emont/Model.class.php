<?php
/**
 * Alles wat met L1- en L2-modellen te maken heeft, en de contexten/subcontexten die eraan hangen
 */
require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/JSON_EMontParser.class.php');

class Model
{
	private function __construct() {}

	/**
	 * Zoekt alle subsituaties en subrollen bij een bepaalde context (slaat subpractices over).
	 * @input De context-URI, zonder vishaken (< en >)
	 */
	static function zoekSubcontexten($context_uri)
	{
		$subcontexten=array();
		if(!$context_uri)
			return $subcontexten;

		$context_uri=Uri::escape_uri($context_uri);

		$query='DESCRIBE ?context WHERE { ?context property:Supercontext '.$context_uri.' }';
		$connectie=new SPARQLConnection();
		$contexten=$connectie->JSONQueryAsMultidimensionalPHPArray($query);

		if(isset($contexten['@graph']))
		{
			foreach($contexten['@graph'] as $item)
			{
				if($item['@id'])
				{
					// Subcases moeten niet worden meegenomen.
					if(!self::isHoofdcontextVanPractice($item['@id']))
					{
						$subcontexten[]=$item['@id'];
						$subcontexten=array_merge($subcontexten,self::zoekSubcontexten($item['@id']));
					}
				}
			}
		}
		return array_unique($subcontexten);
	}

	static function zoekSupercontexten($context_uri)
	{
		$subrollen=array();
		$context_uri=Uri::escape_uri($context_uri);

		$query='DESCRIBE ?supercontext WHERE { '.$context_uri.' property:Supercontext ?supercontext }';
		$connectie=new SPARQLConnection();
		$contexten=$connectie->JSONQueryAsMultidimensionalPHPArray($query);

		$return=array();
		if(isset($contexten['@graph']))
		{
			foreach($contexten['@graph'] as $item)
			{
				if($item['@id'])
					$return[]=$item['@id'];
			}
		}
		return $return;
	}

	/**
	 * Geeft lijst van L1-modellen (situaties)
	 */
	static function geefL1modellen()
	{
		$query='DESCRIBE ?practice WHERE {?practice property:Practice_type "Practice"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
					$contexten[$item['@id']]=strtr(Uri::SMWuriNaarLeesbareTitel($item['@id']),array(' practice'=>''));
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Geeft lijst van L2-cases (situaties), in de vorm van context-uri's
	 */
	static function geefL2cases()
	{
		$query='DESCRIBE ?experience WHERE {?experience property:Practice_type "Experience"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
					$contexten[$item['@id']]=strtr(Uri::SMWuriNaarLeesbareTitel($item['@id']),array(' experience'=>''));
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Bepaalt of een model een practice (L1-model) is.
	 */
	static function modelIsPractice($model_uri)
	{
		$query='DESCRIBE ?practice WHERE {
			?practice property:Practice_type "Practice" .
			FILTER (?practice = '.Uri::escape_uri($model_uri).')}';

		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Bepaalt of een model een experience (L2-case) is.
	 */
	static function modelIsExperience($model_uri)
	{
		$query='DESCRIBE ?practice WHERE {
			?practice property:Practice_type "Experience" .
			FILTER (?practice = '.Uri::escape_uri($model_uri).')}';

		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	static function geefContextVanModel($model_uri)
	{
		$query='SELECT ?context WHERE {
			'.Uri::escape_uri($model_uri).' property:Context ?context}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		return strtr($result['results']['bindings'][0]['context']['value'],array('http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'=>'wiki:'));
	}

	static function geefL1modelVanCase($l2_uri)
	{
		if (!self::modelIsExperience($l2_uri))
		{
			return null;
		}

		$query='SELECT ?model WHERE {
			'.Uri::escape_uri($l2_uri).' property:Part_of ?model}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		return strtr($result['results']['bindings'][0]['model']['value'],array('http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'=>'wiki:'));
	}

	/**
	 *  Bepaalt of een context-uri niet toebehoort aan (sub)practice.
	 */
	static function isHoofdcontextVanPractice($context_uri)
	{
		$context_uri=Uri::escape_uri($context_uri);
		$query="DESCRIBE ?s ?o WHERE {
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASelection_link> ".$context_uri."
			.
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3APractice_back_link> ?o
			}";
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (empty($result['@graph'])) // Leeg resultaat
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 *  Maakt een experience en hoofdcontext voor een nieuwe case aan, gebaseerd op een bestaande practice
	 */
	static function nieuweL2case($titel, $practice_uri)
	{
		$l1model=Uri::SMWuriNaarLeesbareTitel($practice_uri);

		self::nieuweContext($titel);

		$vnTitle = Title::newFromText($titel.' VN');
		$vnArticle = new Article($vnTitle);

		if($vnArticle)
		{
			$vnArticleContents='{{Context VN
|Model link='.$titel.'
}}
{{Intentional Element VN set links}}
{{Intentional Element VN show}}';
			$vnArticle->doEdit($vnArticleContents, 'Pagina aangemaakt via EMontVisualisator.');
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

	static function geefElementenUitContextEnSubcontexten($context_uri)
	{
		$alle_te_doorzoeken_uris=self::geefUrisVanContextEnSubcontexten($context_uri);

		$zoekstring=implode(' } UNION { ?ie property:Context ',$alle_te_doorzoeken_uris);
		$query_inhoud_situatie='DESCRIBE ?ie WHERE {{ ?ie property:Context '.$zoekstring.' }.{?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AIntentional_Element>} UNION {?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AActivity>}}';

		$connectie=new SPARQLConnection();
		return $connectie->JSONQueryAsMultidimensionalPHPArray($query_inhoud_situatie);
	}

	static function geefUrisVanContextEnSubcontexten($context_uri)
	{
		$subrollen=Model::zoekSubcontexten($context_uri);

		foreach(array_merge(array($context_uri),$subrollen) as $te_doorzoeken_uri)
		{
			$alle_te_doorzoeken_uris[]=Uri::escape_uri($te_doorzoeken_uri);
		}
		return $alle_te_doorzoeken_uris;
	}

	static function nieuwIE($instanceOf,$context_uri,$titel)
	{
		$ie_type=SPARQLConnection::geefEersteResultaat($instanceOf,'property:Intentional_Element_type');
		$ie_decomposition_type=SPARQLConnection::geefEersteResultaat(Uri::escape_uri($instanceOf),'property:Intentional_Element_decomposition_type');

		if($ie_type=='Activity')
		{
			$nieuw_ie='{{Activity
|Context='.Uri::SMWuriNaarLeesbareTitel($context_uri).',
|Intentional Element decomposition type='.$ie_decomposition_type.'
}}
{{Heading
|Heading nl='.$_POST['titel'].'
}}
{{VN query}}
{{Activity links
|Instance of='.Uri::SMWuriNaarLeesbareTitel($instanceOf).',
}}
{{Intentional Element query}}';
		}
		else
		{
			$nieuw_ie='{{Intentional Element
|Context='.Uri::SMWuriNaarLeesbareTitel($context_uri).',
|Intentional Element type='.$ie_type.'
|Intentional Element decomposition type='.$ie_decomposition_type.'
}}
{{Heading
|Heading nl='.$titel.'
}}
{{VN query}}
{{Intentional Element links
|Instance of='.Uri::SMWuriNaarLeesbareTitel($instanceOf).',
}}
{{Intentional Element query}}';
		}

		$ieTitle = Title::newFromText($titel);
		$ieArticle = new Article($ieTitle);

		$ieArticle->doEdit($nieuw_ie, 'Pagina aangemaakt via EMontVisualisator.');
	}

	/**
	 * Voegt een verband toe tussen twee IE's
	 */
	static function maakVerband($van,$naar,$type,$extra_informatie)
	{
		$van=Uri::SMWuriNaarLeesbareTitel($van);
		$naar=Uri::SMWuriNaarLeesbareTitel($naar);

		$titel_te_bewerken_artikel=Title::newFromText($van);
		$te_bewerken_artikel=new WikiPage($titel_te_bewerken_artikel);
		$inhoud=$te_bewerken_artikel->getText();

		// {{Intentional Element query}}, indien aanwezig, moet achteraan blijven.
		$achtervoegsel='';
		if(strpos($inhoud,'{{Intentional Element query}}')!==FALSE)
		{
			$inhoud=strtr($inhoud,array('{{Intentional Element query}}'=>''));
			$achtervoegsel='{{Intentional Element query}}';
		}

		$verband_tekst='{{'.$type.'
|Element link='.$naar.'
';
		foreach($extra_informatie as $eigenschap=>$waarde)
		{
			$verband_tekst.='|'.$eigenschap.'='.$waarde.'
';
		}

		$verband_tekst.='}}
';
		$nieuwe_inhoud=$inhoud.$verband_tekst.$achtervoegsel;

		$te_bewerken_artikel->doEdit($nieuwe_inhoud,'Verband toegevoegd via EMontVisualisator',EDIT_UPDATE);
	}

	/**
	 * Verwijdert een verband tussen twee IE's, indien aanwezig.
	 * @type: Met beginhoofdletter, zoals in wikiartikel, na {{
	 */
	static function verwijderVerband($van,$naar,$type)
	{
		$van=Uri::SMWuriNaarLeesbareTitel($van);
		$naar=Uri::SMWuriNaarLeesbareTitel($naar);

		$titel_te_bewerken_artikel=Title::newFromText($van);
		$te_bewerken_artikel=new WikiPage($titel_te_bewerken_artikel);
		$inhoud=$te_bewerken_artikel->getText();

		$verbandaanwezig=self::vindVerband($van,$naar,$type);

		if ($verbandaanwezig===FALSE)
			return;

		$blockstring='{{'.$type;
		$eindstring='}}';
		$posblock=0;

		while(TRUE)
		{
			$posblock=strpos($inhoud,$blockstring,$posblock);
			$posnaar=strpos($inhoud,$naar,$posblock);
			$posvolgendeblock=strpos($inhoud,$blockstring,$posblock+1);
			//echo $posblock.' '.$posnaar.' '.$posvolgendeblock.'<br />';

			if($posnaar<$posvolgendeblock || $posvolgendeblock===FALSE)
			{
				$poseind=strpos($inhoud,$eindstring,$posnaar);
				break;
			}
			// Om te voorkomen dat hetzelfde blok opnieuw wordt gevonden wordt de positie met 1 verhoogd.
			$posblock++;
		}
		$nieuwe_inhoud=substr($inhoud,0,$posblock).trim(substr($inhoud,$poseind+strlen($eindstring)));

		$te_bewerken_artikel->doEdit($nieuwe_inhoud,'Verband verwijderd via EMontVisualisator',EDIT_UPDATE);
	}

	/**
	 * Wijzigt een verband.
	 * @param $nieuwe_eigenschappen: Alle eigenschappen, inclusief de eigenschappen die niet veranderd zijn.
	 */
	static function wijzigVerband($van,$naar,$type,$nieuwe_eigenschappen)
	{
		$verband=self::vindVerband($van,$naar,$type);
		if ($verband===FALSE)
			return;

		self::verwijderVerband($van,$naar,$type);
		self::maakVerband($van,$naar,$type,$nieuwe_eigenschappen);
	}

	/**
	 * Vindt een verband.
	 * @return: Het gevonden verband als array, of FALSE als het niet is gevonden.
	 */
	static function vindVerband($van,$naar,$type)
	{
		$inhoud=self::geefArtikelTekst($van);
		$elementen=self::elementenNaarArrays($inhoud);

		foreach($elementen as $element)
		{
			if($element['type']==$type && $element['Element link']==$naar)
			{
				return $element;
			}
		}
		return FALSE;
	}

	/**
	 * Zet alle eigenschappen uit {{}}-blokjes in een tekst (uit een wiki-artikel) om in een array.
	 */
	static function elementenNaarArrays($tekst)
	{
		// Tekens die buiten de {{}}-blokken staan, inclusief spaties worden weggefilterd.
		// Daarnaast worden de eerste {{ en laatste }} in de tekst weggefilterd, zodat explode()
		// kan splitsen op }}{{.
		$tekst=preg_replace('/^(.*)\{\{/','',$tekst,1);
		$tekst=substr($tekst, 0, strrpos( $tekst, '}}'));
		$tekst=preg_replace("/\}\}([^\{\{]+)\{\{/",'}}{{',$tekst);

		$elementen=explode('}}{{',$tekst);

		$returnelementen=array();
		foreach($elementen as $element)
		{
			// Eigenschappen zijn gescheiden dmv een verticale streep. Overbodige spaties rondom de eigenschappen
			// en hun waardes worden weggefilterd.
			$returneigenschappen=array();
			$elementeigenschappen=explode('|',$element);
			$returneigenschappen['type']=trim($elementeigenschappen[0]);
			array_shift($elementeigenschappen);
			foreach($elementeigenschappen as $elementeigenschap)
			{
				$eigenschapdelen=explode('=',$elementeigenschap);
				$returneigenschappen[trim($eigenschapdelen[0])]=trim($eigenschapdelen[1]);
			}
			$returnelementen[]=$returneigenschappen;
		}
		return $returnelementen;
	}

	static function geefArtikelTekst($artikel)
	{
		$artikel=Uri::SMWuriNaarLeesbareTitel($artikel);

		$titel_op_te_vragen_artikel=Title::newFromText($artikel);
		$op_te_vragen_artikel=new WikiPage($titel_op_te_vragen_artikel);
		return $op_te_vragen_artikel->getText();
	}

	static function nieuweContext($naam)
	{
		$contextTitle = Title::newFromText($naam);
		$contextArticle = new Article($contextTitle);

		if($contextArticle)
		{
			$contextArticleContents='{{Context}}
{{Heading
|Heading nl='.$naam.'
}}
{{Context query}}';
			$contextArticle->doEdit($contextArticleContents, 'Pagina aangemaakt via EMontVisualisator.');
		}
	}

	static function extraSupercontext($context_uri,$supercontext_uri)
	{
		$context=Uri::SMWuriNaarLeesbareTitel($context_uri);
		$supercontext=Uri::SMWuriNaarLeesbareTitel($supercontext_uri);

		self::voegToeAanBlokargumentVanArtikel($context,'Context','Supercontext',$supercontext_uri,'Supercontext toegevoegd via EMontVisualisator');
	}

	static function supercontextVerwijderen($context_uri,$supercontext_uri)
	{
		$context=Uri::SMWuriNaarLeesbareTitel($context_uri);
		$supercontext=Uri::SMWuriNaarLeesbareTitel($supercontext_uri);

		self::verwijderUitBlokargumentVanArtikel($context,'Context','Supercontext',$supercontext_uri,'Supercontext verwijderd via EMontVisualisator');
	}

	static function contextToevoegenAanIE($ie_uri,$context_uri)
	{
		$ie=Uri::SMWuriNaarLeesbareTitel($ie_uri);
		$context=Uri::SMWuriNaarLeesbareTitel($context_uri);
		$ie_type=SPARQLConnection::geefEersteResultaat($ie_uri,'property:Intentional_Element_type');

		self::voegToeAanBlokargumentVanArtikel($ie,$ie_type,'Context',$context,'Context toegevoegd via EMontVisualisator');
	}

	/**
	 * Voegt een waarde uit een argument toe aan een blok op een wiki-pagina.
	 * Bijvoorbeeld {{Context|Supercontext=bestaande_waarde,toe_te_voegen_waarde}}
	 */
	static function voegToeAanBlokargumentVanArtikel($artikel,$bloknaam,$argument,$toe_te_voegen_inhoud,$samenvatting)
	{
		$titel_te_bewerken_artikel=Title::newFromText($artikel);
		$te_bewerken_artikel=new WikiPage($titel_te_bewerken_artikel);
		$inhoud=$te_bewerken_artikel->getText();

		$blockstring='{{'.$bloknaam;
		$eindstring='}}';

		$posblock=strpos($inhoud,$blockstring);
		$poseind=strpos($inhoud,$eindstring,$posblock+1);

		$preblock=substr($inhoud,0,$posblock);
		// De eindstring }} zit in het postblock!
		$postblock=trim(substr($inhoud,$poseind));
		$block=substr($inhoud,$posblock,($poseind-$posblock));

		if(!strpos($block,$argument))
		{
			$block.='|'.$argument.'='.$toe_te_voegen_inhoud.',';
		}
		else
		{
			$argumentblockintro=$argument.'=';
			$argumentblockoutro='|';

			$posintro=strpos($block,$argumentblockintro);
			$posoutro=strpos($block,$argumentblockoutro,$posintro);

			$argumentpreblock=substr($block,0,$posintro);

			if($posoutro)
			{
				$argumentblock=trim(substr($block,$posintro,($posoutro-$posintro)));
				$argumentpostblock="\n".substr($block,$posoutro);
			}
			else
			{
				$argumentblock=trim(substr($block,$posintro));
				$argumentpostblock="\n";
			}

			if(substr($argumentblock,-1,1)!=',')
				$argumentblock.=',';

			$argumentblock.=$toe_te_voegen_inhoud.',';
			$block=$argumentpreblock.$argumentblock.$argumentpostblock;
		}

		$nieuwe_inhoud=$preblock.$block.$postblock;

		$te_bewerken_artikel->doEdit($nieuwe_inhoud,$samenvatting,EDIT_UPDATE);
	}

	/**
	 * Verwijdert een waarde uit een argument van een blok op een wiki-pagina.
	 * Bijvoorbeeld {{Context|Supercontext=te_verwijderen_waarde}}
	 */
	static function verwijderUitBlokargumentVanArtikel($artikel,$bloknaam,$argument,$toe_te_voegen_inhoud,$samenvatting)
	{
		$titel_te_bewerken_artikel=Title::newFromText($artikel);
		$te_bewerken_artikel=new WikiPage($titel_te_bewerken_artikel);
		$inhoud=$te_bewerken_artikel->getText();

		$blockstring='{{'.$bloknaam;
		$eindstring='}}';

		$posblock=strpos($inhoud,$blockstring);
		$poseind=strpos($inhoud,$eindstring,$posblock+1);

		$preblock=substr($inhoud,0,$posblock);
		// De eindstring }} zit in het postblock!
		$postblock=trim(substr($inhoud,$poseind));
		$block=substr($inhoud,$posblock,$poseind);

		$argumentblockintro=$argument.'=';
		$argumentblockoutro='|';

		$posintro=strpos($block,$argumentblockintro);
		$posoutro=strpos($block,$argumentblockoutro,$posintro);

		$argumentpreblock=substr($block,0,$posintro);

		if($posoutro)
		{
			$argumentblock=substr($block,$posintro,$posoutro);
			$argumentpostblock=substr($block,$posoutro);
		}
		else
		{
			$argumentblock=substr($block,$posintro);
			$argumentpostblock='';
		}

		$argumentblock=strtr($argumentblock,array($toe_te_voegen_inhoud=>''));
		$argumentblock=strtr($argumentblock,array(',,'=>','));

		$block=$argumentpreblock.$argumentblock.$argumentpostblock;
		$nieuwe_inhoud=$preblock.$block.$postblock;

		$te_bewerken_artikel->doEdit($nieuwe_inhoud,$samenvatting,EDIT_UPDATE);
	}
}
