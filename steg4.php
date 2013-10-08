<?php
function UKMV_steg4_overnatting($m) {
	$unike_deltakere = sizeof(UKMV_unike_deltakere($m));
	_ret('<a name="overnatting"></a>'
		.'<br />'
		.'<ul class="overnatting">'
		. '<li>'
		.  '<img src="'.UKMN_ico('sleeping-shelter',32,false).'" width="32" />'
		.  '<h2>Oppsummering lederovernatting'
		.   '<span class="forklaring"><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '
		.  'Disse tallene er basert p&aring; lederskjemaet ovenfor og kan ikke endres manuelt'
		.    '</span>'
		.  '</h2>'
		. '</li>'


		// ANTALL LEDERE - INPUTS FOR SKJEMA OVENFOR		
		. '<li class="overnattingen">'
		.  '<div class="right">'
		
		.	'<div class="form-field" >'
		.    '<label class="kortinput">'
		.	 'Antall ledere'
		.    '</label>'
		.    '<input type="text" readonly="readonly" id="overnatting_antall_ledere" value="0" class="kortinput" />'
		.	'</div>'

		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'

		// SPESIFISERING OVER HVOR LEDERE SOVER		
		. '<li class="overnattingen">'
		.  '<div class="right">'
		
		.	'<div class="form-field">'
		.    '<label class="textlabel">'
		.	 'Fordeling overnatting'
		.    '</label>'
		.    '<br clear="all" />'
		    .	'<table class="tabell_overnatting_sumtable">'

			.    '<thead>'
		    .     '<tr>'
		    .      '<th rowspan="2" width="150">Hvor</th>'
		    .      '<th colspan="4" style="text-align: center;">Antall</th>'
		    .     '</tr>'
		    .     '<tr>'
   		    .      '<th>Søn</th>'
		    .      '<th>Man</th>'
		    .      '<th>Tir</th>'
		    .      '<th>Ons</th>'
		    .	  '</tr>'
		    .    '</thead>'
		    
		    .    '<tbody>'

		    .     '<tr>'
		    .      '<td>Thor Heyerdal vgs</td>'
		    .      '<td><input name="over_spektrum_fre" id="natt_spektrum_fre" type="text" readonly="readonly" value="0" class="lederovernatting" rel="spektrum" /></td>'
		    .      '<td><input name="over_spektrum_lor" id="natt_spektrum_lor" type="text" readonly="readonly" value="0" class="lederovernatting" rel="spektrum" /></td>'
		    .      '<td><input name="over_spektrum_son" id="natt_spektrum_son" type="text" readonly="readonly" value="0" class="lederovernatting" rel="spektrum" /></td>'
		    .      '<td><input name="over_spektrum_man" id="natt_spektrum_man" type="text" readonly="readonly" value="0" class="lederovernatting" rel="spektrum" /></td>'
		    .     '</tr>'
		    
		    .     '<tr>'
		    .      '<td>Hotell UKM Norge</td>'
		    .      '<td><input name="over_ukmnorge_fre" id="natt_ukmnorge_fre" type="text" readonly="readonly" value="0" class="lederovernatting" rel="ukmnorge" /></td>'
		    .      '<td><input name="over_ukmnorge_lor" id="natt_ukmnorge_lor" type="text" readonly="readonly" value="0" class="lederovernatting" rel="ukmnorge" /></td>'
		    .      '<td><input name="over_ukmnorge_son" id="natt_ukmnorge_son" type="text" readonly="readonly" value="0" class="lederovernatting" rel="ukmnorge" /></td>'
		    .      '<td><input name="over_ukmnorge_man" id="natt_ukmnorge_man" type="text" readonly="readonly" value="0" class="lederovernatting" rel="ukmnorge" /></td>'
		    .     '</tr>'

		    .     '<tr>'
		    .      '<td>Privat / annet</td>'
		    .      '<td><input name="over_privat_fre" id="natt_privat_fre" type="text" readonly="readonly" value="0" class="lederovernatting" rel="privat" /></td>'
		    .      '<td><input name="over_privat_lor" id="natt_privat_lor" type="text" readonly="readonly" value="0" class="lederovernatting" rel="privat" /></td>'
		    .      '<td><input name="over_privat_son" id="natt_privat_son" type="text" readonly="readonly" value="0" class="lederovernatting" rel="privat" /></td>'
		    .      '<td><input name="over_privat_man" id="natt_privat_man" type="text" readonly="readonly" value="0" class="lederovernatting" rel="privat" /></td>'
		    .     '</tr>'
		    
		    .    '</tbody>'
		    .   '</table>'
		  .	'</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		// ANTALL HOTELLROM
		. '<li class="overnattingen">'
		.  '<div class="right">'
		
		.	'<div class="form-field" >'
		.    '<label class="textlabel">'
		.	 'Antall hotellrom'
		.    '</label>'
		.    '<br />'
	    .       'Det er behov for totalt <span id="hotelldogn">0</span> hotelld&oslash;gn som UKM Norge bestiller'
	    .       '<br />'
	    .       'Pris per d&oslash;gn <span id="prisperdogn">895</span>,- (totalt <span id="pristotal">0</span>,-)'
		.	'</div>'

		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'


		. '<li class="overnattingen">'
		.  '<div class="right" id="spektrumovernatting">'
		
		.	'<div class="form-field" >'
		.    '<label class="textlabel">'
		.	 'Status antall ledere som sover på Thor Heyerdal vgs'
		.    '</label>'
		.	'</div>'
		
		.	'<div class="form-field" >'
		.    '<label class="kortinput">'
		.	 'S&oslash;ndag'
		.    '</label>'
		.    '<input name="status_spektrum_fre" id="status_spektrum_fre" type="text" readonly="readonly" value="0" class="kortinput" />'
		.	'</div>'

		.	'<div class="form-field" >'
		.    '<label class="kortinput">'
		.	 'Mandag'
		.    '</label>'
		.    '<input name="status_spektrum_lor" id="status_spektrum_lor" type="text" readonly="readonly" value="0" class="kortinput" />'
		.	'</div>'


		.	'<div class="form-field" >'
		.    '<label class="kortinput">'
		.	 'Tirsdag'
		.    '</label>'
		.    '<input name="status_spektrum_son" id="status_spektrum_son" type="text" readonly="readonly" value="0" class="kortinput" />'
		.	'</div>'


		.	'<div class="form-field" >'
		.    '<label class="kortinput">'
		.	 'Onsdag'
		.    '</label>'
		.    '<input name="status_spektrum_man" id="status_spektrum_man" type="text" readonly="readonly" value="0" class="kortinput" />'
		.	'</div>'

		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		);
		// EO ANTALL LEDERE
				
		/*
for($i=1; $i<10; $i++) {
		_ret('<li class="rom" id="rom_'.$i.'">'
		
			. '<div class="right">'
			
			.  '<div class="navn">'
			.   'Rom '. $i 
			.   '<span class="tittel"></span>'
			.  '</div>'

			.  '<div class="form-field">'
			.   '<label>Netter</label>'
			.   '<div class="natt"><input type="checkbox" name="rom['.$i.'][\'fre\']" val="ja" />fre</div>'
			.   '<div class="natt"><input type="checkbox" name="rom['.$i.'][\'lor\']" val="ja" />l&oslash;r</div>'
			.   '<div class="natt"><input type="checkbox" name="rom['.$i.'][\'son\']" val="ja" />s&oslash;n</div>'
			.   '<div class="natt"><input type="checkbox" name="rom['.$i.'][\'man\']" val="ja" />man</div>'
#			.   '<div class="natt"><input type="checkbox" name="rom['.$i.'][\'tir\']" val="ja" />tir</div>'
			.  '</div>'

			.  '<div class="form-field">'
			.   '<label>Type rom</label>'
			.   '<div class="romtype">'
			.    '<input type="radio" name="rom['.$i.'][\'type\']" value="enkeltrom" />'
			.    ' enkeltrom</div>'
			.   '<div class="romtype">'
			.    '<input type="radio" name="rom['.$i.'][\'type\']" value="dobbeltrom" />'
			.    ' dobbeltrom</div>'
			.  '</div>'

			.  '<div class="form-field romnavn">'
			.   '<label>Person 1</label>'
			.  '</div>'			
			.  '<div class="form-field">'
			.   '<label>Fornavn</label>'
			.   '<input type="text" name="rom['.$i.'][\'fornavn_1\']" class="rom_fornavn" />'
			.  '</div>'
			.  '<div class="form-field">'
			.   '<label>Etternavn</label>'
			.   '<input type="text" name="rom['.$i.'][\'etternavn_1\']" />'
			.  '</div>'			

			.  '<br clear="all" />'
			.  '<div class="andremann">'
			.  '<div class="form-field romnavn">'
			.   '<label>Person 2</label>'
			.  '</div>'			
			.  '<div class="form-field">'
			.   '<label>Fornavn</label>'
			.   '<input type="text" name="rom['.$i.'][\'fornavn_2\']" />'
			.  '</div>'
			.  '<div class="form-field">'
			.   '<label>Etternavn</label>'
			.   '<input type="text" name="rom['.$i.'][\'etternavn_2\']" />'
			.  '</div>'	
			.  '</div>'		
			
			. '</div>'
			. '<br clear="all" />'
			
			.'</li>'
			);
	}

	
	_ret('<li><a href="#" id="addRom">'.UKMN_ico('fancyplus',20).' Legg til ekstra rom</a></li>'
		.'</ul>'
		.'<br clear="all" />');
*/
	_ret('<li></li>'
		.'</ul>'
		.'<br clear="all" />');

}

