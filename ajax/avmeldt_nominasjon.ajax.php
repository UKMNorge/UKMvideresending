<?php

use UKMNorge\Videresending\VideresendingNominasjoner;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

/**
 * Første steg ved avmeld: fjern rader i videresending_nominasjon (per person og/eller tittel/innslag).
 *
 * Forventer fra avmeld.ajax.php: $til (Mottaker), $_POST['type'], $_POST['innslag'].
 * Valgfritt: $_POST['person'] — da slettes kun denne deltakerens nominasjoner (filtrert på type/tittel der det trengs).
 */

$fra = UKMVideresending::getFra();
$til = UKMVideresending::getValgtTil();
$tilArrangement = $til->getArrangement();
$tilId = $tilArrangement->getId();
$postType = isset($_POST['type']) ? (string) $_POST['type'] : '';

$innslagFra = $fra->getInnslag()->get(intval($_POST['innslag']));
$bId = $innslagFra->getId();

$slettedeIds = [];
$personPostSatt = isset($_POST['person'])
    && $_POST['person'] !== ''
    && $_POST['person'] !== 'false';

if ($personPostSatt) {
    $personId = intval($_POST['person']);
    if ($personId > 0) {
        foreach (VideresendingNominasjoner::getAlleByPersonId($personId, $tilId)->getAll() as $nom) {
            if ($postType === 'tittel') {
                $tittelId = intval($_POST['id']);
                if ($tittelId < 1 || $nom->getTId() !== $tittelId) {
                    continue;
                }
            } elseif ($postType === 'person') {
                $nomBId = $nom->getBId();
                if ($nomBId !== null && $nomBId !== $bId) {
                    continue;
                }
            }
            VideresendingNominasjonWrite::deactivate($nom);
            $slettedeIds[] = $nom->getId();
        }
    }
} elseif ($postType === 'tittel') {
    $tittelId = intval($_POST['id']);
    if ($tittelId > 0) {
        foreach (VideresendingNominasjon::getAlleByTittelId($tittelId, $tilId)->getAll() as $nom) {
            VideresendingNominasjonWrite::deactivate($nom);
            $slettedeIds[] = $nom->getId();
        }
    }
} elseif ($postType === 'person') {
    foreach (VideresendingNominasjon::getAlleByInnslagId($bId)->getAll() as $nom) {
        if ($nom->getArrangementTilId() !== $tilId) {
            continue;
        }
        VideresendingNominasjonWrite::deactivate($nom);
        $slettedeIds[] = $nom->getId();
    }
}

UKMVideresending::addResponseData('avmeldt_nominasjon', [
    'slettede_ids' => $slettedeIds,
]);
UKMVideresending::addResponseData('success', true);
