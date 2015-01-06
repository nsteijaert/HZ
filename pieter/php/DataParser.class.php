<?php
require_once (__DIR__ . '/visitor/SKOSConcept.class.php');

class DataParser {
	private $data;

	function __construct($input) {
		$this -> data = $input;
	}

	function parseDataRDF() {
		$items = array();

		if (!isset($this -> data['@graph']))
			return $items;
		
		foreach ($this->data['@graph'] as $item) {
			$obj = new SKOSConcept($item['@id']);
			foreach ($item as $key => $value) {
				if (!$this -> isRelation($key)) {
					$obj -> addProperty($key, $value);
				}
			}
			$items[$item['@id']] = $obj;
		}

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

	function parseDataJSON() {

	}

	function parseDataXML() {

	}

	function setData($data) {
		$this -> data = $data;
	}

	function getData() {
		return $this -> data;
	}

	function isRelation($key) {
		$relationKeys = array("broader", "narrower", "related", "partof");

		foreach ($relationKeys as $relation) {
			if (strpos($key, $relation))
				return true;
		}

		return false;

		// return array_filter($relationKeys, function($haystack) use ($key) {
		// return (strpos($haystack, $key) !== false);
		// });
	}

}
?>