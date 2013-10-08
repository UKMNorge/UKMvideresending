var viserLeder = 0;

function kalkulerOvernatting() {
	jQuery('.tabell_overnatting_sumtable').find('input.lederovernatting').each(
		function() {
			jQuery(this).val(0);
		}
	);
	jQuery('.tabell_overnatting').find('input.lederovernatting').each(
		function(){
			dag = jQuery(this).attr('rel')
			hvor = jQuery(this).val();
			if(jQuery(this).attr('checked') == 'checked') {
				allerede = jQuery('#natt_'+hvor+'_'+dag).val()*1;
				jQuery('#natt_'+hvor+'_'+dag).val((allerede+1)*1);
				jQuery('#natt_'+hvor+'_'+dag).change();
			}
		}
	);
	jQuery('#hotelldogn').html((jQuery('#natt_ukmnorge_fre').val()*1)
							+ (jQuery('#natt_ukmnorge_lor').val()*1)
							+ (jQuery('#natt_ukmnorge_son').val()*1)
							+ (jQuery('#natt_ukmnorge_man').val()*1)
							  );
	jQuery('#pristotal').html( (jQuery('#hotelldogn').html()*1) * (jQuery('#prisperdogn').html()*1) );
	
	ingenISpektrum();
}

function ingenISpektrum(){
	jQuery('.tabell_overnatting_sumtable input[rel=spektrum]').each(
		function(){
			if(jQuery(this).val()==0) {
				id = jQuery(this).attr('id').replace('natt_','status_');
				tall = jQuery('#tall').html()*1;
				jQuery('#'+id).css('background-color','#f69a9b');
				jQuery('#'+id).val(tall + " for lite");
			}
		}
	);
}


