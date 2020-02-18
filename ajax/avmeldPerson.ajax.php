<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/write_person.class.php');

$videresend_til = UKMVideresending::getValgtTil('POST');
$innslag 		= $videresend_til->getInnslag()->get( $_POST['innslag'] );
$person 		= $innslag->getPersoner()->get( $_POST['person'] );

$innslag->getPersoner()->fjern( $person );
write_person::fjern( $person );

if( UKMVideresending::getFra()->getType() == 'fylke' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);