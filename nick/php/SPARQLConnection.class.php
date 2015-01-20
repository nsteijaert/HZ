<?php
class SPARQLConnection
{
	private $default_endpoint="http://127.0.0.1:3030/ds/query?output=json&query=";
	private $endpoint="";
	
	public function __construct($endpoint=null)
	{
		if ($endpoint==null)
			$this->endpoint=$this->default_endpoint;
	}
	
	public function JSONQuery($query)
	{
		return file_get_contents($this->endpoint.urlencode($query));
	}
	
	public function JSONQueryAsPHPArray($query)
	{
		$json=self::JSONQuery($query);
		return json_decode($json,true);
	}
}
?>