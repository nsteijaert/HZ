<?php
/**
 * Diverse functies voor het manipuleren van uri's.
 */
class Uri
{

 	// Alles is statisch, dus een constructor is niet nodig.
 	private function __construct() {}
	
	/**
	 * Voorziet een uri van vishaken ('<' en '>'), indien niet aanwezig.
	 * Voorziet een iri van geëscapete haakjes en slashes.
	 */	
	public static function escape_uri($uri)
	{
		if (substr($uri,0,4)=='http') //uri
		{
			return '<'.$uri.'>';
		}
		elseif(strpos($uri,':')!==FALSE) //iri
		{
			return self::escapeSpecialeTekens($uri);
		}
		else
		{
			return $uri;
		}
	}
	
	/**
	 * Verwijdert de vishaken ('<' en '>') van de uri, indien aanwezig.
	 * Verwijdert de geëscapete haakjes en slashes van een iri, indien aanwezig.
	 */
	public static function deescape_uri($uri)
	{
		if (substr($uri,0,1)=='<') //uri
		{
			return substr($uri,1,-1);
		}
		elseif(strpos($uri,':')!==FALSE) //iri
		{
			return self::deescapeSpecialeTekens($uri);
		}
		else
		{
			return $uri;
		}
	}
	
	/**
	 * Zet een in SMW opgeslagen naam van een Mediawiki-artikel om in een leesbare variant.
	 * Bijvoorbeeld: Eigenschap-3AElement_link_type naar Eigenschap: Element link type
	 */
	public static function decodeerSMWNaam($naam)
	{
		// ID's in RDF-store staan in procentnotatie, maar met een - ipv een %. Dit stukje code zorgt voor
		// een correcte procentnotatie en zet die vervolgens om naar de bedoelde tekens.
		return urldecode(strtr($naam, "-_","% "));
	}

	/**
	 * Zet een SMW-uri om in een voor mensen leesbare titel.
	 * Bijvoorbeeld: 'http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem'
	 * Naar: 'Building with Nature-interventies op het systeem'.
	 */
	public static function SMWuriNaarLeesbareTitel($uri)
	{
		$name_array=preg_split("/URIResolver\//", self::decodeerSMWNaam($uri));
		if($name_array[1])
		{
			return $name_array[1];
		}
		else
		{
			//Verwijder de prefix: het gedeelte tot en met de eerste(!) dubbele punt.
			$name_array=array_slice(explode(':',self::decodeerSMWNaam($uri)),1);
			return implode(':',$name_array);
		}
	}

	public static function escapeSpecialeTekens($string)
	{
		return strtr($string, array(
		'(' => '\(',
		')' => '\)',
		'/' => '\/'));
	}

	public static function deescapeSpecialeTekens($string)
	{
		return strtr($string, array(
		'\(' => '(',
		'\)' => ')',
		'\/' => '/'));
	}

	public static function stripSMWuriPadEnPrefixes($uri)
	{
		$name_array=preg_split("/URIResolver\//", $uri);
		if($name_array[1])
		{
			return $name_array[1];
		}
		else
		{
			//Verwijder de prefix: het gedeelte tot en met de eerste(!) dubbele punt.
			$name_array=array_slice(explode(':',$uri),1);
			return implode(':',$name_array);
		}
	}
}
?>