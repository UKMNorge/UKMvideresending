<?php

// Mønstring det videresendes til

use UKMNorge\Arrangement\Arrangement;

$til        = UKMVideresending::getValgtTil('POST')->getArrangement();
$fra        = new Arrangement( intval(get_option('pl_id')) );
$innslag    = $fra->getInnslag()->get( intval($_POST['innslag']) );

// Basis-data
$data = [
    'har_titler'			=> $innslag->getType()->harTitler(),
	'har_tekniske'			=> $innslag->getType()->harTekniskeBehov(),
	'type_id' 				=> $innslag->getType()->getId(),
	'type_key' 				=> $innslag->getType()->getKey(),
	'innslag_navn' 			=> $innslag->getNavn(),
    'innslag_id' 			=> $innslag->getId(),
    'til_navn'              => $til->getNavn(),
	'til_id' 				=> $til->getId(),
];


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
	
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$person = [
			'id'			=> $person->getId(),
			'navn'			=> $person->getNavn(),
			'mobil'			=> $person->getMobil(),
			'alder'			=> $person->getAlderTall(),
			'instrument'	=> $person->getRolle(),
			'videresendt'	=> $person->erPameldt( $til->getId() )
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
		'videresendt'	=> $person->erPameldt( $til->getId() )
	];
}


	
UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('data', $data);