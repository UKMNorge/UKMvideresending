<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Videresending\Request\RequestVideresending;

$fra = UKMVideresending::getFra();
$UKMFestivalen = UKMFestival::getCurrentUKMFestival();

UKMVideresending::addViewData(
    [
        'UKMFestivalen' => $UKMFestivalen,
        'requestSendt' => RequestVideresending::finnesKombinasjonen($fra->getId(), $UKMFestivalen->getId())

    ]
);

UKMVideresending::require('controller/statistikk.controller.php');