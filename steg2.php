<?php
function UKMV_steg2_inner($m) {
	$innslagutentitler = array(4,5,8,9);
	$sortert = array();
	
	_ret('<form action="?page='.$_GET['page'].'&save=steg2" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div> og g&aring; videre</div>'
		);
	
	$innslag = $m->videresendte();
	foreach($innslag as $trash => $inn) {
		$i = new innslag($inn['b_id']);
		$i->loadGEO();
		
		$titler = new titleInfo( $i->g('b_id'), $i->g('bt_form'), 'land', $m->videresendTil());
		$titler = $titler->getTitleArray();
		
		$i->videresendte($m->videresendTil());
		$personer = $i->personer();
		
		///
		if(in_array($i->g('bt_id'), $innslagutentitler)) {
			foreach($personer as $trash3 => $p) {
				if(empty($p['p_firstname']) && empty($p['p_firstname']))
					continue;
				$person = new person($p['p_id'], $inn['b_id']);

				$id = 'notitle_'.$inn['b_id'].'_'.$p['p_id'];
				$sortert[$i->g('bt_name')] .= 
					'<div class="innslag" id="container'.$id.'">'
					.'<div id="kommune">'.utf8_decode($i->g('kommune')).'</div>'
					.'<div id="navn">'. $p['p_firstname'].' '.$p['p_lastname'].'</div>'
					.'<br clear="all" />'
						
					.'<div class="korriger_person_navn">'
					.'<strong>Alder: </strong><input type="text" name="alder_'.$p['p_id'].'" value="'.$person->alder().'" />'
					.'<br />'
					.'<strong>Mobil: </strong><input type="text" name="mobilnummer_'.$p['p_id'].'" value="'.$p['p_phone'].'" /> '
					.'<br clear="all" />'
					.'</div>'
							
					.'</div>' # innslagscontainer
					.'<br clear="all" />'
						
					;
			}
		} else {
			foreach($titler as $trash2 => $t) {
				$id = $inn['b_id'].'_'.$t['t_id'];
				$katogsjan = $i->g('kategori_og_sjanger');

				/// DELTAKERE
				$deltakere = '';
				foreach($personer as $trash3 => $p){
					$pid = $inn['b_id'].'_'.$p['p_id'];
					$person = new person($p['p_id'], $inn['b_id']);
					$deltakere .= '<div class="korriger_person_navn">'
								. $p['p_firstname'] . ' ' . $p['p_lastname']
								.'<input type="text" name="alder_'.$p['p_id'].'" class="alder" value="'.$person->alder().'" />'
								.'<input type="text" name="mobilnummer_'.$p['p_id'].'" class="korriger_person_mobilnummer" value="'.$p['p_phone'].'" /> '
								.'<br clear="all" />'
								.'</div>';
				}
	
				$sortert[$i->g('bt_name')] .= '<div class="innslag" id="container'.$id.'">'
						.'<div id="kommune">'.utf8_decode($i->g('kommune')).'</div>'
						#._UKMV_ico($id)
						.'<div id="type">'.$i->g('bt_name').'</div>'
						.(!empty($katogsjan)?': ':'')
						.'<div id="katogsjan"> '. $katogsjan.'</div>'
						.'<div></div>'
						.'<div id="navn">'.$i->g('b_name').' - </div>'
						. '<div id="tittel">'
						.  '<input type="text" name="'.UKMV_tittelKorriger($i->g('bt_form'), $t['t_id'], $i).'" value="'. utf8_encode($t['name']).'" />'
						. '</div>'
									
						.'<div class="korriger">'
						. UKMV_innslagsBeskrivelse($i->g('bt_form'), $t['t_id'],$i)
				
						.'<h3>Deltakere: </h3>'
						.'<div class="korriger_person_navn header">'
						. 'Navn'
						. '<div class="alder header">Alder</div>'
						. '<div class="korriger_person_mobilnummer header">Mobil</div>'
						.'<br clear="all" />'
						.'</div>'
						.'<br clear="all" />'
						 . $deltakere
						.'<br clear="all" />'
					
						.'</div>'
						.'<br clear="all" />'
						.'</div>' # innslagscontainer
						.'<br clear="all" />'
						;
			}
		}
	}
	
	
	$i = 0;
	ksort($sortert);
	foreach($sortert as $tittel => $alleinnslag) {
		$i++;
		$undermeny .= '<div id="steg'.$i.'">'
					.'<a href="#'.$tittel.'">'.$i .': '. $tittel .'</a>'
					.'</div>';
		$alleinnslagsortertmedoverskrift .= '<a name="'.$tittel.'"></a><h2>'.$tittel.'</h2>'.$alleinnslag;
	}

	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 3: Supplér info</div>'

		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets '.$i.' deler: </div>'
		. $undermeny
		. '<div id="leadout"></div>'
		.'<br clear="all" />'
		.'</div>'

		.'<div id="forklaring_innslag">'
		.'<div style="float:left; padding-right: 8px; height: 100%; text-align:center;">'
			. UKMN_ico('info-button',32) 
		. '</div>'
		#.'Mange innslag har mangelfulle beskrivelser av sine tekniske behov o.l. '
		#.'<br />'
		#.'Se over alle felt p&aring; denne siden, og fyll inn all informasjon du gjerne ville '
		#.'<br />'
		#.' visst om innslaget f&oslash;r det kom til din m&oslash;nstring'
		#.'<div class="close">[skjul]</div>'
		. 'Kontrollér at alle opplysninger er korrekte, og s&aelig;rlig viktig er &aring; f&aring; s&aring; gode tekniske beskrivelser som mulig'
		. '<br clear="all" />'
		.'</div>'
		
		.$alleinnslagsortertmedoverskrift
		);
	
	
	
	_ret('</form>');
}

