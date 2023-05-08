<?php
	
$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$filmer = [];
foreach($innslag->getFilmer($monstring->getId())->getAll() as $film) {
    $f['embed_url'] = $film->getEmbedUrl();
    $filmer[] = $f;
}

// Antall filmer i innslag (kan være filmer fra andre arrangementer også)
$antallFilmerArrang = $innslag->getFilmer($monstring->getId())->getAntall();

UKMVideresending::addResponseData('antallFilmer', $antallFilmerArrang);
UKMVideresending::addResponseData('filmer', $filmer);
UKMVideresending::addResponseData('success', true);