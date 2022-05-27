<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

require_once('UKM/Autoloader.php');
	
foreach( $_POST as $data ) {
	if( isset( $data['name'] ) && isset( $data['value'] ) ) {
		$_FORM[ $data['name'] ] = $data['value'];
	}
}

$leder = Leder::getById( intval($_POST['leder']) );
$leder->setType( $_FORM['leder_type'] );
$leder->setNavn( $_FORM['leder_navn']);
$leder->setMobil( intval( str_replace(' ', '', $_FORM['leder_mobil'] ) ) );
$leder->setEpost( $_FORM['leder_epost']);

// Hvis type er endret til sykerom, fjern alle overnattinger som ikke er hotell
if($_FORM['leder_type'] == 'sykerom') {
	foreach($leder->getNetter()->getAll() as $natt) {
		if($natt->getSted() != 'hotell') {
			Write::deleteNatt($natt);
		}
	}
}

$leder = Write::save( $leder );

UKMVideresending::addResponseData('success', true);