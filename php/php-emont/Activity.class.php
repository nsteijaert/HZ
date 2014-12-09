<?
require_once(__DIR__.'IntentionalElement.class.php');
require_once(__DIR__.'Outcome.class.php');
require_once(__DIR__.'Connects.class.php');

class Activity extends IntentionalElement
{
	// Both Outcome objects
	private $produces;
	private $consumes;
	
	// An array of Connects objects
	private $connects=array();
	
	public function __construct()
	{
		super.__construct();
	}
}
