<?php

// Innslaget	

use UKMNorge\Database\SQL\Delete;
use UKMNorge\Database\SQL\Insert;

$fra = UKMVideresending::getFra();
$innslag = $fra->getInnslag()->get( $_POST['innslag'] );


// Sjekk at bildet tilhører innslaget
$valgt_bilde = false;
foreach( $innslag->getBilder()->getAll() as $bilde ) {
	if( $bilde->getId() == $_POST['bilde'] ) {
		$valgt_bilde = $bilde;
	}
}
if( !$valgt_bilde ) {
	throw new Exception('Kunne ikke finne valgt bilde!');
}

// Kun for kunstverkene trenger vi tittelen på et bilde
if( isset( $_POST['kunstverk'] ) && $_POST['kunstverk'] == 'true' ) {
	$tittel 	= $_POST['tittel'] == 'false' ? 0 : $_POST['tittel'];
} else { 
	$tittel = 0;
}

// Håndter lagring av 'bilde_kunstner'
if( $innslag->getType()->getKey() == 'utstilling' && $tittel == 0 ) {
	$type = 'bilde_kunstner';
} else {
	$type = 'bilde';
}


$data = [
//	'pl_id'		=> $monstring->getId(),
	'b_id'		=> $innslag->getId(),
	't_id'		=> $tittel,
];

// Slett gammel relasjon
$old_rel = new Delete(
	'ukm_videresending_media',
	$data
);
$old_rel->run();

// Legg til ny data
$data['rel_id']	= $valgt_bilde->getRelId();//$_POST['bilde'];
$data['bilde_id'] = $valgt_bilde->getId();
$data['m_type']	= $type;
$data['pl_id'] = $fra->getId();


// Sett inn ny relasjon
$new_rel = new Insert('ukm_videresending_media');
foreach( $data as $key => $val ) {
	$new_rel->add( $key, $val );
}
$new_rel->run();

UKMVideresending::addResponseData('bilde', $valgt_bilde);
UKMVideresending::addResponseData('success', true);