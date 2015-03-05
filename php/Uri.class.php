<?php
/**
 * Diverse functies voor het manipuleren van uri's.
 */
 class Uri
 {
 	// Alles is statisch, dus een constructor is niet nodig.
 	private function __construct() {}
	
	public static function codeerHaakjes($string)
	{
		return strtr($string,array('('=>'%28',')'=>'%29'));
	}
	
	public static function decodeerHaakjes($string)
	{
		return strtr($string,array('%28'=>'(', '%29'=>')'));
	}

	/**
	 * Voorziet een uri van vishaken ('<' en '>'), indien niet aanwezig.
	 */	
	public static function escape_uri($uri)
	{
		if (substr($uri,0,4)=='http')
		{
			return '<'.$uri.'>';
		}
		else
		{
			return $uri;
		}
	}
	
	/**
	 * Verwijdert de vishaken ('<' en '>') van de uri, indien aanwezig.
	 */
	public static function deescape_uri($uri)
	{
		if (substr($uri,0,1)=='<')
		{
			return substr($uri,1,-1);
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
			$name_array=explode(':',self::decodeerSMWNaam($uri));
			return end($name_array);
		}
	}
 }
 
?>