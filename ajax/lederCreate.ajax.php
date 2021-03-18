<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();

$leder = new Leder( $fra->getId(), $til->getId() );
$leder->setType('reise');
$leder = Write::create($leder);

UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('netter', $til->getArrangement()->getNetter());
UKMVideresending::addResponseData('overnattingssteder', UKMVideresending::getOvernattingssteder($til->getArrangement()));
UKMVideresending::addResponseData('leder', $leder->getJsObject());