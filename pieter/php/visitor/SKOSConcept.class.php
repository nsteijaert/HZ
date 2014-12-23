<?php
require (__DIR__ . '/Concept.class.php');

class SKOSConcept extends Concept {

	//Global Variables
	private $type;
	private $properties = array();

	function accepts(Visitor $visitor) {
		$visitor -> visit(this);
	}

	//Constructor for a SPARQLObject
	public function __construct($type_in) {
		$this -> type = $type_in;
	}

	//Sets a property
	public function setProperty($property_name, $property_value) {
		$this -> $properties[$property_value] = $property_name;
	}

	//Sets all properties
	public function setProperties($propertyArray) {
		$properties = array_merge($this -> $properties, $propertyarray);
	}

	//Sets the type
	public function getType() {
		return $this -> $type;
	}

	//Return a property with a given key
	public function getProperty($value) {
		return $this -> $properties[$value];
	}

	//Return the array with properties
	public function getAllProperties() {
		return $this -> $properties;
	}

}
?>