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
	case 'nettredaksjon':
	case 'media':
		$nominasjon->setSamarbeid( $_POST['samarbeid'] );
		$nominasjon->setErfaring( $_POST['erfaring'] );
		write_nominasjon::saveMedia( $nominasjon );
		break;
		
	case 'konferansier':
		$nominasjon->setHvorfor( $_POST['hvorfor'] );
		$nominasjon->setBeskrivelse( $_POST['beskrivelse'] );
		$nominasjon->setFilPlassering( $_POST['filopplasting'] );
		$nominasjon->setFilUrl( $_POST['url'] );
		write_nominasjon::saveKonferansier( $nominasjon );
		break;
		
	case 'arrangor':
		$nominasjon->setVoksenErfaring( $_POST['voksen-erfaring'] );
		$nominasjon->setVoksenSamarbeid( $_POST['voksen-samarbeid'] );
		$nominasjon->setVoksenAnnet( $_POST['voksen-annet'] );
		write_nominasjon::saveArrangor( $nominasjon );
		break;
}

$voksen = write_nominasjon::createVoksen( $nominasjon->getId() );
$voksen->setNavn( $_POST['voksen-navn'] );
$voksen->setMobil( $_POST['voksen-mobil'] );
$voksen->setRolle( $_POST['voksen-rolle'] );
write_nominasjon::saveVoksen( $voksen );

write_nominasjon::saveNominertState( $nominasjon, true );