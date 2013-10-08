<?php
/* 
Part of: UKM Videresending rapporter :: mat
Description: Genererer enkel oversikt over matallergier, o.l
Author: S O Bjerkan
Version: 1.0
*/

function UKMV_rapporter_mat() {
	echo '<div class="rapport_print" id="printButton_oversikt" rel="mat" style="margin-top:0px;">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="ukm" id="mat">'
		.	'<li>'
		.		UKMN_ico('medical-case',32)
		.		'<h4>Matallergier</h4>'
		.		'<br clear="all" />'
		.	'</li>';
		
	$sql = new SQL("SELECT `m`.*, `pl`.`pl_name` 	
					FROM `smartukm_videresending_infoskjema` AS `m`
					JOIN `smartukm_place` AS `pl` ON (`pl`.`pl_id` = `m`.`pl_id_from`)
					WHERE `season`=".get_option('season')."
					ORDER BY `pl_name`
					");

	$sql = $sql->run();
	
	echo '<li class="rapport_mat" id="header">'
		 .'<div class="fylke">Fylke</div>'
		 .'<div class="vegetarianere">Vegetarianere</div>'
		 .'<div class="soliaki">S&oslash;liaki</div>'
		 .'<div class="svinekjott">Svinekj&oslash;tt</div>'
		 .'<div class="annet">Annet</div>'
		 .'<br clear="all" />'
 		 .'</li>'
		 ;
	
	while($r = mysql_fetch_assoc($sql)) {
		if (empty($r['mat_vegetarianere']) && empty($r['mat_soliaki']) && 
				empty($r['mat_svinekjott']) &&  empty($r['mat_annet']))
			continue;
		$mat_vegetarianere += (int)$r['mat_vegetarianere'];
		$mat_soliaki += (int)$r['mat_soliaki'];
		$mat_svinekjott += (int)$r['mat_svinekjott'];
		
		echo  '<li class="rapport_mat">'
			  .'<div class="fylke">'.utf8_encode($r['pl_name']).' &nbsp;</div>'
			  .'<div class="vegetarianere">'. utf8_encode($r['mat_vegetarianere']).' &nbsp;</div>'
			  .'<div class="soliaki">'. utf8_encode($r['mat_soliaki']).' &nbsp;</div>'
			  .'<div class="svinekjott">'. utf8_encode($r['mat_svinekjott']).' &nbsp;</div>'
  			  .'<div class="annet">'. utf8_encode($r['mat_annet']).' &nbsp;</div>'
  			  .'<br clear="all" />'
			  .'</li>';
	}
	echo  '<li class="rapport_mat" id="row_footer">'
		  .'<div class="fylke">TOTAL</div>'
		  .'<div class="vegetarianere">'. $mat_vegetarianere.' &nbsp;</div>'
		  .'<div class="soliaki">'. $mat_soliaki.' &nbsp;</div>'
		  .'<div class="svinekjott">'. $mat_svinekjott.' &nbsp;</div>'
  		  .'<div class="annet">&nbsp;</div>'
  		  .'<br clear="all" />'
		  .'</li>'
		  .'</ul>';
}
?>
