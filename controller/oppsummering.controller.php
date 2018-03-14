<?php
	
$monstring_fra = UKMVideresending::getFra();
$season = $monstring_fra->getSesong();

// TOTAL
$kvote_param[]	= (object) array(	'id' 		=> 'total_personer',
									'verdi'		=>	(int) get_site_option('UKMFvideresending_kvote_deltakere_'.$season),
									'tittel'	=> 'Totalt antall deltakere',
									'enhet'		=>	'person',
									'flertall'	=> 'er'
								);

if(get_option('site_type' == 'fylke')) {						
	$kvote_param[]	= (object) array(	'id' 		=> 'total_ledere',
										'verdi'		=>	(int) get_site_option('UKMFvideresending_kvote_ledere_'.$season),
										'tittel'	=> 'Antall ledere',
										'enhet'		=>	'person',
										'flertall'	=> 'er'
									);
						
	$kvote_param[]	= (object) array(	'id' 		=> 'total_deltakere_per_leder',
										'verdi'		=>	10,
										'tittel'	=> 'Antall deltakere per leder',
										'enhet'		=>	'deltaker',
										'flertall'	=> 'e'
									);
}
// SCENE
$kvote_param[]	= (object) array(	'id' 		=> 'scene_antall',
									'verdi'		=>	5,
									'tittel'	=> 'Antall sceneinnslag',
									'enhet'		=>	'innslag',
									'flertall'	=> ''
								);
						
$kvote_param[]	= (object) array(	'id' 		=> 'scene_varighet',
									'verdi'		=>	25,
									'tittel'	=> 'Varighet sceneinnslag',
									'enhet'		=>	'minutt',
									'flertall'	=> 'er'
								);

// FILM
$kvote_param[]	= (object) array(	'id' 		=> 'film_antall',
									'verdi'		=>	1,
									'tittel'	=> 'Antall filmer',
									'enhet'		=>	'film',
									'flertall'	=> 'er',
									'kommentar' => '2 filmer hvis total varighet er under 6 minutter'
								);

$kvote_param[]	= (object) array(	'id' 		=> 'film_varighet',
									'verdi'		=>	5,
									'tittel'	=> 'Varighet filmer',
									'enhet'		=>	'minutt',
									'flertall'	=>  'er',
									'kommentar' => '2 filmer gir utvidet kvote: 6 minutter'
								);

$kvote_param[]	= (object) array(	'id' 		=> 'film_personer',
									'verdi'		=>	2,
									'tittel'	=> 'Antall filmskapere',
									'enhet'		=>	'person',
									'flertall'	=> 'er',
									'kommentar' => '2 filmer gir utvidet kvote: 4 personer'
								);

// KUNST
$kvote_param[]	= (object) array(	'id' 		=> 'kunst_antall',
									'verdi'		=>	4,
									'tittel'	=> 'Antall kunstverk',
									'enhet'		=>	'verk',
									'flertall'	=> ''
								);

$kvote_param[]	= (object) array(	'id' 		=> 'kunst_personer',
									'verdi'		=>	8,
									'tittel'	=> 'Antall kunstnere',
									'enhet'		=>	'person',
									'flertall'	=> 'er'
								);

// TITTELLØSE
$kvote_param[]	= (object) array(	'id' 		=> 'nettredaksjon_antall',
									'verdi'		=>	'nomineres',
									'tittel'	=> 'Antall nettredaksjon',
									'enhet'		=>	'person',
									'flertall'	=> 'er'
								);

$kvote_param[]	= (object) array(	'id' 		=> 'arrangor_antall',
									'verdi'		=>	'nomineres',
									'tittel'	=> 'Antall arrangører',
									'enhet'		=>	'person',
									'flertall'	=> 'er'
								);

$kvote_param[]	= (object) array(	'id' 		=> 'konferansier_antall',
									'verdi'		=>	'nomineres',
									'tittel'	=> 'Antall konferansierer',
									'enhet'		=>	'person',
									'flertall'	=> 'er'
								);


