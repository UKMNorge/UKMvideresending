<?php

## INKLUDER STATISTIKK (skjema vises to plasser, og sender brukeren til informasjonssiden)
require_once('statistikk.controller.php');

UKMVideresending::addViewData('info1', get_site_option('UKMFvideresending_info1_'. UKMVideresending::getFra()->getSesong() ));
UKMVideresending::addViewData('nominasjon_frister', get_site_option('UKMFvideresending_nominasjon_frister') );