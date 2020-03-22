<?php

use UKMNorge\Arrangement\Videresending\Ledere\Hovedleder;
use UKMNorge\Arrangement\Videresending\Ledere\Hovedledere;
use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

$hovedledere = new Hovedledere($fra->getId(), $til->getId());

foreach( $_POST as $data ) {
	if( isset( $data['name'] ) && isset( $data['value'] ) ) {
        $dato = str_replace('hovedleder-', '', $data['name']);
        $leder_id = intval($data['value']);

        $ny_leder = Leder::getById( $leder_id );

        $hovedleder = $hovedledere->get($dato);
        $hovedleder->setLeder($ny_leder);
        Write::saveHovedLeder($hovedleder);
	}
}

UKMVideresending::addResponseData('success',true);