// SET DEFAULT STATES
foreach( $kvote_param as $param ) {
	foreach( UKMVideresending::getTil() as $monstring_til ) {
		$geo_key = 
			$monstring_til->getType() == 'fylke' ? 
				$monstring_til->getFylke()->getId() :
				0
			;
		$videresendte[ $geo_key ][ $param->id ] = 0;
	}
	$TWIG['kvoter'][ $param->id ] = $param;
}

// HVILKE KOMMUNER SKAL INKLUDERES I DENNE OVERSIKTEN
$fra_monstring = UKMVideresending::getFra();
if( $fra_monstring->getType() == 'kommune' ) {
	$kommuner = [];
	foreach( $fra_monstring->getKommuner() as $kommune ) {
		$kommuner[] = $kommune->getId();
	}
}


// LOOP ALLE MØNSTRINGER VI KAN VIDERESENDE TIL
$monstringer = UKMVideresending::getTil();
foreach( $monstringer as $monstring ) {
	// LOOP ALLE INNSLAG I MØNSTRINGEN
	foreach( $monstring->getInnslag()->getAll() as $innslag ) {
		
		// KUN VIS INNSLAGENE SOM KOMMER FRA DENNE MØNSTRINGEN
		if( $fra_monstring->getType() == 'kommune' && !in_array( $innslag->getKommune()->getId(), $kommuner ) ) {
			continue;
		}
		
		if( $monstring->getType() == 'land' ) {
			if( $monstring_fra->getType() == 'fylke' && $innslag->getFylke()->getId() != $monstring_fra->getFylke()->getId() ) {
				continue;
			}
		}
		
		// SETT GEO-NØKKEL FOR INFORMASJON OM DENNE MØNSTRINGEN
		$geo_id = $innslag->getFylke()->getId();

		// Scene-innslag
		if( $innslag->getType()->getId() == 1 ) {
			$videresendte[ $geo_id ]['scene_antall']++;
			$videresendte[ $geo_id ]['scene_varighet'] += $innslag->getVarighet()->getSekunder();
			
			foreach( $innslag->getPersoner()->getAll() as $person ) {
				$unike_personer[ $geo_id ]['scene'][ $person->getId() ] = $person;
			}
		}
		
		// Film-innslag
		elseif( $innslag->getType()->getKey() == 'video' ) {
			$videresendte[ $geo_id ]['film_antall'] += $innslag->getTitler()->getAntall();
			$videresendte[ $geo_id ]['film_varighet'] += $innslag->getVarighet()->getSekunder();
	
			// 2 filmer gir totalt 6 min varighet og 4 pers
			if( $videresendte[ $geo_id ]['film_antall'] > 1 ) {
				$TWIG['kvoter']['film_varighet']->verdi = 6;
				$TWIG['kvoter']['film_personer']->verdi = 4;
			}
			
			foreach( $innslag->getPersoner()->getAll() as $person ) {
				$unike_personer[ $geo_id ]['film'][ $person->getId() ] = $person;
			}
		}
		
		// KUNST
		elseif( $innslag->getType()->getKey() == 'utstilling' ) {
			$videresendte[ $geo_id ]['kunst_antall'] += $innslag->getTitler()->getAntall();
	
			foreach( $innslag->getPersoner()->getAll() as $person ) {
				$unike_personer[ $geo_id ]['kunst'][ $person->getId() ] = $person;
			}
		}
		
		// KONFERANSIER
		elseif( $innslag->getType()->getKey() == 'konferansier' ) {
			$videresendte[ $geo_id ]['konferansier_antall']++;
			
			$person = $innslag->getPersoner()->getSingle();
			$unike_personer[ $geo_id ]['tittellose'][ $person->getId() ] = $person;
		}
		
		// MEDIA
		elseif( $innslag->getType()->getKey() == 'nettredaksjon' ) {
			$videresendte[ $geo_id ]['nettredaksjon_antall']++;
	
			$person = $innslag->getPersoner()->getSingle();
			$unike_personer[ $geo_id ]['tittellose'][ $person->getId() ] = $person;
		}
		
		// ARRANGØR
		elseif( $innslag->getType()->getKey() == 'arrangor' ) {
			$videresendte[ $geo_id ]['arrangor_antall']++;
	
			$person = $innslag->getPersoner()->getSingle();
			$unike_personer[ $geo_id ]['tittellose'][ $person->getId() ] = $person;
		}
		
		// MATKULTUR
		elseif( $innslag->getType()->getKey() == 'matkultur' ) {
			$videresendte[ $geo_id ]['matkultur']++;
			
			foreach( $innslag->getPersoner()->getAll() as $person ) {
				$unike_personer[ $geo_id ]['matkultur'][ $person->getId() ] = $person;
			}
		}
	}
	
	$geo_id_monstring = 
		$monstring->getType() == 'fylke' ? 
			$monstring->getFylke()->getId() :
			0
		;
	// OPPSUMMERING	
	$videresendte[ $geo_id_monstring ]['kunst_personer'] 	= sizeof( $unike_personer[ $geo_id_monstring ]['kunst'] );
	$videresendte[ $geo_id_monstring ]['film_personer'] 	= sizeof( $unike_personer[ $geo_id_monstring ]['film'] );
	$videresendte[ $geo_id_monstring ]['scene_personer'] 	= sizeof( $unike_personer[ $geo_id_monstring ]['scene'] );
	
	$videresendte[ $geo_id_monstring ]['total_personer'] 	= 
		sizeof($unike_personer[ $geo_id_monstring ]['tittellose']) + 
		sizeof( $unike_personer[ $geo_id_monstring ]['kunst'] ) + 
		sizeof( $unike_personer[ $geo_id_monstring ]['film'] ) + 
		sizeof( $unike_personer[ $geo_id_monstring ]['scene'] ) + 
		sizeof( $unike_personer[ $geo_id_monstring ]['matkultur'] );
	
	$videresendte[ $geo_id_monstring ]['scene_tid'] 		= $videresendte[ $geo_id_monstring ]['scene_varighet'];
	$videresendte[ $geo_id_monstring ]['film_tid'] 			= $videresendte[ $geo_id_monstring ]['film_varighet'];
	
}

