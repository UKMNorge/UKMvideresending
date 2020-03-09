<?php

use UKMNorge\Innslag\Typer\Typer;
require_once('UKM/Autoloader.php');

$fra = UKMVideresending::getFra();	
$til = UKMVideresending::getValgtTil();	

// INITIER SUM-OBJEKT FOR MØNSTRINGEN
$sum = 
    [
        'personer' => [],
        'scene' => [
            'innslag' 	=> 0,
            'personer'	=> 0,
            'titler'	=> 0,
            'varighet'	=> 0,
            'mangler_personer' => false,
        ],
        'total' => [
            'innslag' 	=> 0,
            'personer'	=> 0,
            'titler'	=> 0,
            'varighet'	=> 0,
            'unike'		=> 0,
        ],
    ];

foreach( $fra->getVideresendte( $til->getId() )->getAll() as $innslag_sendt ) {
    // Reload innslag med riktig context, for å få riktig personer og titler
    $innslag = $til->getInnslag()->get($innslag_sendt->getId());

    // SET SORT-KEY FOR DETTE INNSLAGET (ALTSÅ HVOR LAGRES SUMMEN?)
    $sort_key = $innslag->getType()->getKey() == 'scene' ? 'annet' : $innslag->getType()->getKey();
    
    // INITIER DATA-ARRAY
    if( !isset( $sum[ $sort_key ] ) ) {
        $sum[ $sort_key ] = [
            'innslag'	=> 0,
            'personer'	=> 0,
            'titler'	=> 0,
            'varighet'	=> 0,
            'mangler_personer' => false,
        ];
    }
    
    // INNSLAG
    $sum[ $sort_key ]['innslag'] += 1;
    
    // PERSONER
    $antall_personer = 0;
    foreach( $innslag->getPersoner()->getAll() as $person ) {
        $antall_personer++;
        $sum[ $sort_key ]['personer'] ++;
        $sum['personer'][] = $person->getId();
    }
    if( $antall_personer == 0 ) {
        $sum[ $sort_key ]['mangler_personer'] = true;
    }
    
    // VARIGHET
    if( $innslag->getType()->harTitler() ) {
        $sum[ $sort_key ]['titler'] 	+= $innslag->getTitler()->getAntall();
        $sum[ $sort_key ]['varighet'] 	+= $innslag->getVarighet()->getSekunder();
    }
}

/**
 * SUMMER OPP KATEGORIENE OG TOTALEN
**/
// SUMMER ALLE SCENE-KATEGORIER TIL EN SCENE-VARIABEL
foreach( Typer::getAllScene() as $scene_kategori ) {
    $sort_key = $scene_kategori->getKey() == 'scene' ? 'annet' : $scene_kategori->getKey();
    if( is_array( $sum[ $sort_key ] ) ) {
        foreach( $sum[ $sort_key ] as $key => $val ) {
            $sum['scene'][ $key ] += $val;
        }
    }
}

// SUMMER ALT TIL EN TOTAL, BRUK DISSE KATEGORIENE
$summeres = [
    'scene',
    'utstilling',
    'video',
    'arrangor',
    'konferansier',
    'nettredaksjon'
];

foreach( $summeres as $kategori ) {
    if( isset( $sum[ $kategori ] ) ) {
        foreach( $sum[ $kategori ] as $key => $val ) {
            $sum['total'][ $key ] += $val;
        }
    }
}

// HVIS PERSONER ER VIDERESENDT, KALKULER ANTALL UNIKE
if( is_array( $sum['personer'] ) ) {
    $sum['total']['unike'] = sizeof( array_unique( $sum['personer'] ) );
}

if( $til->getArrangement()->getType() == 'land' ) {
    UKMVideresending::addviewData('videresendte', $sum);

    $kvote = new stdClass();
    $kvote->deltakere = intval($til->getArrangement()->getMetaValue('kvote_deltakere'));
    $kvote->ledere = intval($til->getArrangement()->getMetaValue('kvote_ledere'));
    $kvote->total = $kvote->deltakere + $kvote->ledere;

    $pris = new stdClass();
    $pris->subsidiert = intval($til->getArrangement()->getMetaValue('avgift_subsidiert'));
    $pris->ordinar = intval($til->getArrangement()->getMetaValue('avgift_ordinar'));
    $pris->reise = intval($til->getArrangement()->getMetaValue('avgift_reise'));

    UKMVideresending::addViewData('kvote', $kvote);
    UKMVideresending::addViewData('pris', $pris);
#    require_once('ledere.controller.php');

}

UKMVideresending::addViewData('summering', $sum);