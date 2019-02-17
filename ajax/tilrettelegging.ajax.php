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
$res = $intoleranse->setTekst( $_POST['intoleranse'] );

$infos = [
    'id' => $_POST['id'],
    'navn' => $_POST['navn'],
    'intoleranse' => $_POST['intoleranse']
];


UKMVideresending::addResponseData('success', $res);
UKMVideresending::addResponseData('data', $infos);