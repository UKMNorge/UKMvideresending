<?php
require_once('UKM/leder.class.php');

$leder = new leder( $_POST['leder'] );
$res = $leder->natt( $_POST['dato'], $_POST['sted'] );

UKMVideresending::addResponseData('success', $res !== false );