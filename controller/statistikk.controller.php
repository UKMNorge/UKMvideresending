<?php

use UKMNorge\Arrangement\Write;

if( isset($_POST) and sizeof( $_POST ) > 0 ) {
	$arrangement = UKMVideresending::getFra();
	
	if(isset($_POST['pl_utenpublikum']) && $_POST['pl_utenpublikum'] == 'on') {
		$arrangement->setPublikum(1);
	}
	else {
		$arrangement->setPublikum( $_POST['pl_public'] );
	}
	$arrangement->setUregistrerte( $_POST['pl_missing'] );

	Write::save( $arrangement );
    
    UKMVideresending::getFlashbag()
		->success('Publikum og uregistrerte er nå lagret');

}