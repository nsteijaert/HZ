<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLObject is part of the intermediate datastructure, this class instantiates an object with an array of properties.
 */
class SPARQLObjectNonArray {

	//Global Variables
	private $id;
	private $type;
	private $name;
	private $a;
	private $b;
	private $c;

	public function __construct($id) {
		setId($id);
	}
	
	public function __construct($id, $type, $name, $a, $b, $c) {
		setId($id);
		setType($type);
		setName($name);
		setA($a);
		setB($b);
		setC($c);
	}

	//
	// From here getters and setters..
	//
	//
	public function setId($id) {
		$this -> id = $id;
	}

	public function setType($type) {
		$this -> type = $type;
	}

	public function setName($name) {
		$this -> name = $name;
	}

	public function setA($a) {
		$this -> a = $a;
	}

	public function setB($b) {
		$this -> b = $b;
	}

	public function setC($c) {
		$this -> c = $c;
	}

	public function getId() {
		return $this -> id;
	}

	public function getType() {
		return $this -> type;
	}

	public function getName() {
		return $this -> name;
	}

	public function getA() {
		return $this -> a;
	}

	public function getB() {
		return $this -> b;
	}

	public function getC() {
		return $this -> c;
	}

}
?>