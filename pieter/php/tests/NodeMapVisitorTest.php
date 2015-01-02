<?php
include_once (__DIR__ . '/../visitor/NodeMapVisitor.class.php');
include_once (__DIR__ . '/../visitor/SKOSConcept.class.php');

class NodeMapVisitorTest extends PHPUnit_Framework_TestCase {
	
	public function testVisit() {
		// Arrange
		$a = new NodeMapVisitor();
		$b = new SKOSConcept("TestConcept");
		$b->addRelation("Eigenschap-3ASkosem-3Abroader", new SKOSConcept("TestConceptA"));
		$b->addRelation("Eigenschap-3ASkosem-3Abroader", new SKOSConcept("TestConceptB"));
		$b->addRelation("Eigenschap-3ASkosem-3Anarrower", new SKOSConcept("TestConceptC"));
		$b->addRelation("Eigenschap-3ASkosem-3Abroader", new SKOSConcept("TestConceptD"));
		$b->addRelation("Eigenschap-3ASkosem-3Anarrower", new SKOSConcept("TestConceptE"));

		// Assert
		// There are 5 relations added.
		$this -> assertEquals(5, count($a -> visit($b)));
	}
}
?>