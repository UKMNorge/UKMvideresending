<?php

use UKMNorge\Allergener\Allergener;

$fra = UKMVideresending::getFra();

// SETUP SENSITIVT-REQUESTER
$requester = new UKMNorge\Sensitivt\Requester(
    'wordpress', 
    wp_get_current_user()->ID,
    get_option('pl_id')
);
UKMNorge\Sensitivt\Sensitivt::setRequester( $requester );

$data_intoleranse = new stdClass();
$data_intoleranse->med = [];
$data_intoleranse->uten = [];

// LIST ALLE ALLERGIER
$personer = [];
foreach( $fra->getInnslag()->getAll() as $innslag ) {
    foreach( $innslag->getPersoner()->getAll() as $person ) {
		
		if( in_array( $person->getId(), $personer ) ) {
			continue;
		}
		$personer[] = $person->getId();

        $allergi = $person->getSensitivt( $requester )->getIntoleranse();
        if( $allergi->har() ) {
			$data_intoleranse->med[] = person_data( $person, $allergi );
        } else {
            $data_intoleranse->uten[] = person_data( $person, false );
        }
    }
}

UKMVideresending::addViewData('personer', $data_intoleranse);

UKMVideresending::addViewData('allergener_standard', Allergener::getStandard());
UKMVideresending::addViewData('allergener_kulturelle', Allergener::getKulturelle());

function person_data( $person, $allergi ) {
	$data = new stdClass();
	$data->ID = $person->getId();
	$data->navn = $person->getNavn();
	if( $allergi ) {
		$data->intoleranse_liste = $allergi->getListe();
		$data->intoleranse_human = $allergi->getListeHuman();
		$data->intoleranse_tekst = $allergi->getTekst();
	}

	return $data;
}