<?php
	
require_once('UKM/leder.class.php');
$festivalen = array_pop( UKMVideresending::getTil() );

$leder = new leder();
$leder->set( 'l_type', 'reise' );
$leder->create( 
	UKMVideresending::getFra()->getId(),
	$festivalen->getId(),
	$festivalen->getSesong()
);

$netter = [];
foreach( $festivalen->getNetter() as $natt ) {
	$netter[] = $natt->getTimestamp();
}
UKMVideresending::addResponseData('success', true);
UKMVideresending::addResponseData('netter', $netter);
UKMVideresending::addResponseData('overnattingssteder', UKMVideresending::overnattingssteder());
UKMVideresending::addResponseData('leder', $leder);