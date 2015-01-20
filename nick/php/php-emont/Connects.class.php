<?php
require_once(__DIR__.'/Activity.class.php');

class Connects
{
	//An Activity
	private $link;
	//An enum
	private $connectionType;
	//Strings
	private $linkCondition;
	private $linkNote;
	
	public function __construct()
	{
		
	}
	
	public function setLink(&$link)
	{
		if ($link instanceOf Activity)
		{
			$this->link=$link;
		}
		else 
		{
			throw new Exception('Not an Activity');
		}
	}
	
	public function getLink()
	{
		return $this->link;
	}
	
	public function setConnectionType($connectionType)
	{
		switch($connectionType)
		{
			case 'seq':
			case 'par':
			case 'join':
			case 'sync':
				$this->connectionType=$connectionType;
				break;
			default:
				throw new Exception('Not a valid connection type');
		}
	}
	
	public function getConnectionType()
	{
		return $this->connectionType;
	}
	
	public function setLinkCondition($linkCondition)
	{
		$this->linkCondition=$linkCondition;
	}
	
	public function getLinkCondition()
	{
		return $linkCondition;
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
