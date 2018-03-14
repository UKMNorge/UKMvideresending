<?php
	
$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$bilder = [];
foreach( $innslag->getBilder()->getAll() as $bilde ) {
	$bilder[] = $bilde;
}
	
UKMVideresending::addResponseData('bilder', $bilder);
UKMVideresending::addResponseData('success', true);