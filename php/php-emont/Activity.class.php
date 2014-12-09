<?
require_once(__DIR__.'IntentionalElement.class.php');
require_once(__DIR__.'Outcome.class.php');
require_once(__DIR__.'Connects.class.php');

class Activity extends IntentionalElement
{
	// Both Outcome objects
	private $produces;
	private $consumes;
	
	// An SplObjectStorage of Connects objects
	private $connects;
	
	public function __construct()
	{
		super.__construct();
		$this->connects=new SplObjectStorage();
	}
	
	public function setProduces($produces)
	{
		if ($produces instanceOf Outcome)
		{
			$this->produces=$produces;
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
	
	public function setConsumes($consumes)
	{
		if ($consumes instanceOf Outcome)
		{
			$this->consumes=$consumes;
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
