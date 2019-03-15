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
$res = $intoleranse->saveTekst( $_POST['tekst'] );

if( $res ) {
	$res = $intoleranse->saveListe( $_POST['liste'] );
}

$infos = [
    'id' => $_POST['id'],
    'navn' => $_POST['navn'],
    'intoleranse' => $_POST['tekst']
];


UKMVideresending::addResponseData('success', $res);
UKMVideresending::addResponseData('data', $infos);


/*
TODO:
håndter oppdatering til samme verdi (affected_rows == 0)
håndter tilbakemelding i view
vurder om saveTekst og saveListe er riktig format
*/
