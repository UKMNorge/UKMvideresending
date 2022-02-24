<?php

use UKMNorge\Arrangement\Skjema\Sporsmal;
use UKMNorge\Arrangement\Skjema\SvarSett;
use UKMNorge\Arrangement\Skjema\Write as WriteSkjema;
use UKMNorge\Twig\Twig;


/**
 * Legger til Twig-path for UKMmønstring-modulen.
 * Dette for å kunne rendre info-skjema, som administreres derfra,
 * og har templates og kontrollere der.
 */
require_once('UKM/Twig/Twig.php');
Twig::addPath(UKMmonstring::getTwigPath());

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil()->getArrangement();
$skjemaTil = $til->getSkjema();


$skjemaFra = null;

try{
    $skjemaFra = $fra->getSkjema();
}catch(Exception $e) {
    // Opprett Skjema hvis det ikke er opprettet
    if($e->getCode() == 151002) {
        $skjemaFra = WriteSkjema::createForArrangement($fra);
    }
}


// Henter Svarsett fra Svarsett klasse
$svarsett = SvarSett::getPlaceholder('arrangement', $fra->getId(), $skjemaFra->getId());
$svarsett->getAll();

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	foreach($_POST as $key => $value) {
		if (strpos($key, 'sporsmal_') === 0) {
            list( $trash, $id, $field) = explode('_', $key);
            $svarsett->setSvar($id, $value );
		}
	}

    try {
        WriteSkjema::saveSvarSett( $svarsett );
        UKMVideresending::getFlash()->success('Skjema er lagret!');
    } catch( Exception $e ) {
        UKMVideresending::getFlash()
            ->error(
                'Ett eller flere av svarene dine ble ikke lagret. Vennligst prøv igjen. ' 
                ."\r\n".
                'Systemet sa: '. $e->getMessage()
            );
	}
}

UKMVideresending::addViewData(
    [
        'skjema'=> $skjema,
        'til' => $til,
        'svarsett' => $svarsett
    ]
);