UKMVideresending::addViewData('til', UKMVideresending::getTil() );
UKMVideresending::addViewData('kvoter', $TWIG['kvoter']);
UKMVideresending::addViewData('info1', get_site_option('UKMFvideresending_info1_'.$season));
UKMVideresending::addViewData('videresendt', $videresendte);


if( $monstring->getType() == 'land' ) {
	// LEDERE
	$ledere = new SQL("SELECT COUNT(`l_id`) AS `num_ledere`
						FROM `smartukm_videresending_ledere_ny`
						WHERE `pl_id_from` = '#pl_from'
						AND `pl_id_to` = '#pl_to'
						AND `season` = '#season'
						ORDER BY `l_navn` ASC",
					array(	'pl_from' 	=> $monstring_fra->getId(),
							'pl_to' 	=> UKMVideresending::getTil()[0]->getId(),
							'season' 	=> UKMVideresending::getTil()[0]->getSesong(),
						)
					);
	$videresendte['total_ledere'] = $ledere->run('field','num_ledere');
	if( $videresendte['total_personer'] > 0 && $videresendte['total_ledere'] > 0) {
		$videresendte['total_deltakere_per_leder'] = ceil( $videresendte['total_personer'] / $videresendte['total_ledere'] );
	} else { 
		$videresendte['total_deltakere_per_leder'] = $videresendte['total_personer'];
	}
/* TODO:	
	update_infoskjema_field( 
		$monstring_fra->getId(), 
		UKMVideresending::getTil()[0]->getId(), 
		'systemet_overnatting_spektrumdeltakere', 
		$videresendte['total_personer']
	);
*/
}