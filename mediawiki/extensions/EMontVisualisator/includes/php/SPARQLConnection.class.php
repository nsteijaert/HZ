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
		$this->default_prefixes['rdf']='http://www.w3.org/1999/02/22-rdf-syntax-ns#';
		$this->default_prefixes['rdfs']='http://www.w3.org/2000/01/rdf-schema#';
		$this->default_prefixes['owl']='http://www.w3.org/2002/07/owl#';
		$this->default_prefixes['swivt']='http://semantic-mediawiki.org/swivt/1.0#';
		$this->default_prefixes['xsd']='http://www.w3.org/2001/XMLSchema#';

		$this->default_prefixes['property']='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3A';
		$this->default_prefixes['wiki']='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/';
	}

	public function escapedQuery($query,$vars=array())
	{
		for($teller=0;strpos($query,'%')!==FALSE;$teller++)
		{
			$pos = strpos($query, '%');
    		if ($pos !== false)
    		{
    			if(trim($vars[$teller])==FALSE)
					return null;
        		$query = substr_replace($query, Uri::escape_uri($vars[$teller]), $pos, 1);
    		}
		}
		$prefixed_query=self::prefixesIntoQuery().$query;

		try
		{
			$return=@file_get_contents($this->endpoint.rawurlencode($prefixed_query));
			return json_decode($return,true);
		}
		catch(Exception $e)
		{
			error_log('Fout bij uitvoeren query: '.$e);
			return null;
		}
	}

	public function escapedQueryAsMultidimensionalPHPArray($query,$vars)
	{
		$data=self::escapedQuery($query,$vars);
		// Eén resultaat wordt anders teruggeven dan meerdere. Dat wordt hiermee afgevangen.
		if($data && !array_key_exists('@graph',$data))
		{
			$return['@graph'][0]=$data;
			return $return;
		}
		return $data;
	}

	public function prefixesIntoQuery($extra_prefixes=array())
	{
		$prefixes=array_merge($this->default_prefixes,$extra_prefixes);

		foreach($prefixes as $prefix=>$uri)
		{
			$return.='PREFIX '.$prefix.': <'.$uri.">\n";
		}
		return $return;
	}

	public static function geefEersteResultaat($subject,$predicate)
	{
		$query='SELECT ?object WHERE { % % ?object}';
		$connectie=new SPARQLConnection();
		$result=$connectie->escapedQuery($query,array($subject,$predicate));

		return $result['results']['bindings'][0]['object']['value'];
	}
}
?>