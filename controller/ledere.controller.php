<?php
require_once( 'UKM/leder.class.php' );

UKMVideresending::calcAntallPersoner();

$monstring = UKMVideresending::getFra();
$festivalen = array_pop( UKMVideresending::getTil() );

/**
 * HENT UT INFORMASJON OM LEDERE
**/
$ledere = [];
// HOVEDLEDER
$hovedleder = new leder();
$hovedleder_load = $hovedleder->load_by_type(
	$monstring->getId(), 
	$festivalen->getId(),
	'hoved'
);
// Opprett hovedleder hvis dette ikke allerede er gjort
if( !$hovedleder_load ) {
	$hovedleder->set('type', 'hoved');
	$hovedleder_create = $hovedleder->create(
		$monstring->getId(), 
		$festivalen->getId(), 
		$festivalen->getSesong()
	);
}

// UTSTILLINGSLEDER
$utstillingleder = new leder();
$utstillingleder_load = $utstillingleder->load_by_type(
	$monstring->getId(),
	$festivalen->getId(),
	'utstilling'
);
// Opprett utstillingsleder hvis dette ikke allerede er gjort
if( !$utstillingleder_load ) {
	$utstillingleder->set('type', 'utstilling');
	$utstillingleder_create = $utstillingleder->create(
		$monstring->getId(),
		$festivalen->getId(),
		$festivalen->getSesong()
	);
}

$ledere[] = $hovedleder;
$ledere[] = $utstillingleder;

// ANDRE LAGREDE LEDERE
$andre_ledere = new SQL("
	SELECT `l_id`
	FROM `smartukm_videresending_ledere_ny`
	WHERE `pl_id_from` = '#pl_from'
	AND `pl_id_to` = '#pl_to'
	AND `season` = '#season'
	AND (`l_type` != 'utstilling' AND `l_type` != 'hoved')",
	[
		'pl_from' => $monstring->getId(),
		'pl_to' => $festivalen->getId(),
		'season' => $festivalen->getSesong(),
	]
);
$res = $andre_ledere->run();

while( $r = SQL::fetch( $res ) ) {
	$ledere[] = new leder( $r['l_id'] );
}


/**
 * HOVEDLEDERE NATT
**/

// Hovedledere natt
$nattledere = [];
foreach( $festivalen->getNetter() as $natt ) {
	$nattledere[ $natt->dag .'_'. $natt->mnd ] = null;
}

$sql = new SQL("
	SELECT `dato`, `l_id` 
	FROM `smartukm_videresending_ledere_nattleder`
	WHERE `pl_id_from` = '#plid'",
	[
		'plid' => $monstring->getId()
	]
);
$res = $sql->run();
if( $res ) {
	while( $r = SQL::fetch( $res ) ) {
		$nattledere[ $r['dato'] ] = $r['l_id'];
	}
}

UKMVideresending::addViewData('nattledere', $nattledere);
UKMVideresending::addViewData('unike_deltakere', UKMVideresending::getInfoSkjema('systemet_overnatting_spektrumdeltakere'));
UKMVideresending::addViewData('ledere', $ledere);
UKMVideresending::addViewData('netter', $festivalen->getNetter());
UKMVideresending::addViewData('overnattingssteder', UKMVideresending::overnattingssteder());
UKMVideresending::addViewData('pris_hotelldogn',  get_site_option('UKMFvideresending_hotelldogn_pris_'. $festivalen->getSesong() ));
UKMVideresending::addViewData('overnatting_kommentar', UKMVideresending::getInfoSkjema('overnatting_kommentar'));