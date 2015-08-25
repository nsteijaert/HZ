<?php
// Direct opvragen kan niet, enkel via als module van de Mediawiki
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/EMontVisualisator/EMontVisualisator" );
EOT;
	exit(1);
}

$wgExtensionCredits['validextensionclass'][] = array(
    'path' => __FILE__,
    'name' => 'EMontVisualisator',
    'author' => 'Michael Steenbeek',
    'url' => 'http://www.deltaexpertise.nl',
    'description' => 'Visualiseert een EMont-model.',
    'version'  => 1.0,
    'license-name' => "CC-BY-NC-SA",
);
$wgResourceLoaderDebug = true;

$wgResourceModules['ext.EMontVisualisator'] = array(
	// Scripts en stijldefinities
	'scripts' => array('js/cola.v3.min.js', 'js/d3.v3.js', 'js/visualisatie.js', 'js/visualisatie-pagina.js'),
	'styles' => array ('css/visualisatie.css'),
	'position' => 'top',

	// Basispaden van scripts en stijldefinities:
	'localBasePath' => __DIR__.'/includes',
	'remoteExtPath' => 'EMontVisualisator'.'/includes'
);

// Specificeert welke pagina er getoond moet worden
$wgAutoloadClasses['SpecialEMontVisualisator'] = __DIR__ . '/SpecialEMontVisualisator.php';
// Registreer bovenstaande pagina als Speciale Pagina van EMontVisualisator
$wgSpecialPages['EMontVisualisator'] = 'SpecialEMontVisualisator';
?>