<?php
/* 
Part of: UKM Videresending rapporter :: hotell
Description: Genererer rapport over overnattinger for ledere
Author: S O Bjerkan
Version: 1.0
*/


function UKMV_rapporter_hotell() {
	$sql = new SQL("SELECT `pl_name`, `leder_navn`, `leder_e-post`, `leder_mobilnummer`, `leder_over_fre`,
					`leder_over_lor`, `leder_over_son`, `leder_over_man` FROM `smartukm_videresending_ledere` 
					JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `smartukm_videresending_ledere`.`pl_id_from`)
					WHERE `pl_season`=".get_option('season')."
					ORDER BY `pl_name`, `leder_navn` ASC");
	$res = $sql->run();
		
	$days = array('fredag', 'lørdag', 'søndag', 'mandag');
	
	$overnatter_hvor = (isset($_GET['overnatter_hvor']) ? $_GET['overnatter_hvor'] : 'ukmnorge');
	
	while ($r = mysql_fetch_assoc($res)) {
		if ($r['leder_over_fre'] === $overnatter_hvor)
			$overnatter[0][] = $r;
		if ($r['leder_over_lor'] === $overnatter_hvor)
			$overnatter[1][] = $r;
		if ($r['leder_over_son'] === $overnatter_hvor)
			$overnatter[2][] = $r;
		if ($r['leder_over_man'] === $overnatter_hvor)
			$overnatter[3][] = $r;
	}
		
	echo '<div class="rapport_print" id="printButton_oversikt" rel="hotell">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="ukm" id="hotell">'
		.	'<li>'
		.		UKMN_ico('city',32)
		.		'<h4>Overnatting '.($overnatter_hvor == 'spektrum' ? 'i Spektrum' : 'på hotell').'</h4>'
		.		'<br clear="all" />'
		.	'</li>';

		
		for ($i = 0; $i<sizeof($days); $i++) {
			echo '<li class="rapport_rom">'
               .'<h4>'.ucfirst($days[$i]).' <span class="forklaring">('.sizeof($overnatter[$i]).
			   ($overnatter_hvor == 'spektrum' ? ' ledere' : ' enkeltrom').')</span></h4>';
			   foreach ($overnatter[$i] as $key => $value) {
			   		echo '<div class="row">
                               <div class="fylke">'.utf8_encode($value['pl_name']).'</div>
                               <div class="navn">'.ucwords(utf8_encode($value['leder_navn'])).'</div>'.
                               '<div class="mobil">'.
							   (empty($value['leder_mobilnummer']) ? '' : $value['leder_mobilnummer']).'</div>'.
							   ($overnatter_hvor == 'spektrum' ? 
							   		'<div class="epost">'.$value['leder_e-post'].'</div>' : 
									'').
                       '<br clear="all" /></div>';
			   }
			   echo '</li>';
		}
		echo '</ul>';
}

?>