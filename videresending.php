<?php

// Hva kan videresendes?
$innslagutentitler = array(4,5,8,9);

/**
 * UKMV_innslagMediaKrav * 
 * Skriver ut hvilke krav som stilles til denne typen innslag
 *
 * @param object $innslag
 * @return HTML
*/
function UKMV_innslagMediaKrav($innslag) {
	switch($innslag->g('bt_form')) {
		case 'smartukm_titles_scene':
		case 'smartukm_titles_other':
			return 'Bilde og video av innslaget';
		
		case 'smartukm_titles_exhibition':
			return 'Bilde av kunstverket og kunstneren';
		
		case 'smartukm_titles_video':
			return 'Originalfil av video (h&oslash;yoppl&oslash;selig)';
	}
}
/**
 * UKMV_innslagMediaKrav * 
 * Returnerer et array med hva som kreves
 *
 * @param object $innslag
 * @return array(bilde=>bool, kunstbilde=>bool, video=>bool)
*/
function UKMV_innslagMediaKravTrueFalse($bt_form) {
	switch($bt_form) {
		case 'smartukm_titles_scene':
		case 'smartukm_titles_other':
			return array('bilde'=>true, 'kunstbilde'=>false, 'video'=>true);
		
		case 'smartukm_titles_exhibition':
			return array('bilde'=>true, 'kunstbilde'=>true, 'video'=>false);
		
		case 'smartukm_titles_video':
			return array('bilde'=>false, 'kunstbilde'=>false, 'video'=>true);
	}
}

/**
 * UKMVideresending_gui * 
 * Funksjon for å generere gui, samt håndtere lagring
 *
 * @return HTML gui
*/
function UKMVideresending_gui() {
	$m = new monstring(get_option('pl_id'));
#	UKMV_save($m);
	
	_ret('<div class="wrap"><h2>Videresend til '.UKMV_til().'</h2></div>');
	
	_ret(UKMV_stegBar());
	
	$steg = 'UKMV_steg'.UKMV_steg();
	$steg($m);
	
	global $return;
	return $return;
}

/**
 * UKMV_steg * 
 * Angir hvilket steg man har kommet til 
 *
 * @return Int $steg
*/
function UKMV_steg() {
	if(!isset($_GET['steg']))
		return 1;
	if(is_numeric($_GET['steg']))
		return $_GET['steg'];
	return false;
}

/**
 * UKMV_steg1 * 
 * Genererer og mellomlagrerer gui for steg 1 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg1($m){
	global $innslagutentitler;
	require_once('steg1.php');
	UKMV_steg1_inner($m);
}

/**
 * UKMV_steg15 * 
 * Genererer og mellomlagrerer gui for steg 1,5 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg15($m){
	global $innslagutentitler;
	require_once('steg15.php');
	UKMV_steg15_inner($m);
}



/**
 * UKMV_steg2 * 
 * Genererer og mellomlagrerer gui for steg 2 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg2($m){
	require_once('steg2.php');
	UKMV_steg2_inner($m);
}

/**
 * UKMV_steg3 * 
 * Genererer og mellomlagrerer gui for steg 3 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg3($m){
	require_once('steg3.php');
	UKMV_steg3_inner($m);
}

function UKMV_steg35($m) {
	require_once('steg35.php');
	_ret('<form action="?page='.$_GET['page'].'&save=steg35" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">OK,</div>g&aring; videre</div>'
		);
	UKMV_steg35_inner($m);
	
	_ret('</form>');
}
/**
 * UKMV_steg4 * 
 * Genererer og mellomlagrerer gui for steg 42 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg4($m){
	require_once('steg4.php');
	UKMV_steg4_nav();
#	UKMV_steg4_kunst($m);
	_ret('<form action="?page='.$_GET['page'].'&save=steg4" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div>og g&aring; videre</div>'
		);
	UKMV_steg4_ledere($m);
	UKMV_steg4_overnatting($m);
	UKMV_steg4_ledermiddag();
	_ret('</form>');
}

/**
 * UKMV_steg4 * 
 * Genererer og mellomlagrerer gui for steg 42 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg5($m){
	require_once('steg5.php');
	$val = UKMV_steg5_val($m);
	_ret('<form action="?page='.$_GET['page'].'&save=steg5" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div>og g&aring; videre</div>'
		);
	UKMV_steg5_nav();
#	UKMV_steg5_kunst($m, $val);
	UKMV_steg5_reisedetaljer($m, $val);
	UKMV_steg5_matogallergier($m, $val);
	_ret('</form>');
}

/**
 * UKMV_steg6 * 
 * Genererer og mellomlagrerer gui for steg 42 via _ret()
 *
 * @param $m MonstringsObjekt
 * @return void
*/
function UKMV_steg6($m){
	_ret('<form action="?page='.$_GET['page'].'&save=steg6" id="hugeform" method="post">');
	require_once('steg6.php');
	UKMV_steg6_inner($m);
	_ret('</form>');
}

