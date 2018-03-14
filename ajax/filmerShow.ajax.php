<?php
	
$monstring = UKMVideresending::getFra();
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

UKMVideresending::addResponseData('filmer', $innslag->getFilmer());
UKMVideresending::addResponseData('success', true);