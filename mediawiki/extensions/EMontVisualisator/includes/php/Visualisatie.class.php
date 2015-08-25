<?php
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');

if($_GET['echo']==TRUE)
{
	$visualisatie=new Visualisatie($_GET['model_uri']);
	echo '<html><head></head><body>';
	echo '<script type="text/javascript" src="/mediawiki/resources/lib/jquery/jquery.js"></script>';
	echo $visualisatie->geefInhoud();
	echo '</body></html>';
}

Class Visualisatie
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		//TODO NA: als ResourceLoader-modules toevoegen
		$this->inhoud.='<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/d3.v3.js"></script>';
		$this->inhoud.='<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/cola.v3.min.js"></script>';
		$this->inhoud.='<link rel="stylesheet" type="text/css" href="/mediawiki/extensions/EMontVisualisator/includes/css/visualisatie.css"></style>';

		$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$urlsplit=explode('index.php',$url);
		$domeinprefix=$urlsplit[0].'index.php/';

		$context_uri=Model::geefContextVanModel($model_uri);

		$svgheight=2280;

		$visualisatie_id='visualisatie-'.Uri::stripSMWuriPadEnPrefixes($model_uri);

		$this->inhoud.='
		<svg id="'.$visualisatie_id.'" width="100%" height="'.$svgheight.'">
			<defs>
				<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="5" markerHeight="5" orient="auto">
		        	<path d="M0,-5L10,0L0,5"></path>
		    	</marker>
			</defs>
		</svg>
		<script type="text/javascript">

		function openInNewTab(url) {
			var win = window.open(url, \'_blank\');
			win.focus();
		}

		var graph;
		var visualisatieId = "#'.$visualisatie_id.'";
		var width = $(visualisatieId).width();
		var	height = $(visualisatieId).height();
		var nodewidth = 30;
		var nodeheight = 100;
		var domeinprefix = "'.$domeinprefix.'";
		var op_te_vragen_context_uri = "'.$context_uri.'";';

		$this->inhoud.="\n".file_get_contents(__DIR__. '/../js/visualisatie.js')."\n";
		$this->inhoud.='		</script><button onclick="javascript:verhelpOverlappendeNodes();">Tik</button>';
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}
