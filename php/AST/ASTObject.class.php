<?php
/**
 * ASTObject is the base for the objects encountered in an asynchronous tree
 */

class ASTObject {

	private $relations = array();
	private $properties = array();
	private $name;

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
	 * @param (String) $relationName = instance of ASTObject class
	 */
	public function addRelation($relationName, $relationValue = "") {
		$this -> relations[$relationName] = $relationValue;
	}

	/**
	 * @param (String) $relationName = instance of ASTObject class
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