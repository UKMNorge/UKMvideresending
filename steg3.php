<?php
function UKMV_steg3_inner($m) {
	_ret('<form action="?page='.$_GET['page'].'&save=steg3" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div>og g&aring; videre</div>'
		);
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

			$valgtBildeK = $valgtBildeK->run('field','rel_id');

			#if($kategori !== $i->g('bt_name')) {
			#	_ret('<h1>'.$i->g('bt_name').'</h1>'
			#		. '<div style="margin-top:-14px;margin-bottom:10px;">'
			#		. '<strong>KRAV:</strong> '. UKMV_innslagMediaKrav($i).'</div>');

			#	$kategori = $i->g('bt_name');
			#}
			
			$id = $inn['b_id'].'_'.$t['t_id'];
			$katogsjan = $i->g('kategori_og_sjanger');
			$items = $i->related_items();
			$krav[$i->g('bt_name')] = UKMV_innslagMediaKrav($i);		
			$sortert[$i->g('bt_name')] .= 
					'<a name="b_'.$inn['b_id'].'"></a><div class="innslag" rel="media" id="container'.$id.'">'
					.'<div id="kommune">'.utf8_decode($i->g('kommune')).'</div>'
					.'<div id="navn">'.$i->g('b_name').' - </div><div id="tittel">'. utf8_encode($t['name']).'</div>'
					.'<a name="#a'.$i->g('b_id').'_'.$t['t_id'].'" style="display:none;" /></a>'
					.'<input type="hidden" '
						.'class="valgt_bilde" '
						.'id="bilde_'.$i->g('b_id').'_'.$t['t_id'].'" '
						.'name="valgt_bilde'.$i->g('b_id').'_'.$t['t_id'].'" '
						.'value="'.$valgtBilde.'" '
						.'/>'
					.($i->g('bt_id')==3 
						? '<input type="hidden" '
							.'class="valgt_kunstner_bilde" '
							.'id="kunstner_bilde_'.$i->g('b_id').'_'.$t['t_id'].'" '
							.'name="valgt_kunstner_bilde_'.$i->g('b_id').'_'.$t['t_id'].'" '
							.'value="'.$valgtBildeK.'" '
							.'/>'
						: '')
								
					. UKMV_innslag_bilder($items['image'], $i, $valgtBilde, $t['t_id'])	
					. UKMV_kunstnerbilde($items['image'], $i, $valgtBildeK, $t['t_id'])
					. UKMV_innslag_videoer($items['video'], $i)
				
					.'<br clear="all" />'
					.UKMV_lastOpp($i)
					.'<br clear="all" />'
					.'</div>' # innslagscontainer
					.'<br clear="all" />'
					;
		}
	}
	
	
	$i = 0;
	foreach($sortert as $tittel => $alleinnslag) {
		$i++;
		$undermeny .= '<div id="steg'.$i.'">'
					.'<a href="#'.$tittel.'">'.$i .': '. $tittel .'</a>'
					.'</div>';
		$alleinnslagsortertmedoverskrift .= '<a name="'.$tittel.'"></a>'
										.   '<h2>'.$tittel.'</h2>'
										.	'<div style="margin-top:-14px;margin-bottom:10px;">'
										. 	'<strong>KRAV:</strong> '. $krav[$tittel].'</div>'
										.$alleinnslag
										;
	}

	_ret(''
		. '<div class="UKMV_steg_title" id="subnavtitle">STEG 4: Last opp media</div>'
		.'<div class="UKMV_steg" id="subnav">'
		. '<div id="leadin">Skjemaets '.$i.' deler: </div>'
		. $undermeny
		. '<div id="leadout"></div>'
		.'<br clear="all" />'
		.'</div>'

		.'<div id="forklaring_innslag">'
		.'<div style="float:left; padding: 8px; height: 100%; text-align:center; padding-top: 0px; padding-bottom: 0px;">'
			. UKMN_ico('info-button',32) 
		. '</div>'
		.'Hvis m&oslash;nstringen hadde en nettredaksjon som publiserte til UKM.no er bilder og video allerede knyttet opp mot innslagene, '
		#.'<br />'
		.'og du kan derfor oppleve at flere av dine innslag allerede har nødvendig media'
		#.'<div class="close">[skjul]</div>'
		. '<br clear="all" />'
		.'</div>'
		
		.$alleinnslagsortertmedoverskrift
		);
	
	
