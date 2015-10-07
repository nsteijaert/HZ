<?php
require_once(__DIR__.'/Context.class.php');
require_once(__DIR__.'/PHPEMontVisitor.interface.php');
require_once(__DIR__.'/PHPEMontVisitee.interface.php');

class IntentionalElement implements PHPEMontVisitee
{
	private $uri;

	private $heading;
	// An enum
	private $decompositionType='';

	// A Context object
	private $context;

	// A URI of an Intentional Element
	private $instanceOf;

	// An SplObjectStorage of Intentional Elements
	private $partOf;

	//An SplObjectStorage of Contributes objects
	private $contributes;
	//An SplObjectStorage of Depends objects
	private $depends;

	public function __construct($uri)
	{
		$this->contributes=new SplObjectStorage();
		$this->depends=new SplObjectStorage();
		$this->partOf=new SplObjectStorage();
		$this->context=new SplObjectStorage();
		$this->uri=$uri;
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
			case 'IOR':
			case 'XOR':
			case 'AND':
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

	public function addContext(&$context)
	{
		// This is the PHP opcode, not the EMont property!
		if ($context instanceOf Context)
		{
			$this->context->attach($context);
		}
		else
		{
			throw new Exception('Not a Context');
		}
	}
	public function removeContext(&$context)
	{
		// This is the PHP opcode, not the EMont property!
		if ($context instanceOf Context)
		{
			$this->context->detach($context);
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
		if($instanceOf)
			$this->instanceOf=$instanceOf;

	}

	public function getInstanceOf()
	{
		return $this->instanceOf;
	}

	public function addPartOf(&$partOf)
	{
		if ($partOf instanceOf IntentionalElement)
		{
			$this->partOf->attach($partOf);
		}
		else
		{
			throw new Exception('Not an Intentional Element');
		}
	}

	public function removePartOf(&$partOf)
	{
		if ($partOf instanceOf IntentionalElement)
		{
			$this->partOf->detach($partOf);
		}
		else
		{
			throw new Exception('Not an Intentional Element');
		}
	}

	public function getPartOf()
	{
		return $this->partOf;
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

	public function getUri()
	{
		return $this->uri;
	}

	function accepts(PHPEMontVisitor $v)
	{
		return $v->visit($this);
	}
}