<?php
	
$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$filmer = [];
foreach($innslag->getFilmer()->getAll() as $film) {
    $f['embed_url'] = $film->getEmbedUrl();
    $filmer[] = $f;
}

UKMVideresending::addResponseData('filmer', $filmer);
UKMVideresending::addResponseData('success', true);