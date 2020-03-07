<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    UKMVideresending::require('save/nominasjon.save.php');
}

UKMVideresending::addViewData(
	'nominasjonstyper',
	[
		'arrangor' => 'Arrangører',
		'konferansier' => 'Konferansierer',
		'nettredaksjon' => 'UKM Media',
	]
);

$fra = UKMVideresending::getFra();
$alle_innslag = [];
foreach( $fra->getInnslag()->getAll() as $innslag ) {
	// Vis kun arrangører, media og konferansierer i listen
	if( !in_array( $innslag->getType()->getId(), [4,5,8] ) ) {
		continue;
	}
    $alle_innslag[ $innslag->getType()->getKey() ][] = $innslag;
}

UKMVideresending::addViewData('innslag', $alle_innslag);