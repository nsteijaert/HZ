<?php
require_once(__DIR__.'/IntentionalElement.class.php');
require_once(__DIR__.'/Outcome.class.php');
require_once(__DIR__.'/Connects.class.php');

class Activity extends IntentionalElement
{
	// Both SplObjectStorages of Outcome objects
	private $produces;
	private $consumes;
	
	// An SplObjectStorage of Connects objects
	private $connects;
	
	public function __construct($uri)
	{
		parent::__construct($uri);
		$this->connects=new SplObjectStorage();
		$this->produces=new SplObjectStorage();
		$this->consumes=new SplObjectStorage();
	}
	
	public function addProduces(&$produces)
	{
		if ($produces instanceOf Outcome)
		{
			$this->produces->attach($produces);
		}
		else 
		{
			throw new Exception('Not an Outcome');
		}
	}
	
	public function removeProduces(&$produces)
	{
		if ($produces instanceOf Outcome)
		{
			$this->produces->detach($produces);
		}
		else 
		{
			throw new Exception('Not an Outcome');
		}
	}
	
	public function getProduces()
	{
		return $this->produces;
	}
	
	public function addConsumes(&$consumes)
	{
		if ($consumes instanceOf Outcome)
		{
			$this->consumes->attach($consumes);
		}
		else 
		{
			throw new Exception('Not an Outcome');
		}
	}
	public function removeConsumes(&$consumes)
	{
		if ($consumes instanceOf Outcome)
		{
			$this->consumes->detach($consumes);
		}
		else 
		{
			throw new Exception('Not an Outcome');
		}
	}
	
	public function getConsumes()
	{
		return $this->consumes;
	}
	
	public function addConnects(&$connects)
	{
		if ($connects instanceOf Connects)
		{
			$this->connects->attach($connects);
		}
		else 
		{
			throw new Exception('Not a Connects');
		}
	}
	
	public function removeConnects(&$connects)
	{
		if ($connects instanceOf Connects)
		{
			$this->connects->detach($connects);
		}
		else 
		{
			throw new Exception('Not a Connects');
		}		
	}
	
	public function getConnects()
	{
		return $this->connects;
	}
}
