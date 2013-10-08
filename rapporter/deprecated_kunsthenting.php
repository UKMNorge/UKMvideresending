<?php
/* 
Part of: UKM Videresending rapporter :: kunsthenting
Description: Genererer enkel henterapport for henting av kunst
Author: UKM Norge / M Mandal
Version: 1.0
*/


function UKMV_rapporter_kunsthenting() {
	$skjema = new SQL("SELECT `kunst`.*, `place`.`pl_name`
					FROM `smartukm_videresending_infoskjema_kunst` AS `kunst`
					JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `kunst`.`pl_id_from`)
					ORDER BY `pl_name` ASC");
	$skjema = $skjema->run();
	
	echo ''
		.'<div class="rapport_back" style="display:none;" id="printButton_fraktseddel_back">'
		. UKMN_icoButton('arrow-blue-left',35,'Tilbake til oversikten',11)
		.'</div>'	
	
		.'<div class="rapport_print" style="display:none;" id="printButton_fraktseddel" rel="fraktseddelen">'
#		. UKMN_icoButton('print',35,'Skriv ut fraktbrev',11)
		.'</div>'

		.'<div id="fraktseddelen" style="display:none;"></div>';
	
	echo '<div class="rapport_print" id="printButton_oversikt" rel="kunstrapport">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="rapport" id="kunstrapport">'
#		.'<link rel="stylesheet" id="UKMVideresending_css-css"  href="http://ukm.no/wp-content/plugins/UKMVideresending/videresending.css?ver=3.3.1" type="text/css" media="all" />'
		.	'<li>'
		.		'<h3>Oversikt henting</h3>'
		.	'</li>'
		;

	echo '<li>'
		.	'<div class="fylke">Fylke</div>'
		.	'<div class="hentesfra">Kan hentes fra</div>'
		.	'<div class="kontaktperson">Kontaktperson</div>'
		.	'<div class="kommentar">Kommentar henting</div>'
		.	'<div class="tillegg">Tilleggsopplysninger</div>'
		.	'<br clear="all" />'
		.'</li>'
		;
	
	while($r = mysql_fetch_assoc($skjema)) {
		$kolli = new SQL("SELECT COUNT(`skjema_id`) AS `kolli`
						  FROM `smartukm_videresending_infoskjema_kunst_kolli`
						  WHERE `pl_id_from` = '#from'
						  AND `pl_id` = '#to'",
						  array('from'=>$r['pl_id_from'],
						  		'to'=>$r['pl_id'])
						 );
		$r['kolli'] = $kolli->run('field','kolli');

		foreach($r as $key => $val)
			$r[$key] = utf8_encode($val);
		echo '<li>'
		.		'<div class="fylke">'
		.			$r['pl_name']
		.			'<br />'
		.			'<span>'.$r['kolli'].' kolli</span><br />'
		.			'<a href="#" class="fraktseddel" id="'.$r['pl_id_from'].'|'.$r['pl_id'].'">[vis fraktseddel]</a>'
		.		'</div>'
		.		'<div class="hentesfra">'
		.			$r['kunst_hentesnar'].' &nbsp;'
		.		'</div>'
		.		'<div class="kontaktperson">'
		.			$r['kunst_kontaktperson_ved_henting'].' &nbsp;'
		.			'<br />'
		.			'('.$r['kunst_kontaktperson_ved_henting_mobil'].')'
		.		'</div>'
		.		'<div class="kommentar">'.$r['kunst_hentesnar_detaljer'].' &nbsp;</div>'
		.		'<div class="tillegg">'.$r['kunst_kommentarer'].' &nbsp;</div>'
		.		'<br clear="all" />'
		.	'</li>';
	}
	
	echo '</ul>';
}

?>