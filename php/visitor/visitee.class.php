<?php
abstract class Visitee {
    abstract function accept(Visitor $visitorIn);
}
?>