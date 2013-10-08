<?php
function UKMV_steg35_inner($m) {
	$sortert = array();	
	$innslag = $m->videresendte();
	foreach($innslag as $trash => $inn) {
		$i = new innslag($inn['b_id']);
		$i->loadGEO();
		
		$titler = new titleInfo( $i->g('b_id'), $i->g('bt_form'), 'land', $m->videresendTil());
		$titler = $titler->getTitleArray();
		
		$i->videresendte($m->videresendTil());
		$personer = $i->personer();
		
		foreach($titler as $trash2 => $t) {			
			$id = $inn['b_id'].'_'.$t['t_id'];
	
			$valgtBilde = new SQL("SELECT `media`.`rel_id`
							  FROM `smartukm_videresending_media` AS `media`
							  JOIN `ukmno_wp_related` ON (`ukmno_wp_related`.`rel_id` = `media`.`rel_id`)
							  WHERE `media`.`b_id` = '#bid'
							  AND `m_type` = 'bilde'
	  						  AND (`t_id` = '0' OR `t_id` = '#tid' OR `t_id` IS NULL)",
							  array('bid'=>$i->g('b_id'),'tid'=>$t['t_id']));
			$valgtBilde = $valgtBilde->run('field','rel_id');
		
			$valgtBildeK = new SQL("SELECT `media`.`rel_id`
							  FROM `smartukm_videresending_media` AS `media`
							  JOIN `ukmno_wp_related` ON (`ukmno_wp_related`.`rel_id` = `media`.`rel_id`)
							  WHERE `media`.`b_id` = '#bid'
							  AND `m_type` = 'bilde_kunstner'
							  AND (`t_id` = '0' OR `t_id` = '#tid' OR `t_id` IS NULL)",
							  array('bid'=>$i->g('b_id'),'tid'=>$t['t_id']));
#			if($i->g('b_id')=='51442')
#				echo $valgtBildeK->debug();
			$valgtBildeK = $valgtBildeK->run('field','rel_id');
		
			$katogsjan = $i->g('kategori_og_sjanger');
			$items = $i->related_items();
			$krav[$i->g('bt_name')] = UKMV_innslagMediaKravTrueFalse($i->g('bt_form'));
			$kravTekst[$i->g('bt_name')] = UKMV_innslagMediaKrav($i);
			$overskrifter[$i->g('bt_form')] = $i->g('bt_name');
			
			$sortert[$i->g('bt_form')] .= '<tr>'
										.  '<td class="bname">'
										.   $i->g('b_name') . '<div class="kommune">'.utf8_decode($i->g('kommune')).'</div>'
										.  '</td>'
										;
					
					if ($krav[$i->g('bt_name')]['bilde']) {
						if ($valgtBilde > 0)
							$sortert[$i->g('bt_form')] .= '<td>'.UKMN_icoButton('circle-green', 16, 'Bilde OK').'</td>';
						else
							$sortert[$i->g('bt_form')] .= '<td>'
														.  '<a href="?page='.$_GET['page'].'&steg=3#b_'.$i->g('b_id').'">'
														.   UKMN_icoButton('circle-red', 16, 'Bilde mangler')
														.  '</a>'
														.'</td>'; 
					}
					if ($krav[$i->g('bt_name')]['kunstbilde']) {
						if ($valgtBildeK > 0 && $valgtBildeK !== $valgtBilde)
							$sortert[$i->g('bt_form')] .= '<td>'.UKMN_icoButton('circle-green', 16, 'Bilde OK').'</td>';
						else
							$sortert[$i->g('bt_form')] .= '<td>'
														.  '<a href="?page='.$_GET['page'].'&steg=3#b_'.$i->g('b_id').'">'
														.   UKMN_icoButton('circle-red', 16, 'Bilde mangler')
														.  '</a>'
														.'</td>';
					}
					if ($krav[$i->g('bt_name')]['video']) {
						if (is_array($items['video']))
							$sortert[$i->g('bt_form')] .= '<td>'.UKMN_icoButton('circle-green', 16, 'Video OK').'</td>';
						else 
							$sortert[$i->g('bt_form')] .= '<td>'
														.  '<a href="?page='.$_GET['page'].'&steg=3#b_'.$i->g('b_id').'">'
														.  UKMN_icoButton('circle-red', 16, 'Video mangler')
														.  '</a>'
														.'</td>';
					}
					
			$sortert[$i->g('bt_form')] .= '</tr>';

		}
	}
	
	
	$i = 0;
	foreach($sortert as $bt_form => $alleinnslag) {
		$tittel = $overskrifter[$bt_form];
		$i++;
		$undermeny .= '<div id="steg'.$i.'">'
					.'<a href="#'.$tittel.'">'.$i .': '. $tittel .'</a>'
					.'</div>';
		$alleinnslagsortertmedoverskrift .= '<a name="'.$tittel.'"></a>'
										.   '<h2 class="mediaoverskrift">'.$tittel.'</h2>'
										.	'<table cellpadding="0" cellspacing="0" class="medieoversikt">'
										.   '<thead>'
										.    '<tr>'
										.     '<th class="lefttext"> Innslag</th>'
										.     ($krav[$tittel]['bilde'] ? '<th width="75">Bilde</th>' : '')
										.     ($krav[$tittel]['video'] ? '<th width="75">Video</th>' : '')
										.     ($krav[$tittel]['kunstbilde'] ? '<th width="75">Bilde av kunstner</th>' : '')
										.    '</tr>'
										.   '</thead>'
										.   '<tbody>'
										.	$alleinnslag
										.   '</tbody>'
										.   '</table>'
										;
	}

	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 5: Kontroller media</div>'
		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets '.$i.' deler: </div>'
		. $undermeny
		. '<div id="leadout"></div>'
		.'<br clear="all" />'
		.'</div>'
		.'<div class="mediaskjema">'
		.$alleinnslagsortertmedoverskrift
		.'</div>'
		);
}
?>