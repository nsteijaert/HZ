<?php
/**
 *  @author Nick Steijaert
 */
abstract class Visitee {
    abstract function accept(Visitor $visitorIn);
}
?>