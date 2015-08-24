<?php
/**
 * Context-object uit EMont. Kan een situatie of rol zijn
 * @author Michael Steenbeek
 */
require_once(__DIR__.'/PHPEMontVisitor.interface.php');
require_once(__DIR__.'/PHPEMontVisitee.interface.php');

class Context
{
	private $uri;
	private $description='';
	private $type='';

	// An SplObjectStorage of Context objects
	private $supercontext;

	public function __construct($uri)
	{
		$this->supercontext=new SplObjectStorage();
		$this->uri=$uri;
	}

	public function setDescription($description)
	{
		$this->description=$description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setType($type)
	{
		if($type=='situatie' || $type=='rol')
		{
			$this->type=$type;
		}
	}

	public function getType()
	{
		return $this->type;
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

	public function getUri()
	{
		return $this->uri;
	}

	public function accepts(PHPEMontVisitor $v)
	{
		return $v->visit($this);
	}
}
