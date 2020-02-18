<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/write_person.class.php');

$videresend_til = UKMVideresending::getValgtTil('POST');
$innslag 		= $videresend_til->getInnslag()->get( $_POST['innslag'] );
$person 		= $innslag->getPersoner()->get( $_POST['person'] );

$innslag->getPersoner()->leggTil( $person );
write_person::leggTil( $person );

if( UKMVideresending::getFra()->getType() == 'fylke' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);