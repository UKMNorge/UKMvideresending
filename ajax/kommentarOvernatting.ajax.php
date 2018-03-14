<?php
	
$res = UKMVideresending::updateInfoskjema(
	'overnatting_kommentar',
	$_POST['kommentar']
);

UKMVideresending::addResponseData('success', $res );