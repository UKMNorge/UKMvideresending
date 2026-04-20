<?php

use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

if (!isset($_FORM['personer']) || !is_array($_FORM['personer'])) {
	return;
}

$fraId = $monstring->getId();
$tilId = $til->getId();
$bId = $innslag->getId();
$tittelId = $_POST['id'] != 'false' ? $_POST['id'] : -1;

foreach ($_FORM['personer'] as $personId => $persondata) {
	if (!is_array($persondata)) {
		continue;
	}

    $beskrivelse = null;
    if(isset($persondata['videresending_beskrivelse'])) {
        $beskrivelse = trim((string) $persondata['videresending_beskrivelse']);
    }

    $sporsmal = null;
    if(isset($persondata['videresending_sporsmal'])) {
        $sporsmal = trim((string) $persondata['videresending_sporsmal']);
    }

	$personId = (int) $personId;
	if ($personId < 1) {
		continue;
	}

	$nominasjon = VideresendingNominasjon::finnVedNokkel($fraId, $tilId, $bId, $personId, $tittelId, true);
	if ($nominasjon === null) {
		continue;
	}

	$nominasjon->setBeskrivelse($beskrivelse !== '' ? $beskrivelse : null);
	$nominasjon->setSporsmal($sporsmal !== '' ? $sporsmal : null);
	VideresendingNominasjonWrite::save($nominasjon);
}
