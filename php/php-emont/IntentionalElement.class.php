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
	
	//An SplObjectStorage of Contributes objects
	private $contributes;
	//An SplObjectStorage of Depends objects
	private $depends;
	
	public function __construct()
	{
		$this->contributes=new SplObjectStorage();
		$this->depends=new SplObjectStorage();
	}
	
	public function setHeading($heading)
	{
		$this->heading=$heading;
	}
	
	public function getHeading()
	{
		return $this->heading;
	}
	
	public function setDecompositionType($decompositionType)
	{
		switch($decompositionType)
		{
			case "IOR":
			case "XOR":
			case "AND":
				$this->decompositionType=$decompositionType;
				break;
			default:
				throw new Exception('Incorrect decomposition type');
		}
	}
	
	public function getDecompositionType()
	{
		return $this->decompositionType;
	}
	
	public function setContext($context)
	{
		// This is the PHP opcode, not the EMont property!
		if ($context instanceOf Context)
		{
			$this->context=$context;
		}
		else
		{
			throw new Exception('Not a Context');
		}
	}
	
	public function getContext()
	{
		return $this->context;
	}
	
	public function setInstanceOf($instanceOf)
	{
		// This separates the men from the boys. The first is the parameter for setting the EMont property,
		// the second is the PHP opcode to check whether the parameters is an Intentional Element object.
		if ($instanceOf instanceOf IntentionalElement)
		{
			$this->instanceOf=$instanceOf;
		}
		else 
		{
			throw new Exception('Not an Intentional Element');
		}
	}
	
	public function getInstanceOf()
	{
		return $this->instanceOf;
	}
	
	public function setPartOf($partOf)
	{
		if ($partOf instanceOf IntentionalElement)
		{
			$this->partOf=$partOf;
		}
		else 
		{
			throw new Exception('Not an Intentional Element');
		}
	}
	
	public function getPartOf()
	{
		return $this->partOf();
	}
	
	public function addContributes(&$contributes)
	{
		if ($contributes instanceOf Contributes)
		{
			$this->contributes->attach($contributes);
		}
		else 
		{
			throw new Exception('Not a Contributes');
		}
	}
	
	public function removeContributes(&$contributes)
	{
		if ($contributes instanceOf Contributes)
		{
			$this->contributes->detach($contributes);
		}
		else 
		{
			throw new Exception('Not a Contributes');
		}
	}
	
	public function getContributes()
	{
		return $this->contributes;
	}
	
	public function addDepends(&$depends)
	{
		if ($depends instanceOf Depends)
		{
			$this->depends->attach($depends);
		}
		else 
		{
			throw new Exception('Not a Depends');
		}
	}
	
	public function removeDepends(&$depends)
	{
		if ($depends instanceOf Depends)
		{
			$this->depends->detach($depends);
		}
		else 
		{
			throw new Exception('Not a Depends');
		}
		
	}
	
	public function getDepends()
	{
		return $this->depends;
	}
}
