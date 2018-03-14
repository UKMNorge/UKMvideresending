<?php
require_once('UKM/write_innslag.class.php');

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
write_innslag::save( $innslag );



/**
 * INFORMASJON OM EVENTUELLE TITLER
**/
if( $_POST['type'] == 'tittel' ) {
	require_once('UKM/write_tittel.class.php');
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
	write_tittel::save( $tittel );
}


/**
 * INFORMASJON OM EVENTUELLE PERSONER
**/
if( isset( $_FORM['personer'] ) ) {
	require_once('UKM/write_person.class.php');

	foreach( $_FORM['personer'] as $person_id => $persondata ) {
		$person = $innslag->getPersoner()->get( $person_id );
		
		// MOBIL
		if( isset( $persondata['mobil'] ) ) {
			$person->setMobil( $persondata['mobil'] );
		}
		// ALDER
		if( isset( $persondata['alder'] ) ) {
			$person->setFodselsdato( write_person::fodselsdatoFraAlder( $persondata['alder'] ) );
		}
		
		# LAGRE PERSON
		write_person::save( $person );
		
		// INSTRUMENT
		if( isset( $persondata['instrument'] ) ) {
			$person->setRolle( $persondata['instrument'] );
			
			# LAGRE INSTRUMENT / ROLLE
			write_person::saveRolle( $person );
		}
		
	}
}


UKMVideresending::addResponseData('success',true);