<?php
	
require_once('UKM/leder.class.php');
	
foreach( $_POST as $data ) {
	if( isset( $data['name'] ) && isset( $data['value'] ) ) {
		$_FORM[ $data['name'] ] = $data['value'];
	}
}
	
$leder = new leder( $_POST['leder'] );
$leder->set('l_type', $_FORM['leder_type']);
$leder->set('l_navn', utf8_encode( $_FORM['leder_navn'] ) );
$leder->set('l_mobilnummer', str_replace(' ','', $_FORM['leder_mobil'] ) );
$leder->set('l_epost', $_FORM['leder_epost']);

$res = $leder->update();

UKMVideresending::addResponseData('success', $res);