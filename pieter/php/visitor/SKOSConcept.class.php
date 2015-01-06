<?php
require (__DIR__ . '/Concept.class.php');

class SKOSConcept extends Concept {

	//Global Variables
	private $relations = array();
	private $properties = array();
	private $name;

	function accept(Visitor $visitor) {
		return $visitor -> visit($this);
	}

	/**
	 * @param (String) $name = Name of the object.
	 */
	public function __construct($name) {
		$this -> setName($name);
	}

	/**
	 * @param (String) $name = Name of the object.
	 */
	public function setName($name) {
		$this -> name = $name;
	}

	/**
	 * Return the name of the object.
	 */
	public function getName() {
		return $this -> name;
	}

	/**
	 * @param (String) $relationName = Name of the relation
	 * @param (String) $relationValue = Value/Object of the relation
	 */
	public function addRelation($relationName, $relationValue = "") {
		if (array_key_exists($relationName, $this -> relations))
			array_push($this -> relations[$relationName], $relationValue);
		else
			$this -> relations[$relationName] = array($relationValue);
	}
	
	/**
	 * Returns an array of all relations
	 */
	public function getRelations() {
		return $this->relations;
	}

	/**
	 * @param (String) $relationName = Name of the relation
	 */
	public function removeRelation($relationName) {
		if (($key = array_search($relationName, $this -> relations)) !== false) {
			unset($this -> relations[$key]);
		}
	}

	/**
	 * @param (String) $propertyName = Name of the property to add.
	 */
	public function addProperty($propertyName, $propertyValue = "") {
		$this -> properties[$propertyName] = $propertyValue;
	}

	/**
	 * @param (String) $arrayOfProperties = Array of properties to add.
	 */
	public function addProperties($arrayOfProperties = array()) {
		foreach ($arrayOfProperties as $property) {
			$this -> addProperty($property);
		}
	}
	
	public function getProperty($name) {
		return $this -> properties[$name];
	}

	/**
	 * @param (String) $propertyName = Name of the removable property.
	 */
	public function removeProperty($propertyName = "") {
		if (($key = array_search($propertyName, $this -> properties)) !== false) {
			unset($this -> properties[$key]);
		}
	}

}
?>