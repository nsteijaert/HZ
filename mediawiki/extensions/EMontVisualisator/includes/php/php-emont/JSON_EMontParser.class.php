<?php
/**
 * EMont-modellen in JSON-formaat omzetten naar PHP-objecten
 * @author Michael Steenbeek
 */

// Notices worden voornamelijk bij niet-gedefinieerde eigenschappen gegeven. In productie uitzetten.
error_reporting(E_ALL & ~E_NOTICE);

require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/../Uri.class.php');
require_once(__DIR__.'/Model.class.php');

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
		$items = array();
		$verbanden=array();

		$data=Model::geefElementenUitContextEnSubcontexten($this->situatie_uri);

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
						$obj=new IntentionalElement($item['@id']);
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
					if(is_array($value))
					{
						foreach($value as $singlevalue)
						{
							$verbanden=self::verwerkIEEigenschap($item, $key, $singlevalue, $verbanden);
						}
					}
					else
					{
						if($value=='Eigenschap-3AIntentional_Element_decomposition_type')
						{
							$obj->setDecompositionType($value);
						}
						else
						{
							$verbanden=self::verwerkIEEigenschap($item, $key, $value, $verbanden);
						}
					}
				}

				$query='DESCRIBE ?s WHERE { ?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AElement_back_link> '.Uri::escape_uri($item['@id']).' }';
				$resultaat=$connectie->JSONQueryAsMultidimensionalPHPArray($query);

				foreach($resultaat as $deelresultaat)
				{
					if(@array_key_exists(0,$deelresultaat))
					{
						foreach($deelresultaat as $koppeling)
						{
							$verbanden['ccd'][]=array('source'=>$item['@id'],'ccdkoppeling'=>$koppeling);
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
		foreach(Model::geefUrisVanContextEnSubcontexten($this->situatie_uri) as $context_uri)
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
			$supercontexten=$connectie->JSONQueryAsMultidimensionalPHPArray('DESCRIBE ?supercontext WHERE { '.Uri::escape_uri($context_uri).' property:Supercontext ?supercontext}');

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
		}

		foreach($verbanden as $type=>$verbanden2)
		{
			foreach($verbanden2 as $verband)
			{
				try{
					switch($type)
					{
						case 'produces':
							@$items[$verband['source']]->addProduces($items[$verband['target']]);
							break;
						case 'consumes':
							@$items[$verband['source']]->addConsumes($items[$verband['target']]);
							break;
						case 'partOf':
							@$items[$verband['source']]->addPartOf($items[$verband['target']]);
							break;
						case 'instanceOf':
							@$items[$verband['source']]->addInstanceOf($items[$verband['target']]);
							break;
						case 'context':
							@$items[$verband['source']]->addContext($contexten[$verband['target']]);
							break;
						case 'ccd':
							$items[$verband['source']]=self::verwerkKoppeling($verband['ccdkoppeling'],$items[$verband['source']],$items);
							break;
						default:
					}
				}
				catch(Exception $e) {}
			}
		}
		return array_merge($items,$contexten);
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

	static function verwerkIEEigenschap($item,$key,$value,$verbanden)
	{
		switch($key)
		{
			case 'Eigenschap-3AProduces':
				$verbanden['produces'][]=array('source'=>$item['@id'],'target'=>$value);
				break;
			case 'Eigenschap-3AConsumes':
				$verbanden['consumes'][]=array('source'=>$item['@id'],'target'=>$value);
				break;
			case 'Eigenschap-3AContext':
				$verbanden['context'][]=array('source'=>$item['@id'],'target'=>$value);
				break;
			case 'Eigenschap-3APart_of':
				$verbanden['partOf'][]=array('source'=>$item['@id'],'target'=>$value);
				break;
			case 'Eigenschap-3AInstance_of':
				$verbanden['instanceof'][]=array('source'=>$item['@id'],'target'=>$value);
				break;
			default:
		}
		return $verbanden;
	}
}
?>