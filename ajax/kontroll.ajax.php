<?php
require_once('UKM/innslag.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

// Basis-data
$data = [
	'har_titler'			=> $innslag->getType()->harTitler(),
	'har_tekniske'			=> $innslag->getType()->harTekniskeBehov(),
	'type_id' 				=> $innslag->getType()->getId(),
	'type_key' 				=> $innslag->getType()->getKey(),
	'innslag_navn' 			=> $innslag->getNavn(),
	'innslag_id' 			=> $innslag->getId(),
];

// Mønstring det videresendes til
$valgt_til = UKMVideresending::loadValgtTil('POST');

// Innslag med titler
if( $innslag->getType()->harTitler() ) {
	$tittel = $innslag->getTitler()->get( $_POST['id'] );
	$data['innslag_sjanger'] 	= $innslag->getSjanger();
	$data['tekniske_behov']		= $innslag->getTekniskeBehov();
	$data['tittel_navn']		= $tittel->getTittel();
	
	// Innslagets varighet (eller utstilling)
	if( $innslag->getType()->getKey() == 'utstilling' ) {
		$data['har_varighet']	= false;
		$data['tittel_type']	= $tittel->getType();
	} else {
		$data['har_varighet']	= true;
		$data['varighet']		= $tittel->getVarighet()->getSekunder();
	}
	
	// Personer som følger innslaget
	if( $innslag->getType()->getId() == 1 ) {
		$data['alle_personer']	= true;
	} else {
		$data['alle_personer']	= false;
	}
	
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$person = [
			'id'			=> $person->getId(),
			'navn'			=> $person->getNavn(),
			'mobil'			=> $person->getMobil(),
			'alder'			=> $person->getAlderTall(),
			'instrument'	=> $person->getRolle(),
			'videresendt'	=> $person->erPameldt( $valgt_til->getId() )
		];
		$data['personer'][]		= $person;
	}
}
// Tittelløse innslag
else {
	$person = $innslag->getPersoner()->getSingle();

	$data['person'] = [
		'id'			=> $person->getId(),
		'navn'			=> $person->getNavn(),
		'mobil'			=> $person->getMobil(),
		'alder'			=> $person->getAlderTall(),
		'instrument'	=> $person->getRolle(),
		'videresendt'	=> $person->erPameldt( $valgt_til->getId() )
	];
}


	
UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('data', $data);