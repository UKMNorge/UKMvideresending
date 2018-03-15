<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/write_person.class.php');

$monstring		= UKMVideresending::getFra();
$innslag 		= $monstring->getInnslag()->get( $_POST['innslag'] );
$videresend_til = UKMVideresending::loadValgtTil();

// Videresend innslaget
try {
	$videresend_til->getInnslag()->leggTil( $innslag );
	
	$innslag = $videresend_til->getInnslag()->get( $innslag->getId() );
	write_innslag::leggTil( $innslag );
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
	$innslag = $videresend_til->getInnslag()->get( $_POST['innslag'] );	
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	$innslag->getTitler()->leggTil( $tittel );

	write_tittel::leggTil( $tittel );
}

if( UKMVideresending::getFra()->getType() == 'fylke' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);