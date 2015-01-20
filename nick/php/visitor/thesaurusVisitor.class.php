<?php
/**
 *  @author Nick Steijaert
 */
class contextVisitor extends Visitor {
    private $value1 = null;
    function getValue1() {
        return $this -> value1;
    }

    function setValue1($value1_in) {
        $this -> value1 = $value1_in;
    }

    function visitContext(ContextVisitee $contextVisiteeIn) {
        $this -> setValue1($contextVisiteeIn -> getValue1());
    }

}
?>