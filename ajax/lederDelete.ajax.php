<?php
	
require_once('UKM/leder.class.php');
	
$leder = new leder( $_POST['leder'] );
$res = $leder->delete( UKMVideresending::getFra()->getId() );

UKMVideresending::addResponseData('success', $res);
