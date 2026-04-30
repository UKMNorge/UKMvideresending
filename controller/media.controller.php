<?php

use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Innslag\Innslag;

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil()->getArrangement();


$nominerteInnslag = [];
foreach (VideresendingNominasjon::getAlleTilArrangement($til->getId())->getAll() as $vNominasjon) {
    $objNominasjon = $vNominasjon->getArrObj();

    $innslag = Innslag::getById($objNominasjon['b_id']);
    $nominerteInnslag[] = $innslag;
}

UKMVideresending::addViewData('nominerteInnslag', $nominerteInnslag);
