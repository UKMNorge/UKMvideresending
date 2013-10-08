<?php
function UKMV_steg1_inner($m) {
	// Hva kan videresendes?
	$innslagutentitler = array(4,5,8,9);

	$PLvideresendTil = $m->videresendTil(true);
	$PLvideresendTilTillatt = $PLvideresendTil->getBandTypes();
	foreach($PLvideresendTilTillatt as $i => $bt)
		$tillattForVideresending[] = $bt['bt_id'];	
		
	if(!$PLvideresendTil->subscribable()) {
		if($PLvideresendTil->frist() == 'Tidspunkt ikke registrert') {
			if($PLvideresendTil->g('type')=='land') {
				_ret('<h3 style="color: #f3776f;">UKM Norge har ikke registrert sin mønstring, og videresending er derfor ikke mulig</h3>'
					.'Vennligst ta kontakt med din fylkeskontakt.'
					);
			} else {
				_ret('<h3 style="color: #f3776f;">Fylkeskontakten har ikke registrert sin mønstring, og videresending er derfor ikke mulig</h3>'
					.'Vennligst ta kontakt med din fylkeskontakt.'
					);
			}
		
		_ret('<form action="?page='.$_GET['page'].'&save=fristute" id="hugeform" method="post">');
		_ret('</form>');			
		} else {
		_ret('<h3 style="color: #f3776f;">Du kan fortsatt supplere informasjon, men videresendingsfristen gikk ut '
			. $PLvideresendTil->frist()
			.'</h3>'
			.'Dvs at du kan fortsatt laste opp media, supplere teknisk informasjon, fylle ut'
			.'leder- og informasjonsskjema osv. '
			.'<br />'
			.'Klikk deg frem i navigasjonsbaren ovenfor'
			);
		
		_ret('<form action="?page='.$_GET['page'].'&save=fristute" id="hugeform" method="post">');
		_ret('</form>');
		}
		return;
	}

	_ret('<form action="?page='.$_GET['page'].'&save=steg1" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div> og g&aring; videre</div>'
		);	
	$innslag = $m->innslag_alpha();
	if(is_array($innslag))
	foreach($innslag as $trash => $inn) {
		$i = new innslag($inn['b_id']);
		if(!in_array($i->g('bt_id'),$tillattForVideresending))
			continue;
		$i->loadGEO();
		
		$titler = new titleInfo( $i->g('b_id'), $i->g('bt_form'), get_option('site_type') );
		$titler = $titler->getTitleArray();

		$personer = $i->personer();
		$deltakerForklaring = ($i->g('bt_form')=='smartukm_titles_scene')
			? '(alle deltakere blir automatisk videresendt med innslaget)'
			: '(velg hvem som skal v&aelig;re med videre)';
		if(in_array($i->g('bt_id'), $innslagutentitler)) {
			foreach($personer as $trash3 => $p) {
				if(empty($p['p_firstname']) && empty($p['p_firstname']))
					continue;
				$id = 'notitle_'.$inn['b_id'].'_'.$p['p_id'];
				$sortert[$i->g('bt_name')] .= 
					'<div class="innslag" id="container'.$id.'">'
					.'<input type="hidden" style="float: right; width: 20px;" id="status'.$id.'" '
						.' name="videresendt_'.$id.'" value="'.UKMV_forwarded_p($inn['b_id'],$p['p_id'],$m).'" />'
					.'<div id="kommune">'.$i->g('kommune').'</div>'
					._UKMV_ico($id)
					.'<div id="navn">'. $p['p_firstname'].' '.$p['p_lastname'].'</div>'
					.'<br clear="all" />'
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
					$pid = $inn['b_id'].'_'.$p['p_id'].'_'.$t['t_id'];
	
					if($i->g('bt_form')=='smartukm_titles_scene')
						$deltakere .= $p['p_firstname'] . ' ' . $p['p_lastname'].', ';		
					else {
						$deltakere .= '<div class="deltaker" id="container_deltaker'.$pid.'_'.$t['t_id'].'">'
									.	_UKMV_deltaker_ico($pid.'_'.$t['t_id'])
									.	 $p['p_firstname'] . ' ' . $p['p_lastname']	
									. 	'<input type="hidden" name="videresendt_deltaker_'.$pid.'" '
										.'id="status_deltaker'.$pid.'_'.$t['t_id'].'" '
										.'value="'.UKMV_forwarded_p($inn['b_id'],$p['p_id'],$m).'" />'
									. '</div>';
					}
				}
	
				if($i->g('bt_form')=='smartukm_titles_scene')
					$deltakere = substr($deltakere, 0, strlen($deltakere)-2);
				/// DELTAKERE
				$sortert[$i->g('bt_name')] .= 
				'<div class="innslag" id="container'.$id.'">'
					.'<input type="hidden" style="float: right; width: 20px;" id="status'.$id.'" '
						.' name="videresendt_'.$id.'" value="'.UKMV_forwarded($inn['b_id'],$t['t_id'],$m).'" />'
					.'<div id="kommune">'.$i->g('kommune').'</div>'
					._UKMV_ico($id)
					.'<div id="type">'.$i->g('bt_name').'</div>'
					.(!empty($katogsjan)?': ':'')
					.'<div id="katogsjan"> '. $katogsjan.'</div>'
					.'<div></div>'
					.'<div id="navn">'.$i->g('b_name').' - </div><div id="tittel">'. utf8_encode($t['name']).'</div>'
					
					.'<div id="deltakere'.(($i->g('bt_form')=='smartukm_titles_scene')?'Auto':'').$id.'" class="deltakere" style="display:none;">'
					.'<h3>Deltakere <span id="forklaring">'.$deltakerForklaring.'</span></h3>'
					.$deltakere
					.'</div>'
					.'<br clear="all" />'
					.'</div>' # innslagscontainer
					.'<br clear="all" />'
					;
			}
		}
	}
	
	@ksort($sortert);
	
	$i = 0;
	if(is_array($sortert))
	foreach($sortert as $tittel => $alleinnslag) {
		$i++;
		$undermeny .= '<div id="steg'.$i.'">'
					.'<a href="#'.$tittel.'">'.$i .': '. $tittel .'</a>'
					.'</div>';
		$alleinnslagsortertmedoverskrift .= '<a name="'.$tittel.'"></a><h2>'.$tittel.'</h2>'.$alleinnslag;
	}

	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 1: Hvem skal videresendes?</div>'
		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets '.$i.' deler:</div>'
		. $undermeny
		. '<div id="leadout"></div>'
		. '<br clear="all" />'
		. '</div>'
		);


	_ret('<div id="forklaring_innslag">'
			.'<div style="float:left; width: 80px; vertical-align: bottom; '
			. 'height: 100%; text-align:center; margin-right: 20px; margin-top: 15px; margin-bottom: 5px;">'	
				. UKMN_ico('arrow-blue-down',32) 
			. '</div>'

			.'<strong>Klikk p&aring; den r&oslash;de sirkelen for &aring; videresende innslaget.</strong>'
			.'<br />'
#			.'<strong>Videresending m&aring; skje f&oslash;r '. $m->frist() .'</strong>'
#			.'<br />'
			.'N&aring;r alle du skal videresende har en gr&oslash;nn sirkel, trykker du '
			.'<br />&quot;Lagre og g&aring; videre&quot; oppe til h&oslash;yre'
		#	.'<div class="close">[skjul]</div>'
			. '<br clear="all" />'
			.'</div>'
		);
			
	_ret($alleinnslagsortertmedoverskrift);
