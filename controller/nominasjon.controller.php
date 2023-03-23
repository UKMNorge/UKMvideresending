<?php

use UKMNorge\Innslag\Typer\Typer;
use UKMNorge\Wordpress\User;

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    UKMVideresending::require('save/nominasjon.save.php');
}

UKMVideresending::addViewData('wp_user_navn', wp_get_current_user()->display_name);
UKMVideresending::addViewData('wp_user_phone', User::loadByEmail(wp_get_current_user()->user_email)->getPhone());

$fra = UKMVideresending::getFra();