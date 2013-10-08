<?php
require_once(ABSPATH.'wp-content/plugins/UKMVideresending/videresending.php');
require_once(ABSPATH.'wp-content/plugins/UKMVideresending/steg6.php');

function UKMV_rapporter_videresendte() {
	$monstringer = new SQL("SELECT `pl_id` 
							FROM `smartukm_place`
							WHERE `season` = '#season'
							AND `pl_fylke` > 0
							ORDER BY `pl_name` ASC",
							array('season'=>get_option('season')));
	$monstringer = $monstringer->run();
	while($r = mysql_fetch_assoc($monstringer)) {
		$m = new monstring($r['pl_id']);
		UKMV_steg6_inner($m);
	}
	
	global $return;
	echo $return;
}
?>