#	_ret('<a href="#top">Til toppen</a>');
	_ret('</form>');
}


/**
 * _UKMV_deltaker_ico * 
 * Lager to videresendingsikoner som begge er skjult, for deltaker
 * Vises via jQuery
 *
 * @param $text String
 * @return void
*/
function _UKMV_ico($bid){
	return '<div class="videresend" id="videresendt'.$bid.'" rel="videresendt" style="display:none;">'
		.  UKMN_icoButton('circle-green', 16, 'videresendt')
		.  '</div>'
		. '<div class="videresend" id="ikke_videresendt'.$bid.'" rel="ikke_videresendt" style="display:none;">'
		.  UKMN_icoButton('circle-red', 16, 'ikke videresendt')
		.  '</div>';
}

/**
 * _UKMV_deltaker_ico * 
 * Lager to videresendingsikoner som begge er skjult, for deltaker
 * Vises via jQuery
 *
 * @param $text String
 * @return void
*/
function _UKMV_deltaker_ico($id){
	return '<div class="videresendDeltaker" id="videresendt_deltaker'.$id.'" rel="videresendt">'
		.  UKMN_ico('circle-green', 14)
		.  '</div>'
		. '<div class="videresendDeltaker" id="ikke_videresendt_deltaker'.$id.'" rel="ikke_videresendt">'
		.  UKMN_ico('circle-red', 14)
		.  '</div>';
}

/**
 * UKMV_forwarded * 
 * Sjekker om et innslag er videresendt fra denne mønstringen
 *
 * @param $b_id innslagsID
 * @param $m monstringsobjekt
 * @return int 1 / 0
*/
function UKMV_forwarded($b_id,$t_id, $m) {
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();

	$videresendt = new SQL("SELECT *
							FROM `smartukm_fylkestep`
							WHERE `pl_id` = '#pl_id'
							AND `b_id` = '#b_id'
							AND `t_id` = '#t_id'",
							array('pl_id'=>$videresendTil->g('pl_id'),'b_id'=>$b_id,'t_id'=>$t_id));
	$videresendt = $videresendt->run();
	if(mysql_num_rows($videresendt)==0)	
		return 0;
	return 1;
}

/**
 * UKMV_forwarded_p * 
 * Sjekker om et innslag er videresendt fra denne mønstringen
 *
 * @param $b_id innslagsID
 * @param $m monstringsobjekt
 * @return int 1 / 0
*/
function UKMV_forwarded_p($b_id,$p_id, $m) {
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();

	$videresendt = new SQL("SELECT *
							FROM `smartukm_fylkestep_p`
							WHERE `pl_id` = '#pl_id'
							AND `b_id` = '#b_id'
							AND `p_id` = '#p_id'",
							array('pl_id'=>$videresendTil->g('pl_id'),'b_id'=>$b_id,'p_id'=>$p_id));
	$videresendt = $videresendt->run();
	if(mysql_num_rows($videresendt)==0)	
		return 0;
	return 1;
}

?>