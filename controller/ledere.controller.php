<?php

// Tell opp antall personer, sånn at vi er sikker på at vi jobber med riktig antall

use UKMNorge\Arrangement\Videresending\Ledere\Hovedledere;
use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Ledere;
use UKMNorge\Arrangement\Videresending\Ledere\Write as WriteLeder;

UKMVideresending::beregnAntallVideresendtePersoner();

$til = UKMVideresending::getValgtTil();
$fra = UKMVideresending::getFra();

// Sørg for at hovedleder og utstillingsleder finnes før vi starter
$hovedleder = Leder::getByType( $fra->getId(), $til->getId(), 'hoved');
if( !$hovedleder->eksisterer() ) {
    WriteLeder::create($hovedleder);
}

$utstillingleder = Leder::getByType( $fra->getId(), $til->getId(), 'utstilling');
if( !$utstillingleder->eksisterer() ) {
    WriteLeder::create($utstillingleder);
}

$ledere = new Ledere( $fra->getId(), $til->getId() );
$hovedledere = new Hovedledere($fra->getId(), $til->getId());

UKMVideresending::addViewData(
    [
        'ledere' => $ledere,
        'hovedledere' => $hovedledere,
        'pris_hotelldogn' => $til->getArrangement()->getMetaValue('pris_hotelldogn'),
        'overnattingssteder' => UKMVideresending::getOvernattingssteder( $til->getArrangement() )
    ]
);
/*
## HOVEDLEDERE NATT

// Hovedledere natt
$nattledere = [];
foreach( $festivalen->getNetter() as $natt ) {
	$nattledere[ $natt->format('d_m') ] = null;
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
UKMVideresending::addViewData('netter', $festivalen->getNetter());
UKMVideresending::addViewData('overnattingssteder', UKMVideresending::overnattingssteder());
UKMVideresending::addViewData('pris_hotelldogn',  get_site_option('UKMFvideresending_hotelldogn_pris_'. $festivalen->getSesong() ));
UKMVideresending::addViewData('overnatting_kommentar', UKMVideresending::getInfoSkjema('overnatting_kommentar'));
*/