<?php
require_once(__DIR__.'/IntentionalElement.class.php');

class Depends
{
	//An IntentionalElement
	private $link;
	//A string
	private $note;
	
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
	
	public function setLinkNote($linkNote)
	{
		$this->linkNote=$linkNote;
	}
	
	public function getLinkNote()
	{
		return $this->linkNote;
	}
}