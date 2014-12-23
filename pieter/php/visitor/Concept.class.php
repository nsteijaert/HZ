<?php

//Element interface
abstract class Concept implements JsonSerializable {
	function accept(Visitor $visitor);
	
	function toJSON(){
		return json_encode($this);
	}
}
?>