<?php

use UKMNorge\Sensitivt\Requester;
use UKMNorge\Sensitivt\Sensitivt;
use UKMNorge\Sensitivt\Write\Intoleranse;
use UKMNorge\Sensitivt\Write\LederIntoleranse as WriteLederIntoleranse;


Sensitivt::setRequester(
    new Requester(
        'wordpress', 
        wp_get_current_user()->ID,
        get_option('pl_id')
    )
);

// Sjek hvis brukeren er leder
if(isset($_POST['is_leder']) && $_POST['is_leder'] == 'true') {
    $intoleranse = new WriteLederIntoleranse( $_POST['id'] );
}
else {
    $intoleranse = new Intoleranse( $_POST['id'] );
}

if( !is_array( $_POST['liste'] ) ) {
	$_POST['liste'] = [];
}

$res1 = $intoleranse->saveTekst( $_POST['tekst'] );
$res2 = $intoleranse->saveListe( $_POST['liste'] );

$infos = [
    'id' => $_POST['id'],
    'navn' => $_POST['navn'],
	'intoleranse_liste' => $intoleranse->getListe(),
	'intoleranse_human' => $intoleranse->getListeHuman(),
	'intoleranse_tekst' => $intoleranse->getTekst()
];


UKMVideresending::addResponseData('success', ($res1 && $res2));
UKMVideresending::addResponseData('data', $infos);


/*
TODO:
håndter tilbakemelding i view
*/
