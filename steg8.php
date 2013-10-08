<?php
function UKMV_steg8_inner($m) {
	$kunstskjema = new SQL("SELECT * 
							FROM `smartukm_videresending_infoskjema_kunst` 
							WHERE `pl_id` = '#plid' AND `pl_id_from` = '#fromplid'",
			array('fromplid'=>$m->g('pl_id'), 'plid'=>$m->videresendTil()));
	$kunstskjema = $kunstskjema->run('array');
	if(is_array($kunstskjema))
		foreach($kunstskjema as $key => $val)
			$kunstskjema[$key] = utf8_encode($val);

	$kolli = new SQL("SELECT * 
					 FROM `smartukm_videresending_infoskjema_kunst_kolli` 
					 WHERE `pl_id` = '#plid' AND `pl_id_from` = '#fromplid'",
			array('fromplid'=>$m->g('pl_id'), 'plid'=>$m->videresendTil()));
	$kolli = $kolli->run();
	while($r = mysql_fetch_assoc($kolli))
		foreach($r as $key => $val) 
			$kunstskjema[$key.'_'.$r['kolli_id']] = utf8_encode($val);

	$val = $kunstskjema;


	_ret('<div id="forklaring_innslag">'
			.'<div style="float:left; width: 80px; vertical-align: bottom; '
			. 'height: 100%; text-align:center; margin-right: 20px;">'	
				. UKMN_ico('info-button',32) 
			. '</div>'
			
#			.'<strong>Alle fylkets kunstverk hentes samlet p&aring; ett sted!</strong>'
			.'<strong>I 2013 SKAL ALL KUNST SENDES TIL LARVIK!</strong>'
			.'<br />'
			.'se info 1 for mer informasjon'
			. '<br clear="all" />'
			.'</div>'
		);


	_ret('<a name="kunsthent"></a>'
		.'<br />'
		.'<ul class="kunst" style="display:none;">'		# IKKE GYLDIG 2013
		. '<li>'
		.  '<img src="'.UKMN_ico('delivery',32,false).'" width="32" />'
		.  '<h2>Kunsthenting'
		.   '<span class="forklaring">OBS: Hentes med bud!'
		.    '</span>'
		.  '</h2>'
		. '</li>'
		
		
		. '<li class="kunsthenting">'		
		.  '<div class="right">'

		.   '<div class="form-field">'
		.    '<label>Henteadresse</label>'
		.    '<input type="text" name="kunst_henteadresse" value="'.$val['kunst_henteadresse'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label class="kortinput">Etasje</label>'
		.    '<input type="text" name="kunst_etasje" class="kortinput" value="'.$val['kunst_etasje'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label class="kortinput">Inngang nr / fra</label>'
		.    '<input type="text" name="kunst_inngang" class="kortinput" value="'.$val['kunst_inngang'].'" />'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>Heis</label>'
		.    '<div style="float:left;"><input type="radio" name="kunst_heis" value="ja" '.($val['kunst_heis']=='ja' ? 'checked="checked"':'').' /> ja</div>'
		.    '<div style="float:left;"><input type="radio" name="kunst_heis" value="nei" '.($val['kunst_heis']=='nei' ? 'checked="checked"':'').' /> nei</div>'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label class="kortinput">Postnummer</label>'
		.    '<input type="text" name="kunst_postnummer" class="kortinput" value="'.$val['kunst_postnummer'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label>Poststed</label>'
		.    '<input type="text" name="kunst_poststed" value="'.$val['kunst_poststed'].'" />'
		.   '</div>'
		
		.   '<div class="form-field">'
		.    '<label><br /></label>'
		.   '</div>'


		.   '<div class="form-field">'
		.    '<label class="kortinput">Kunsten kan hentes fra og med</label>'
		.    '<input type="text" name="kunst_hentesnar" class="kortinput" value="'.$val['kunst_hentesnar'].'" />'
		.   '</div>'
		
		
		.   '<div class="form-field">'
		.    'Spedit&oslash;ren vil hente forsendelsen etter ovenst&aring;ende dato, innenfor normal arbeidstid.'
		.   '</div>'

		
		.   '<div class="form-field">'
		.    '<label>Hvis det er spesielle dager / tidspunkt som m&aring; unng&aring;s, spesifiser dette her:</label>'
		.    '<textarea name="kunst_hentesnar_detaljer">'.$val['kunst_hentesnar_detaljer'].'</textarea>'
		.   '</div>'
		
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		.'</ul>');




	_ret('<a name="kontaktperson"></a>'
		.'<br />'
		.'<ul class="kunst" style="display:none;">'		# IKKE GYLDIG 2013
		. '<li>'
		.  '<img src="'.UKMN_ico('user-business',32,false).'" width="32" />'
		.  '<h2>Kontaktperson ved henting'
		.   '<span class="forklaring"></span>'
		.  '</h2>'
		. '</li>'		
				
		. '<li class="kunsthenting">'		
		.  '<div class="right">'
		
		.   '<div class="form-field">'
		.    '<label>Navn</label>'
		.    '<input type="text"  name="kunst_kontaktperson_ved_henting" value="'.$val['kunst_kontaktperson_ved_henting'].'" />'
		.   '</div>'
		
	
		.   '<div class="form-field">'
		.    '<label class="kortinput">Mobilnummer</label>'
		.    '<input type="text" name="kunst_kontaktperson_ved_henting_mobil" value="'.$val['kunst_kontaktperson_ved_henting_mobil'].'" class="kortinput" />'
		.   '</div>'
		
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		.'</ul>'
		
		);
		
				


	_ret('<a name="kolli"></a>'
		.'<br />'
		.'<ul class="kunst" id="kunstkolli" style="display:none;">'		# IKKE GYLDIG 2013
		. '<li>'
		.  '<img src="'.UKMN_ico('kolli',32,false).'" width="32" />'
		.  '<h2>Kolli'
		.   '<span class="forklaring">Oppgi detaljert info per kolli!'
		.    '</span>'
		.  '</h2>'
		. '</li>'	
		);
	for($i=1; $i<6; $i++) {
	
		_ret('<li class="kunstkolli" '.($i!=1?'style="display:none;"':'').'>'		
			.  '<div class="right">'
			.   '<h3>Kolli '.$i.'</h3>'
			
			.   '<div class="form-field">'
			.    '<label class="kortinput">Vekt (i kg)</label>'
			.    '<input type="text" rel="kolliname" name="kolli['.$i.'][kolli_vekt]" class="kortinput" value="'.$val['kolli_vekt_'.$i.''].'" />'
			.   '</div>'

			.   '<div class="form-field">'
			.    '<label class="kortinput">Bredde (i cm)</label>'
			.    '<input type="text" name="kolli['.$i.'][kolli_bredde]" class="kortinput" value="'.$val['kolli_bredde_'.$i.''].'" />'
			.   '</div>'

			.   '<div class="form-field">'
			.    '<label class="kortinput">H&oslash;yde (i cm)</label>'
			.    '<input type="text" name="kolli['.$i.'][kolli_hoyde]" class="kortinput" value="'.$val['kolli_hoyde_'.$i.''].'" />'
			.   '</div>'

			.   '<div class="form-field">'
			.    '<label class="kortinput">Dybde (i cm)</label>'
			.    '<input type="text" name="kolli['.$i.'][kolli_dybde]" class="kortinput" value="'.$val['kolli_dybde_'.$i.''].'" />'
			.   '</div>'
			.  '</div>'
			.  '<br clear="all" />'
			. '</li>'
			);
	}

		
	_ret('<li><a href="#" id="addKolli">'.UKMN_ico('fancyplus',20).' Legg til ekstra kolli</a></li>'
		.'</ul>'
		.'<br clear="all" />');
		
		
		
	_ret('<a name="kunstretur"></a>'
		.'<br />'
		.'<ul class="kunst" style="display:none;">'		# IKKE GYLDIG 2013
		. '<li>'
		.  '<img src="'.UKMN_ico('info',32,false).'" width="32" />'
		.  '<h2>Eventuelle tilleggsopplysinger'
		.   '<span class="forklaring"></span>'
		.  '</h2>'
		. '</li>'		
				
		. '<li class="kunsthenting">'		
		.  '<div class="right">'
	
		.   '<div class="form-field">'
		.    '<label>Skriv alt du tror noen har behov for &aring; vite</label>'
		.    '<textarea name="kunst_kommentarer">'.$val['kunst_kommentarer'].'</textarea>'
		.   '</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		.'</ul>'
		
		);
		
		
		
		
	// KUNSTRETUR	
	_ret('<a name="kunstretur"></a>'
		.'<br />'
		.'<ul class="kunst" style="display:none;">'		# IKKE GYLDIG 2013
		. '<li>'
		.  '<img src="'.UKMN_ico('mailbox',32,false).'" width="32" />'
		.  '<h2>Retur av kunst etter festivalen'
		.   '<span class="forklaring"><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '
		.   'Skriv inn fullstendig bud-adresse'
		.    '</span>'
		.  '</h2>'
		. '</li>'		
				
		. '<li class="kunsthenting">'		
		.  '<div class="right">'


		.   '<div class="form-field">'
		.    '<label>Er leveringsadressen samme som henteadressen?</label>'
			.   '<div class="samtidig">'
			.    '<input type="radio" class="leveringersammesomhente" name="kunst_leveringsadresse_samme" value="ja" '
					.($val['kunst_leveringsadresse_samme']=='ja'?'checked="checked"':'').' />'
			.    ' ja</div>'
			.   '<div class="samtidig">'
			.    '<input type="radio" class="leveringersammesomhente" name="kunst_leveringsadresse_samme" value="nei" '
					.($val['kunst_leveringsadresse_samme']=='nei'?'checked="checked"':'').' />'
			.    ' nei</div>'
		.   '</div>'

		.   '<div class="form-field" style="display:none;" id="annenleveringsadresse">'
		.    '<label>Returadresse</label>'
		.    '<textarea name="kunst_postretur">'.$val['kunst_postretur'].'</textarea>'
		.   '</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		.'</ul>'
		
		);
		
}
?>