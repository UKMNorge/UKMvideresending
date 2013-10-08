<?php
function UKMV_steg6_inner($m,$visRelasjoner=false) {
	_ret('<h1>Kvittering videresending '.$m->g('pl_name').'</h1>');
	UKMV_steg6_videresendte($m,$visRelasjoner);
	UKMV_steg6_ledere($m);
	UKMV_steg6_lederhotell($m);
}

function UKMV_steg6_lederhotell($m) {
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();
		
	$qry = new SQL("SELECT * FROM `smartukm_videresending_ledere`
					WHERE `pl_season` = '#season'
					AND `pl_id_from` = '#from'
					AND `pl_id_to` = '#to'",
					array('season'=>get_option('season'),
						  'from'=>$m->g('pl_id'),
						  'to'=>$videresendTil->g('pl_id'))
						);
	$ledere = $qry->run();
	
	$navn_dager = array('fredag'=>'Fredag','lordag'=>'L&oslash;rdag','sondag'=>'S&oslash;ndag','mandag'=>'Mandag');
	$navn_hvor = array('ukmnorge'=>'Hotell UKM Norge', 'privat'=>'Privat', 'spektrum'=>'Spektrum');
	$overnatting = array();
	
	while($r = mysql_fetch_assoc($ledere)) {
		$overnatting[$r['leder_over_fre']]['fredag'][] = $r;
		$overnatting[$r['leder_over_lor']]['lordag'][] = $r;
		$overnatting[$r['leder_over_son']]['sondag'][] = $r;
		$overnatting[$r['leder_over_man']]['mandag'][] = $r;
	}
	
	unset($overnatting['privat']);
	
	_ret('<ul class="kvittering">'
		. '<li>'
		.  '<img src="'.UKMN_ico('people',32,false).'" width="32" />'
		.  '<h2>Oppsummering lederovernatting'
		.   '<span class="forklaring"></span>'
		.  '</h2>'
		. '</li>'
		);
	
	foreach($overnatting as $hvor => $dag) {
		_ret(''
			. '<li>'
			.  '<div class="right">'
	 		.   '<div class="bekreftelse" id="ledere">'

			.   '<div class="overnattingssted">'.$navn_hvor[$hvor].'</div>'
			);
		
		$antallrom[$hvor] = 0;
		foreach($dag as $navn => $ledere) {
			_ret(
			    '<div class="overnattingsdag">'
				. $navn_dager[$navn]
				. ($hvor=='ukmnorge' ? ' <span class="bekreftelse_antall">'.sizeof($ledere).' rom</span>' : '')
				. '<br clear="all" />'
				);
			$antallrom[$hvor] += sizeof($ledere);

 			foreach($ledere as $i => $leder) {
	 			_ret('<div class="dagsdetaljer">'
				. '<span class="bekreftelse_navn">'.utf8_encode($leder['leder_navn']).'</span>'
				. '<span class="bekreftelse_personer">'.$leder['leder_mobilnummer'].'</span>'
				. '<span class="bekreftelse_personer">'.$ledertype.'</span>'
				. '</div>'
				);
			}

			_ret('</div>');
		}

		if($hvor == 'ukmnorge')
			_ret( '<br clear="all" /><br />'
				. '<div class="overnattingssted">'.$navn_hvor[$hvor].', totalt '.$antallrom[$hvor].' hotelld&oslash;gn'
				. '</div>'
				);


		_ret('' 
			.   '<br clear="all" />'
			.  '</div>'
			.  '</div>'
			.  '<br clear="all" />'
			. '</li>'
			);
	}
	_ret('<li></li>'
		.'</ul>'
		.'<br clear="all" />');

}

