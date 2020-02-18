<?php

use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Innslag\Titler\Write;

$til        = UKMVideresending::getValgtTil();
$innslag 	= $til->getArrangement()->getInnslag()->get( $_POST['innslag'] );

/*
 * Innslaget har titler
**/
if( $innslag->getType()->harTitler() ) {
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	
	// Fjern tittelen
    $innslag->getTitler()->fjern( $tittel );
    Write::fjern($tittel);

	/**
	 * Meld av innslaget hvis dette var siste tittel
	 * som var videresendt
	**/
	if( $innslag->getTitler()->getAntall() == 0 ) {
		try {
			$til->getArrangement()->getInnslag()->fjern( $innslag );
            WriteArrangement::fjernInnslag($innslag);
		} catch( Exception $e ) {
			/**
			 * Håndter feil som oppstår hvis innslaget
			 * ikke har en fylkestep-rad med t_id = 0
			 *
			 * 2: innslag_collection finner ikke innslaget
			**/
			if( $e->getCode() == 2 ) {
				// Fortsett
			} else {
				throw $e;
			}
		}
	}
}
/**
 * Innslaget har ikke titler (tittelløs)
**/
else {
	$til->getArrangement()->getInnslag()->fjern( $innslag );
    WriteArrangement::fjernInnslag( $innslag );
}

if( $til->getArrangement()->getEierType() == 'land' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);