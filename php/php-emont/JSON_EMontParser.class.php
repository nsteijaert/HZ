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
			
			foreach ($item as $key => $value) {
				echo $key.'<br />';
				if ($key == 'Eigenschap-3AIntentional_Element_decomposition_type') {
					$obj->setDecompositionType($value);
				}
			}
			$items[$item['@id']] = $obj;
		}
		return $items;
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