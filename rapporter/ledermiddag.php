<?php
/* 
Part of: UKM Videresending rapporter :: ledermiddag
Description: Genererer enkel oversikt over hvilke ledere som skal ha middag, og hvem som betaler
Author: UKM Norge / M Mandal
Version: 1.0
*/

function UKMV_rapporter_ledermiddag() {
	if(isset($_GET['slett_id'])) 
		echo '<div style="margin-top: 10px;margin-bottom:0px; width:500px; font-size: 14px;">'.UKMV_ledermiddagmiddag_slett($_GET['slett_id']).'</div>';
	echo UKMV_ledermiddagmiddag_skjema();

	$mulige_deltakere = array('ukm','fylke1','fylke2');
	echo '<ul class="ukm">'
		.	'<li>'
		.		UKMN_ico('chef',32)
		.		'<h4>Deltakerliste ledermiddag</h4>'
		.		'<br clear="all" />'
		.	'</li>';
		
	$deltakere = new SQL("SELECT `m`.*, `pl`.`pl_name` 	
					FROM `smartukm_videresending_ledere_middag` AS `m`
					JOIN `smartukm_place` AS `pl` ON (`pl`.`pl_id` = `m`.`pl_from`)
					WHERE `pl`.`season` = '#season'
					ORDER BY `pl_name` ASC
					", array('season'=>get_option('season')));
	$deltakere = $deltakere->run();
	
	
	$ekstradeltakere = new SQL("SELECT * FROM `smartukm_videresending_ledermiddag_ekstra`
							ORDER BY `navn` ASC");
	$ekstradeltakere = $ekstradeltakere->run();
	while($elr = mysql_fetch_assoc($ekstradeltakere)) {
		$info['leder_navn'] = $elr['navn'];
		$info['slett'] = '<a href="?page='.$_GET['page'].'&rapport='.$_GET['rapport'].'&slett_id='.$elr['d_id'].'">'
						.UKMN_icoButtonLine('trash',16,'Slett overnatting').'</a>';
						
		$html[$elr['navn'].'_'.$elr['d_id']] = '<li class="ledermiddag_leder">'
													. '<input type="checkbox"  /> '
													. $elr['navn']
													. '<span class="fylke"> (UKM Norge) '
														.'<a href="?page='.$_GET['page'].'&rapport='.$_GET['rapport'].'&slett_id='.$elr['d_id'].'">'
														.UKMN_icoButtonLine('trash',16,'Slett deltaker').'</a>'
													.'</span>'
												. '</li>';
		$ukmntot++;

	}
	
	$totalt_antall_eks_ukm = 0;
	while($fylke = mysql_fetch_assoc($deltakere)) {
		$antall = 0;
		foreach($mulige_deltakere as $trash => $leder) {
			if(!empty($fylke['ledermiddag_'.$leder])) {
				$antall++;
				$totalt_antall_eks_ukm++;
				$html[$fylke['ledermiddag_'.$leder].'_'.$leder['skjema_id']] =
					'<li class="ledermiddag_leder">'
					. '<input type="checkbox"  /> '
					. utf8_encode($fylke['ledermiddag_'.$leder])
					. '<span class="fylke"> ('.utf8_encode($fylke['pl_name']).')</span>'
					. '</li>';
			}
		}
		$teller[$antall][] = $fylke['pl_name'];
	}
	ksort($html);
	echo implode('', $html);
	echo '</ul>';
	
	ksort($teller);
	echo '<ul class="ukm">'
		.	'<li>'
		.		UKMN_ico('people',32)
		.		'<h4>Antall deltakere per fylke '.'<span class="forklaring">(Totalt '.($totalt_antall_eks_ukm+$ukmntot).' ledere)</h4>'
		.		'<br clear="all" />'
		.	'</li>';
	foreach($teller as $antall => $fylker) {
		echo '<li class="rapport_ledermiddag_antall">'
			.'<div class="antall">'.($antall==0 ? 'Ingen ':$antall).' leder'.($antall==1?'':'e').'</div>'
			.'<div class="fylke">'.utf8_encode(implode(', ',$fylker)).'</div>'
			.'</li>'
			;
	}
	echo '<li class="rapport_ledermiddag_antall">'
		.'<div class="antall">UKM Norge</div>'
		.'<div class="fylke">'.	$ukmntot .' ledere</div>'
		.'</li>'
		;	

	echo '</ul>';
}


function UKMV_ledermiddagmiddag_leggtil() {
	if(isset($_POST['UKMF_ledermiddag_ekstra'])){
		$qry = new SQLins('smartukm_videresending_ledermiddag_ekstra');
		$qry->add('navn', $_POST['UKMF_ledermiddag_navn']);
	#	$qry->add('mobil', $_POST['UKMF_ledermiddag_mobil']);
		$res = $qry->run();

		if($res && $res !== -1)
			return '<div id="message" class="updated" style="padding:5px;">Ledermiddag lagt til for '.utf8_encode($_POST['UKMF_ledermiddag_navn']).'</div>';

 		return '<div class="error" style="padding:5px;">Kunne ikke legge til deltaker</div>';
	}
}

function UKMV_ledermiddagmiddag_skjema(){
	echo UKMV_ledermiddagmiddag_leggtil();
	return '<form action="?page='.$_GET['page'].'&rapport='.$_GET['rapport'].'" method="post">'
		.'<h4>Legg til ekstra deltaker</h4>'
		.'<input type="hidden" name="UKMF_ledermiddag_ekstra" value="true" />'
		.'Navn: <input type="input" name="UKMF_ledermiddag_navn" /> '
	#	.'Mobil: <input type="input" name="UKMF_ledermiddag_mobil" /> '
		.'<input type="submit" name="submit" value="Legg til" />'
		.'</form>'
		.'<br clear="all" />';
		
}


function UKMV_ledermiddagmiddag_slett($id) {
	if(!is_numeric($id))
		return '<div class="error" style="padding:5px;">Kunne ikke slette deltaker</div>';

	$sql = new SQLdel('smartukm_videresending_ledermiddag_ekstra', array('d_id'=>$id));
	$res = $sql->run();

	if(!$res)
		return '<div class="error" style="padding:5px;">Kunne ikke slette deltaker</div>';

	return '<div id="message" class="updated" style="padding:5px;">Deltaker slettet</div>';
}

?>