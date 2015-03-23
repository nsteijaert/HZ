<?php
require_once(__DIR__.'/Uri.class.php');

class SPARQLConnection
{
	private $default_endpoint="http://127.0.0.1:3030/ds/query?output=json&query=";
	private $endpoint="";
	private $default_prefixes=array();

	public function __construct($endpoint=null)
	{
		if ($endpoint==null)
			$this->endpoint=$this->default_endpoint;
		$this->default_prefixes[]='rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>';
		$this->default_prefixes[]='rdfs: <http://www.w3.org/2000/01/rdf-schema#>';
		$this->default_prefixes[]='owl: <http://www.w3.org/2002/07/owl#>';
		$this->default_prefixes[]='swivt: <http://semantic-mediawiki.org/swivt/1.0#>';
		$this->default_prefixes[]='xsd: <http://www.w3.org/2001/XMLSchema#>';

		$this->default_prefixes[]='property: <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3A>';
		$this->default_prefixes[]='wiki: <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/>';
	}

	public function JSONQuery($query)
	{
		$prefixed_query=self::prefixesIntoQuery().$query;
		return file_get_contents($this->endpoint.rawurlencode($prefixed_query));
	}

	public function JSONQueryAsPHPArray($query)
	{
		$json=self::JSONQuery($query);
		return json_decode($json,true);
	}

	public function prefixesIntoQuery($extra_prefixes=array())
	{
		$prefixes=array_merge($this->default_prefixes,$extra_prefixes);

		foreach($prefixes as $prefix)
		{
			$return.='PREFIX '.$prefix." \n";
		}
		return $return;
	}
}
?>