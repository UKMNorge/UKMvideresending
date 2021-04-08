<?php
/* 
Plugin Name: UKM Videresending
Plugin URI: http://www.ukm-norge.no
Description: Videresendingsfunksjoner for alle mønstringer.
Author: UKM Norge / M Mandal / A Hustad
Version: 3.0 
Author URI: http://mariusmandal.no
*/

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Videresending\Mottaker;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Meta\Write as WriteMeta;
use UKMNorge\Sensitivt\Intoleranse;

require_once('UKM/Autoloader.php');


class UKMVideresending extends UKMNorge\Wordpress\Modul
{
    public static $action = 'velg';
    public static $path_plugin = null;
    /**
     * @var Arrangement
     */
    public static $arrangement;
    public static $til = null;

    /**
     * Initier Videresending-objektet
     *
     **/
    public static function init($plugin_path)
    {
        parent::init($plugin_path);

        if (is_numeric(get_option('pl_id'))) {
            try {
                static::$arrangement = new Arrangement(intval(get_option('pl_id')));
            } catch (Exception $e) {
                // silenty let the exception pass 🧙🏼‍♀️
            }
        }
    }

    /**
     * Hooker modulen inn i Wordpress
     *
     * @return void
     */
    public static function hook()
    {
        # Kun initier på mønstringssider
        if (is_numeric(get_option('pl_id'))) {
            if (get_option('pl_id')) {
                add_action('admin_menu', ['UKMVideresending', 'meny'], 101);
                add_action('wp_ajax_UKMVideresending_ajax', ['UKMVideresending', 'ajax']);
                add_action('admin_init', [static::class, 'registerScript']);
            }
        }
    }

    /**
     * Get type mønstring vi videresender fra
     *
     * @return string (kommune|fylke|land)
     **/
    public static function getType()
    {
        if (is_null(static::$arrangement)) {
            return 'kommune'; //laveste nivå
        }
        return static::$arrangement->getType();
    }

    /**
     * Get arrangementet vi videresender fra
     *
     * @return Arrangement 
     **/
    public static function getFra()
    {
        return static::$arrangement;
    }

    /**
     * getValgt til sjekker at vi har tilgang til ønsket arrangement, og
     * legger det til i viewdata
     *
     * @return Mottaker
     **/
    public static function getValgtTil()
    {
        $til = intval($_REQUEST['til']);
        if (!static::$arrangement->getVideresending()->harMottaker($til)) {
            throw new Exception('Beklager, kan ikke se at du kan videresende til arrangementet du prøver å videresende til.');
        }
        $arrangement = static::$arrangement->getVideresending()->getMottaker($til);
        static::addViewData('til', $arrangement);
        return $arrangement;
    }


    /**
     * Hent alle view-data
     *
     * @return array
     **/
    public static function getViewData()
    {
        static::addViewData('fra', static::getFra());
        if (isset($_REQUEST['til'])) {
            static::addViewData('til', static::getValgtTil());
        }
        static::addViewData('tab', static::getAction());
        return parent::getViewData();
    }

    /**
     * Håndterer alle ajax-kall
     *
     * @return void
     */
    public static function ajax()
    {
        if (is_array($_POST)) {
            static::addResponseData('POST', $_POST);
        }

        try {
            $supported_actions = [
                'avmeld',
                'videresend',
                'kontroll',
                'kontrollSave',
                'avmeldPerson',
                'videresendPerson',
                'bilderShow',
                'bildeSet',
                'filmerShow',
                'playbackShow',
                'nominasjon',
                'lederDelete',
                'lederSave',
                'lederCreate',
                'lederSaveNatt',
                'kommentarOvernatting',
                'lederSaveHoved',
                'tilrettelegging',
            ];

            if (in_array($_POST['subaction'], $supported_actions)) {
                static::setupLogger();
                require_once('ajax/' . $_POST['subaction'] . '.ajax.php');
            } else {
                throw new Exception('Beklager, støtter ikke denne handlingen!');
            }
        } catch (Exception $e) {
            static::addResponseData('success', false);
            static::addResponseData('message', $e->getMessage());
            static::addResponseData('code', $e->getCode());
        }

        $data = json_encode(static::getResponseData());
        echo $data;
        die();
    }

