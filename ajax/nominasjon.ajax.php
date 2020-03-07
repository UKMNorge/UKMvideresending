<?php

use UKMNorge\Innslag\Nominasjon\Write;

if( isset( $_POST['innslag'] ) && isset( $_POST['status'] ) ) {

    $til = UKMVideresending::getValgtTil();
    $fra = UKMVideresending::getFra();
    
    $innslag = $fra->getInnslag()->get(intval($_POST['innslag']));
	$nominasjon = $innslag->getNominasjoner()->getTil( $til->getId() );

	try {
        // Opprett databaseraden i tilfelle den ikke finnes
        // da saveState ikke har nok info til å opprette.
        if( !$nominasjon->eksisterer() ) {
            $nominasjon = Write::create( $innslag, $innslag->getContext()->getMonstring()->getId(), $til->getId());
        }
		Write::saveState( $nominasjon, $_POST['status'] == 'true' );
	} catch( Exception $e ) {
		die($e->getMessage());
	}

	UKMVideresending::addResponseData('success',true);
}