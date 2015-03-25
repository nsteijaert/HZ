<?php
class SpecialEMontVisualisator extends SpecialPage
{
function __construct() {
		parent::__construct('EMontVisualisator');
	}
 
	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders('HolderDeBolder');
 
		# Get request data from, e.g.
		$param = $request->getText( 'param' );
		$pars=explode('/',$par); 

		// Bij geen parameters het overzicht tonen		
		if(!$par)
		{
			ob_start();
			require_once(__DIR__.'/includes/modelselectie.php');
			$output->setPageTitle('Modellen');
			$output->addHTML(ob_get_contents());
			ob_end_clean();
		}
		else
		{
			$output->addModules('ext.EMontVisualisator');
			ob_start();
			require_once(__DIR__.'/includes/visualisatie.php');
			$output->setPageTitle(Uri::SMWuriNaarLeesbareTitel($context_uri));	
			$output->addHTML(ob_get_contents());
			ob_end_clean();
		}
	}
}
?>
