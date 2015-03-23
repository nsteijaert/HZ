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

$wgHooks['BeforePageDisplay'][] = 'wgAddVisualisationScripts';

function wgAddVisualisationScripts(&$parser, &$text) {

  global $addVisualisationScripts;
  //if ($addVisualisationScripts === true) return true;

  $parser->addScript('<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/d3.v3.js"></script>');
  $parser->addScript('<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/cola.v3.min.js"></script>');

  $addVisualisationScripts = true;

  return true;

}

$wgAutoloadClasses['SpecialEMontVisualisator'] = __DIR__ . '/SpecialEMontVisualisator.php'; # Location of the SpecialEMontVisualisator class (Tell MediaWiki to load this file)
$wgSpecialPages['EMontVisualisator'] = 'SpecialEMontVisualisator'; # Tell MediaWiki about the new special page and its class name
?>