jQuery(document).ready(
	function(){	
		jQuery('ul.videresendingsskjema_svar').find('li:odd').addClass('odd');
		jQuery('ul.videresendingsskjema_svar').find('li:first').addClass('first');
		jQuery('ul.videresendingsskjema_svar').find('li:last').addClass('last');

		jQuery('div.row:odd').addClass('row_odd');
		jQuery('div.row:even').addClass('row_even');

		jQuery('ul.ukm').find('li:odd').addClass('odd');
		jQuery('ul.ukm').find('li:first').addClass('first');
		jQuery('ul.ukm').find('li:last').addClass('last');

		jQuery('div.row:odd').addClass('row_odd');
		jQuery('div.row:even').addClass('row_even');
			
		jQuery('ul.kvittering').find('li:odd').addClass('odd');
		jQuery('ul.kvittering').find('li:first').addClass('first');
		jQuery('ul.kvittering').find('li:last').addClass('last');

		jQuery('ul.rapport').find('li:odd').addClass('odd');
		jQuery('ul.rapport').find('li:first').addClass('first');
		jQuery('ul.rapport').find('li:last').addClass('last');
		
		jQuery('ul.ledere li:odd').addClass('odd');
		jQuery('ul.ledere li:first').addClass('first');
		jQuery('ul.ledere li:last').addClass('last');

		jQuery('ul.kunst').find('li:odd').addClass('odd');
		jQuery('ul.kunst').find('li:first').addClass('first');
		jQuery('ul.kunst').find('li:last').addClass('last');
		
		jQuery('ul.matogallergier li:first').addClass('first');
		jQuery('ul.matogallergier li:last').addClass('last');

		jQuery('ul.ledermiddag li:first').addClass('first');
		jQuery('ul.ledermiddag li:last').addClass('last');

		jQuery('ul.reisedetaljer li:odd').addClass('odd');
		jQuery('ul.reisedetaljer li:first').addClass('first');
		jQuery('ul.reisedetaljer li:last').removeClass('odd').addClass('last');

		jQuery('ul.overnatting li:odd').addClass('odd');
		jQuery('ul.overnatting li:first').addClass('first');
		jQuery('ul.overnatting li:last').addClass('last');
	
		jQuery('.takk').effect("shake", { times:3, distance:15, direction:'up' }, 50);
			
		jQuery('.viskommentar').click(
			function(){
				alert(jQuery('#kommentar_'+jQuery(this).attr('rel')).html());
			}
		);
		
			
		jQuery('.lederovernatting').click(
			function() {
				kalkulerOvernatting();
			}
		);
				
		jQuery('#addLeder').click(
			function(){
				jQuery('ul.ledere').find('li:hidden:first').slideDown();
				return false;
			}
		);
		
		jQuery('#addKolli').click(
			function() {
				jQuery('ul#kunstkolli').find('li:hidden:first').slideDown();
				return false;
			}
		);
		jQuery('.kunstkolli input[rel=kolliname]').each(
			function() {
				if(jQuery(this).val()!='')
					jQuery(this).parent().parent().parent().show();
			}
		);
		
		jQuery('.mediaSelectRadio').click(
			function(){
				jQuery(this).parent().parent().find('div a img').attr('class','notactive');
				var selectedIMG = jQuery(this).parent().find('img');
				selectedIMG.attr('class','active');
				jQuery('#bilde_'+selectedIMG.attr('rel')).val(selectedIMG.attr('id'));
			}
		);
		
		jQuery('.mediaSelectKunstnerRadio').click(
			function(){
				jQuery(this).parent().parent().find('div a img').attr('class','notactive');
				var selectedIMG = jQuery(this).parent().find('img');
				selectedIMG.attr('class','active');
				jQuery('#kunstner_bilde_'+selectedIMG.attr('rel')).val(selectedIMG.attr('id'));
			}
		);

		jQuery('.deltaker').each(function(){
			id = jQuery(this).attr('id').replace('container_deltaker','');
			status_deltaker = jQuery('input#status_deltaker'+id).val();			
			if(status_deltaker == '1' || status_deltaker == 1)
				videresendt_p(id);
			else
				ikke_videresendt_p(id);
		});

		jQuery('.videresend').click(
			function(){
				id = jQuery(this).attr('id').replace(jQuery(this).attr('rel'),'');
				jQuery(this).hide();
				if(jQuery(this).attr('rel')=='videresendt')
					ikke_videresendt(id);
				else
					videresendt(id);
			}
		);

		jQuery('.videresendDeltaker').click(
			function(){
				id = jQuery(this).attr('id').replace(jQuery(this).attr('rel'),'');
				jQuery(this).hide();
				if(jQuery(this).attr('rel')=='videresendt')
					ikke_videresendt(id);
				else
					videresendt(id);
			}
		);

		jQuery('.close').click(
			function(){
				jQuery(this).parent().slideUp();
				tmpclass = jQuery(this).parent().attr('class');
				if(tmpclass != undefined && tmpclass != null)
					jQuery('.'+tmpclass).slideUp();
			}
		);

		jQuery(function($){
 		   $('a.zoombox').zoombox({
		        theme       : 'darkprettyphoto',   //available themes : zoombox,lightbox, prettyphoto, darkprettyphoto, simple
		        opacity     : 0.8,              // Black overlay opacity
        		duration    : 400,              // Animation duration
		        animation   : true,             // Do we have to animate the box ?
        		width       : 600,              // Default width
		        height      : 300,              // Default height
        		gallery     : false,             // Allow gallery thumb view
		        autoplay : false,
		        overflow: false                // Autoplay for video
		    });
		});		
	
		jQuery('ul.ledere li').each(
			function(){
				id = jQuery(this).attr('id');
				if(id !== undefined && id !== null) {
					id = id.replace('leder_','');
					if(id < 3)
						jQuery(this).show();
				}
				navn = jQuery(this).find('input.leder_navn').val();
				if(navn != undefined && navn != null && navn != "")
					jQuery(this).show();
			}
		);

		if(jQuery('#overnatting_antall_ledere').val() < jQuery('#tall').html())
			for(i=0; i<jQuery('#tall').html()-1; i++)
				jQuery('#addLeder').click();

		jQuery('.romtype').each(
			function() {
				input = jQuery(this).find('input');
				if(input.val() == 'enkeltrom' && input.attr('checked')=='checked')
					jQuery(this).parent().parent().find('div.andremann').slideUp();
				if(input.val() == 'dobbeltrom' && input.attr('checked')=='checked')
					jQuery(this).parent().parent().find('div.andremann').slideDown();
			}
		);

		jQuery('.samtidig').each(
			function() {
				jQuery(this).click(
					function(){
						jQuery(this).find('input').attr('checked','checked');
						if(jQuery(this).find('input').val() == 'ja')
							jQuery(this).parent().parent().find('textarea.forklaring').parent().slideUp();
						else
							jQuery(this).parent().parent().find('textarea.forklaring').parent().slideDown();
					}
				);
			
				input = jQuery(this).find('input');
				if(input.val() == 'ja' && input.attr('checked')=='checked')
					jQuery(this).parent().parent().find('textarea.forklaring').parent().slideUp();
				if(input.val() == 'nei' && input.attr('checked')=='checked')
					jQuery(this).parent().parent().find('textarea.forklaring').parent().slideDown();
			}
		);
			
		jQuery('.romtype').click(
			function() {
				jQuery(this).find('input').attr('checked','checked');
				if(jQuery(this).find('input').val()=='dobbeltrom')
					jQuery(this).parent().parent().find('div.andremann').slideDown();
				else
					jQuery(this).parent().parent().find('div.andremann').slideUp();
			}
		);
		
		jQuery('#addRom').click(
			function() {
				jQuery('li.rom:hidden:first').slideDown();
				return false;
			}
		);
		
		jQuery('li.rom').each(
			function(){
				if(jQuery(this).attr('id').replace('rom_','') < 3)
					jQuery(this).show();
				if(jQuery(this).find('input.rom_fornavn').val() != "")
					jQuery(this).show();
			}
		);
		
		jQuery('#unike_deltakere_hjelp').click(
			function(){
				alert(jQuery(this).attr('rel'));
				return false;
			}
		);
		
		jQuery('.videofeedback').effect("shake", { times:3, distance:15 }, 60);
		
		
		jQuery('#overnatting_spektrumdeltakere').change(
			function(){
				tall = jQuery('#systemet_overnatting_spektrumdeltakere').val();
				if(tall !== jQuery(this).val() && jQuery(this).val() != '')
					jQuery('#avvik_overnatting_spektrumdeltakere').slideDown();
				else
					jQuery('#avvik_overnatting_spektrumdeltakere').slideUp();
			}
		);
		
		jQuery('#overnatting_spektrumdeltakere').change();
		
		
		jQuery('#the_one_and_only_nav_bar a').click(
			function(){
				nyttsteg = jQuery(this).attr('href').replace('?page=UKMVideresending&steg=','');
				mal = jQuery('#hugeform').attr('action');
				jQuery('#hugeform').attr('action', mal+'&returnto='+nyttsteg);
				jQuery('#hugeform').submit();
				return false;
			}
		);
		
		jQuery('.nullstill a').click(
			function() {
				id = jQuery(this).attr('rel');
				jQuery('.leder#leder_'+id+' input[type=radio]').each(
					function(){
						jQuery(this).prop('checked', false);
					}
				);
				jQuery('.leder#leder_'+id+' input[type=text]').each(
					function(){
						jQuery(this).val('');
					}
				);
				kalkulerOvernatting();
				return false;
			}
		);

		jQuery('#overnatting_antall_ledere').val(0);
		
		jQuery('.leder_navn').each(
			function() {
				if(jQuery(this).val().length > 0)
					jQuery('#overnatting_antall_ledere').val((jQuery('#overnatting_antall_ledere').val()*1)+1);
				jQuery(this).change(
					function() {
						jQuery('#overnatting_antall_ledere').val(0);
						jQuery('.leder_navn').each(
							function() {
								if(jQuery(this).val().length > 0)
									jQuery('#overnatting_antall_ledere').val((jQuery('#overnatting_antall_ledere').val()*1)+1);		
							}
						);
					}
				);
			}
		);
		
		jQuery('.tabell_overnatting_sumtable input.lederovernatting').change(
			function(){
				if(jQuery(this).attr('rel') == 'spektrum') {
					tall = jQuery('#tall').html()*-1;
					hvor = jQuery(this)
					antall = jQuery(this).val()*1;
					if(antall == 0)
						alert('ingen');
					id = jQuery(this).attr('id').replace('natt_','status_');
					status = antall + tall;
					if(status > -1)
						status = 'OK'
					if(status == 'OK') {
						jQuery('#'+id).css('background-color','#a0cf67');
						jQuery('#'+id).val(status);
					} else {
						jQuery('#'+id).css('background-color','#f69a9b');
						jQuery('#'+id).val((status*-1) + " for lite");
					}
				} else {
					ingenISpektrum();
				}
			}
		);
		kalkulerOvernatting();
		
		jQuery('.tid_select').change(
			function(){
				var rel = jQuery(this).attr('rel');
				var mins = jQuery('#tid_min_'+rel).val();
				var sek = mins*60;
				sek += jQuery('#tid_sek_'+rel).val()*1;
				
				
				
				jQuery('#'+rel+'_time').val(sek);
			}
		);

		jQuery('#hugesubmit').click(
			function(){
				jQuery('#hugeform').submit();
			}
		);
		
		jQuery('.ukmv_lo_video').click(
			function() {
				jQuery('#hugeform').attr('action', jQuery('#hugeform').attr('action')+'&lo_video='+jQuery(this).attr('rel'));
				jQuery('#hugeform').submit();
				return false;
			}
		);
		jQuery('.ukmv_lo_bilde').click(
			function() {
				jQuery('#hugeform').attr('action', jQuery('#hugeform').attr('action')+'&lo_bilde='+jQuery(this).attr('rel'));
				jQuery('#hugeform').submit();
				return false;
			}
		);

		jQuery('.innslag').each(function(){

			// MEDIA-SIDEN
			// Innslag med bilder hvor ingen er valgt, skal automatisk få valgt siste bilde
			if(jQuery(this).attr('rel')=='media') {
				if(jQuery(this).find('input.valgt_bilde').val()=='')
					jQuery(this).find('.media div.mediaSelect').last().find('input.mediaSelectRadio').click();
				else if(jQuery(this).find('input.valgt_kunstner_bilde').val()=='')
					jQuery(this).find('.media div.mediaSelectKunstner').last().find('input.mediaSelectKunstnerRadio').click();

			// VELG INNSLAG Å VIDERESENDE-SIDEN
			} else {
				do_videresend(jQuery(this).attr('id').replace('container',''));
			}
		});
		
		
		jQuery('.leveringersammesomhente').click(
			function(){
				if(jQuery(this).attr('checked') == 'checked') {
					if(jQuery(this).val()=='ja')
						jQuery('#annenleveringsadresse').slideUp();
					else
						jQuery('#annenleveringsadresse').slideDown();
				}
			}
		);
		
		jQuery('.leveringersammesomhente:checked').each(
			function(){
				if(jQuery(this).val()=='ja')
					jQuery('#annenleveringsadresse').slideUp();
				else
					jQuery('#annenleveringsadresse').slideDown();
			}
		);
		
		
		jQuery('.rapport_print').click(
			function(){
				jQuery('#'+jQuery(this).attr('rel')).printElement();
				return false;
			}
		);
				
		jQuery('.fraktseddel').click(
			function(){
				toggleRapportFrakt('fraktbrev');

				plid = jQuery(this).attr('id').split('|');
				jQuery.post(ajaxurl,
							{	action: 'UKMV_rapport_fraktseddel',
								cookie: encodeURIComponent(document.cookie),
								pl_id: plid[1],
								pl_id_from: plid[0]
							},
							function(response){
								jQuery('#fraktseddelen').html(response);
								return false;
							}
				);
			}
		);
		
		jQuery('#printButton_fraktseddel_back').click(
			function(){
				toggleRapportFrakt('oversikt');
			}
		);

	}
);

