<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

$leder = new Leder( $fra->getId(), $til->getId() );

$tilArrangement = new Arrangement($til->getId());

// Landsfestivalen trenger godkjenning
if($tilArrangement->getType() == 'land') {
    $leder->setGodkjent(false);
}
else {
    // Trenger ikke godkjenning på andre typer arrangement
    $leder->setGodkjent(true);
}

$leder->setType('reise');
$leder = Write::create($leder);

UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('netter', $til->getArrangement()->getNetter());
UKMVideresending::addResponseData('overnattingssteder', UKMVideresending::getOvernattingssteder($til->getArrangement()));
UKMVideresending::addResponseData('leder', $leder->getJsObject());
UKMVideresending::addResponseData('isLandsfestivalen', $tilArrangement->getType() == 'land');