function UKMV_tittelKorriger($tabell, $t_id, $innslag) {
	$tabellID = explode('_', $tabell);
	$tabellID = $tabellID[2];
	switch($tabell) {
		case 'smartukm_titles_scene':
			return $tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_name';
		case 'smartukm_titles_exhibition':
			return $tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_title';
		case 'smartukm_titles_video':
			return $tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_title';
		;
	}
}
/**
 * UKMV_innslagsBeskrivelse * 
 * Finner ut hvilken beskrivelse som skal sjekkes
 *
 * @param $tabell
 * @param $t_id
 * @return HTML for beskrivelse
*/
function UKMV_innslagsBeskrivelse($tabell, $t_id, $innslag) {
	$tabellID = explode('_', $tabell);
	$tabellID = $tabellID[2];
	switch($tabell) {
		case 'smartukm_titles_scene':
		case 'smartukm_titles_other':
			$tittel = new SQL("SELECT * FROM `smartukm_titles_scene`
							   WHERE `t_id` = '#tid'",
							   array('tid'=>$t_id));
			$tittel = $tittel->run('array');
			
			$h = (int)($tittel['t_time'] / 3600);
			$m = (int)(($tittel['t_time'] - $h*3600) / 60);
			$s = (int)($tittel['t_time'] - $h*3600 - $m*60);
			$s = round($s/5)*5; 
			
			$tidSelect = '<select id="tid_min_'.$innslag->g('b_id').'_'.$t_id.'" class="tid_select" rel="'.$innslag->g('b_id').'_'.$t_id.'" name="videresendt_dummy_tid_min">';
			for ($i = 0; $i <= 10; $i++) {
				$tidSelect .= '<option value="'.$i.'"';
				if ($i == $m)
					$tidSelect .= ' selected="selected"';
				$tidSelect .= ';>'.$i.'</option>';
			}
			$tidSelect .= '</select> min ';
			$tidSelect .= '<select id="tid_sek_'.$innslag->g('b_id').'_'.$t_id.'" class="tid_select" rel="'.$innslag->g('b_id').'_'.$t_id.'" name="videresendt_dummy_tid_sek">';
			for ($i = 0; $i < 60; $i += 5) {
				$tidSelect .= '<option value="'.$i.'"';
				if ($i == $s)
					$tidSelect .= ' selected="selected"';
				$tidSelect .=  '>'.$i.'</option>';
			}
			$tidSelect .= '</select> sek ';
			
			return '<h3>Tekniske behov</h3>'
				.  '<textarea name="demand_'.$innslag->g('b_id').'_'.$t_id.'" '
				.  'class="korriger_tekniske">'.$innslag->g('td_demand').'</textarea>'
				. ($tabell == 'smartukm_titles_scene'	?
					   '<div class="korriger_label">Tid: '.$tidSelect
					.  '<input name="scene_'.$innslag->g('b_id').'_'.$t_id.'_time"'
					.  ' id="'.$innslag->g('b_id').'_'.$t_id.'_time"'
					.  ' class="korriger_data" type="hidden" value="'.$tittel['t_time'].'" />'
					.  '</div>'
					: '')
				;
		case 'smartukm_titles_exhibition':
			$tittel = new SQL("SELECT * FROM `smartukm_titles_exhibition`
							   WHERE `t_id` = '#tid'",
							   array('tid'=>$t_id));
			$tittel = $tittel->run('array');
			return '<h3>Beskrivelse</h3>'
				.  '<textarea name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_comments" '
				.  'class="korriger_tekniske">'.utf8_encode($tittel['t_e_comments']).'</textarea>'
				.  '<div class="korriger_label">Type:'
				.  '<input name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_type" '
				.  ' class="korriger_data" type="text" value="'.utf8_encode($tittel['t_e_type']).'" />'
				.  '<br clear="all" /></div>'
				.  '<div class="korriger_label">Teknikk:'
				.  '<input name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_technique" '
				.  ' class="korriger_data" type="text" value="'.utf8_encode($tittel['t_e_technique']).'" />'
				.  '<br clear="all" /></div>'
				.  '<div class="korriger_label">Format:'
				.  '<input name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_format" '
				.  ' class="korriger_data" type="text" value="'.utf8_encode($tittel['t_e_format']).'" />'
				.  '<br clear="all" /></div>'
				;

		case 'smartukm_titles_video':
			$tittel = new SQL("SELECT * FROM `smartukm_titles_video`
							   WHERE `t_id` = '#tid'",
							   array('tid'=>$t_id));
			$tittel = $tittel->run('array');
			
			$h = (int)($tittel['t_v_time'] / 3600);
			$m = (int)(($tittel['t_v_time'] - $h*3600) / 60);
			$s = (int)($tittel['t_v_time'] - $h*3600 - $m*60);
			$s = round($s/5)*5; 
						
			$tidSelect = '<select id="tid_min_'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'" class="tid_select" rel="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'" name="videresendt_dummy_tid_min">';
			for ($i = 0; $i <= 10; $i++) {
				$tidSelect .= '<option value="'.$i.'"';
				if ($i == $m)
					$tidSelect .= ' selected="selected"';
				$tidSelect .= ';>'.$i.'</option>';
			}
			$tidSelect .= '</select> min ';
			$tidSelect .= '<select id="tid_sek_'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'" class="tid_select" rel="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'" name="videresendt_dummy_tid_sek">';
			for ($i = 0; $i < 60; $i += 5) {
				$tidSelect .= '<option value="'.$i.'"';
				if ($i == $s)
					$tidSelect .= ' selected="selected"';
				$tidSelect .=  '>'.$i.'</option>';
			}
			$tidSelect .= '</select> sek ';
			
			return '<h3>Beskrivelse</h3>'
				.  '<textarea name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_comments" '
				.  ' class="korriger_tekniske">'.utf8_encode($tittel['t_v_comments']).'</textarea>'
				#.  '<div class="korriger_label">Filformat:'
				#.  '<input name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_format" '
				#.  ' class="korriger_data" type="text" value="'.utf8_encode($tittel['t_v_format']).'" />'
				#.  '<br clear="all" /></div>'
				.  '<div class="korriger_label">Tid:'.$tidSelect
				.  '<input name="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_time" '
				.	' id="'.$tabellID.'_'.$innslag->g('b_id').'_'.$t_id.'_time"'
				.  ' class="korriger_data" type="hidden" value="'.utf8_encode($tittel['t_v_time']).'" />'
				.  '<br clear="all" /></div>'
				;

		default:
			return '';
	}
} 
?>