#	_ret('<a href="#top">Til toppen</a>');
	_ret('</form>');

}
/**
 * UKMV_innslag_videoer * 
 * Returnerer en HTML-"liste" over alle videoer til det gitte innslaget
 *
 * @param array $videos
 * @param object $innslag
 * @return HTML
*/
function UKMV_innslag_videoer($videos, $innslag){
	if(!in_array($innslag->g('bt_id'), array(1,2,7)))
		return '';
		
	$container = '<div class="media">'
				.	'<h3>Video av innslaget</h3>'
				.'<div class="forklaring_video">'
					.'<div style="float:left; padding: 4px;padding-top:2px; margin-right:4px; height: 100%; text-align:center;">'
						. UKMN_ico('video-upload',24) 
					. '</div>'
					.'Det skal v&aelig;re video av alle videresendte innslag. '
					.'<br />Vi &oslash;nsker h&oslash;yeste kvalitet du har tilgjengelig.'
					.'<br />Det er ikke mulig &aring; se videoene p&aring; denne siden'
					.'<div class="close">[skjul]</div>'
					.'</div>'
				;

	if(!is_array($videos) && (!isset($_GET['videoupload']) || $_GET['videoupload'] != $innslag->g('b_id')))
		return $container
			. 'Det er ikke lastet opp noen video av innslaget'
			. '</div>';
	
	$container .= '<div class="videocontainer">';
	foreach($videos as $i => $video) {
		$link = 'http://video.ukm.no/'.$video['post_meta']['img'];#.'.jpg';
		$container .= '<div>'
					.  '<img src="'.$link.'" /> '
					.  '<br />'
					. 'Video fra '.strtolower($video['post_meta']['title']).'sm&oslash;nstringen.'
					. '</div>'
					;
	}
	$container .= '</div>';
	
	if (isset($_GET['videoupload']) && $_GET['videoupload'] == $innslag->g('b_id')) {
		$container .= '<br /><div class="videofeedback">En video er nylig lastet opp og snart tilgjenglig for dette innslaget.</div>';
	}
	
	return $container
			. '</div>';

}


/**
 * UKMV_lastOpp * 
 * Returnerer knapper og veiledning for opplasting av bilder + video
 *
 * @param object $innslag
 * @return HTML
*/
function UKMV_lastOpp($innslag) {
#	$video = '<a href="upload.php?page=UKM_videos&band='.$innslag->g('b_id').'&return=videresending" class="ukmv_lo_video" rel="'.$innslag->g('b_id').'">'
	$video = '<a href="#" class="ukmv_lo_video" rel="'.$innslag->g('b_id').'">'
			.UKMN_ico('video-upload', 20)
			.' Last opp video(er)</a>';
#	$bilde = '<a href="upload.php?page=UKM_images&c=upload&band='.$innslag->g('b_id').'" class="ukmv_lo_bilde" rel="'.$innslag->g('b_id').'">'
	$bilde = '<a href="#" class="ukmv_lo_bilde" rel="'.$innslag->g('b_id').'">'
			.UKMN_ico('camera', 20)
			.' Last opp bilde(r)</a>';

	if($innslag->g('bt_id')==2)
		$return = $video;
	elseif($innslag->g('bt_id')==3)
		$return = $bilde;
	else
		$return = $video . $bilde;
		
	return '<h3>Last opp media</h3>'
		.'<div class="forklaring_lastopp">'
					.'<div style="float:left; padding: 4px;padding-top:2px; margin-right:4px; height: 100%; text-align:center;">'
						. UKMN_ico('info-button',24) 
					. '</div>'
					.'Hvis dette innslaget mangler ett eller flere mediaobjekt bruker du knappene nedenfor. '
					.'<br />Alle bilder og videoer du laster opp knyttes til dette innslaget.'
					.'<div class="close">[skjul]</div>'
					.'</div>'
		. '<div class="ukmv_lastopp">'
		. $return 
		. '</div>'
		;
}

