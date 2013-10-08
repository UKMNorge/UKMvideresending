<?php
$kolli = new SQL("SELECT `kolli`.*, `place`.`pl_name`
				FROM `smartukm_videresending_infoskjema_kunst_kolli` AS `kolli`
				JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `kolli`.`pl_id_from`)
				WHERE `kolli`.`pl_id` = '#pl_id'
				AND `kolli`.`pl_id_from` = '#pl_from_id'
				ORDER BY `pl_name` ASC, `kolli_id` ASC",
				array('pl_from_id'=>$_POST['pl_id_from'], 'pl_id'=>$_POST['pl_id']));
$kolli = $kolli->run();

$skjema = new SQL("SELECT `kunst`.*, `place`.`pl_name`
				FROM `smartukm_videresending_infoskjema_kunst` AS `kunst`
				JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `kunst`.`pl_id_from`)
				WHERE `kunst`.`pl_id` = '#pl_id'
				AND `kunst`.`pl_id_from` = '#pl_from_id'
				ORDER BY `pl_name` ASC",
				array('pl_from_id'=>$_POST['pl_id_from'], 'pl_id'=>$_POST['pl_id']));

$skjema = $skjema->run('array');
foreach($skjema as $key => $val) 
	$skjema[$key] = utf8_encode($val);
		
		
while($k = mysql_fetch_assoc($kolli)) {
	foreach($k as $key => $val) 
		$k[$key] = utf8_encode($val);

	$kolliene .= '<div class="kolonne">Kolli '.$k['kolli_id'].'</div>'
				.'<div class="kolonne">'.$k['kolli_bredde'].'</div>'
				.'<div class="kolonne">'.$k['kolli_hoyde'].'</div>'
				.'<div class="kolonne">'.$k['kolli_dybde'].'</div>'
				.'<div class="kolonne">'.$k['kolli_vekt'].'</div>'
				.'<br clear="all" />'
				;
	$lastrow = $k;
}


echo '<html>'
	.'<head>'
	.'<script type="text/javascript" src="http://ukm.no/wp-admin/load-scripts.php?c=1&amp;load=jquery,utils&amp;ver=edec3fab0cb6297ea474806db1895fa7"></script>'
	.'<link rel="stylesheet" id="UKMVideresending_css-css"  href="http://ukm.no/wp-content/plugins/UKMVideresending/videresending.css?ver=3.3.1" type="text/css" media="all" />'
	.'<script type="text/javascript" src="http://ukm.no/wp-content/plugins/UKMVideresending/videresending.js?ver=3.3.1"></script>'


	#.'<a href="javascript:window.print();">Skriv ut</a>'

	.'<h1>UKM Norge Fraktseddel'
	.	'<span id="forklaring"> (' . $lastrow['pl_name'] .')</span>'
	.'</h1>'	

	.'<h3>'
	.	'Kontaktperson: ' 
	.	$skjema['kunst_kontaktperson_ved_henting']
	.	' ('. $skjema['kunst_kontaktperson_ved_henting_mobil'].')'
	.'</h3>'
	
	.'<h3 style="margin-bottom: 0px;" class="fraogmed">'
	.	'Kunsten kan hentes fra og med: ' . $skjema['kunst_hentesnar']
	.'</h3>'

	.'<h3 style="margin-bottom: 0px;">Kommentar til spedit&oslash;r</h3>'	
	.$skjema['kunst_hentesnar_detaljer']
	
	.'<h3 style="margin-bottom: 0px;">Henteadresse</h3>'	
	. $skjema['kunst_henteadresse'] . '<br />'
	. $skjema['kunst_postnummer'] . ' ' . $skjema['kunst_poststed']. '<br />'
	.'<strong>Etasje: </strong>' . $skjema['kunst_etasje'] . '<br />'
	.'<strong>Inngang nr / fra: </strong>' . $skjema['kunst_inngang'] . '<br />'
	.'<strong>Heis: </strong>' . $skjema['kunst_heis'] . '<br />'
	
	.'<h3 style="margin-bottom: 0px;">Kolli som skal fraktes</h3>'
	. '<div id="kolliliste">'
	.	'<div class="kolonne header">Kolli</div>'
	.	'<div class="kolonne header">Bredde (i cm)</div>'
	.	'<div class="kolonne header">H&oslash;yde (i cm)</div>'
	.	'<div class="kolonne header">Dybde (i cm)</div>'
	.	'<div class="kolonne header">Vekt (i kg)</div>'
	. '<br clear="all" />'
	. $kolliene
	. '</div>'
	
	.'<h3 style="margin-bottom: 0px;">Eventuelle tilleggsopplysninger</h3>'	
	.$skjema['kunst_kommentarer']


	.'<h3 style="margin-bottom: 0px;">Retur etter festivalen</h3>'	
	.($skjema['kunst_leveringsadresse_samme']=='ja'
		? 'Samme som henteadresse'
		: $skjema['kunst_postretur']
	)

	;
	
die();
?>