<?php

$monstring = UKMVideresending::getFra();
$festivalen = UKMVideresending::getTil()[0];

if( isset( $_POST ) && sizeof( $_POST ) > 0 ) {
	require_once('reiseinfo_save.controller.php');
}

$load = new SQL("SELECT *
					FROM `smartukm_videresending_infoskjema`
					WHERE `pl_id` = '#pl_to'
					AND `pl_id_from` = '#pl_from'",
				array(	'pl_to' 	=> $festivalen->getId(),
						'pl_from'	=> $monstring->getId()
					)
				);
$db = $load->run('array');

$reise = new stdClass();
$reise->ank = new stdClass();
$reise->avr = new stdClass();

$reise->ank->dato = $db['reise_inn_dato'];
$reise->ank->tid = $db['reise_inn_tidspunkt'];
$reise->ank->sted = $db['reise_inn_sted'];
$reise->ank->mate = $db['reise_inn_mate'];
$reise->ank->annet = $db['reise_inn_samtidig_nei'];

$reise->avr->dato = $db['reise_ut_dato'];
$reise->avr->tid = $db['reise_ut_tidspunkt'];
$reise->avr->annet = $db['reise_ut_samtidig_nei'];

$matogallergi = new stdClass();
$matogallergi->vegetarianere = $db['mat_vegetarianere'];
$matogallergi->soliaki = $db['mat_soliaki'];
$matogallergi->svinekjott = $db['mat_svinekjott'];
$matogallergi->annet = $db['mat_annet'];

$tilrettelegging = new stdClass();
$tilrettelegging->bevegelseshemninger = $db['tilrettelegging_bevegelseshemninger'];
$tilrettelegging->annet = $db['tilrettelegging_annet'];

UKMVideresending::addViewData('reise', $reise);
UKMVideresending::addViewData('matogallergi', $matogallergi);
UKMVideresending::addViewData('tilrettelegging', $tilrettelegging);