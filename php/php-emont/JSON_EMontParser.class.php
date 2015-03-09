<?php
/**
 * EMont-modellen in JSON-formaat omzetten naar PHP-objecten
 * @author Michael Steenbeek
 */

// Notices worden voornamelijk bij niet-gedefinieerde eigenschappen gegeven. In productie uitzetten.
error_reporting(E_ALL & ~E_NOTICE);

require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/../Uri.class.php');

require_once(__DIR__.'/Context.class.php');
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Activity.class.php');
require_once(__DIR__.'/Belief.class.php');
require_once(__DIR__.'/Condition.class.php');
require_once(__DIR__.'/Goal.class.php');
require_once(__DIR__.'/Outcome.class.php');
require_once(__DIR__.'/Contributes.class.php');
require_once(__DIR__.'/Connects.class.php');
require_once(__DIR__.'/Depends.class.php');

class JSON_EMontParser
{
	private $situatie_uri="";

	public function __construct($situatie_uri)
	{
		//TODO: Check of uri wel een situatie is.
		$this->situatie_uri=$situatie_uri;
	}

	public function geefElementenInSituatie()
	{
		$connectie=new SPARQLConnection();

		$subrollen=self::zoekSubrollen($this->situatie_uri);

		foreach(array_merge(array(Uri::escape_uri($this->situatie_uri)),$subrollen) as $te_doorzoeken_uri)
		{
			$alle_te_doorzoeken_uris[]=Uri::escape_uri($te_doorzoeken_uri);
		}

		$zoekstring=implode(' } UNION { ?ie property:Context ',$alle_te_doorzoeken_uris);

		$query_inhoud_situatie='DESCRIBE ?ie WHERE {{ ?ie property:Context '.$zoekstring.' }.{?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AIntentional_Element>} UNION {?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AActivity>}}';

		$data=$connectie->JSONQueryAsPHPArray($query_inhoud_situatie);

		$items = array();
		$produces=array();
		$consumes=array();
		$ie_context=array();
		$instanceOf=array();
		$partOf=array();

		if($data['@graph'])
		{
			foreach ($data['@graph'] as $item)
			{
				// Bepaal type IE
				switch($item['Eigenschap-3AIntentional_Element_type'])
				{
					case 'Activity':
						$obj=new Activity($item['@id']);
						break;
					case 'Outcome':
						$obj=new Outcome($item['@id']);
						break;
					case 'Goal':
						$obj=new Goal($item['@id']);
						break;
					case 'Belief':
						$obj=new Belief($item['@id']);
						break;
					case 'Condition':
						$obj=new Condition($item['@id']);
						break;
					default:
						$obj = new IntentionalElement($item['@id']);
				}

				if($item['Eigenschap-3AHeading_nl']!="")
				{
					$obj->setHeading($item['Eigenschap-3AHeading_nl']);
				}
				elseif($item['Eigenschap-3AHeading_en']!="")
				{
					$obj->setHeading($item['Eigenschap-3AHeading_en']);
				}
				else
				{
					$obj->setHeading(Uri::SMWuriNaarLeesbareTitel($item['@id']));
				}

				foreach ($item as $key => $value)
				{
					if(!empty($value))
					{
						switch($key)
						{
							case 'Eigenschap-3AIntentional_Element_decomposition_type':
								$obj->setDecompositionType($value);
								break;
							case 'Eigenschap-3AProduces':
								$produces[$item['@id']]=$value;
								break;
							case 'Eigenschap-3AConsumes':
								$consumes[$item['@id']]=$value;
								break;
							case 'Eigenschap-3AContext':
								if (is_array($value))
								{
									$ie_context[$item['@id']]=$value;
								}
								else
								{
									$ie_context[$item['@id']]=array($value);
								}
								break;
							case 'Eigenschap-3APart_of':
								$partOf[$item['@id']]=$value;
								break;
							case 'Eigenschap-3AInstance_of':
								$instanceOf[$item['@id']]=$value;
								break;
							default:
						}
					}
				}
				$items[$item['@id']] = $obj;
			}
		}
		/**
		 * Maak voor alle mee te nemen Contexten een object aan
		 */
		$contexten=array();
		foreach($alle_te_doorzoeken_uris as $context_uri)
		{
			$context_uri=Uri::deescape_uri($context_uri);
			$nieuwecontext=new Context($context_uri);
			$nieuwecontext->setDescription(self::geefContextbeschrijving($context_uri));
			$contexten[$context_uri]=$nieuwecontext;
		}

		/**
		 * Leg de verbanden tussen de Contexten
		 */
		foreach($contexten as $context_uri => $context_object)
		{
			$supercontexten=$connectie->JSONQueryAsPHPArray('DESCRIBE ?supercontext WHERE { '.Uri::escape_uri($context_uri).' property:Supercontext ?supercontext}');

			if(array_key_exists('@graph',$supercontexten)) // Meerdere resultaten
			{
				foreach($supercontexten['@graph'] as $supercontext)
				{
					if(array_key_exists($supercontext['@id'],$contexten)) // Overslaan als supercontext niet meegenomen moest worden
					{
						try
						{
							@$context_object->addSupercontext($contexten[$supercontext['@id']]);
							$contexten[$context_uri]=$context_object;
						}
						catch(Exception $e)
						{
							// Supercontext blijkbaar niet in scope
						}
					}
				}
			}
			elseif(!empty($supercontexten)) // Eén resultaat
			{
				if(array_key_exists($supercontexten['@id'],$contexten)) // Overslaan als supercontext niet meegenomen moest worden
				{
					try
					{
						@$context_object->addSupercontext($contexten[$supercontexten['@id']]);
						$contexten[$context_uri]=$context_object;
					}
					catch(Exception $e)
					{
						// Supercontext blijkbaar niet in scope
					}
				}
			}
		}

		foreach ($items as $uri => $item)
		{
			try
			{
				if(array_key_exists($uri,$produces))
				{
					@$item->addProduces($items[$produces[$uri]]);
					$items[$uri]=$item;
				}
			}
			catch(Exception $e) {}

			try
			{
				if(array_key_exists($uri,$consumes))
				{
					@$item->addConsumes($items[$consumes[$uri]]);
					$items[$uri]=$item;
				}
			}
			catch(Exception $e) {}

			try
			{
				if(array_key_exists($uri,$partOf))
				{
					@$item->addPartOf($items[$partOf[$uri]]);
					$items[$uri]=$item;
				}
			}
			catch(Exception $e) {}

			try
			{
				if(array_key_exists($uri,$instanceOf))
				{
					@$item->addInstanceOf($items[$instanceOf[$uri]]);
					$items[$uri]=$item;
				}
			}
			catch(Exception $e) {}

			if(array_key_exists($uri,$ie_context))
			{
				foreach($ie_context[$uri] as $context_uri)
				{
					try
					{
						@$item->addContext($contexten[$context_uri]);
					}
					catch(Exception $e) {}
				}
				@$items[$uri]=$item;
			}

			$query='DESCRIBE ?s WHERE { ?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AElement_back_link> '.Uri::escape_uri($uri).' }';
			$resultaat=$connectie->JSONQueryAsPHPArray($query);

			if(array_key_exists('@graph',$resultaat))
			{
				foreach($resultaat as $deelresultaat)
				{
					if(array_key_exists(0,$deelresultaat))
					{
						foreach($deelresultaat as $koppeling)
						{
							$items[$uri]=self::verwerkKoppeling($koppeling,$items[$uri],$items);
						}
					}
				}
			}
			else
			{
				$items[$uri]=self::verwerkKoppeling($resultaat,$items[$uri],$items);
			}
		}
		return array_merge($items,$contexten);
	}



