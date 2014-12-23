<?php

//Element interface
abstract class Concept {
	function accept(Visitor $visitor);
}
?>