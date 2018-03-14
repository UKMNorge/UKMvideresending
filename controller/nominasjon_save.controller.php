<?php
require_once('UKM/logger.class.php');
require_once('UKM/write_nominasjon.class.php');

$monstring = new monstring_v2( get_option('pl_id') );

#echo '<pre>';var_dump( $_POST );echo'</pre>';

global $current_user;
UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );

$nominert = new innslag_v2( $_GET['id'] );

// DO CREATE GENERIC NOMINASJON
$nominasjon = write_nominasjon::create( 
	$nominert->getId(),				// Innslag ID
	get_option('season'), 			// Sesong
	'land', 						// TODOondemand: støtt også nominasjon fra lokal til fylke
	$nominert->getKommune(),		// Innslagets kommune
	$nominert->getType()->getKey()	// Type nominasjon
);

switch( $nominert->getType()->getKey() ) {
	case 'media':
		$nominasjon->setSamarbeid( utf8_encode( $_POST['samarbeid'] ) );
		$nominasjon->setErfaring( utf8_encode( $_POST['erfaring'] ) );
		write_nominasjon::saveMedia( $nominasjon );
		break;
		
	case 'konferansier':
		$nominasjon->setHvorfor( utf8_encode( $_POST['hvorfor'] ) );
		$nominasjon->setBeskrivelse( utf8_encode( $_POST['beskrivelse'] ) );
		$nominasjon->setFilPlassering( $_POST['filopplasting'] );
		$nominasjon->setFilUrl( $_POST['url'] );
		write_nominasjon::saveKonferansier( $nominasjon );
		break;
		
	case 'arrangor':
		$nominasjon->setVoksenErfaring( utf8_encode( $_POST['voksen-erfaring'] ) );
		$nominasjon->setVoksenSamarbeid( utf8_encode( $_POST['voksen-samarbeid'] ) );
		$nominasjon->setVoksenAnnet( utf8_encode( $_POST['voksen-annet'] ) );
		write_nominasjon::saveArrangor( $nominasjon );
		break;
}

$voksen = write_nominasjon::createVoksen( $nominasjon->getId() );
$voksen->setNavn( utf8_encode( $_POST['voksen-navn'] ) );
$voksen->setMobil( $_POST['voksen-mobil'] );
$voksen->setRolle( utf8_encode( $_POST['voksen-rolle'] ) );
write_nominasjon::saveVoksen( $voksen );

write_nominasjon::saveNominertState( $nominasjon, true );