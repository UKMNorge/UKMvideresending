<?php

use UKMNorge\Allergener\Allergener;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Database\SQL\Write;

require_once('UKM/mail.class.php');
require_once('UKM/Autoloader.php');

$epost = new UKMmail();
$epost->text( 'Det må lages nytt system for å slette allergier og intoleranser etter en viss periode.' )
	->to('marius@ukm.no,support@ukm.no')
	->subject('SKULLE VÆRT SLETTET: Allergier og intoleranser')
	->ok();
die('FUNKER IKKE SÅNN LENGRE');
if( date('n') > 6 && date('n') < 8 ) {
	$report = new Query(
		"SELECT `liste` 
		FROM `ukm_sensitivt_intoleranse`"
	);
	$res = $report->run();
	if( Query::numRows( $res ) == 0 ) {
		die('Allergier allerede slettet');
	}
	$allergier = [];
	while( $r = Query::fetch( $res ) ) {
		$mine_allergier = explode('|', $r['liste']);
		
		if( is_array( $mine_allergier ) ) {
			foreach( $mine_allergier as $allergi ) {
				if( isset( $allergier[ $allergi ] ) ) {
					$allergier[ $allergi ] ++;
				} else {
					$allergier[ $allergi ] = 1;
				}
			}
		}
	}

	$melding = 'Før sletting, var det registrert følgende antall av de forskjellige allergiene.'. "\r\n".
		'Eventuelle allergier spesifisert i ren tekst er slettet uten behandling av personvernhensyn.' . "\r\n";

	foreach( $allergier as $allergi_id => $allergi_count ) {
		if( !empty( $allergi_id ) ) {
			$melding .= "\r\n" . Allergener::getById( $allergi_id )->getNavn() .': '. (int) $allergi_count;
		}
	}

	$epost = new UKMmail();
	$epost->text( $melding )
		->to('marius@ukm.no,support@ukm.no')
		->subject('SLETTET: Allergier og intoleranser')
		->ok();
	
	$sql = new Write("
		DELETE 
		FROM `ukm_sensitivt_intoleranse`"
	);
	$res = $sql->run();
	if( $res ) {
		die('Slettet intoleranser');
	}
}

die('Beklager, kunne ikke slette intoleranser på nåværende tidspunkt');