    /**
     * Legg til alle scripts som videresendingen bruker
     * 
     * (og ja, det er en del!)
     *
     * @return void
     */
    public static function script()
    {
        wp_enqueue_script('WPbootstrap3_js');
        wp_enqueue_style('WPbootstrap3_css');
        wp_enqueue_script('TwigJS');
        wp_enqueue_style('UKMVideresending_style', static::getPluginUrl() . 'ukmvideresending.css');
        wp_enqueue_script('UKMVideresending_script_emitter', static::getPluginUrl() . 'javascript/emitter.js');
        // JS-app for videresending
        wp_enqueue_script('UKMVideresending_script_videresend_app', static::getPluginUrl() . 'javascript/videresendApp.js');
        wp_enqueue_script('UKMVideresending_script_videresend_item', static::getPluginUrl() . 'javascript/videresendItem.js?v=2019-03-13-V2');
        // JS-app for medie-håndtering
        wp_enqueue_script('UKMVideresending_script_medie_app', static::getPluginUrl() . 'javascript/medieApp.js');
        wp_enqueue_script('UKMVideresending_script_medie_item', static::getPluginUrl() . 'javascript/medieItem.js');
        // JS-app for leder-håndtering
        if (isset($_GET['action']) && $_GET['action'] == 'ledere') {
            wp_enqueue_script('UKMVideresending_script_leder_app', static::getPluginUrl() . 'javascript/lederApp.js');
            wp_enqueue_script('UKMVideresending_script_leder_item', static::getPluginUrl() . 'javascript/lederItem.js');
            wp_enqueue_script('UKMVideresending_script_leder_overnatting', static::getPluginUrl() . 'javascript/lederOvernatting.js');
        }
        // JS-app for leder-håndtering
        switch (static::getAction()) {
            case 'nominasjon':
                wp_enqueue_script('UKMVideresending_script_nominasjon', static::getPluginUrl() . 'javascript/nominasjon.js');
                break;
            case 'reiseinfo':
            case 'intoleranser':
                wp_enqueue_script('UKMVideresending_script_tilrettelegging');
                break;
        }
        wp_enqueue_script('UKMVideresending_script_videresending', static::getPluginUrl() . 'ukmvideresending.js');
    }

    public static function registerScript() {
        wp_register_script('UKMVideresending_script_tilrettelegging', static::getPluginUrl() . 'javascript/tilrettelegging.js');
    }

    /**
     * Registrer menyer
     *
     **/
    public static function meny()
    {
        add_action(
            'admin_print_styles-' .
                add_menu_page(

                    'Send videre',
                    'Send videre',
                    'editor',
                    'UKMVideresending',
                    ['UKMVideresending', 'renderAdmin'],
                    'dashicons-external', #'//ico.ukm.no/paper-airplane-20.png',
                    90
                ),
            ['UKMVideresending', 'script']
        );
    }

    /**
     * Håndter lagring
     */
    public static function save()
    {
        static::require('save/' . basename($_GET['save']) . '.save.php');
    }


    /**
     * Beregn og lagre antall videresendte personer som metadata
     *
     * @throws Exception
     * @return Bool
     */
    public static function beregnAntallVideresendtePersoner()
    {
        $fra = static::getFra();
        $til = UKMVideresending::getValgtTil();


        $unike_personer = [];
        foreach( $fra->getVideresendte( $til->getId() )->getAll() as $innslag ) {
            foreach ($innslag->getPersoner()->getAll() as $person) {
                $unike_personer[] = $person->getId();
            }
        }
        $unike_personer = array_unique($unike_personer);

        WriteMeta::set(
            $fra->getMeta('antall_videresendte_personer_til_'. $til->getId())
                ->set(
                    sizeof($unike_personer)
                )
        );

        return true;
    }

    /**
     * Hent alle overnattingssteder for et gitt arrangement
     *
     * @param Arrangement $arrangement_til
     * @return Array
     */
    public static function getOvernattingssteder( Arrangement $arrangement_til )
    {
        $deltakerovernatting = $arrangement_til->getMetaValue('navn_deltakerovernatting') ? 
            $arrangement_til->getMetaValue('navn_deltakerovernatting') :
            'Deltakerovernatting';

        return [
            'deltakere'     => $deltakerovernatting,
            'hotell'        => 'Lederhotellet',
            'privat'        => 'Privat/annet'
        ];
    }

    /**
     * Hent et TwigJS-objekt av en person og dens allergier
     *
     * @param Person $person
     * @param Intoleranse $allergi
     * @return stdClass
     */
    public static function getIntoleransePersonData(Person $person, Intoleranse $allergi = null)
    {
        $data = new stdClass();
        $data->ID = $person->getId();
        $data->navn = $person->getNavn();
        $data->mobil = $person->getMobil();
        if (!is_null($allergi)) {
            $data->intoleranse_liste = $allergi->getListe();
            $data->intoleranse_human = $allergi->getListeHuman();
            $data->intoleranse_tekst = $allergi->getTekst();
        }

        return $data;
    }
}


UKMVideresending::init(__DIR__);
UKMVideresending::hook();
