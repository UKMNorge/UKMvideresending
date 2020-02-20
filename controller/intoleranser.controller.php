<?php

use UKMNorge\Allergener\Allergener;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

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
foreach( $fra->getVideresendte($til->getId())->getAll() as $innslag ) {
    foreach( $innslag->getPersoner()->getAll() as $person ) {
		
		if( in_array( $person->getId(), $personer ) ) {
			continue;
        }
		$personer[] = $person->getId();
        
        $id = $person->getNavn() .'-'. $person->getId();
        $allergi = $person->getSensitivt( $requester )->getIntoleranse();
        if( $allergi->har() ) {
			$data_intoleranse->med[ $id ] = UKMVideresending::getIntoleransePersonData( $person, $allergi );
        } else {
            $data_intoleranse->uten[ $id ] = UKMVideresending::getIntoleransePersonData( $person );
        }
    }
}

ksort($data_intoleranse->med);
ksort($data_intoleranse->uten);

UKMVideresending::addViewData('personer', $data_intoleranse);
UKMVideresending::addViewData('allergener_standard', Allergener::getStandard());
UKMVideresending::addViewData('allergener_kulturelle', Allergener::getKulturelle());