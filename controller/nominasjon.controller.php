<?php
require_once('UKM/monstring.class.php');
require_once('UKM/monstringer.class.php');

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if( in_array( $_GET['save'], ['nettredaksjon','media','konferansier','arrangor'] ) ) {
		require_once('nominasjon_save.controller.php');
	}
}

UKMVideresending::addViewData(
	'nominasjonstyper',
	[
		'arrangor' => 'Arrangører',
		'konferansier' => 'Konferansierer',
		'nettredaksjon' => 'UKM Media',
	]
);

$monstring = new monstring_v2( get_option('pl_id') );
$alle_innslag = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	// Vis kun arrangører, media og konferansierer i listen
	if( !in_array( $innslag->getType()->getId(), [4,5,8] ) ) {
		continue;
	}
	$alle_innslag[ $innslag->getType()->getKey() ][] = $innslag;
}

UKMVideresending::addViewData('innslag', $alle_innslag);
UKMVideresending::addViewData('festivalen', monstringer_v2::land( $monstring->getSesong() ));