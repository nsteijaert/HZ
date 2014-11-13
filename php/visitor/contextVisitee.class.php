<?php
/**
 * This visitor class is used to generate an abstract syntax tree with all elements related to contexts
 */
class ContextVisitee extends Visitor {
	
	private $value1;
	private $value2;
	function __construct($value1_in, $value2_in){
		$this->value1 = $value1_in;
		$this->value2 = $value2_in;
	}
	
	function getValue1() {return $this->value1;}
	function getValue2() {return $this->value2;}
	function accept(Visitor $visitorIn) {
		$visitorIn->visit($this);
	}
}
?>