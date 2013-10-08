<?php
/* 
Part of: UKM Videresending
Description: Splashscreen for Videresendingsrapporter
Author: UKM Norge / M Mandal
Version: 1.0
*/

function UKMV_rapporter_splash() {
	$nav = new nav('UKM Norge', 'Her finner du en del verkt&oslash;y laget for UKM Norge');	

	### STATISTIKKVERKTØY
	$cell = new navCell('Videresendte',
						'creep_documents',
						'');
	$cell->link('?page='.$_GET['page'].'&rapport=spesbehov', 'Spesielle behov');
	$cell->link('?page='.$_GET['page'].'&rapport=mat', 'Matallergier');
	$cell->link('?page='.$_GET['page'].'&rapport=oppsummering', 'Oppsummering');
	$cell->link('?page='.$_GET['page'].'&rapport=relasjoner', 'Relasjoner');

	$nav->add($cell);

	### STATISTIKKVERKTØY
	$cell = new navCell('Ledere',
						'user-business',
						'');
	$cell->link('?page='.$_GET['page'].'&rapport=ledere', 'Alle ledere inkl turister');
	$cell->link('?page='.$_GET['page'].'&rapport=ledere&turist', 'Kun turister');

	$cell->link('?page='.$_GET['page'].'&rapport=ledermiddag', 'Ledermiddag');

	$nav->add($cell);
		

	return $nav->run();
}
?>