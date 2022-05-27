<?php

use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Write;

$leder = Leder::getById( intval( $_POST['leder'] ) );
$natt = $leder->getNatt( $_POST['dato'] );

// Det tillates ikke at sykerom har et sted utenfor hotell
if($leder->getType() == 'sykerom' && $_POST['sted'] != 'hotell') {
    UKMVideresending::addResponseData('success', [] );
}
else {
    $natt->setSted($_POST['sted'] );
    
    $res = Write::saveNatt($natt);
    
    UKMVideresending::addResponseData('success', !!$res );
}
