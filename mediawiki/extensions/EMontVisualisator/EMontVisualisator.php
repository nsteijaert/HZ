<?php
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
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
    'license-name' => "CC-BY-NC-SA",   // Short name of the license, links LICENSE or COPYING file if existing - string, added in 1.23.0
);
$wgResourceLoaderDebug = true;


$wgResourceModules['ext.EMontVisualisator'] = array(
	// JavaScript and CSS styles. To combine multiple files, just list them as an array.
	'scripts' => array( 'js/cola.v3.min.js', 'js/d3.v3.js' ),
	'position' => 'top',
	//'styles' => 'css/ext.myExtension.css',
 
	// You need to declare the base path of the file paths in 'scripts' and 'styles'
	'localBasePath' => __DIR__.'/includes',
	// ... and the base from the browser as well. For extensions this is made easy,
	// you can use the 'remoteExtPath' property to declare it relative to where the wiki
	// has $wgExtensionAssetsPath configured:
	'remoteExtPath' => 'EMontVisualisator'.'/includes'
);

$wgAutoloadClasses['SpecialEMontVisualisator'] = __DIR__ . '/SpecialEMontVisualisator.php'; # Location of the SpecialEMontVisualisator class (Tell MediaWiki to load this file)
$wgSpecialPages['EMontVisualisator'] = 'SpecialEMontVisualisator'; # Tell MediaWiki about the new special page and its class name
?>