function UKMV_steg6_ledere($m) {
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();
	
	$sql = new SQL('SELECT * FROM `smartukm_videresending_ledere` WHERE 
						`pl_season`=#season AND `pl_id_from`=#pl_from AND `pl_id_to`=#pl_to', 
						array('season'=>get_option('season'), 'pl_from'=>$m->g('pl_id'), 
						'pl_to'=>$videresendTil->g('pl_id')));
	$res = $sql->run();
	
	$lederdata = array();
	$i = 3;
	while ($data = mysql_fetch_assoc($res)) {
		if ($data['leder_type'] == 'hoved')
			$i2 = 1;
		else if ($data['leder_type'] == 'utstilling')
			$i2 = 2;
		else {
			$i2 = $i;
			$i++;
		}
		$lederData[$i2] = $data;
	}
	
	_ret('<ul class="kvittering">'
		. '<li>'
		.  '<img src="'.UKMN_ico('user-business',32,false).'" width="32" />'
		.  '<h2>Ledere'
		.   '<span class="forklaring"></span>'
		.  '</h2>'
		. '</li>'
		
		. '<li>'
 		.  '<div class="right">'
 		.   '<div class="bekreftelse" id="ledere">'
 		);
	if(is_array($lederData))
	foreach($lederData as $i => $leder) {
		if($leder['leder_type']=='hoved')
			$ledertype = 'Hovedleder';
		elseif($leder['leder_type']=='utstilling')
			$ledertype = 'Utstillingsleder';
		elseif($leder['leder_turist']=='j')
			$ledertype = 'Ledsager / turist';
		else
			$ledertype = '';
		_ret(utf8_encode($leder['leder_navn'])
			. '<span class="bekreftelse_personer">'.$leder['leder_mobilnummer'].'</span>'
			. '<span class="bekreftelse_personer">'.$ledertype.'</span>'
			. '<br clear="all" />'
			);
	}
	_ret(    '<br clear="all" />'
		.   '</div>'
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		. '<li></li>'
		.'</ul>'
		.'<br clear="all" />');

}

