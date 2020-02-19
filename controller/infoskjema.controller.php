<?php

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
$skjema = $fra->getSkjema();
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$questions = array();
	
	foreach($_POST as $key => $value) {
		if (strpos($key, 'question_') === 0) {
			// value starts with book_
			$str = explode('_', $key); // $str[0] = "question", $str[1] == id, $str[2] (if any) == "navn"/"mobil"/"epost"
			$q_id = $str[1];
	
			if(count($str) > 2) {
				$questions[$q_id][$str[2]] = $value;
			} else {
				$questions[$q_id] = $value;
			}
		}
	}
	
	$results = array();
	
	$numQ = 0;
	foreach ($questions as $q_id => $answer ) {
		$res = $skjema->answerQuestion($q_id, $answer, $debug );
		if( !is_numeric( $res ) ) {
			$errors[] = $res->error();
		}
		$numQ++;
	}
	
	if ( count( $errors ) == 0 ) {
        UKMVideresending::getFlash()
            ->success('Skjema er lagret!');
	}
	else {
        UKMVideresending::getFlash()
            ->error('Ett eller flere av svarene dine ble ikke lagret. Prøv igjen.');
	}
}

UKMVideresending::addViewData(
    [
        'skjema'=> $skjema,
        'til' => $til
    ]
);