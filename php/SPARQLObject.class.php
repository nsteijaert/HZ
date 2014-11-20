<?php
/**
 * @author Pieter Moens, Nick Steijaert
 * SPARQLObject is part of the intermediate datastructure, this class instantiates an object with an array of properties.
 */
class SPARQLObject {
  
    //Global Variables
    private $type;
    private $properties = array();
    
    public function __construct($type_in) {
        $this->type = $type_in;
    }
    
    public function setProperties($property) {
        array_push($properties, $property);
    }
    
    public function getType() {
        return $this->$type;
    }
    
    public function getProperties() {
        return $this->$properties;
    }
}  
?>