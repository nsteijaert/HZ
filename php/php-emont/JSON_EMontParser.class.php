<?php
/**
 * EMont-modellen in JSON-formaat omzetten naar PHP-objecten
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/Context.class.php');

class JSON_EMontParser {
	private $data;

	function __construct($input) {
		$this -> data = $input;
		$this->parseRDFData();
	}

	function parseRDFData()
	{
		$items = array();

		foreach ($this->data['@graph'] as $item) {
			$obj = new Context();
			foreach ($item as $key => $value) {
				if ($key == 'label') {
					//Nu nog niets
				}
			}
			$items[$item['@id']] = $obj;
		}
		echo '<pre>';
		var_dump($items);
		echo '</pre>';
	}

	function parseDataRDF() {

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

		return $items;
	}

	function setData($data) {
		$this -> data = $data;
	}

	function getData() {
		return $this -> data;
	}

/*	function isRelation($key) {
		$relationKeys = array("broader", "narrower", "related", "partof");

		foreach ($relationKeys as $relation) {
			if (strpos($key, $relation))
				return true;
		}

		return false;


	}*/

}
?>