/**
 * UKMV_kunstnerbilde * 
 * Genererer en liste over valgbare bilder for kunstneren
 * $valgtBilde er fremmednøkkel til "related_wp"-tabellen
 *
 * @param array $images
 * @param object $innslag
 * @param int $valgtBilde
 * @return HTML
*/
function UKMV_kunstnerbilde($images, $innslag, $valgtBilde, $tittel) {
	if($innslag->g('bt_id')!=3)
		return '';

	$container = '<div class="media">'
				.	'<h3>Bilde av kunstner</h3>';

	if(!is_array($images))
		return $container
			. 'Det er ikke lastet opp noen bilder for innslaget'
			. '</div>';

	global $blog_id;
	if(sizeof($images)>1)
		$container .='<div class="forklaring_kunstnerbilder">'
					.'<div style="float:left; padding: 4px;padding-top:2px; margin-right:4px; height: 100%; text-align:center;">'
						. UKMN_ico('user-blue',24) 
					. '</div>'
					.'Alle kunstnere presenteres med et bilde i kunstkatalogen i passfoto-stil. '
					.'<br />Velg hvilket bilde som viser kunstneren.'
					.'<div class="close">[skjul]</div>'
					.'</div>';

	foreach($images as $k => $image) {
		$container .='<div class="mediaSelectKunstner">' 
					. '<a href="'.$image['blog_url'].'/files/'.$image['post_meta']['sizes']['large']['file'].'" '
					.	'title="<br /> Foto: '.$image['post_meta']['author'].'" '
					.   'callbackfunction="jQuery(\'#radio_kunstner_bilde_'.$image['rel_id'].'\').click();" '
					.   'callbacktext="Velg dette bildet" '
					.   'callbackanchor="a'.$innslag->g('b_id').'_'.$tittel.'" '
					.   'class="zoombox zgallery1'.$innslag->g('b_id').'" '
					. '>'
					.  '<img src="'.$image['blog_url'].'/files/'.$image['post_meta']['sizes']['thumbnail']['file'].'" '
					.	'rel="'.$innslag->g('b_id').'_'.$tittel.'"  id="'.$image['rel_id'].'" '
					.   ($valgtBilde==$image['rel_id']?'class="active" ':'')
					.   '/>'
					. '</a>'
					. '<input type="radio" class="mediaSelectKunstnerRadio" '
					.  'id="radio_kunstner_bilde_'.$image['rel_id'].'" '
					.  'name="kunstner_bilde_'.$innslag->g('b_id').'_'.$tittel.'" '
					.  'value="måsettes" '
					.  ($valgtBilde==$image['rel_id']?'checked="checked" ':'')
					.  '/>'
					.'</div>';
			}
	$container .= '<br clear="all" />'
				. '</div>';
	return $container;

}

/**
 * UKMV_innslag_bilder * 
 * Genererer en liste over valgbare bilder for innslaget
 * $valgtBilde er fremmednøkkel til "related_wp"-tabellen
 *
 * @param array $images
 * @param object $innslag
 * @param int $valgtBilde
 * @return HTML
*/
function UKMV_innslag_bilder($images, $innslag,$valgtBilde,$tittel){
	if($innslag->g('bt_id')==2)
		return '';
		
	$container = '<div class="media">'
				.	'<h3>Bilde til '
				.($innslag->g('bt_id')==3 	
					? 'kunstkatalog / litteraturhefte'
					: 'program'
				 )
				.'</h3>';

	if(!is_array($images))
		return $container
			. 'Det er ikke lastet opp noen bilder for innslaget'
			. '</div>';
	

	global $blog_id;
	if(sizeof($images)>1)
		$container .='<div class="forklaring_bilder">'
					.'<div style="float:left; padding: 4px;padding-top:2px; margin-right:4px; height: 100%; text-align:center;">'
						. UKMN_ico('camera',24) 
					. '</div>'		
					.'Velg hvilket bilde som skal brukes i programmet, ved å klikke p&aring; knappen under bildet. '
					.'<br />Valgt bilde markeres med gr&oslash;nn ramme. '
					.'Klikk <b>p&aring;</b> bildet for &aring; se st&oslash;rre utgave'
					.'<div class="close">[skjul]</div>'
					.'</div>';

	foreach($images as $k => $image) {
		$container .='<div class="mediaSelect">' 
					. '<a href="'.$image['blog_url'].'/files/'.$image['post_meta']['sizes']['large']['file'].'" '
					.	'title="<br /> Foto: '.$image['post_meta']['author'].'" '
					.   'callbackfunction="jQuery(\'#radio_bilde_'.$image['rel_id'].'\').click();" '
					.   'callbacktext="Velg dette bildet" '
					.   'callbackanchor="a'.$innslag->g('b_id').'_'.$tittel.'" '
					.   'class="zoombox zgallery1'.$innslag->g('b_id').'" '
					. '>'
					.  '<img src="'.$image['blog_url'].'/files/'.$image['post_meta']['sizes']['thumbnail']['file'].'" '
					.	'rel="'.$innslag->g('b_id').'_'.$tittel.'" id="'.$image['rel_id'].'" '
					.   ($valgtBilde==$image['rel_id']?'class="active" ':'')
					.   '/>'
					. '</a>'
					. '<input type="radio" class="mediaSelectRadio" '
					.  'id="radio_bilde_'.$image['rel_id'].'" '
					.  'name="bilde_'.$innslag->g('b_id').'_'.$tittel.'" '
					.  'value="måsettes" '
					.  ($valgtBilde==$image['rel_id']?'checked="checked" ':'')
					. '/>'
					.'</div>';
			}
	$container .= '<br clear="all" />'
				. '</div>';
	return $container;
}
?>