<?php

//Element interface
abstract class Concept {
	abstract function accept(Visitor $visitor);
}
?>