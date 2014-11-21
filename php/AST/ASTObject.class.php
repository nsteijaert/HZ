<?

class ASTObject {

	private $relations = array();
	private $properties = array();
	private $name;

	/**
	 * @param (String) $name = Name of the object.
	 */
	public function ASTObject($name) {
		setName($name);
	}

	/**
	 * @param (String) $name = Name of the object.
	 */
	public function setName($name) {
		$this -> name = $name;
	}

	/**
	 * @param (String) $relationName = Name of the relation to add.
	 */
	public function addRelation($relationName = "") {
		array_push($this -> relations, $relationName);
	}

	/**
	 * @param (String) $relationName = Name of the removable relation.
	 */
	public function removeRelation($relationName = "") {
		if (($key = array_search($relationName, $this -> relations)) !== false) {
			unset($this -> relations[$key]);
		}
	}

	/**
	 * @param (String) $propertyName = Name of the property to add.
	 */
	public function addProperty($propertyName = "") {
		array_push($this -> properties, $propertyName);
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