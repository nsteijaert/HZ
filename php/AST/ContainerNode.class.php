<?php
require_once ("ASTObject.class.php");

/**
 * ContainerNode is part of a abstract syntax tree pattern used for parsing SPARQL data.
 */
class ContainerNode extends ASTObject {

	public function __construct($name) {
		$this -> setName($name);
	}

}
?>