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
	
	/**
	 * Verstuurt een verzoek via POST, en geeft het antwoord terug.
	 */
	public static function getPostData($url,$parameters)
	{
		// JSON levert de meest bruikbare resultaten terug (bruikbaarder dan format=php(!))
		$fields_string='format=json&';
		
		// Codeer alle parameters en hun waardes als HTML-parameters
		foreach($parameters as $key=>$value)
		{
			$fields_string .= urlencode($key).'='.urlencode($value).'&';
		}
		rtrim($fields_string,'&');
		
		$ch = curl_init();
		$settings['cookiefile']=dirname(__FILE__).'cookies.tmp';
		
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $settings['cookiefile']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $settings['cookiefile']);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_USERAGENT, "PHPEmontVisualisatie/1.0 (http://www.deltaexpertise.nl/)");
		// Geeft het resultaat als variable in plaats van het direct weer te geveb
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result=curl_exec($ch);
		curl_close($ch);
		
		// Geef het resultaat terug als associatieve array
		return json_decode($result,TRUE);
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

	public static function geefIEtype($ie_uri)
	{
		$query='SELECT ?type WHERE {
			'.Uri::escape_uri($ie_uri).' property:Intentional_Element_type ?type}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);
		
		return $result['results']['bindings'][0]['type']['value'];
	}
}
?>