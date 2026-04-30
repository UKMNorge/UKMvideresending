<?php

use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Innslag\Innslag;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil()->getArrangement();


$nominerteInnslag = [];
foreach (VideresendingNominasjon::getAlleTilArrangement($til->getId())->getAll() as $vNominasjon) {
    if($vNominasjon->getPId() != $fra->getId()) {
        continue;
    }
    $objNominasjon = $vNominasjon->getArrObj();

    $innslag = Innslag::getById($objNominasjon['b_id']);
    $nominerteInnslag[$innslag->getId()] = $innslag;
}

UKMVideresending::addViewData('nominerteInnslag', $nominerteInnslag);
