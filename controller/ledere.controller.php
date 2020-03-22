<?php

// Tell opp antall personer, sånn at vi er sikker på at vi jobber med riktig antall

use UKMNorge\Arrangement\Videresending\Ledere\Hovedledere;
use UKMNorge\Arrangement\Videresending\Ledere\Leder;
use UKMNorge\Arrangement\Videresending\Ledere\Ledere;
use UKMNorge\Arrangement\Videresending\Ledere\Write as WriteLeder;

UKMVideresending::beregnAntallVideresendtePersoner();

$til = UKMVideresending::getValgtTil();
$fra = UKMVideresending::getFra();

// Sørg for at hovedleder og utstillingsleder finnes før vi starter
$hovedleder = Leder::getByType( $fra->getId(), $til->getId(), 'hoved');
if( !$hovedleder->eksisterer() ) {
    WriteLeder::create($hovedleder);
}

$utstillingleder = Leder::getByType( $fra->getId(), $til->getId(), 'utstilling');
if( !$utstillingleder->eksisterer() ) {
    WriteLeder::create($utstillingleder);
}

$ledere = new Ledere( $fra->getId(), $til->getId() );
$hovedledere = new Hovedledere($fra->getId(), $til->getId());

UKMVideresending::addViewData(
    [
        'ledere' => $ledere,
        'hovedledere' => $hovedledere,
        'pris_hotelldogn' => $til->getArrangement()->getMetaValue('pris_hotelldogn'),
        'overnattingssteder' => UKMVideresending::getOvernattingssteder( $til->getArrangement() )
    ]
);