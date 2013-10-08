<?php
require_once(ABSPATH.'wp-content/plugins/UKMVideresending/videresending.php');
require_once(ABSPATH.'wp-content/plugins/UKMVideresending/steg6.php');

function UKMV_steg6_relsjonssjekk_visRelasjoner($b_id,$pl_id) {
	$qry = new SQL("SELECT *
					FROM `smartukm_rel_pl_b`
					WHERE `pl_id` = '#plid'
					AND `b_id` = '#bid'",
					array('plid'=>$pl_id, 'bid'=>$b_id));
	$res = $qry->run();
	/*
if((!$res||mysql_num_rows($res)==0)) {
		$insert = new SQLins('smartukm_rel_pl_b');
		$insert->add('pl_id',$pl_id);
		$insert->add('b_id',$b_id);
		$insert->add('season',2012);
		$insert->run();
	}
*/
	
	$tilbake = (!$res||mysql_num_rows($res)==0) ? ' (mangler rel_pl_b)' : '';

	$qry = new SQL("SELECT *
					FROM `smartukm_fylkestep`
					WHERE `pl_id` = '#plid'
					AND `b_id` = '#bid'",
					array('plid'=>$pl_id, 'bid'=>$b_id));
	$res = $qry->run();
	$tilbake .= (!$res||mysql_num_rows($res)==0) ? ' (mangler fylkestep)' : '';

	return $tilbake;
}

function UKMV_rapporter_relasjoner() {
	$monstringer = new SQL("SELECT `pl_id` 
							FROM `smartukm_place`
							WHERE `season` = '#season'
							AND `pl_fylke` > 0
							ORDER BY `pl_name` ASC",
							array('season'=>get_option('season')));
	$monstringer = $monstringer->run();
	while($r = mysql_fetch_assoc($monstringer)) {
		$m = new monstring($r['pl_id']);
		UKMV_steg6_videresendte($m, true);
	}
	
	global $return;
	echo $return;
}
?>