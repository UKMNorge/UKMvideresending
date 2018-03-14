<?php
if( isset( $_POST['innslag'] ) && isset( $_POST['status'] ) ) {
	require_once('UKM/logger.class.php');
	require_once('UKM/write_nominasjon.class.php');
	require_once('UKM/monstringer.class.php');
	
	global $current_user;
	UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );

	require_once('UKM/write_nominasjon.class.php');
	$festival = monstringer_v2::land( get_option('season') );
	
	$innslag = new innslag_v2( $_POST['innslag'] );
	$nominasjon = $innslag->getNominasjon( $festival );

	try {
		write_nominasjon::saveNominertState( $nominasjon, $_POST['status'] == 'true' );
	} catch( Exception $e ) {
		die($e->getMessage());
	}

	UKMVideresending::addResponseData('success',true);
}