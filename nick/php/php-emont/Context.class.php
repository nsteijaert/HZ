<?php

class Context
{
	private $description;
	
	// An SplObjectStorage of Context objects
	private $supercontext;
	
	public function __construct()
	{
		$this->supercontext=new SplObjectStorage();
	}
	
	public function setDescription($description)
	{
		$this->description=$description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function addSupercontext(&$supercontext)
	{
		if ($supercontext instanceOf Context)
		{
			$this->supercontext->attach($supercontext);
		}
		else 
		{
			throw new Exception('Not a Context');
		}
	}
	
	public function removeSupercontext(&$supercontext)
	{
		if ($supercontext instanceOf Context)
		{
			$this->supercontext->detach($supercontext);
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
