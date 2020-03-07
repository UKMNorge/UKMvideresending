<?php

use UKMNorge\Innslag\Nominasjon\Write;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

$innslag = $fra->getInnslag()->get( intval($_GET['id']) );
$nominasjon = $innslag->getNominasjoner()->getTil( $til->getId() );

if( !$nominasjon->eksisterer() ) {
    $nominasjon = Write::create($innslag, $fra->getId(), $til->getId());
}

try {
    switch( $innslag->getType()->getKey() ) {
        case 'nettredaksjon':
        case 'media':
            $nominasjon->setSamarbeid( $_POST['samarbeid'] );
            $nominasjon->setErfaring( $_POST['erfaring'] );
            break;
            
        case 'konferansier':
            $nominasjon->setHvorfor( $_POST['hvorfor'] );
            $nominasjon->setBeskrivelse( $_POST['beskrivelse'] );
            $nominasjon->setFilPlassering( $_POST['filopplasting'] );
            $nominasjon->setFilUrl( $_POST['url'] );
            break;
            
        case 'arrangor':
            $nominasjon->setVoksenErfaring( $_POST['voksen-erfaring'] );
            $nominasjon->setVoksenSamarbeid( $_POST['voksen-samarbeid'] );
            $nominasjon->setVoksenAnnet( $_POST['voksen-annet'] );
            break;
    }

    Write::save( $nominasjon );

    $voksen = Write::createVoksen( $nominasjon->getId() );
    $voksen->setNavn( $_POST['voksen-navn'] );
    $voksen->setMobil( intval($_POST['voksen-mobil']) );
    Write::saveVoksen( $voksen );

    $nominasjon->setVoksen($voksen);

    Write::saveState( $nominasjon, true );

    UKMVideresending::getFlash()->success(
        'Lagret nominasjon for '. 
        '<a href="#innslag_'. $innslag->getId() .'">'. $innslag->getNavn() .'</a>'
    );
} catch( Exception $e ) {
    UKMVideresending::getFlash()->error(
        'Kunne ikke lagre nominasjon for '. 
        '<a href="#innslag_'. $innslag->getId() .'">'. $innslag->getNavn() .'</a>, '.
        'vennligst prøv igjen. '.
        'Systemet sa: '. $e->getMessage() .' ('. $e->getCode() .')'
    );
    echo '<pre>';
    debug_print_backtrace();
    echo '</pre>';
}
