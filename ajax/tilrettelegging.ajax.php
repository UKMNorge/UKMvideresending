<?php

// SETUP SENSITIVT-REQUESTER
require_once('UKM/Sensitivt/Sensitivt.php');

UKMNorge\Sensitivt\Sensitivt::setRequester(
    new UKMNorge\Sensitivt\Requester(
        'wordpress', 
        wp_get_current_user()->ID,
        get_option('pl_id')
    )
);


// SET DATA
require_once('UKM/Sensitivt/Write/Intoleranse.php');
$intoleranse = new UKMNorge\Sensitivt\Write\Intoleranse( $_POST['id'] );

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
