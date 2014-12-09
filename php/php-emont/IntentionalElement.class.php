<?php
require_once(__DIR__.'Context.class.php');

class IntentionalElement
{
	private $heading;
	// An enum
	private $decompositionType;
	
	// A Context object
	private $context;
	
	// Both Intentional Elements
	private $instanceOf;
	private $partOf;
	
	//An array of Contributes objects
	private $contributes=array();
	//An array of Depends objects
	private $depends=array();
	
	public function __construct()
	{
		
	}
}
