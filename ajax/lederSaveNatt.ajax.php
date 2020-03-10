<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$leder = Leder::getById( intval( $_POST['leder'] ) );
$natt = $leder->getNatt( $_POST['dato'] );
$natt->setSted($_POST['sted'] );

$res = Write::saveNatt($natt);

UKMVideresending::addResponseData('success', !!$res );