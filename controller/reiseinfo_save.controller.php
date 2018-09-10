<?php
// Eksisterende rad?
$sqlTest = new SQL("
	SELECT `skjema_id`
		FROM `smartukm_videresending_infoskjema`
		WHERE `pl_id` = '#pl_to'
		AND `pl_id_from` = '#pl_from'",
	[
		'pl_to' 	=> $festivalen->getId(),
		'pl_from'	=> $monstring->getId()
	]
);
$testRes = $sqlTest->run();

if( !$testRes || SQL::numRows( $testRes ) == 0 ) {
	$sql = new SQLins('smartukm_videresending_infoskjema');
} else {
	$sql = new SQLins(
		'smartukm_videresending_infoskjema', 
		[
			'pl_id' 	=> $festivalen->getId(),
			'pl_id_from'	=> $monstring->getId()
		]
	);
}

$sql->add('pl_id',		$festivalen->getId());
$sql->add('pl_id_from',	$monstring->getId());


// ANKOMST
$sql->add('reise_inn_mate',			$_POST['reise_ank_mate']);
$sql->add('reise_inn_dato',			$_POST['reise_ank_dato']);
$sql->add('reise_inn_tidspunkt',	$_POST['reise_ank_tid']);
$sql->add('reise_inn_sted',			$_POST['reise_ank_sted']);
$sql->add('reise_inn_samtidig',		empty( $_POST['reise_ank_annet'] ) ? 'nei' : 'ja');
$sql->add('reise_inn_samtidig_nei', $_POST['reise_ank_annet']);

// AVREISE
$sql->add('reise_ut_dato',			$_POST['reise_avr_dato']);
$sql->add('reise_ut_tidspunkt',		$_POST['reise_avr_tid']);
$sql->add('reise_ut_sted',			'');
$sql->add('reise_ut_samtidig',		empty( $_POST['reise_avr_annet'] ) ? 'nei' : 'ja');
$sql->add('reise_ut_samtidig_nei', 	$_POST['reise_avr_annet']);


// MAT
$sql->add('mat_vegetarianere', 	$_POST['matogallergi_vegetarianere']);
$sql->add('mat_soliaki', 		$_POST['matogallergi_soliaki']);
$sql->add('mat_svinekjott', 	$_POST['matogallergi_svinekjott']);
$sql->add('mat_annet',		 	$_POST['matogallergi_annet']);


// TILRETTELEGGING
$sql->add('tilrettelegging_bevegelseshemninger',	$_POST['tilrettelegging_bevegelseshemninger']);
$sql->add('tilrettelegging_annet',		 			$_POST['tilrettelegging_annet']);

$res = $sql->run();
	
if( false === $res || $res == -1) {
	UKMVideresending::addViewData(
		'message',
		[
			'success' => false,
			'body' => 
				'Det har dessverre oppstått en feil ved lagring, '.
				'og skjemaet ble derfor ikke lagret. '.
				'Vennligst prøv igjen, og kontakt UKM Norge om problemet vedvarer.'
		]
	);
} else {
	UKMVideresending::addViewData(
		'message',
		[
			'success' => true,
			'title' => 
				'Skjema er lagret!'
		]
	);
}