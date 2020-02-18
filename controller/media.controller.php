<?php

$videresendte_innslag = [];
$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil()->getArrangement();

UKMVideresending::addViewData('videresendte', $innslag );