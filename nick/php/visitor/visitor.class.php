<?php
/**
 *  @author Nick Steijaert
 */
abstract class Visitor {
    abstract function visitContext(ContextVisitee $contextVisitee);
}
?>