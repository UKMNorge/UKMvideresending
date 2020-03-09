<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$leder = Leder::getById( intval($_POST['leder'] ));

$res = Write::delete( $leder );

UKMVideresending::addResponseData('success', $res);
