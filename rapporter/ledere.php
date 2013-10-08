<?php
/* 
Part of: UKM Videresending rapporter :: ledere
Description: Genererer rapport over ledere
Author: S O Bjerkan
Version: 1.0
*/


function UKMV_rapporter_ledere() {
	$sql = new SQL("SELECT `pl_id_from`, `pl_name`, `leder_type`, `leder_navn`, `leder_e-post`, `leder_mobilnummer`, `leder_turist`
					FROM `smartukm_videresending_ledere` 
					JOIN `smartukm_place` AS `place` ON (`place`.`pl_id` = `smartukm_videresending_ledere`.`pl_id_from`)
					WHERE `pl_season`=".get_option('season')."
					ORDER BY `pl_name`, `leder_navn` ASC");
	$res = $sql->run();
	
	$ledere = array();
	while ($r = mysql_fetch_assoc($res)) {
		if (isset($_GET['turist']) && $r['leder_turist'] === 'n')
			continue;
		$ledere[$r['pl_id_from']][] = $r;
	}
	
	echo '<div class="rapport_print" id="printButton_oversikt" rel="ledere">'
		. UKMN_icoButton('print',35,'Skriv ut',11)
		.'</div>'
		.'<br />'
		.'<ul class="ukm" id="ledere" style="width: 700px;">'
		.	'<li>'
		.		UKMN_ico('city',32)
		.		'<h4>'.(isset($_GET['turist']) ? 'Turister' : 'Ledere og turister').'</h4>'
		.		'<br clear="all" />'
		.	'</li>';

		foreach ($ledere as $key => $ledere_fylke) {
			echo '<li class="rapport_rom">'
               .'<h4>'.utf8_encode($ledere_fylke[0]['pl_name']).' <span class="forklaring">('.sizeof($ledere_fylke)
			   .' leder'.(sizeof($ledere_fylke)==1?'':'e').')</span></h4>';
			   $ledere_html = array();
			   foreach ($ledere_fylke as $key => $value) {
			   		switch($value['leder_type']) {
			   			case 'hoved':
			   				$tittel = 'Hovedleder';
			   				$style = ' style="color: #1e4a45; font-weight: bold;"';
			   			break;
			   			case 'utstilling':
			   				$tittel = 'Utstillingsleder';
			   				$style = ' style="color: #f3776f;"';
			   			break;
						default:
							if($value['leder_turist']=='j')
								$tittel = 'Turist';
							else 
				   				$tittel = 'Reiseleder';
			   				$style = '';
			   			break;
			   		}
			   		
			   		$ledere_html[$value['leder_type']] .= '<div class="row">'
			   				.'<div class="navn"'.$style.'>'.ucwords(utf8_encode($value['leder_navn'])).' ('.$tittel.')</div>'
                            .'<div class="mobil">'
								.(empty($value['leder_mobilnummer']) ? '' : $value['leder_mobilnummer']).'</div>'
						   	.'<div class="epost">'.$value['leder_e-post'].'</div>'
							.'<br clear="all" />'
						.'</div>';
			   }
			   echo $ledere_html['hoved'] . $ledere_html['utstilling'] . $ledere_html['reise'];
			   echo '</li>';
		}
		echo '</ul>';
}

?>