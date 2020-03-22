<?php

use UKMNorge\Meta\Write;

$til = UKMVideresending::getValgtTil();
$fra = UKMVideresending::getFra();

$meta = $fra->getMeta('kommentar_overnatting_til_'.$til->getId());
$meta->set($_POST['kommentar']);

$res = Write::set($meta);
UKMVideresending::addResponseData('success', $res );