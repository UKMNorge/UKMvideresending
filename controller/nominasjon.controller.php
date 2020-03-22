<?php

use UKMNorge\Innslag\Typer\Typer;

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    UKMVideresending::require('save/nominasjon.save.php');
}

$fra = UKMVideresending::getFra();