function UKMV_steg6_videresendte($m,$visRelasjoner) {
	if($visRelasjoner)
		_ret('<h2>'.$m->g('pl_name').'</h2>');
	$innslagutentitler = array(4,5,8,9);
	$alle_videresendte = array();
	$videresendte = $m->videresendte();
	$teller_videresendte_innslag = 0;
	$tittellose_titler = array();
	$antall_unike_deltakere = array();
	foreach($videresendte as $i => $inn) {
		$innslag = new innslag($inn['b_id']);
		$innslag->loadGEO();
		
		$titler = new titleInfo( $innslag->g('b_id'), $innslag->g('bt_form'), 'land', $m->videresendTil());
		$titler = $titler->getTitleArray();

		$innslag->videresendte($m->videresendTil());
		$personer = $innslag->personer();
		$antall_innslag[$innslag->g('bt_name')] += 1;
		if(in_array($innslag->g('bt_id'), $innslagutentitler)) {
			$tittellose_titler[] = $innslag->g('bt_name');
			foreach($personer as $trash3 => $p) {
				if(empty($p['p_firstname']) && empty($p['p_firstname']))
					continue;
					$antall_deltakere[$innslag->g('bt_name')][$p['p_id']] = $p['p_firstname']; 				
					if(isset($antall_unike_deltakere[$p['p_id']]))
						$forklaringunike = str_replace($p['p_firstname'].' '.$p['p_lastname'].', ','', $forklaringunike)
										  . $p['p_firstname'].' '.$p['p_lastname'].', ';
					$antall_unike_deltakere[$p['p_id']][] = $p['p_firstname'];
				$alle_videresendte[$innslag->g('bt_name')] .= '<div class="bekreftelse_ett_innslag">'
															.  $innslag->g('b_name')
										. ($visRelasjoner 
											? UKMV_steg6_relsjonssjekk_visRelasjoner($innslag->g('b_id'),$m->videresendTil()) 
											: '')
															#.  '<span class="bekreftelse_personer">('. sizeof($personer) . ' personer)</span>'
															. '</div>';
			}
		} else {
			$teller_videresendte_innslag++;
			$valgtBilde = new SQL("SELECT `rel_id`
							  FROM `smartukm_videresending_media`
							  WHERE `b_id` = '#bid'
							  AND `m_type` = 'bilde'",
							  array('bid'=>$innslag->g('b_id')));
			$valgtBilde = $valgtBilde->run('field','rel_id');
			
			$valgtBildeK = new SQL("SELECT `rel_id`
							  FROM `smartukm_videresending_media`
							  WHERE `b_id` = '#bid'
							  AND `m_type` = 'bilde_kunstner'",
							  array('bid'=>$innslag->g('b_id')));
			$valgtBildeK = $valgtBildeK->run('field','rel_id');
			$items = $innslag->related_items();


			foreach($titler as $trash2 => $t) {
				foreach($personer as $j => $p){
					$antall_deltakere[$innslag->g('bt_name')][$p['p_id']] = $p['p_firstname']; 				
					if(isset($antall_unike_deltakere[$p['p_id']]))
						$forklaringunike = str_replace($p['p_firstname'].' '.$p['p_lastname'].', ','', $forklaringunike)
										  . $p['p_firstname'].' '.$p['p_lastname'].', ';
					$antall_unike_deltakere[$p['p_id']][] = $p['p_firstname'];
				}
				
				$oppfyltmedia = false;
				
				$mediakrav = UKMV_innslagMediaKravTrueFalse($innslag->g('bt_form'));
				# BILDER
				if($mediakrav['bilde'])
					if($valgtBilde > 0)
						$oppfyltmedia = true;
					else
						$oppfyltmedia = false;

				# KUNSTNERBILDE
				if($mediakrav['kunstbilde'])
					if($valgtBildeK > 0 && $oppfyltmedia)
						$oppfyltmedia = true;
					else
						$oppfyltmedia = false;
				# VIDEO
				if ($mediakrav['video'])
					if((is_array($items['video']) && $oppfyltmedia) || is_array($items['video']) && !($mediakrav['kunstbilde'] && $mediakrav['bilde'])) 
						$oppfyltmedia = true;
					else
						$oppfyltmedia = false;	

						

				$alle_videresendte[$innslag->g('bt_name')] .= '<div class="bekreftelse_ett_innslag">'
															.  $innslag->g('b_name')
										. ($visRelasjoner 
											? UKMV_steg6_relsjonssjekk_visRelasjoner($innslag->g('b_id'),$m->videresendTil()) 
											: '')
															.  '<span class="bekreftelse_personer'.(sizeof($personer)==0?' manglerdeltakere':'').'">'
															.   sizeof($personer) . ' person'.(sizeof($personer)==1?'':'er')
															.  '</span>'
															.  '<span class="bekreftelse_media '.(!$oppfyltmedia?' manglerdeltakere':'').'">Media '.($oppfyltmedia ? 'OK' : 'MANGLER').'</span>'
															. '</div>';
			}
		}
	}

	
	$forklaringunike = substr($forklaringunike, 0, strlen($forklaringunike)-2);
	if(!empty($forklaringunike))
		$forklaringunike .= ' deltar i flere innslag men telles som én';
	
	_ret('<ul class="kvittering">'
		. '<li>'
		.  '<img src="'.UKMN_ico('people',32,false).'" width="32" />'
		.  '<h2>Videresendte deltakere og innslag'
		.   '<span class="forklaring"></span>'
		.  '</h2>'
		. '</li>'
		
		. '<li>'
 		.  '<div class="right">'
		
		.   '<div class="bekreftelse_total">'
			.'Det er videresendt '.$teller_videresendte_innslag.' innslag og '.sizeof($antall_unike_deltakere).' unike deltakere'
		.    '<div class="deltakereforklaring">'.$forklaringunike.'</div>'
		.   '</div>'
		
		.   '<br clear="all" />'
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		);
	
	
	
	_ret( '<li>'
 		.  '<div class="right">'
		);
	ksort($alle_videresendte);
	foreach($alle_videresendte as $tittel => $html)
		_ret('<div class="bekreftelse_gruppe">'.$tittel.' '
			.(!in_array($tittel, $tittellose_titler)
				? '<span class="bekreftelse_antall">('.$antall_innslag[$tittel].' innslag, '.sizeof($antall_deltakere[$tittel]).' deltakere)</span>'
				: '<span class="bekreftelse_antall">('.sizeof($antall_deltakere[$tittel]).' deltaker'.(sizeof($antall_deltakere[$tittel])!=1?'e':'').')</span>'
				)

			.'</div>'
			.'<div class="bekreftelse_innslag">'
			. $html
			.'</div>'
			);


	_ret(   '<br clear="all" />'
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		. '<li></li>'
		.'</ul>'
		.'<br clear="all" />');
}
?>