function toggleRapportFrakt(vis) {
	if(vis == 'fraktbrev') {
		jQuery('#kunstrapport').slideUp();
		jQuery('#printButton_oversikt').hide();

		jQuery('#fraktseddelen').html('Vennligst vent, laster fraktseddel');
		jQuery('#fraktseddelen').slideDown();
		jQuery('#printButton_fraktseddel').show();
		jQuery('#printButton_fraktseddel_back').show();
	} else {
		jQuery('#kunstrapport').slideDown();
		jQuery('#printButton_oversikt').show();

		jQuery('#fraktseddelen').slideUp();
		jQuery('#printButton_fraktseddel').hide();
		jQuery('#printButton_fraktseddel_back').hide();
	}
}

function do_videresend(id) {
	if(jQuery('input#status'+id).val() == '1' || jQuery('input#status'+id).val() == 1)
		videresendt(id);
	else
		ikke_videresendt(id);
}

function videresendt(id) {
	jQuery('#status'+id).val(1);
	jQuery('#videresendt'+id).show();
	jQuery('#deltakere'+id).slideDown().effect("shake", { times:3, distance:10 }, 50);
	jQuery('#deltakereAuto'+id).slideDown();
}
function ikke_videresendt(id) {
	jQuery('#status'+id).val(0);
	jQuery('#ikke_videresendt'+id).show();
	jQuery('#deltakere'+id).slideUp();
	jQuery('#deltakereAuto'+id).slideUp();
}
function videresendt_p(id) {
	jQuery('#status_deltaker'+id).val(1);
	jQuery('#videresendt_deltaker'+id).show();
	jQuery('#ikke_videresendt_deltaker'+id).hide();
}
function ikke_videresendt_p(id) {
	jQuery('#status_deltaker'+id).val(0);
	jQuery('#ikke_videresendt_deltaker'+id).show();
	jQuery('#videresendt_deltaker'+id).hide();
}