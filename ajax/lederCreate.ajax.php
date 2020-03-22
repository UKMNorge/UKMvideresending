<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

$leder = new Leder( $fra->getId(), $til->getId() );
$leder->setType('reise');
$leder = Write::create($leder);

$netter = [];
foreach( $til->getArrangement()->getNetter() as $natt ) {
    $netter[] = $natt->getTimestamp();
}

UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('netter', $netter);
UKMVideresending::addResponseData('overnattingssteder', UKMVideresending::getOvernattingssteder($til->getArrangement()));
UKMVideresending::addResponseData('leder', $leder->getJsObject());