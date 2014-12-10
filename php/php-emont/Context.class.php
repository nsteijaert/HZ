<?php

class Context
{
	private $description;
	
	// A Context object
	private $supercontext;
	
	public function __construct()
	{
		
	}
	
	public function setDescription($description)
	{
		$this->description=$description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setSupercontext($supercontext)
	{
		if ($supercontext instanceOf Context)
		{
			$this->supercontext=$supercontext;
		}
		else 
		{
			throw new Exception('Not a Context');
		}
	}
	
	public function getSupercontext()
	{
		return $this->supercontext;
	}
}