function UKMV_steg4_ledermiddag() {
	$m = new monstring(get_option('pl_id'));
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();

	
	$sql = new SQL('SELECT * FROM `smartukm_videresending_ledere_middag` WHERE 
						`season`=#season AND `pl_from`=#pl_from AND `pl_to`=#pl_to', 
						array('season'=>get_option('season'), 'pl_from'=>get_option('pl_id'), 
						'pl_to'=>$videresendTil->g('pl_id')));
	$res = $sql->run('array');
	
	_ret('<a name="ledermiddag"></a>'
		.'<br />'
		.'<ul class="ledermiddag">'
		. '<li>'
		.  '<img src="'.UKMN_ico('chef',32,false).'" width="32" />'
		.  '<h2>Lederlunsj tirsdag'
		.   '<span class="forklaring">'
#		.    'Ledermiddagen er l&oslash;rdag kveld p&aring; Rica Bakklandet'
		.    '</span>'
		.  '</h2>'
		. '</li>'
		
		
		. '<li class="ledermiddag">'		
		.  '<div class="right">'

		.   '<div class="form-field">'
		.    '<label>'
		.	  'Navn p&aring; leder som deltar'
		.	  '<div class="betaltav">(betalt av UKM Norge)</div>'
		.    '</label>'
		.    '<input type="text" name="ledermiddag_ukm" value="'.utf8_encode($res['ledermiddag_ukm']).'"/>'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>'
		.	  'Navn p&aring; leder som deltar'
		.	  '<div class="betaltav">(betalt av fylket selv)</div>'
		.    '</label>'
		.    '<input type="text" name="ledermiddag_fylke1"  value="'.utf8_encode($res['ledermiddag_fylke1']).'"/>'
		.   '</div>'

		.   '<div class="form-field">'
		.    '<label>'
		.	  'Navn p&aring; leder som deltar'
		.	  '<div class="betaltav">(betalt av fylket selv)</div>'
		.    '</label>'
		.    '<input type="text" name="ledermiddag_fylke2" value="'.utf8_encode($res['ledermiddag_fylke2']).'" />'
		.   '</div>'
		
		.  '</div>'
		.  '<br clear="all" />'
		. '</li>'
		
		. '<li></li>'
		.'</ul>'
		.'<br clear="all" />');

}


