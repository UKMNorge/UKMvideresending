<?php

$videresendte_innslag = [];
$monstring = UKMVideresending::getFra();

// Alle typer innslag som er støttet
$innslag_typer = [];
if( $monstring->getType() == 'fylke' ) {
	$festivalen = array_pop( UKMVideresending::getTil() );
	foreach( $festivalen->getInnslagTyper() as $innslag_type ) {
			$innslag_typer[ $innslag_type->getKey() ] = $innslag_type;
		}
} else {
	foreach( $monstring->getFylkesmonstringer() as $fylkesmonstring ) {
		foreach( $monstring->getInnslagTyper() as $innslag_type ) {
			$innslag_typer[ $innslag_type->getKey() ] = $innslag_type;
		}
	}
}

// Finn alle videresendte innslag
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	
	// Fra fylke til land
	if( $monstring->getType() == 'fylke' ) {
		if( !$innslag->erVideresendtTil( $festivalen ) ) {
			continue;
		}
	}
	// Fra lokal til fylke
	else {
		// "Videresendt at all" er godt nok
		if( !$innslag->erVideresendt() ) {
			continue;
		}
	}

	$videresendte_innslag[] = $innslag;
}

UKMVideresending::addViewData('innslag_typer', $innslag_typer );
UKMVideresending::addViewData('videresendte', $videresendte_innslag );
#UKMVideresending::addViewData('fra', $monstring);