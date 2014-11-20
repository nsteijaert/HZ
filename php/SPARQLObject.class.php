<?php
/**
 * @author Pieter Moens, Nick Steijaert, Michael Steenbeek
 * SPARQLObject is part of the intermediate datastructure, this class instantiates an object with an array of properties.
 */
class SPARQLObject {

    //Global Variables
    private $type;
    private $properties = array();

    //Constructor for a SPARQLObject
    public function __construct($type_in) {
        $this -> type = $type_in;
    }

    //Sets a property
    public function setProperty($property_name, $property_value) {
        $this -> $properties[$property_value] = $property_name;
    }

    //Sets the type
    public function getType() {
        return $this -> $type;
    }

    //Return a property with a given key
    public function getProperty($value) {
        return $this -> $properties[$value];
    }

    //Return the array with properties
    public function getAllProperties() {
        return $this -> $properties;
    }

}
?>