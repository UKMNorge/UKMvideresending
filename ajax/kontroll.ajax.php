<?php

// Mønstring det videresendes til

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Oppgave\Oppgave;
use UKMNorge\Database\SQL\Query;

$til        = UKMVideresending::getValgtTil('POST')->getArrangement();
$fra        = new Arrangement( intval(get_option('pl_id')) );
$innslag    = $fra->getInnslag()->get( intval($_POST['innslag']) );

// Basis-data
$data = [
    'har_titler'			=> $innslag->getType()->harTitler(),
	'er_enkelperson'		=> $innslag->getType()->erEnkeltPerson(),
	'har_tekniske'			=> $innslag->getType()->harTekniskeBehov(),
	'type_id' 				=> $innslag->getType()->getId(),
	'type_key' 				=> $innslag->getType()->getKey(),
	'innslag_navn' 			=> $innslag->getNavn(),
    'innslag_id' 			=> $innslag->getId(),
    'til_navn'              => $til->getNavn(),
	'til_id' 				=> $til->getId(),
	'har_nominasjon'		=> count($innslag->getVideresendingNominasjonTil( $til->getId() )) > 0,
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
		$personNominasjon = $innslag->getPersonNominasjon($person->getId(), $til->getId());

		$person = [
			'id'			=> $person->getId(),
			'navn'			=> $person->getNavn(),
			'mobil'			=> $person->getMobil(),
			'alder'			=> $person->getAlderTall(),
			'instrument'	=> $person->getRolle(),
			'videresendt'	=> $person->erPameldt( $til->getId() ),
			'har_nominasjon'    => $personNominasjon != null,
			'nominasjon_status' => $personNominasjon != null ? $personNominasjon->getStatus() : null,
			'nominasjon_sporsmal'	=> $personNominasjon != null ? $personNominasjon->getSporsmal() : null,
			'nominasjon_svar'		=> $personNominasjon != null ? $personNominasjon->getSvar() : null,
		];
		$data['personer'][]		= $person;
	}
}
// Har tittelløs men har flere personer
else if(!$innslag->getType()->erEnkeltPerson()) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$personNominasjon = $innslag->getPersonNominasjon($person->getId(), $til->getId());

		$person = [
			'id'			=> $person->getId(),
			'navn'			=> $person->getNavn(),
			'mobil'			=> $person->getMobil(),
			'alder'			=> $person->getAlderTall(),
			'instrument'	=> $person->getRolle(),
			'videresendt'	=> $person->erPameldt( $til->getId() ),
			'har_nominasjon'    => $personNominasjon != null,
			'nominasjon_status' => $personNominasjon != null ? $personNominasjon->getStatus() : null,
			'nominasjon_sporsmal'	=> $personNominasjon != null ? $personNominasjon->getSporsmal() : null,
			'nominasjon_svar'		=> $personNominasjon != null ? $personNominasjon->getSvar() : null,
		];
		$data['personer'][]		= $person;
	}
}
// Tittelløse innslag
else {
	$person = $innslag->getPersoner()->getSingle();
	$personNominasjon = $innslag->getPersonNominasjon($person->getId(), $til->getId());

	$data['person'] = [
		'id'			=> $person->getId(),
		'navn'			=> $person->getNavn(),
		'mobil'			=> $person->getMobil(),
		'alder'			=> $person->getAlderTall(),
		'instrument'	=> $person->getRolle(),
		'videresendt'	=> $person->erPameldt( $til->getId() ),
		'har_nominasjon'    => $personNominasjon != null,
		'nominasjon_status' => $personNominasjon != null ? $personNominasjon->getStatus() : null,
		'videresending_beskrivelse' => $personNominasjon != null ? $personNominasjon->getBeskrivelse() : '',
		'nominasjon_sporsmal'	=> $personNominasjon != null ? $personNominasjon->getSporsmal() : null,
		'nominasjon_svar'		=> $personNominasjon != null ? $personNominasjon->getSvar() : null,

	];
}


if(Oppgave::getAllByArrangementVideresending($til->getId()) > 0) {
	$oppgave = Oppgave::getAllByArrangementVideresending($til->getId())[0];
	
	if($data['personer'] && count($data['personer']) > 0) {
		foreach($data['personer'] as &$person) {
			if($person['mobil'] && $person['id']) {
				$deltaUserId = getDeltaUserIdByMobil($person['mobil']);
				if(!$deltaUserId) {
					$person['videresending_oppgave_status'] = -1;
				}else {
					$person['videresending_oppgave_status'] = $oppgave->getOppgaveBesvartStatus($deltaUserId, $person['id']);
				}
			}
			else {
				$person['videresending_oppgave_status'] = 0;
			}
		}
	}
	else if($data['person']) {
		if($data['person']['mobil'] && $data['person']['id']) {
			$deltaUserId = getDeltaUserIdByMobil($data['person']['mobil']);
			if(!$deltaUserId) {
				$data['person']['videresending_oppgave_status'] = -1;
			}else {
				$data['person']['videresending_oppgave_status'] = $oppgave->getOppgaveBesvartStatus($deltaUserId, $data['person']['id']);
			}
		}
		else {
			$data['person']['videresending_oppgave_status'] = 0;
		}
	}
}

function getDeltaUserIdByMobil($phone) {
	if($phone) {	
		$sql = new Query(
			"SELECT id from ukm_user WHERE phone = '#phone'",
			['phone' => $phone],
			'ukmdelta'
		);
		
		$res = $sql->run('array');
		return $res['id'];
	}

	return null;
}

	
UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('data', $data);