<?php
/**
 * EMont-modellen in JSON-formaat omzetten naar PHP-objecten
 * @author Michael Steenbeek
 */

require_once(__DIR__.'/../SPARQLConnection.class.php');
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
		
		$alle_te_doorzoeken_uris=array_merge(array($this->situatie_uri),$subrollen);
		//var_dump($alle_te_doorzoeken_uris);
		$zoekstring=implode('> } UNION { ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> <',$alle_te_doorzoeken_uris);
		//$zoekstring=$this->situatie_uri;
		
		$query_inhoud_situatie='DESCRIBE ?ie WHERE {{ ?ie <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AContext> <'.$zoekstring.'> }.{?ie <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AIntentional_Element>} UNION {?ie <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AActivity>}}';
		echo '<pre>'.$query_inhoud_situatie.'</pre>';

		$data=$connectie->JSONQueryAsPHPArray($query_inhoud_situatie);

		$items = array();

		foreach ($data['@graph'] as $item) 
		{
			// Bepaal type IE
			switch($item['Eigenschap-3AIntentional_Element_type'])
			{
				case 'Activity':
					$obj=new Activity();
					break;
				case 'Outcome':
					$obj=new Outcome();
					break;
				case 'Goal':
					$obj=new Goal();
					break;
				case 'Belief':
					$obj=new Belief();
					break;
				case 'Condition':
					$obj=new Condition();
					break;
				default:
					$obj = new IntentionalElement();		
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
				$obj->setHeading(self::decodeerSMWNaam($item['@id']));
			}


			foreach ($item as $key => $value) 
			{
				switch($key)
				{
					case 'Eigenschap-3AIntentional_Element_decomposition_type':
						$obj->setDecompositionType($value);
						break;
					default:
				}
			}
			$items[$item['@id']] = $obj;
		}

		$contexten=array();

		foreach($alle_te_doorzoeken_uris as $context_uri)
		{
			$contexten[$context_uri]=new Context();
		}
		foreach($contexten as $context_uri => $context_object)
		{
		}
		echo '<pre>';
		var_dump($contexten);
		echo'</pre>';

		foreach ($data['@graph'] as $item) 
		{
			$object=$items[$item['@id']];
			
			try
			{
				if(@$item['Eigenschap-3AProduces']!='')
				{
					@$verwijsobject=$items[$item['Eigenschap-3AProduces']];
					@$object->addProduces($verwijsobject);
					$items[$item['@id']]=$object;	
				}
			}
			catch(Exception $e)
			{
				// Produces valt vermoedelijk buiten de Context
 			}

			try
			{
				if(@$item['Eigenschap-3AConsumes']!='')
				{
					@$verwijsobject=$items[$item['Eigenschap-3AConsumes']];
					@$object->addConsumes($verwijsobject);
					$items[$item['@id']]=$object;
				}
			}
			catch(Exception $e)
			{
				// Produces valt vermoedelijk buiten de Context
			}
		}

		foreach ($items as $uri => $item)
		{

			$query='DESCRIBE ?s WHERE { ?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AElement_back_link> <'.$uri.'> }';
			$koppeling=$connectie->JSONQueryAsPHPArray($query);

			try
			{
				$object=$items[$uri];

				switch(@$koppeling['Eigenschap-3AElement_link_type'])
				{
					case 'Depends':
						$koppelobject=new Depends();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setLink($items[$koppeling['Eigenschap-3AElement_link']]);
						$object->addDepends($koppelobject);
						break;
					case 'Connects':
						$koppelobject=new Connects();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setLinkCondition($koppeling['Eigenschap-3AElement_condition']);
						@$koppelobject->setConnectionType($koppeling['Element_connection_type']);
						@$koppelobject->setLink($items[$koppeling['Eigenschap-3AElement_link']]);
						$object->addConnects($koppelobject);
						break;
					case 'Contributes':
						$koppelobject=new Contributes();
						@$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setContributionValue($koppeling['Eigenschap-3AElement_contribution_value']);
						@$koppelobject->setLink($items[$koppeling['Eigenschap-3AElement_link']]);
						$object->addContributes($koppelobject);
						break;
					default:
						break;
				}

				$items[$uri]=$object;
			}
			catch(Exception $e)
			{
				// Link valt vermoedelijk buiten de Context
			}
		}
		return $items;
	}

	/**
	 * Zet een in SMW opgeslagen naam van een Mediawiki-artikel om in een leesbare variant.
	 * Bijvoorbeeld: Eigenschap-3AElement_link_type naar Eigenschap: Element link type
	 */
	static function decodeerSMWNaam($naam)
	{
		// ID's in RDF-store staan in procentnotatie, maar met een - ipv een %. Dit stukje code zorgt voor
		// een correcte procentnotatie en zet die vervolgens om naar de bedoelde tekens.
		return urldecode(strtr($naam, "-_","% "));
	}

	/**
	 * Staat hier omdat het op URL gaat.
	 */
	static function isSituatie($context_uri)
	{
		$query="DESCRIBE ?s ?o WHERE {
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASelection_link> <".$context_uri.">
			.
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3APractice_back_link> ?o
			}";
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (empty($result)) // Leeg resultaat
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

		$query='DESCRIBE ?context WHERE { ?context <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASupercontext> <'.$context_uri.'> }';
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
}
?>