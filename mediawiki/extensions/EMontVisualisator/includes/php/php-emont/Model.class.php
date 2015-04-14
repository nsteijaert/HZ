<?php
/**
 * Alles wat met L1- en L2-modellen te maken heeft, en de contexten/subcontexten die eraan hangen
 */
require_once(__DIR__.'/../SPARQLConnection.class.php');
require_once(__DIR__.'/JSON_EMontParser.class.php');

class Model
{
	private function __construct() {}

	/**
	 * Zoekt alle subsituaties en subrollen bij een bepaalde context (slaat subpractices over).
	 * @input De context-URI, zonder vishaken (< en >)
	 */
	static function zoekSubcontexten($context_uri)
	{
		$subrollen=array();
		$context_uri=Uri::escape_uri($context_uri);

		$query='DESCRIBE ?context WHERE { ?context <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASupercontext> '.$context_uri.' }';
		$connectie=new SPARQLConnection();
		$contexten=$connectie->JSONQueryAsPHPArray($query);

		if(isset($contexten['@graph']))
		{
			foreach($contexten['@graph'] as $item)
			{
				// Subsituaties moeten niet worden meegenomen.
				if(!self::isHoofdcontextVanPractice($item['@id']))
				{
					$subrollen[]=$item['@id'];
					$subrollen=array_merge($subrollen,self::zoekSubcontexten($item['@id']));
				}
			}
		}
		return $subrollen;
	}

	/**
	 * Geeft lijst van L1-modellen (situaties)
	 */
	static function geefL1modellen()
	{
		$query='DESCRIBE ?practice WHERE {?practice property:Practice_type "Practice"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
					$contexten[$item['@id']]=strtr(Uri::SMWuriNaarLeesbareTitel($item['@id']),array(' practice'=>''));
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Geeft lijst van L2-cases (situaties), in de vorm van context-uri's
	 */
	static function geefL2cases()
	{
		$query='DESCRIBE ?experience WHERE {?experience property:Practice_type "Experience"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
					$contexten[$item['@id']]=strtr(Uri::SMWuriNaarLeesbareTitel($item['@id']),array(' experience'=>''));
			}

			return $contexten;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Bepaalt of een context-uri aan een practice (L1-model) toebehoort.
	 */
	static function isPractice($context_uri)
	{
		$query='DESCRIBE ?practice WHERE {
			?practice property:Context '.Uri::escape_uri($context_uri).'.
			?practice property:Practice_type "Practice"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Bepaalt of een context-uri aan een experience (L2-case) toebehoort.
	 */
	static function isExperience($context_uri)
	{
		$query='DESCRIBE ?experience WHERE {
			?experience property:Context '.Uri::escape_uri($context_uri).'.
			?experience property:Practice_type "Experience"}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Bepaalt of een model een practice (L1-model) is.
	 */
	static function modelIsPractice($model_uri)
	{		
		$query='DESCRIBE ?practice WHERE {
			?practice property:Practice_type "Practice" .
			FILTER (?practice = '.Uri::escape_uri($model_uri).')}';

		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Bepaalt of een model een experience (L2-case) is.
	 */
	static function modelIsExperience($model_uri)
	{
		$query='DESCRIBE ?practice WHERE {
			?practice property:Practice_type "Experience" .
			FILTER (?practice = '.Uri::escape_uri($model_uri).')}';

		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (count($result)>1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	static function geefContextVanModel($model_uri)
	{
		$query='SELECT ?context WHERE {
			'.Uri::escape_uri($model_uri).' property:Context ?context}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		return strtr($result['results']['bindings'][0]['context']['value'],array('http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'=>'wiki:'));
	}
	
	static function geefL1modelVanCase($l2_uri)
	{
		if (!self::modelIsExperience($l2_uri))
		{
			return null;
		}
		
		$query='SELECT ?model WHERE {
			'.Uri::escape_uri($l2_uri).' property:Part_of ?model}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);
		
		return strtr($result['results']['bindings'][0]['model']['value'],array('http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'=>'wiki:'));
	}

	/**
	 *  Bepaalt of een context-uri niet toebehoort aan (sub)practice.
	 */
	static function isHoofdcontextVanPractice($context_uri)
	{
		$context_uri=Uri::escape_uri($context_uri);
		$query="DESCRIBE ?s ?o WHERE {
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3ASelection_link> ".$context_uri."
			.
			?s <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Eigenschap-3APractice_back_link> ?o
			}";
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if (empty($result['@graph'])) // Leeg resultaat
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	static function nieuweL2case($naam, $l1model)
	{
		if (!isHoofdcontextVanPractice($situatie_uri))
			return FALSE;
	}

	static function geefElementenUitContextEnSubcontexten($context_uri)
	{
		$alle_te_doorzoeken_uris=self::geefUrisVanContextEnSubcontexten($context_uri);

		$zoekstring=implode(' } UNION { ?ie property:Context ',$alle_te_doorzoeken_uris);
		$query_inhoud_situatie='DESCRIBE ?ie WHERE {{ ?ie property:Context '.$zoekstring.' }.{?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AIntentional_Element>} UNION {?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AActivity>}}';

		$connectie=new SPARQLConnection();
		return $connectie->JSONQueryAsMultidimensionalPHPArray($query_inhoud_situatie);
	}

	static function geefUrisVanContextEnSubcontexten($context_uri)
	{
		$subrollen=Model::zoekSubcontexten($context_uri);

		foreach(array_merge(array($context_uri),$subrollen) as $te_doorzoeken_uri)
		{
			$alle_te_doorzoeken_uris[]=Uri::escape_uri($te_doorzoeken_uri);
		}
		return $alle_te_doorzoeken_uris;
	}
}
