<?php

$monstring = UKMVideresending::getFra();
$festivalen = array_pop( UKMVideresending::getTil() );

// SLETT ALL FRA DENNE MØNSTRINGEN
$SQLdel = new SQLdel(
	'smartukm_videresending_ledere_nattleder', 
	[
		'pl_id_from' => $monstring->getId()
	]
);
$SQLdel->run();


foreach( $_POST as $data ) {
	if( isset( $data['name'] ) && isset( $data['value'] ) ) {
		$dato = str_replace('hovedleder-', '', $data['name']);
		$leder = $data['value'];
		$SQL = new SQLins('smartukm_videresending_ledere_nattleder');
		$SQL->add('l_id', $leder);
		$SQL->add('dato', $dato );
		$SQL->add('pl_id_from', $monstring->getId());
		$SQL->run();
	}
}

UKMVideresending::addResponseData('success',true);