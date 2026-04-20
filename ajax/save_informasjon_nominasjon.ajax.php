<?php

use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

if (!isset($_FORM['personer']) || !is_array($_FORM['personer'])) {
	return;
}

$fraId = $monstring->getId();
$tilId = $til->getId();
$bId = $innslag->getId();

foreach ($_FORM['personer'] as $personId => $persondata) {
	if (!is_array($persondata) || !isset($persondata['videresending_beskrivelse']) || !isset($persondata['videresending_sporsmal'])) {
		continue;
	}
	$personId = (int) $personId;
	if ($personId < 1) {
		continue;
	}
	$beskrivelse = trim((string) $persondata['videresending_beskrivelse']);
	$sporsmal = trim((string) $persondata['videresending_sporsmal']);
	$nominasjon = VideresendingNominasjon::finnVedNokkel($fraId, $tilId, $bId, $personId, -1, true);
	if ($nominasjon === null) {
		continue;
	}
	$nominasjon->setBeskrivelse($beskrivelse !== '' ? $beskrivelse : null);
	$nominasjon->setSporsmal($sporsmal !== '' ? $sporsmal : null);
	VideresendingNominasjonWrite::save($nominasjon);
}
