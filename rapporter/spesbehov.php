<?php
/* 
Part of: UKM Videresending rapporter :: spesbehov
Description: Genererer enkel oversikt over spesielle behov
Author: S O Bjerkan
Version: 1.0
*/

function UKMV_rapporter_spesbehov() {
	echo '<div class="rapport_print" id="printButton_oversikt" rel="spesbehov" style="margin-top:0px;">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="ukm" id="spesbehov">'
		.	'<li>'
		.		UKMN_ico('handicap',32)
		.		'<h4>Spesielle behov</h4>'
		.		'<br clear="all" />'
		.	'</li>';
		
	$sql = new SQL("SELECT `tilrettelegging_bevegelseshemninger`, `tilrettelegging_annet`, `pl`.`pl_name` 	
					FROM `smartukm_videresending_infoskjema` AS `m`
					JOIN `smartukm_place` AS `pl` ON (`pl`.`pl_id` = `m`.`pl_id_from`)
					WHERE `season`=".get_option('season')."
					ORDER BY `pl_name`
					");

	$sql = $sql->run();
	
	echo '<li class="rapport_spesbehov" id="header">'
		 .'<div class="fylke">Fylke</div>'
		 .'<div class="annet">Annet</div>'
		 .'<div class="tilrettelegging">Bevegelseshemninger</div>'
		 .'<br clear="all" />'
 		 .'</li>'
		 ;
	
	while($r = mysql_fetch_assoc($sql)) {
		if (empty($r['tilrettelegging_bevegelseshemninger']) && empty($r['tilrettelegging_annet']))
			continue;
		echo  '<li class="rapport_spesbehov">'
			  .'<div class="fylke">'. utf8_encode($r['pl_name']) .'</div>'
			  .'<div class="annet">'. utf8_encode($r['tilrettelegging_annet']).' &nbsp;</div>'
			  .'<div class="tilrettelegging">'. utf8_encode($r['tilrettelegging_bevegelseshemninger']).' &nbsp;</div>'
			  .'<br clear="all" />'
			  .'</li>';
	}
	echo '</ul>';
}
?>