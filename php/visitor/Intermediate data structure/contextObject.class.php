<?php
/**
 * contextObject is part of intermediate datastructure used by the visitor pattern 
 * @author Nick Steijaert
 */
class contextObject {
    

private $contextName;
private $contextURL;

public function setContextName($contextName_in){
    $this->contextName = $contextName_in;
}

public function setContextURL($contextURL_in){
    $this->contextURL = $contextURL_in;
}

public function getContextName(){
    return $this->contextName;
}

public function getContextName(){
    return $this->contextURL;
}

}
?>