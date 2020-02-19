<?php

use UKMNorge\Arrangement\Write;

if( isset($_POST) and sizeof( $_POST ) > 0 ) {
	$arrangement = UKMVideresending::getFra();
	
	$arrangement->setUregistrerte( $_POST['pl_missing'] );
	$arrangement->setPublikum( $_POST['pl_public'] );
	Write::save( $arrangement );
    
    UKMVideresending::getFlashbag()
        ->success('Publikum og uregistrerte er nå lagret');
}