function UKMV_steg4_nav() {
	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 6: Lederskjema</div>'
		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets 3 deler: </div>'
		. '<div id="steg2"><a href="#ledere">1: Ledere</a></div>'
		. '<div id="steg3"><a href="#overnatting">2: Overnatting</a></div>'
		. '<div id="steg4"><a href="#ledermiddag">3: Ledermiddag</a></div>'
		. '<div id="leadout"></div>'
		. '<br clear="all" />'
		.'</div>');
}

function UKMV_steg4_ledere($m) {
	$unike_deltakere = UKMV_unike_deltakere($m);
	$antall_ledere = ceil(sizeof($unike_deltakere) / 10);
	
	$m = new monstring(get_option('pl_id'));
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();
	
	$sql = new SQL('SELECT * FROM `smartukm_videresending_ledere` WHERE 
						`pl_season`=#season AND `pl_id_from`=#pl_from AND `pl_id_to`=#pl_to', 
						array('season'=>get_option('season'), 'pl_from'=>get_option('pl_id'), 
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
		
	$leder[1] = 'Hovedleder';
	$leder[2] = 'Leder med ansvar for utstillere';
	$leder[0] = 'Reiseleder';
	
		
	_ret('<a name="ledere"></a>'
		.'<br />'
		.'<ul class="ledere">'
		. '<li>'
		#.  '<img src="'.UKMN_ico('user-business',32,false).'" width="32" />'
		.  '<h2>Ledere'
		.   '<span class="forklaring">Husk at minst '
		.    '<span id="tall">'.$antall_ledere.'</span> '
		.    'ledere skal sove på Thor Heyerdal vgs hver natt'
		.    '</span>'
		.  '</h2>'
		. '</li>');
	for($i=1; $i<20; $i++) {
		_ret('<li class="leder" id="leder_'.$i.'">'
			. UKMN_ico($i == 1 ? 'user-business' : 'user-black'
					  ,32)
		
			. '<div class="right">'
			.  '<div class="navn">'
			. ($i < 3 ? $leder[$i]
					  : 'Leder '. $i 
						.   '<span class="tittel">'. $leder[(isset($leder[$i]) ? $i : 0)] .'</span>'
			  )
			.  '</div>'
			
			.  '<div class="form-field">'
			.	'<input type="hidden" name="leder['.$i.'][id]" value="'.$lederData[$i]['leder_id'].'">'
			.   '<label>Navn</label>'
			.   '<input type="text" name="leder['.$i.'][navn]" class="leder_navn" value="'.utf8_encode($lederData[$i]['leder_navn']).'" />'
			.  '</div>'

			.  '<div class="form-field">'
			.   '<label>E-post</label>'
			.   '<input type="text" name="leder['.$i.'][e-post]" value="'.utf8_encode($lederData[$i]['leder_e-post']).'" />'
			.  '</div>'
			 
			.  '<div class="form-field">'
			.   '<label>Mobilnummer</label>'
			.   '<input type="text" name="leder['.$i.'][mobilnummer]" value="'.utf8_encode($lederData[$i]['leder_mobilnummer']).'" />'
			.  '</div>'
			
			.($i > 2 ?
			   '<div class="form-field">'
			.   '<label>Er bare ledsager / turist?</label>'
			.   '<input type="checkbox" name="leder['.$i.'][turist]" value="1"'
			.	($lederData[$i]['leder_turist'] == 'j' ? ' checked="checked"' : '')
			.	' />Kryss av hvis ja'
			.  '</div>'
			:  '')			

		    .  '<div class="form-field">'
		    .   '<label>Overnatting</label>'
		    .	'<table class="tabell_overnatting">'

			.    '<thead>'
		    .     '<tr>'
		    .      '<th>Hvor</th>'
		    .      '<th>S&oslash;n</th>'
		    .      '<th>Man</th>'
		    .      '<th>Tir</th>'
		    .      '<th>Ons</th>'
		    .     '</tr>'
		    .    '</thead>'
		    
		    .    '<tbody>'

		    .     '<tr>'
		    .      '<td>Thor Heyerdal vgs</td>'
		    .      '<td><input name="leder['.$i.'][over_fre]"'
			. 		($lederData[$i]['leder_over_fre'] == 'spektrum' ? ' checked="checked"' : '')
			. 		' type="radio" value="spektrum" class="lederovernatting" rel="fre" /></td>'
		    .      '<td><input name="leder['.$i.'][over_lor]"'
			. 		($lederData[$i]['leder_over_lor'] == 'spektrum' ? ' checked="checked"' : '')
			.		' type="radio" value="spektrum" class="lederovernatting" rel="lor" /></td>'
		    .      '<td><input name="leder['.$i.'][over_son]"'
			. 		($lederData[$i]['leder_over_son'] == 'spektrum' ? ' checked="checked"' : '')
			.		' type="radio" value="spektrum" class="lederovernatting" rel="son" /></td>'
		    .      '<td><input name="leder['.$i.'][over_man]"'
			. 		($lederData[$i]['leder_over_man'] == 'spektrum' ? ' checked="checked"' : '')
			.		' type="radio" value="spektrum" class="lederovernatting" rel="man" /></td>'
		    .     '</tr>'

		    .     '<tr>'
		    .      '<td>Hotell UKM Norge</td>'
		    .      '<td><input name="leder['.$i.'][over_fre]"'
			. 		($lederData[$i]['leder_over_fre'] == 'ukmnorge' ? ' checked="checked"' : '')
			. 		' type="radio" value="ukmnorge" class="lederovernatting" rel="fre" /></td>'
		    .      '<td><input name="leder['.$i.'][over_lor]"'
			. 		($lederData[$i]['leder_over_lor'] == 'ukmnorge' ? ' checked="checked"' : '')
			.		' type="radio" value="ukmnorge" class="lederovernatting" rel="lor" /></td>'
		    .      '<td><input name="leder['.$i.'][over_son]"'
			. 		($lederData[$i]['leder_over_son'] == 'ukmnorge' ? ' checked="checked"' : '')
			.		' type="radio" value="ukmnorge" class="lederovernatting" rel="son" /></td>'
		    .      '<td><input name="leder['.$i.'][over_man]"'
			. 		($lederData[$i]['leder_over_man'] == 'ukmnorge' ? ' checked="checked"' : '')
			.		' type="radio" value="ukmnorge" class="lederovernatting" rel="man" /></td>'
		    .     '</tr>'


		    .     '<tr>'
		    .      '<td>Privat / annet</td>'
		    .      '<td><input name="leder['.$i.'][over_fre]"'
			. 		($lederData[$i]['leder_over_fre'] == 'privat' ? ' checked="checked"' : '')
			.		' type="radio" value="privat" class="lederovernatting" rel="fre" /></td>'
		    .      '<td><input name="leder['.$i.'][over_lor]"'
			. 		($lederData[$i]['leder_over_lor'] == 'privat' ? ' checked="checked"' : '')
			.		' type="radio" value="privat" class="lederovernatting" rel="lor" /></td>'
		    .      '<td><input name="leder['.$i.'][over_son]"'
			. 		($lederData[$i]['leder_over_son'] == 'privat' ? ' checked="checked"' : '')
			.		' type="radio" value="privat" class="lederovernatting" rel="son" /></td>'
		    .      '<td><input name="leder['.$i.'][over_man]"'
			. 		($lederData[$i]['leder_over_man'] == 'privat' ? ' checked="checked"' : '')
			.		' type="radio" value="privat" class="lederovernatting" rel="man" /></td>'
		    .     '</tr>'
		    
		    .    '</tbody>'
		    .   '</table>'
		    
			.  '</div>'


			. '<div class="nullstill">'
			.  '<a href="#" rel="'.$i.'">[nullstill skjema for denne lederen]</a>'
			. '</div>'
			
			. '</div>'
			. '<br clear="all" />'
			
			.'</li>'
			);
	}
	_ret('<li><a href="#ledere" id="addLeder">'.UKMN_ico('user-add',20).' Legg til ekstra leder / turist</a></li>');
	_ret('</ul>');
}



?>