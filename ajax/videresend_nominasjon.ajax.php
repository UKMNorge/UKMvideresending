<?php

use UKMNorge\Videresending\VideresendingNominasjoner;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

/**
 * Oppretter rader i videresending_nominasjon ut fra videresend-POST (kjøres etter vellykket videresending).
 *
 * Forventer: $fra og $til (Arrangement), samt $_POST['type'], $_POST['innslag'].
 */

$innslagFra = $fra->getInnslag()->get(intval($_POST['innslag']));
$postType = isset($_POST['type']) ? (string) $_POST['type'] : '';
$tilId = $til->getId();
$fraId = $fra->getId();
$season = (int) $fra->getSesong();
$typeKey = $innslagFra->getType()->getKey();
$bId = $innslagFra->getId();

$opprettetIds = [];

$finnesForPersonOgTittel = static function (int $personId, int $arrangementTil, ?int $tittelId): bool {
    foreach (VideresendingNominasjoner::getAlleByPersonId($personId, $arrangementTil)->getAll() as $nom) {
        $existingT = $nom->getTId();
        if ($existingT === $tittelId) {
            return true;
        }
    }
    return false;
};

$harNominasjonForTittel = static function (int $tittelId, int $arrangementTil): bool {
    return count(VideresendingNominasjon::getAlleByTittelId($tittelId, $arrangementTil)->getAll()) > 0;
};

if ($postType === 'tittel') {
    $tittelId = intval($_POST['id']);
    if ($tittelId < 1) {
        UKMVideresending::addResponseData('videresend_nominasjon', ['opprettet' => [], 'advarsel' => 'Ugyldig tittel-id']);
        return;
    }

    $personer = $innslagFra->getPersoner()->getAll();
    if (count($personer) === 0) {
        if (!$harNominasjonForTittel($tittelId, $tilId)) {
            $nom = VideresendingNominasjonWrite::create(
                $season,
                $typeKey,
                $fraId,
                $tilId,
                null,
                $bId,
                false,
                null,
                $tittelId
            );
            $opprettetIds[] = $nom->getId();
        }
    } else {
        foreach ($personer as $person) {
            $pId = $person->getId();
            if ($finnesForPersonOgTittel($pId, $tilId, $tittelId)) {
                continue;
            }
            $nom = VideresendingNominasjonWrite::create(
                $season,
                $typeKey,
                $fraId,
                $tilId,
                $pId,
                $bId,
                false,
                null,
                $tittelId
            );
            $opprettetIds[] = $nom->getId();
        }
    }
} elseif ($postType === 'person') {
    foreach ($innslagFra->getPersoner()->getAll() as $person) {
        $pId = $person->getId();
        if ($finnesForPersonOgTittel($pId, $tilId, null)) {
            continue;
        }
        $nom = VideresendingNominasjonWrite::create(
            $season,
            $typeKey,
            $fraId,
            $tilId,
            $pId,
            $bId,
            false,
            null,
            null
        );
        $opprettetIds[] = $nom->getId();
    }
}

UKMVideresending::addResponseData('videresend_nominasjon', [
    'opprettet_ids' => $opprettetIds,
]);
UKMVideresending::addResponseData('success',true);
