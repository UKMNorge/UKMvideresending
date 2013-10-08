<?php
/* 
Part of: UKM Videresending rapporter :: oppsummering
Description: Genererer rapport med oppsummering
Author: S O Bjerkan
Version: 1.0
*/

function UKMV_rapporter_oppsummering() {
	$kvote = get_ukm_option('kvote_ledere')+get_ukm_option('kvote_deltakere');
	echo '<div class="rapport_print" id="printButton_oversikt" rel="oppsummering" style="margin-top:0px;">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="ukm" id="oppsummering">'
		.	'<li>'
		.		UKMN_ico('handicap',32)
		.		'<h4>Oppsummering</h4>'
		.		'<br clear="all" />'
		.	'</li>';
		
	$sql = new SQL("SELECT `overnatting_spektrumdeltakere`, `systemet_overnatting_spektrumdeltakere`,
					`overnatting_spektrumdeltakere`, `avvik_overnatting_spektrumdeltakere`, `pl_id_from`, `pl`.`pl_name` 	
					FROM `smartukm_videresending_infoskjema` AS `m`
					JOIN `smartukm_place` AS `pl` ON (`pl`.`pl_id` = `m`.`pl_id_from`)
					WHERE `season`=".get_option('season')."
					ORDER BY `pl_name`
					");

	$sql = $sql->run();
	
	$sql_ledere = new SQL("SELECT `leder_over_fre`, `leder_over_lor`, `leder_over_son`, `leder_over_man`, `pl_id_from`
					FROM `smartukm_videresending_ledere` 
					JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `smartukm_videresending_ledere`.`pl_id_from`)
					WHERE `pl_season`=".get_option('season')."
					ORDER BY `pl_name`, `leder_navn` ASC");
	$sql_ledere = $sql_ledere->run();
	
	$ledere = array('fre'=>0,'lor'=>0,'son'=>0,'man'=>0);
	while ($r = mysql_fetch_assoc($sql_ledere)) {
		$antall_ledere[$r['pl_id_from']]++;
		if ($r['leder_over_fre'] === 'spektrum')
			++$ledere[$r['pl_id_from']]['fre'];
		if ($r['leder_over_lor'] === 'spektrum')
			++$ledere[$r['pl_id_from']]['lor'];
		if ($r['leder_over_son'] === 'spektrum')
			++$ledere[$r['pl_id_from']]['son'];
		if ($r['leder_over_man'] === 'spektrum')
			++$ledere[$r['pl_id_from']]['man'];
	}
	
	echo '<li class="oppsummering" id="header">'
		 .'<div class="fylke">Fylke</div>'
		 .'<div>Systemets antall deltakere</div>'
		 .'<div>Deltakere som sover i spektrum</div>'
#		 .'<div class="ant">Antall<br />fre</div>'
#		 .'<div class="ant">Antall<br />l&oslash;r</div>'
#		 .'<div class="ant">Antall<br />s&oslash;n</div>'
#		 .'<div class="ant">Antall<br />man</div>'
		 .'<div>Antall ledere</div>'
		 .'<div>Antall ledere og deltakere</div>'
		 .'<div>Status ledere fredag</div>'
		 .'<div>Status ledere l&oslash;rdag</div>'
		 .'<div>Status ledere s&oslash;ndag</div>'
		 .'<div>Status ledere mandag</div>'
		 .'<div>Over kvote</div>'
		 .'<div class="kommentar">Eventuelle kommentarer</div>'
		  .'<br clear="all" />'
 		 .'</li>'
		 ;
	
	while($r = mysql_fetch_assoc($sql)) {
		$status_overnatting['fre'] = $ledere[$r['pl_id_from']]['fre']-(int)ceil($r['overnatting_spektrumdeltakere']/10);
		$status_overnatting['lor'] = $ledere[$r['pl_id_from']]['lor']-(int)ceil($r['overnatting_spektrumdeltakere']/10);
		$status_overnatting['son'] = $ledere[$r['pl_id_from']]['son']-(int)ceil($r['overnatting_spektrumdeltakere']/10);
		$status_overnatting['man'] = $ledere[$r['pl_id_from']]['man']-(int)ceil($r['overnatting_spektrumdeltakere']/10);
	
		// FREDAG	
		if($status_overnatting['fre'] >= 0 && sizeof($ledere[$r['pl_id_from']]['fre']) > 0)
			$status_overnatting['fre'] = '<span class="spektrum_ok">OK</span>';
		else
			$status_overnatting['fre'] = '<span class="spektrum_fail">'.$status_overnatting['fre'].'</span>';

		// LØRDAG	
		if($status_overnatting['lor'] >= 0 && sizeof($ledere[$r['pl_id_from']]['lor']) > 0)
			$status_overnatting['lor'] = '<span class="spektrum_ok">OK</span>';
		else
			$status_overnatting['lor'] = '<span class="spektrum_fail">'.$status_overnatting['lor'].'</span>';

		// SØNDAG	
		if($status_overnatting['son'] >= 0 && sizeof($ledere[$r['pl_id_from']]['son']) > 0)
			$status_overnatting['son'] = '<span class="spektrum_ok">OK</span>';
		else
			$status_overnatting['son'] = '<span class="spektrum_fail">'.$status_overnatting['son'].'</span>';

		// MANDAG	
		if($status_overnatting['man'] >= 0 && sizeof($ledere[$r['pl_id_from']]['man']) > 0)
			$status_overnatting['man'] = '<span class="spektrum_ok">OK</span>';
		else
			$status_overnatting['man'] = '<span class="spektrum_fail">'.$status_overnatting['man'].'</span>';

		// KOMMENTARER
		if(!empty($r['avvik_overnatting_spektrumdeltakere']))
			$kommentar = '<a href="#" class="viskommentar" rel="'.$r['pl_id_from'].'">vis kommentarer</a>'
						.'<div id="kommentar_'.$r['pl_id_from'].'" style="display:none;">'
							.utf8_encode($r['avvik_overnatting_spektrumdeltakere'])
						.'</div>';
		else
			$kommentar = '';
		
		// ANTALL DELTAKERE
		$deltakere = $r['systemet_overnatting_spektrumdeltakere'] > $r['overnatting_spektrumdeltakere'] 
					? $r['systemet_overnatting_spektrumdeltakere']
					: $r['overnatting_spektrumdeltakere'];
		$deltakereogledere = $antall_ledere[$r['pl_id_from']]+$deltakere;
	
		// STATUS IFT KVOTE		
		if($deltakereogledere > $kvote)
			$status_kvote = '<span class="spektrum_fail">JA</span>';
		else
			$status_kvote = '<span class="spektrum_ok">Nei</span>';
	
		$sum['sys'] += $r['systemet_overnatting_spektrumdeltakere'];
		$sum['spe'] += $r['overnatting_spektrumdeltakere'];
		$sum['led'] += $antall_ledere[$r['pl_id_from']];
		$sum['ant'] += $deltakereogledere;
		
		echo  '<li class="oppsummering">'
			  .'<div class="fylke">'.utf8_encode($r['pl_name']).'</div>'
			  .'<div class="systemet_overnatting_spektrumdeltakere">'. $r['systemet_overnatting_spektrumdeltakere'].' &nbsp;</div>'
			  .'<div class="overnatting_spektrumdeltakere">'.$r['overnatting_spektrumdeltakere'].' &nbsp;</div>'
			 # .'<div class="ant">'.$ledere[$r['pl_id_from']]['fre'].' &nbsp;</div>'
			 # .'<div class="ant">'.$ledere[$r['pl_id_from']]['lor'].' &nbsp;</div>'
			 # .'<div class="ant">'.$ledere[$r['pl_id_from']]['son'].' &nbsp;</div>'
			 # .'<div class="ant">'.$ledere[$r['pl_id_from']]['man'].' &nbsp;</div>'
			  .'<div>'.$antall_ledere[$r['pl_id_from']].' &nbsp;</div>'
			  .'<div>'.$deltakereogledere.' &nbsp;</div>'
			  .'<div class="fre">'.$status_overnatting['fre'].' &nbsp;</div>'
			  .'<div class="lor">'.$status_overnatting['lor'].' &nbsp;</div>'
			  .'<div class="son">'.$status_overnatting['son'].' &nbsp;</div>'
			  .'<div class="man">'.$status_overnatting['man'].' &nbsp;</div>'
			  .'<div class="kvote">'.$status_kvote.' &nbsp;</div>'
			  .'<div class="kommentar">'.$kommentar.' &nbsp;</div>'
			  .'<br clear="all" />'
			  .'</li>';
	}
		
		echo  '<li class="oppsummering" id="row_footer">'
			  .'<div class="fylke">SUM</div>'
			  .'<div class="systemet_overnatting_spektrumdeltakere">'.$sum['sys'].' &nbsp;</div>'
			  .'<div class="overnatting_spektrumdeltakere">'.$sum['spe'].' &nbsp;</div>'
			  .'<div>'.$sum['led'].' &nbsp;</div>'
			  .'<div>'.$sum['ant'].' &nbsp;</div>'
			  .'<div class="fre"> &nbsp;</div>'
			  .'<div class="lor"> &nbsp;</div>'
			  .'<div class="son"> &nbsp;</div>'
			  .'<div class="man"> &nbsp;</div>'
			  .'<div class="kvote"> &nbsp;</div>'
			  .'<div class="kommentar"> &nbsp;</div>'
			  .'<br clear="all" />'
			  .'</li>';

	echo '</ul>';
}
?>