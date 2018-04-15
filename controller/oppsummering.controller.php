<?php

$summering = [];
foreach( UKMVideresending::getTil() as $monstring ) {
	
	/**
	 * SETT OPP MØNSTRINGS-OBJEKTET
	 * Lag et array for summering
	 * Finn ut hvilke kommuner denne mønstringen har
	**/

	// INITIER SUM-OBJEKT FOR MØNSTRINGEN
	$sum_monstring = 
		[
			'personer' => [],
			'scene' => [
				'innslag' 	=> 0,
				'personer'	=> 0,
				'titler'	=> 0,
				'varighet'	=> 0,
			],
			'total' => [
				'innslag' 	=> 0,
				'personer'	=> 0,
				'titler'	=> 0,
				'varighet'	=> 0,
				'unike'		=> 0,
			],
		];

	// HVILKE KOMMUNER SKAL INKLUDERES I DENNE OVERSIKTEN
	if( UKMVideresending::getFra()->getType() == 'kommune' ) {
		$kommuner = [];
		foreach(  UKMVideresending::getFra()->getKommuner() as $kommune ) {
			$kommuner[] = $kommune->getId();
		}
	}
	
	/**
	 * LOOP ALLE INNSLAG I MOTTAKER-MØNSTRINGEN
	 * Tell kun de som er fra kommunene i denne mønstringen (array $kommuner)
	**/
	foreach( $monstring->getInnslag()->getAll() as $innslag ) {
		// KUN VIS INNSLAGENE SOM KOMMER FRA DENNE MØNSTRINGEN
		if( UKMVideresending::getFra()->getType() == 'kommune' && !in_array( $innslag->getKommune()->getId(), $kommuner ) ) {
			continue;
		}
		
		// SET SORT-KEY FOR DETTE INNSLAGET (ALTSÅ HVOR LAGRES SUMMEN?)
		$sort_key = $innslag->getType()->getKey() == 'scene' ? 'annet' : $innslag->getType()->getKey();
		
		// INITIER DATA-ARRAY
		if( !isset( $sum_monstring[ $sort_key ] ) ) {
			$sum_monstring[ $sort_key ] = [
				'innslag'	=> 0,
				'personer'	=> 0,
				'titler'	=> 0,
				'varighet'	=> 0,
			];
		}
		
		// INNSLAG
		$sum_monstring[ $sort_key ]['innslag'] += 1;
		
		// PERSONER
		foreach( $innslag->getPersoner()->getAllVideresendt( $monstring ) as $person ) {
			$sum_monstring[ $sort_key ]['personer'] ++;
			$sum_monstring['personer'][] = $person->getId();
		}
		
		// VARIGHET
		if( $innslag->getType()->harTitler() ) {
			$sum_monstring[ $sort_key ]['titler'] 	+= $innslag->getTitler()->getAntall();
			$sum_monstring[ $sort_key ]['varighet'] 	+= $innslag->getVarighet()->getSekunder();
		}
	}
	
	/**
	 * SUMMER OPP KATEGORIENE OG TOTALEN
	**/
	// SUMMER ALLE SCENE-KATEGORIER TIL EN SCENE-VARIABEL
	foreach( innslag_typer::getAllScene() as $scene_kategori ) {
		$sort_key = $scene_kategori->getKey() == 'scene' ? 'annet' : $scene_kategori->getKey();
		if( is_array( $sum_monstring[ $sort_key ] ) ) {
			foreach( $sum_monstring[ $sort_key ] as $key => $val ) {
				$sum_monstring['scene'][ $key ] += $val;
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
		if( isset( $sum_monstring[ $kategori ] ) ) {
			foreach( $sum_monstring[ $kategori ] as $key => $val ) {
				$sum_monstring['total'][ $key ] += $val;
			}
		}
	}
	
	// HVIS PERSONER ER VIDERESENDT, KALKULER ANTALL UNIKE
	if( is_array( $sum_monstring['personer'] ) ) {
		$sum_monstring['total']['unike'] = sizeof( array_unique( $sum_monstring['personer'] ) );
	}
	
	$summering[ $monstring->getId() ] = $sum_monstring;
}

if( $monstring->getType() == 'land' ) {
	UKMVideresending::addviewData('videresendte', $sum_monstring);
}
UKMVideresending::addViewData('summering', $summering);

#var_dump( $sum_monstring );

$kvote = new stdClass();
$kvote->deltakere = 30;
$kvote->ledere = 3;
$kvote->total = $kvote->deltakere + $kvote->ledere;

$pris = new stdClass();
$pris->subsidiert = 1300;
$pris->ordinar = 1800;
$pris->reise = 1500;

UKMVideresending::addViewData('kvote', $kvote);
UKMVideresending::addViewData('pris', $pris);

require_once('ledere.controller.php');