<?php
require_once(__DIR__.'IntentionalElement.class.php');

class Contributes
{
	//An IntentionalElement
	private $link;
	//An enum
	private $contributionValue;
	//A string
	private $linkNote;
	
	public function __construct()
	{
		
	}
	
	public function setLink(&$link)
	{
		if ($link instanceOf IntentionalElement)
		{
			$this->link=$link;
		}
		else 
		{
			throw new Exception('Not an IntentionalElement');
		}
	}
	
	public function getLink()
	{
		return $this->link;
	}
	
	public function setContributionValue($contributionValue)
	{
		switch($contributionValue)
		{
			case '+/-':
			case '---':
			case '--':
			case '-':
			case '0':
			case '+':
			case '++':
			case '+++':
				$this->contributionValue=$contributionValue;
				break;
			default:
				throw new Exception('Not a valid contribution value');
		}
	}
	
	public function getContributionValue()
	{
		return $this->contributionValue;
	}
	
	public function setLinkNote($linkNote)
	{
		$this->linkNote=$linkNote;
	}
	
	public function getLinkNote()
	{
		return $this->linkNote;
	}
}
