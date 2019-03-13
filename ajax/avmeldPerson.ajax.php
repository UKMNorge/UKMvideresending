<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/write_person.class.php');

/**
 * loadValgtTil() avhenger av hvilket fylke som er valgt.
 * For fellesmønstringer på tvers av fylkesgrenser
 * trengs dette parameteret for å videresende til
 * riktig fylkesfestival.
 */
if( isset( $_POST['fylke'] ) ) {
	$_GET['fylke'] = $_POST['fylke'];
}

$videresend_til = UKMVideresending::loadValgtTil();
$innslag 		= $videresend_til->getInnslag()->get( $_POST['innslag'] );
$person 		= $innslag->getPersoner()->get( $_POST['person'] );

$innslag->getPersoner()->fjern( $person );
write_person::fjern( $person );

if( UKMVideresending::getFra()->getType() == 'fylke' ) {
	UKMVideresending::calcAntallPersoner();
}
UKMVideresending::addResponseData('success',true);