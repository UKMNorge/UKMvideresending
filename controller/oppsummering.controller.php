<?php

use UKMNorge\Innslag\Typer\Typer;
require_once('UKM/Autoloader.php');

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
		elseif( UKMVideresending::getFra()->getType() == 'fylke' && $innslag->getFylke()->getId() != UKMVideresending::getFra()->getFylke()->getId() ) {
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
				'mangler_personer' => false,
			];
		}
		
		// INNSLAG
		$sum_monstring[ $sort_key ]['innslag'] += 1;
		
		// PERSONER
		$antall_personer = 0;
		foreach( $innslag->getPersoner()->getAllVideresendt( $monstring ) as $person ) {
			$antall_personer++;
			$sum_monstring[ $sort_key ]['personer'] ++;
			$sum_monstring['personer'][] = $person->getId();
		}
		if( $antall_personer == 0 ) {
			$sum_monstring[ $sort_key ]['mangler_personer'] = true;
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
	foreach( Typer::getAllScene() as $scene_kategori ) {
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

	$kvote = new stdClass();
	$kvote->deltakere = get_site_option('UKMFvideresending_kvote_deltakere'.'_'.$monstring->getSesong());
	$kvote->ledere = get_site_option('UKMFvideresending_kvote_ledere'.'_'.$monstring->getSesong());
	$kvote->total = $kvote->deltakere + $kvote->ledere;
	
	$pris = new stdClass();
	$pris->subsidiert = get_site_option('UKMFvideresending_avgift_subsidiert'.'_'.$monstring->getSesong());
	$pris->ordinar = get_site_option('UKMFvideresending_avgift_ordinar'.'_'.$monstring->getSesong());;
	$pris->reise = get_site_option('UKMFvideresending_avgift_reise'.'_'.$monstring->getSesong());;
	
	UKMVideresending::addViewData('kvote', $kvote);
	UKMVideresending::addViewData('pris', $pris);
	require_once('ledere.controller.php');
}
UKMVideresending::addViewData('summering', $summering);

