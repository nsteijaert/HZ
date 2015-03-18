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
	 * Zoekt alle subrollen bij een bepaalde context (slaat subsituaties over).
	 * @input De context-URI, zonder vishaken (< en >)
	 */
	static function zoekSubrollen($context_uri)
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
				if(!self::isSituatie($item['@id']))
				{
					$subrollen[]=$item['@id'];
					$subrollen=array_merge($subrollen,self::zoekSubrollen($item['@id']));
				}
			}
		}
		return $subrollen;
	}

	/**
	 * Geeft lijst van L1-modellen (situaties), in de vorm van context-uri's
	 */
	static function geefL1modellen()
	{
		$query='DESCRIBE ?context WHERE {?context <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AContext>}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
				if(self::isPractice($item['@id']))
				{
					$context=new Context($item['@id']);
					$context->setDescription(JSON_EMontParser::geefContextbeschrijving($item['@id']));
					$contexten[$item['@id']]=$context;
				}
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
		$query='DESCRIBE ?context WHERE {?context <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AContext>}';
		$connectie=new SPARQLConnection();
		$result=$connectie->JSONQueryAsPHPArray($query);

		if(isset($result['@graph']))
		{
			$contexten=array();

			foreach($result['@graph'] as $item)
			{
				if(self::isExperience($item['@id']))
				{
					$context=new Context($item['@id']);
					$context->setDescription(JSON_EMontParser::geefContextbeschrijving($item['@id']));
					$contexten[$item['@id']]=$context;
				}
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
	 *  Bepaalt of een bepaalde uri een situatie is (en bijvoorbeeld geen rol).
	 */
	static function isSituatie($context_uri)
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
}
