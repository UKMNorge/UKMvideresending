<?php

function UKMV_steg5_val($m){
	
	$infoskjema = new SQL("SELECT * 
							FROM `smartukm_videresending_infoskjema` 
							WHERE `pl_id` = '#plid' AND `pl_id_from` = '#fromplid'",
			array('fromplid'=>$m->g('pl_id'), 'plid'=>$m->videresendTil()));
	$values = $infoskjema->run('array');
	if(!is_array($values))
		$values = array();
	else
		foreach($values as $key => $val)
			$values[$key] = utf8_encode($val);
		
	return $values;
}

function UKMV_steg5_reisedetaljer($m,$val) {
	_ret('<a name="reisedetaljer"></a>'
		.'<br />'
		.'<ul class="reisedetaljer">'
		. '<li>'
		.  '<img src="'.UKMN_ico('buss',32,false).'" width="32" />'
		.  '<h2>Reisedetaljer'
		.   '<span class="forklaring">'
		.    '</span>'
		.  '</h2>'
		. '</li>'
		
		. '<li>'
 		.  '<div class="right">'

		.   '<div class="form-field">'
		.    '<label class="kortinput">'
		.	  'Antall unike deltakere videresendt fra m&oslash;nstringen'
		.     '<a id="unike_deltakere_hjelp" rel="'
				.'Om du har videresendt en deltaker i to forskjellige innslag, '
				.'skal systemet skj&oslash;nne at dette er samme person, '
				.'og telle vedkommende som 1. '
				.'Hvis totalantallet fra dine lister ikke stemmer med tall fra systemet, '
				.'m&aring; du dobbeltsjekke..">[mer info]</a>'
		.    '</label>'
		.    '<input type="text" name="systemet_overnatting_spektrumdeltakere" readonly="readonly" id="systemet_overnatting_spektrumdeltakere" class="kortinput" value="'.sizeof(UKMV_unike_deltakere($m)).'" />'
		.   '</div>'	


		.   '<div class="form-field">'
		.    '<label class="kortinput">'
		.	  'Antall deltakere som sover p&aring; Thor Heyerdal vgs'
		.    '</label>'
		.    '<input type="text" name="overnatting_spektrumdeltakere" id="overnatting_spektrumdeltakere" class="kortinput" value="'.$val['overnatting_spektrumdeltakere'].'" />'
		.   '</div>'	
		
		.   '<div class="form-field" id="avvik_overnatting_spektrumdeltakere" style="display: none;">'
		.    '<label>Forklar avvik mellom systemet og deres beregning</label>'
		.    '<textarea name="avvik_overnatting_spektrumdeltakere" class="forklaring">'.$val['avvik_overnatting_spektrumdeltakere'].'</textarea>'
		.   '</div>'

		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'

		
		#########################
		## INNREISE
		
		. '<li class="innreise">'		
		.  '<div class="right">'
		
		.  '<h3>Ankomst</h3>'

		.   '<div class="form-field">'
		.    '<label>Reisem&aring;te</label>'
		.    '<input type="text" name="reise_inn_mate" value="'.$val['reise_inn_mate'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Ankomstdato</label>'
		.    '<input type="text" name="reise_inn_dato" value="'.$val['reise_inn_dato'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label>Ankomsttid (ca. klokkeslett)</label>'
		.    '<input type="text" name="reise_inn_tidspunkt" value="'.$val['reise_inn_tidspunkt'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Ankomststed</label>'
		.    '<input type="text" name="reise_inn_sted" value="'.$val['reise_inn_sted'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label>Kommer alle deltakere samtidig?</label>'
			.   '<div class="samtidig">'
			.    '<input type="radio" name="reise_inn_samtidig" value="ja" '
					.($val['reise_inn_samtidig']=='ja'?'checked="checked"':'').' />'
			.    ' ja</div>'
			.   '<div class="samtidig">'
			.    '<input type="radio" name="reise_inn_samtidig" value="nei" '
					.($val['reise_inn_samtidig']=='nei'?'checked="checked"':'').' />'
			.    ' nei</div>'
		.   '</div>'
		.   '<div class="form-field" id="innreise">'
		.    '<label>Forklaring p&aring; ankomst, hvem kommer hvor n&aring;r?</label>'
		.    '<textarea name="reise_inn_samtidig_nei" class="forklaring">'.$val['reise_inn_samtidig_nei'].'</textarea>'
		.   '</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'


		########################
		## UTREISE
		. '<li class="utreise">'		
		.  '<div class="right">'
		
		.  '<h3>Avreise</h3>'

		.   '<div class="form-field">'
		.    '<label>Avreisedato</label>'
		.    '<input type="text" name="reise_ut_dato" value="'.$val['reise_ut_dato'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label>Avreisetidspunkt (ca. klokkeslett)</label>'
		.    '<input type="text" name="reise_ut_tidspunkt" value="'.$val['reise_ut_tidspunkt'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Avreisested</label>'
		.    '<input type="text" name="reise_ut_sted" value="'.$val['reise_ut_sted'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label>Drar alle deltakere samtidig?</label>'
			.   '<div class="samtidig">'
			.    '<input type="radio" name="reise_ut_samtidig" value="ja" '
					.($val['reise_ut_samtidig']=='ja'?'checked="checked"':'').' />'
			.    ' ja</div>'
			.   '<div class="samtidig">'
			.    '<input type="radio" name="reise_ut_samtidig" value="nei" '
					.($val['reise_ut_samtidig']=='nei'?'checked="checked"':'').' />'
			.    ' nei</div>'
		.   '</div>'
		.   '<div class="form-field" id="utreise">'
		.    '<label>Forklaring p&aring; avreise, hvem reiser hvor n&aring;r?</label>'
		.    '<textarea name="reise_ut_samtidig_nei" class="forklaring">'.$val['reise_ut_samtidig_nei'].'</textarea>'
		.   '</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'

		
		. '<li></li>'
		.'</ul>'
		.'<br clear="all" />');

}

function UKMV_steg5_matogallergier($m,$val) {
	_ret('<a name="matogallergier"></a>'
		.'<br />'
		.'<ul class="matogallergier">'
		. '<li>'
		.  '<img src="'.UKMN_ico('medical-case',32,false).'" width="32" />'
		.  '<h2>Spesielle behov mat og allergier'
		.   '<span class="forklaring">'
		.    '</span>'
		.  '</h2>'
		. '</li>'
		
		
		. '<li class="innreise">'		
		.  '<div class="right">'
		
		.  '<h3>Spesielle behov i forbindelse med bespisning</h3>'

		.   '<div class="form-field">'
		.    '<label>Antall vegetarianere</label>'
		.    '<input type="text" name="mat_vegetarianere" value="'.$val['mat_vegetarianere'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Antall med s&oslash;liaki</label>'
		.    '<input type="text" name="mat_soliaki" value="'.$val['mat_soliaki'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Antall som ikke kan spise svinekj&oslash;tt</label>'
		.    '<input type="text" name="mat_svinekjott" value="'.$val['mat_svinekjott'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Andre spesielle forhold om m&aring;ltider</label>'
		.    '<textarea name="mat_annet">'.$val['mat_annet'].'</textarea>'
		.   '</div>'

		.  '<br clear="all" />'
		.  '<h3>Andre spesielle forhold i forbindelse med tilrettelegging</h3>'

		.   '<div class="form-field">'
		.    '<label>Bevegelseshemninger</label>'
		.    '<input type="text" name="tilrettelegging_bevegelseshemninger" '
		.     'value="'.$val['tilrettelegging_bevegelseshemninger'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Andre spesielle behov</label>'
		.    '<textarea name="tilrettelegging_annet">'.$val['tilrettelegging_annet'].'</textarea>'
		.   '</div>'

		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'

		
		. '<li></li>'
		.'</ul>'
		.'<br clear="all" />');
}
function UKMV_steg5_nav() {
	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 5: Reiseskjema</div>'
		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets 2 deler: </div>'
#		. '<div id="steg1"><a href="#kunst">1: Kunst</a></div>'
		. '<div id="steg5"><a href="#reisedetaljer">1: Reisedetaljer</a></div>'
		. '<div id="steg6"><a href="#matogallergier">2: Mat og allergier</a></div>'
		. '<div id="leadout"></div>'
		. '<br clear="all" />'
		.'</div>');
}
?>