<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Personer\Write as WritePerson;
use UKMNorge\Innslag\Titler\Write as WriteTittel;
use UKMNorge\Innslag\Write;

use statistikk;
require_once('UKM/statistikk.class.php');

require_once('UKM/Autoloader.php');

$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

/**
 * REFORMATER POST-DATA
**/
foreach( $_POST as $post_key => $post_val ) {
	if( is_numeric( $post_key) && isset( $post_val['name'] ) && isset( $post_val['value'] ) ) {
		$key = $post_val['name'];
		$val = $post_val['value'];
		
		// Person-data
		if( strpos( $key, 'person[' ) !== false ) {
			$prepared = str_replace( 'person[', '', rtrim( $key, ']' ) );
			$key_parts = explode( '][', $prepared );
			
			$_FORM[ 'personer' ][ $key_parts[0] ][ $key_parts[1] ] = $val;
		} else {
			$_FORM[ $key ] = $val;
		}
	}
}

// Sjekk godkjenning...
$til = Arrangement::getById($_FORM['til_id']);
$innslagType = $innslag->getType();
// Sjekk hvis innslaget har nominasjon for innslag type
if($til->harNominasjonFor($innslagType)) {
	try {
		$nominasjon = $innslag->getNominasjoner()->getTil($_FORM['til_id']);
		if(!$nominasjon || !$nominasjon->erGodkjent()) {
			throw new Exception("Du kan ikke videresende før godkjennelse");
		}
	}
	catch(Exception $e) {
		throw $e;
	}
}

/**
 * INFORMASJON OM INNSLAGET
**/
// NAVN
if( isset( $_FORM['innslag_navn'] ) ) {
	$innslag->setNavn( $_FORM['innslag_navn'] );
}
// SJANGER
if( isset( $_FORM['innslag_sjanger'] ) ) {
	$innslag->setSjanger( $_FORM['innslag_sjanger'] );
}
// TEKNISKE BEHOV
if( isset( $_FORM['tekniske_behov'] ) ) {
	$innslag->setTekniskeBehov( $_FORM['tekniske_behov'] );
}

# LAGRE INNSLAG
Write::save( $innslag );



/**
 * INFORMASJON OM EVENTUELLE TITLER
**/
if( $_POST['type'] == 'tittel' ) {
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	
	// TITTELEN
	if( isset( $_FORM['tittel_tittel'] ) ) {
		$tittel->setTittel( $_FORM['tittel_tittel'] );
	}
	// VARIGHET
	if( isset( $_FORM['tittel_varighet_sek'] ) ) {
		$tittel->setVarighet( $_FORM['tittel_varighet_sek'] );
	}
	// TYPE
	if( isset( $_FORM['tittel_type'] ) ) {
		$tittel->setType( $_FORM['tittel_type'] );
	}
	
	# LAGRE TITTEL
	WriteTittel::save( $tittel );
}


/**
 * INFORMASJON OM EVENTUELLE PERSONER
**/
if( isset( $_FORM['personer'] ) ) {

	foreach( $_FORM['personer'] as $person_id => $persondata ) {
		$person = $innslag->getPersoner()->get( $person_id );
		
		// MOBIL
		if( isset( $persondata['mobil'] ) ) {
			$person->setMobil( $persondata['mobil'] );
		}
		// ALDER
		if( isset( $persondata['alder'] ) ) {
			$person->setFodselsdato( WritePerson::fodselsdatoFraAlder( $persondata['alder'] ) );
		}
		
		# LAGRE PERSON
		WritePerson::save( $person );
		
		// INSTRUMENT
		if( isset( $persondata['instrument'] ) ) {
			$person->setRolle( $persondata['instrument'] );
			
			# LAGRE INSTRUMENT / ROLLE
			WritePerson::saveRolle( $person );
		}
		
	}
}


// statistikk::oppdater_innslag($innslag, $til);


UKMVideresending::addResponseData('success',true);