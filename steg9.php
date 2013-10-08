<?php
$m = new monstring(get_option('pl_id'));

_ret('<h2>Uregistrerte deltakere og publikum</h2>

<strong>Det er viktig for statistikken at du registrerer alle ungdommer som har vært med i din produksjon,<br /> men ikke har vært registrert i arrangørsystemet</strong>

<br /><br />

<label>Uregistrerte deltakere</label><br />
<input type="text" name="pl_missing" value="'.$m->g('pl_missing').'" />

<br /> <br />
<label>Totalt publikumstall</label><br />
<input type="text" name="pl_public" value="'.$m->g('pl_public').'" />');