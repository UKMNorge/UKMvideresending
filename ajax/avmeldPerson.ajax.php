<?php

use UKMNorge\Innslag\Personer\Write;

require_once('UKM/Autoloader.php');

$videresend_til = UKMVideresending::getValgtTil('POST');
$innslag 		= $videresend_til->getInnslag()->get( $_POST['innslag'] );
$person 		= $innslag->getPersoner()->get( $_POST['person'] );

$innslag->getPersoner()->fjern( $person );
Write::fjern( $person );

UKMVideresending::beregnAntallVideresendtePersoner();

UKMVideresending::addResponseData('success',true);