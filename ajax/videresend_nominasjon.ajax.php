<?php

use UKMNorge\Videresending\VideresendingNominasjoner;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

/**
 * Oppretter rader i videresending_nominasjon ut fra videresend-POST (kjøres etter vellykket videresending).
 *
 * Forventer: $fra og $til (Arrangement) — settes fra videresend.ajax.php, eller hentes her ved inkludering fra videresendPerson.ajax.php.
 * Samt $_POST['type'], $_POST['innslag']. For én person: $_POST['subaction'] === 'videresendPerson' og $_POST['person'].
 */

if (!isset($fra)) {
    $fra = UKMVideresending::getFra();
}
if (!isset($til)) {
    $til = UKMVideresending::getValgtTil()->getArrangement();
}

$innslagFra = $fra->getInnslag()->get(intval($_POST['innslag']));
$postType = isset($_POST['type']) ? (string) $_POST['type'] : '';
$tilId = $til->getId();
$fraId = $fra->getId();
$season = (int) $fra->getSesong();
$typeKey = $innslagFra->getType()->getKey();
$bId = $innslagFra->getId();

$opprettetIds = [];

$finnesAktivNominasjonForPersonOgTittel = static function (int $personId, int $arrangementTil, int $innslagId, ?int $tittelId = -1): bool {
    foreach (VideresendingNominasjoner::getAlleByPersonIdOgInnslagId($personId, $innslagId, $arrangementTil)->getAll() as $nom) {
        if ($nom->getTId() === $tittelId) {
            return true;
        }
    }
    return false;
};

$getNominasjonStatusForPersonOgTittel = static function (int $personId, int $arrangementTil, int $innslagId, ?int $tittelId = -1): VideresendingNominasjon|null {
    foreach (VideresendingNominasjoner::getAlleByPersonIdOgInnslagId($personId, $innslagId, $arrangementTil)->getAll() as $nom) {
        if ($nom->getTId() === $tittelId) {
            return $nom;
        }
    }
    return null;
};

$harNominasjonForTittel = static function (int $tittelId, int $arrangementTil): bool {
    return count(VideresendingNominasjon::getAlleByTittelId($tittelId, $arrangementTil)->getAll()) > 0;
};

$subaction = isset($_POST['subaction']) ? (string) $_POST['subaction'] : '';
$personIdFromPost = isset($_POST['person']) ? intval($_POST['person']) : 0;

if ($subaction === 'videresendPerson' && $personIdFromPost > 0) {
    $innslagFra->getPersoner()->get($personIdFromPost);

    $tittelIdForNom = null;
    if ($postType === 'tittel') {
        $tittelIdForNom = intval($_POST['id']);
        if ($tittelIdForNom < 1) {
            UKMVideresending::addResponseData('videresend_nominasjon', ['opprettet_ids' => [], 'advarsel' => 'Ugyldig tittel-id']);
            UKMVideresending::addResponseData('success', true);
            return;
        }
    }

    if (!$finnesAktivNominasjonForPersonOgTittel($personIdFromPost, $tilId, $innslagFra->getId(), $tittelIdForNom)) {
        $nom = VideresendingNominasjonWrite::create(
            $season,
            $typeKey,
            $fraId,
            $tilId,
            $personIdFromPost,
            $bId,
            false,
            null,
            $tittelIdForNom
        );
        $opprettetIds[] = $nom->getId();
    }

    UKMVideresending::addResponseData('videresend_nominasjon', [
        'opprettet_ids' => $opprettetIds,
    ]);
    UKMVideresending::addResponseData('success', true);
    return;
}

$nominasjonStatus = '';

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
            if ($finnesAktivNominasjonForPersonOgTittel($pId, $tilId, $innslagFra->getId(), $tittelId)) {
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
    $tittel = $innslagFra->getTitler()->get($tittelId);
    $nominasjonStatus = $tittel->getNominasjonStatus($tilId);
} elseif ($postType === 'person') {
    // uten titler
    if (!$innslagFra->getType()->harTitler()) {
        $person = $innslagFra->getPersoner()->getSingle();
        $pId = $person->getId();

        if (!$finnesAktivNominasjonForPersonOgTittel($pId, $tilId, $innslagFra->getId(), -1)) {
            $nom = VideresendingNominasjonWrite::create(
                $season,
                $typeKey,
                $fraId,
                $tilId,
                $pId,
                $bId,
                false,
                null,
                -1
            );
            $opprettetIds[] = $nom->getId();
            $nominasjonStatus = $nom->getStatus();
        }
        else {
            $nominasjon = $getNominasjonStatusForPersonOgTittel($pId, $tilId, $innslagFra->getId(), -1);
        }
        
    } else {
        foreach ($innslagFra->getPersoner()->getAll() as $person) {
            $pId = $person->getId();
            if ($finnesAktivNominasjonForPersonOgTittel($pId, $tilId, $innslagFra->getId(), -1)) {
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
}
$nominasjonStatusTekst = '';
switch ($nominasjonStatus) {
    case 'godkjent':
        $nominasjonStatusTekst = 'Godkjent';
        break;
    case 'hos-deltaker':
        $nominasjonStatusTekst = 'Nominasjon akseptert, venter på deltaker';
        break;
    case 'hos-mottaker':
        $nominasjonStatusTekst = 'Venter på mottaker';
        break;
    case 'hos-avsender':
        $nominasjonStatusTekst = 'Venter på avsender';
        break;
    default:
        $nominasjonStatusTekst = 'Ukjent';
        break;
}
// $nominasjonStatus = $nominasjonStatusTekst;


UKMVideresending::addResponseData('videresend_nominasjon', [
    'opprettet_ids' => $opprettetIds,
    'tittel_status' => $nominasjonStatusTekst,
    'tittel_status_code' => $nominasjonStatus,
]);
UKMVideresending::addResponseData('success',true);
