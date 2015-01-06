<?php
/**
 * EMont-modellen in JSON-formaat omzetten naar PHP-objecten
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Activity.class.php');
require_once(__DIR__.'/Belief.class.php');
require_once(__DIR__.'/Condition.class.php');
require_once(__DIR__.'/Goal.class.php');
require_once(__DIR__.'/Outcome.class.php');
require_once(__DIR__.'/Contributes.class.php');
require_once(__DIR__.'/Connects.class.php');
require_once(__DIR__.'/Depends.class.php');

class JSON_EMontParser {

	private function __construct($input) {
		
	}

	public static function parse($input)
	{
		
		$data = json_decode($input,true);
		
		$items = array();

		foreach ($data['@graph'] as $item) {
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
				// ID's in RDF-store staan in procentnotatie, maar met een - ipv een %. Dit stukje code zorgt voor
				// een correcte procentnotatie en zet die vervolgens om naar de bedoelde tekens.
				$obj->setHeading(urldecode(strtr($item['@id'], "-_","% ")));
			}


			foreach ($item as $key => $value) {
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

		foreach ($items as $uri => $item)
		{

			$query='DESCRIBE ?s WHERE { ?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3AElement_back_link> <'.$uri.'> }';
			$koppeling=self::geefEen($query);

			try
			{
				$object=$items[$uri];
				switch($koppeling['Eigenschap-3AElement_link_type'])
				{
					case 'Depends':
						$koppelobject=new Depends();
						$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						@$koppelobject->setLink($items[$koppeling['Eigenschap-3AElement_link']]);
						$object->addDepends($koppelobject);
						break;
					case 'Connects':
						$koppelobject=new Connects();
						$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						$koppelobject->setLinkCondition($koppeling['Eigenschap-3AElement_condition']);
						$koppelobject->setConnectionType($koppeling['Element_connection_type']);
						@$koppelobject->setLink($items[$koppeling['Eigenschap-3AElement_link']]);
						$object->addConnects($koppelobject);
						break;
					case 'Contributes':
						$koppelobject=new Contributes();
						$koppelobject->setLinkNote($koppeling['Eigenschap-3AElement_link_note']);
						$koppelobject->setContributionValue($koppeling['Eigenschap-3AElement_contribution_value']);
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

	static function geefEen($query)
	{
		//TODO: Netter maken
		$query=urlencode($query);
  
		$result=file_get_contents('http://127.0.0.1:3030/ds/query?output=json&query='.$query);
		$data=json_decode($result,true);
		
		return $data;
		
	}

/*	function parseDataRDF() {

		foreach ($this->data['@graph'] as $item) {
			$obj = $items[$item['@id']];
			foreach ($item as $key => $value) {
				if ($this -> isRelation($key)) {
					if (is_array($value)) {
						foreach ($value as $relation) {
							if (array_key_exists($relation, $items))
								$obj -> addRelation($key, $items[$relation]);
						}
					} else {
						if (array_key_exists($value, $items))
							$obj -> addRelation($key, $items[$value]);
					}
				}
			}
		}

	}
*/
}
?>