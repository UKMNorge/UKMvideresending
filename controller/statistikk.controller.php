<?php
if( isset($_POST) and sizeof( $_POST ) > 0 ) {
	require_once('UKM/write_monstring.class.php');
	$monstring = UKMVideresending::getFra();
	
	$monstring->setUregistrerte( $_POST['pl_missing'] );
	$monstring->setPublikum( $_POST['pl_public'] );
	write_monstring::save( $monstring );
	
	UKMVideresending::addViewData(
		'message',
		[
			'success' => true,
			'body' => 'Publikum og uregistrerte er nå lagret'
		]
	);
}