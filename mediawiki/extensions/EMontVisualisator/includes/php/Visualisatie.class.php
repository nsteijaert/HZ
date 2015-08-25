<?php
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');

Class Visualisatie
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$urlsplit=explode('index.php',$url);
		$domeinprefix=$urlsplit[0].'index.php/';

		$context_uri=Model::geefContextVanModel($model_uri);

		$svgheight=1280;

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
			var domeinprefix = "'.$domeinprefix.'";
			startVisualisatie("#'.$visualisatie_id.'", "'.$context_uri.'");
		</script>
		<button onclick="javascript:verhelpOverlappendeNodes();">Tik</button>';
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}
