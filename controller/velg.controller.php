<?php

use UKMNorge\Arrangement\UKMFestival;

UKMVideresending::addViewData(
    [
        'UKMFestivalen' => UKMFestival::getCurrentUKMFestival(),
    ]
);

UKMVideresending::require('controller/statistikk.controller.php');