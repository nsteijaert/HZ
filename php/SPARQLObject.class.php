<?php
/**
 * @author Pieter Moens, Nick Steijaert, Michael Steenbeek
 * SPARQLObject is part of the intermediate datastructure, this class instantiates an object with an array of properties.
 */
class SPARQLObject {
  
    //Global Variables
    private $type;
    private $properties = array();
    
    public function __construct($type_in) {
        $this->type = $type_in;
    }
    
    public function setProperty($property_name, $property_value) {
        $properties[$property_value] = $property_name;
    }
    
    public function getType() {
        return $this->$type;
    }
    
    public function getProperty($value) {
        return $properties[$value];
    }
    
    public function getAllProperties() {
        return $this->$properties;
    }
}  
?>