function UKMV_unike_deltakere($m) {
	$unike_deltakere = array();
	$innslag = $m->videresendte();
	foreach($innslag as $trash => $inn) {
		$i = new innslag($inn['b_id']);
		$i->videresendte($m->videresendTil());
		$personer = $i->personer();
		foreach($personer as $other_trash => $part)
			$unike_deltakere[$part['p_id']] = $part;
	}
	return $unike_deltakere;
}


function UKMV_steg8($m) {
	require_once('steg8.php');
	_ret('<form action="?page='.$_GET['page'].'&save=steg8" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre,</div>og g&aring; videre</div>'
		);
	UKMV_steg8_inner($m);
	
	_ret('</form>');
}

function UKMV_steg10($m) {
	_ret('<form action="?page='.$_GET['page'].'&save=steg10" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div></div>'
		);
	require_once('steg10.php');
	_ret('</form>');
}

function UKMV_steg9($m) {
	_ret('<form action="?page='.$_GET['page'].'&save=steg9" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">Lagre</div></div>'
		);
	require_once('steg9.php');
	_ret('</form>');
}






/**
 * UKMV_stegBar * 
 * Genererer statusbar for hvilket steg man jobber med
 *
 * @return HTML statusbar
*/
function UKMV_stegBar() {
	$steg = UKMV_steg();
	$m = new monstring(get_option('pl_id'));
	return '<div class="UKMV_steg" id="the_one_and_only_nav_bar">'
		. '<div id="leadin">Videresendingens '.(get_option('site_type')=='fylke' ? '10':'5').' steg: </div>'
		
		. '<div id="steg1" '.($steg==1?'class="active"':'').'>'
			.'<a href="?page='.$_GET['page'].'&steg=1">1: Hvem skal videresendes</a>'
		. '</div>'

		. '<div id="steg15" '.($steg==15?'class="active"':'').'>'
			.'<a href="?page='.$_GET['page'].'&steg=15">2: Kontrolliste</a>'
		. '</div>'
		
		. '<div id="steg2" '.($steg==2?'class="active"':'').'>'
			.'<a href="?page='.$_GET['page'].'&steg=2">3: Supplér info</a>'
		. '</div>'
		
		.(get_option('site_type')=='kommune'
		? '<div id="steg2" '.($steg==9?'class="active"':'').'>'
			.'<a href="?page='.$_GET['page'].'&steg=9">4: Uregistrerte og publikum</a>'
		. '</div>'
		: '')
				
		.(get_option('site_type')=='fylke' 
		? ''
			. '<div id="steg3" '.($steg==3?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=3">4: Last opp media</a>'
			. '</div>'
	
			. '<div id="steg15" '.($steg==35?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=35">5: Kontroller media</a>'
			. '</div>'
					
			. '<div id="steg4" '.($steg==4?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=4">6: Lederskjema</a>'
			. '</div>'
	
			. '<div id="steg5" '.($steg==5?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=5">7: Reiseskjema</a>'
			. '</div>'
	
			. '<div id="steg5" '.($steg==8?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=8">8: Kunsthenting</a>'
			. '</div>'

			.'<div id="steg9" '.($steg==9?'class="active"':'').'>'
			.'<a href="?page='.$_GET['page'].'&steg=9">9: Uregistrerte og publikum</a>'
			. '</div>'
	
	
			. '<div id="steg7" '.($steg==6?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=6">10: Full oversikt</a>'
			. '</div>'
			
		: ''
			. '<div id="steg10" '.($steg==10?'class="active"':'').'>'
				.'<a href="?page='.$_GET['page'].'&steg=10">5: Infoskjema</a>'
			. '</div>'

		)
				
		. '<div id="leadout"></div>'
		.'<br clear="all" />'
		.'</div>';
}

/**
 * UKMV_til * 
 * Kalkulerer hvem man skal videresende til
 *
 * @return String pl_name / "UKM-Festivalen"
*/
function UKMV_til() {
	$pl_type = get_option('site_type');
	if($pl_type == 'fylke')
		return 'UKM-Festivalen';
	
	$m = new monstring(get_option('pl_id'));
	$fm = $m->videresendTil(true);
	return 'fylkesmønstringen';
}

/**
 * _ret * 
 * Mellomlagrer output for å hente det inn senere
 *
 * @param $text String
 * @return void
*/
function _ret($text) {
	global $return;
	$return .= $text;
}
?>