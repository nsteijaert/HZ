<?php
require (__DIR__ . '/Visitor.class.php');

class NodeMapVisitor extends Visitor {

	private $data = array();

	function visit(SKOSConcept $concept) {
		$relations = $concept -> getRelations();
		if (count($relations) != 0) {
			$arr = array();
			foreach ($relations as $key => $relation) {
				foreach ($relation as $object) {
					$item = array();
					$item["type"] = $key;
					switch (true) {
						case strpos($key, "broader") :
							$item["urlsource"] = $concept -> getProperty("page");
							$item["urltarget"] = $object -> getProperty("page");
							$item["source"] = ucfirst(str_replace("uri:TZW-3A", "", $concept -> getName()));
							$item["target"] = ucfirst(str_replace("uri:TZW-3A", "", $object -> getName()));
							break;
						case strpos($key, "narrower") :
							$item["urlsource"] = $object -> getProperty("page");
							$item["urltarget"] = $concept -> getProperty("page");
							$item["source"] = ucfirst(str_replace("uri:TZW-3A", "", $object -> getName()));
							$item["target"] = ucfirst(str_replace("uri:TZW-3A", "", $concept -> getName()));
							break;

						default :
							return;
							break;
					}
					array_push($arr, $item);
				}
			}
			$this -> data = array_merge($this -> data, $arr);
		}
	}

	function getUsableJSON() {
		return json_encode($this -> data);
	}

}
?>