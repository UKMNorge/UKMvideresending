<?php
	
$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$filer = [];
foreach( $innslag->getPlayback()->getAll() as $playback ) {
	$filer[] = $playback;
}
	
UKMVideresending::addResponseData('playback', $filer);
UKMVideresending::addResponseData('success', true);