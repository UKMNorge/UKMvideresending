<?php

use UKMNorge\Arrangement\Write;
use UKMNorge\Innslag\Titler\Write as WriteTittel;

$fra		= UKMVideresending::getFra();
$innslag 	= $fra->getInnslag()->get( $_POST['innslag'] );
$til        = UKMVideresending::getValgtTil('POST')->getArrangement();

// Videresend innslaget
try {
    Write::leggTilInnslag($til, $innslag, $fra);
} catch( Exception $e ) {
	/**
	 * Selv om innslaget er videresendt fra før, betyr ikke det
	 * nødvendigvis at tittelen er videresendt.
	 * Fortsett derfor, men dø på alle andre exceptions.
	**/
	if( $e->getCode() == 10404 ) {
		// 10404: Innslag collection: innslaget er allerede lagt til
		// fortsett til videresending av evt tittel
	} else {
		throw $e;
	}
}


// Videresend evntuell tittel
if( $_POST['type'] == 'tittel' ) {
	/**
	 * Hent innslag på nytt.
	 * Henter fra mønstringen det skal videresendes til, slik at
	 * tittelen meldes på riktig mønstring (og ikke avsender-mønstringen)
	**/
	$innslag = $til->getInnslag()->get( $_POST['innslag'] );	
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	$innslag->getTitler()->leggTil( $tittel );

	WriteTittel::leggtil( $tittel );
}

UKMVideresending::beregnAntallVideresendtePersoner();

UKMVideresending::addResponseData('success',true);