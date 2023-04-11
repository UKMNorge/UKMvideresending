<?php

use UKMNorge\Allergener\Allergener;
use UKMNorge\Arrangement\Videresending\Ledere\Ledere;

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

$ledere_intoleranse = new stdClass();
$ledere_intoleranse->med = [];
$ledere_intoleranse->uten = [];
$ledere = new Ledere($fra->getId(), $til->getId());
$ledere_list = [];
foreach($ledere->getAll() as $leder){ 
    if( in_array( $leder->getId(), $ledere_list ) ) {
        continue;
    }
    $ledere_list[] = $leder->getId();
    
    $id = $leder->getNavn() .'-'. $leder->getId();
    $allergi = $leder->getSensitivt( $requester )->getIntoleranse();
    var_dump($leder->getNavn());
    if($leder->getNavn() != null) {
        if( $allergi->har() ) {
            $ledere_intoleranse->med[ $id ] = UKMVideresending::getIntoleranseLederData( $leder, $allergi );
        } else {
            $ledere_intoleranse->uten[ $id ] = UKMVideresending::getIntoleranseLederData( $leder );
        }
    }
}


UKMVideresending::addViewData('personer', $data_intoleranse);
UKMVideresending::addViewData('ledere', $ledere_intoleranse);
UKMVideresending::addViewData('allergener_standard', Allergener::getStandard());
UKMVideresending::addViewData('allergener_kulturelle', Allergener::getKulturelle());