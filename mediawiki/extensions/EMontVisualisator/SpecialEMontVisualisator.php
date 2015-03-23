<?php
class SpecialEMontVisualisator extends SpecialPage
{
function __construct() {
		parent::__construct('EMontVisualisator');
	}
 
	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();
 
		# Get request data from, e.g.
		$param = $request->getText( 'param' );
 
		# Do stuff
		ob_start();
		require_once(__DIR__.'/includes/visualisatie.php');

		$output->addHTML(ob_get_contents());
		ob_end_clean();
	}
}
?>
