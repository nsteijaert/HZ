<?php
require_once(__DIR__.'/includes/php/php-emont/Model.class.php');

class SpecialEMontVisualisator extends SpecialPage
{
	function __construct()
	{
		parent::__construct('EMontVisualisator');
	}

	function execute($par)
	{
		$output = $this->getOutput();

		// Parameters uit url
		$pars=explode('/',$par);

		// Bij geen parameters het overzicht tonen
		if(!$par)
		{
			require_once(__DIR__.'/includes/php/Modelselectie.class.php');
			$modelselectie=new Modelselectie();

			$output->setPageTitle('Modellen');
			$output->addHTML($modelselectie->geefInhoud());
		}
		else
		{
			$model_uri='wiki:'.$pars[1];
			$context_uri=Model::geefContextVanModel($model_uri);

			require_once(__DIR__.'/includes/php/Visualisatiepagina.class.php');
			$output->setPageTitle(Uri::SMWuriNaarLeesbareTitel($context_uri));
			$output->addModules('ext.EMontVisualisator');

			$visualisatiepagina=new Visualisatiepagina($model_uri);
			$output->addHTML($visualisatiepagina->geefInhoud());
		}
	}
}
?>