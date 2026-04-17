<?php

use UKMNorge\Arrangement\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Titler\Write as WriteTittel;

use statistikk;
require_once('UKM/statistikk.class.php');


$fra		= UKMVideresending::getFra();
$innslag 	= $fra->getInnslag()->get( $_POST['innslag'] );
$til        = UKMVideresending::getValgtTil('POST')->getArrangement();


// Send videre KUN hvis mottaker arrangement har videresendingnominasjon
if(!$til->harVideresendingNominasjon()) {
	throw new Exception('Mottaker arrangementet har ikke videresendingnominasjon');
}

// Send videre KUN hvis alle personer har godkjent nominasjon
if(!$innslag->erAlleNominasjonerGodkjent($til->getId())) {
	throw new Exception('Alle personer må ha godkjent nominasjon før videresending');
}

// Sjekk godkjenning...
$innslagType = $innslag->getType();
// Sjekk hvis innslaget har nominasjon for innslag type
if($til->harNominasjonFor($innslagType)) {
	try {
		$nominasjon = $innslag->getNominasjoner()->getTil($til->getId());
		if(!$nominasjon || !$nominasjon->erGodkjent()) {
			throw new Exception("Du kan ikke videresende før godkjennelse");
		}
	}
	catch(Exception $e) {
		throw $e;
	}
}

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

// Oppdater statistikk
$til        = UKMVideresending::getValgtTil('POST')->getArrangement();
$fra        = new Arrangement( intval(get_option('pl_id')) );
$innslag    = $fra->getInnslag()->get( intval($_POST['innslag']) );
statistikk::oppdater_innslag($innslag, $til);

if ($til->harVideresendingNominasjon()) {
	require_once __DIR__ . '/videresend_nominasjon.ajax.php';
}

UKMVideresending::addResponseData('success', true);