	/**
	 * Staat hier omdat het op URL gaat.
	 */
	static function isSituatie($context_uri)
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
	 * @input De context-URI, zonder vishaken (< en >)
	 */
	static function zoekSubrollen($context_uri)
	{
		$subrollen=array();
		$context_uri=Uri::escape_uri($context_uri);

		$query='DESCRIBE ?context WHERE { ?context <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASupercontext> '.$context_uri.' }';
		$connectie=new SPARQLConnection();
		$contexten=$connectie->JSONQueryAsPHPArray($query);

		if(isset($contexten['@graph']))
		{
			foreach($contexten['@graph'] as $item)
			{
				// Subsituaties moeten niet worden meegenomen.
				if(!self::isSituatie($item['@id']))
				{
					$subrollen[]=$item['@id'];
					$subrollen=array_merge($subrollen,self::zoekSubrollen($item['@id']));
				}
			}
		}
		return $subrollen;
	}

	/**
	 * Geeft lijst van L1-modellen (situaties)
	 * TODO: Op een geschiktere plek onderbrengen.
	 */
	static function geefL1modellen()
	{
		$query='DESCRIBE ?context WHERE {?context <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AContext>}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
				if(self::isPractice($item['@id']))
				{
					$context=new Context($item['@id']);
					$context->setDescription(self::geefContextbeschrijving($item['@id']));
					$contexten[$item['@id']]=$context;
				}
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Geeft lijst van L2-cases (situaties)
	 * TODO: Op een geschiktere plek onderbrengen.
	 */
	static function geefL2cases()
	{
		$query='DESCRIBE ?context WHERE {?context <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AContext>}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
				if(self::isExperience($item['@id']))
				{
					$context=new Context($item['@id']);
					$context->setDescription(self::geefContextbeschrijving($item['@id']));
					$contexten[$item['@id']]=$context;
				}
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	static function geefContextbeschrijving($context_uri)
	{
		$connectie=new SPARQLConnection();
		$description_query='SELECT ?description WHERE { '.Uri::escape_uri($context_uri).' <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ADescription> ?description }';
		$description_result=$connectie->JSONQueryAsPHPArray($description_query);
		$description=$description_result['results']['bindings'][0]['description']['value'];

		if ($description!="")
		{
			return $description;
		}
		else
		{
			return Uri::SMWuriNaarLeesbareTitel($context_uri);
		}
	}

	static function verwerkKoppeling($koppeling,&$object,&$objectenpool)
	{
		try{
			if(is_array($koppeling) && $objectenpool[$koppeling['Eigenschap-3AElement_link']])
			{
				switch(@$koppeling['Eigenschap-3AElement_link_type'])
				{
					case 'Depends':
						$koppelobject=new Depends();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setLink($objectenpool[$koppeling['Eigenschap-3AElement_link']]);
						$object->addDepends($koppelobject);
						break;
					case 'Connects':
						$koppelobject=new Connects();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setLinkCondition($koppeling['Eigenschap-3AElement_condition']);
						@$koppelobject->setConnectionType($koppeling['Eigenschap-3AElement_connection_type']);
						@$koppelobject->setLink($objectenpool[$koppeling['Eigenschap-3AElement_link']]);
						$object->addConnects($koppelobject);
						break;
					case 'Contributes':
						$koppelobject=new Contributes();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setContributionValue($koppeling['Eigenschap-3AElement_contribution_value']);
						@$koppelobject->setLink($objectenpool[$koppeling['Eigenschap-3AElement_link']]);
						$object->addContributes($koppelobject);
						break;
					default:
						break;
				}
			}
		}
		catch(Exception $e) {}
		return $object;
	}

	static function isPractice($context_uri)
	{
		$query='DESCRIBE ?practice WHERE {
			?practice property:Context '.Uri::escape_uri($context_uri).'.
			?practice property:Practice_type "Practice"}';
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

	static function isExperience($context_uri)
	{
		$query='DESCRIBE ?experience WHERE {
			?experience property:Context '.Uri::escape_uri($context_uri).'.
			?experience property:Practice_type "Experience"}';
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
}
?>