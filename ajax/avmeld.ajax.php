<?php
	
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');

/**
 * loadValgtTil() avhenger av hvilket fylke som er valgt.
 * For fellesmønstringer på tvers av fylkesgrenser
 * trengs dette parameteret for å videresende til
 * riktig fylkesfestival.
 */
if( isset( $_POST['fylke'] ) ) {
	$_GET['fylke'] = $_POST['fylke'];
}

$monstring		= UKMVideresending::getFra();
$videresend_til = UKMVideresending::loadValgtTil();
$innslag 		= $videresend_til->getInnslag()->get( $_POST['innslag'] );

/*
 * Innslaget har titler
**/
if( $_POST['type'] == 'tittel' ) {
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	
	// Fjern tittelen
	$innslag->getTitler()->fjern( $tittel );
	write_tittel::fjern( $tittel );

	/**
	 * Meld av innslaget hvis dette var siste tittel
	 * som var videresendt
	**/
	if( $innslag->getTitler()->getAntall() == 0 ) {
		try {
			$videresend_til->getInnslag()->fjern( $innslag );
			write_innslag::fjern( $innslag );
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
	$videresend_til->getInnslag()->fjern( $innslag );
	write_innslag::fjern( $innslag );
}

if( UKMVideresending::getFra()->getType() == 'fylke' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);