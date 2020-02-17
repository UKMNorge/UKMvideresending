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

require_once('UKM/Autoloader.php');


class UKMVideresending extends UKMNorge\Wordpress\Modul {
	public static $action = 'velg';
    public static $path_plugin = null;
    public static $arrangement;
    public static $til = null;
	
	/**
	 * Initier Videresending-objektet
	 *
	**/
	public static function init($plugin_path) {
        parent::init( $plugin_path );

        if( is_numeric( get_option('pl_id') ) ) {
            try {
                static::$arrangement = new Arrangement( intval(get_option('pl_id')) );
            } catch( Exception $e ) {
                // silenty let the exception pass 🧙🏼‍♀️
            }
        }
    }
    
    public static function hook() {
        # Kun initier på mønstringssider
        if( is_numeric( get_option('pl_id') )	 ) {
            if( get_option('pl_id') ) {
                add_action('admin_menu', ['UKMVideresending', 'meny'], 101);
                add_action('wp_ajax_UKMVideresending_ajax', ['UKMVideresending', 'ajax']);
            }
        }

        # Network dash kjører uten mønstringside
        add_filter( 'UKMWPNETWDASH_messages', ['UKMVideresending', 'checkDocuments'] );
    }
	
	/**
	 * Get type mønstring vi videresender fra
	 *
	 * @return string (kommune|fylke|land)
	**/	
	public static function getType() {
		if( is_null( static::$arrangement ) ) {
            return 'kommune'; //laveste nivå
        }
        return static::$arrangement->getType();
	}
	
	/**
	 * Get mønstringen vi videresender fra
	 *
	 * @return monstring_v2 $mønstring
	**/
	public static function getFra() {
		return static::$arrangement;
	}
	
	/**
	 * Hent alle mønstringer vi kan videresende til
	 * 
	 * @return array[ monstring_v2 ]
	**/
	public static function getTil() {
        return [];
		if( static::getType() == 'fylke' )  {
			$monstringer = [
				monstringer_v2::land( static::getFra()->getSesong() )
			];
		} else {
			$monstringer = static::getFra()->getFylkesmonstringer();
		}
		
		foreach( $monstringer as $monstring ) {
			$monstring->setAttr(
				'infotekst',
				stripslashes(
					get_site_option( 'videresending_info_pl'. $monstring->getId() )
				)
			);
		}
		return $monstringer;
	}
	
	/**
	 * getValgt til
	 *
	 * Hvis GET['fylke'] er satt, legger modulen opp til at vi skal hente
	 * en gitt fylkesmønstring. Legg derfor til denne som "valgt_til" hvis
	 * dette er en lokalmønstring
	 *
	 * @return valgt_til for bruk i controller
	**/
	public static function loadValgtTil() {
		$valgt_til = false;
		if( isset( $_GET['fylke'] ) && static::getFra()->getType() == 'kommune' && sizeof( static::getTil() ) > 1 ) {
			foreach( UKMVideresending::getTil() as $monstring ) {
				if( $monstring->getFylke()->getId() == $_GET['fylke'] ) {
					$valgt_til = $monstring;
				}
			}
			if( false == $valgt_til ) {
				throw new Exception('Beklager, klarte ikke å finne valgt mønstring ('. $_GET['fylke'] .')');
			}
		} else {
			$valgt_til = array_pop( UKMVideresending::getTil() );
		}
		
		static::addViewData('valgt_til', $valgt_til );
		return $valgt_til;
	}
	
	
	/**
	 * Hent alle view-data
	 *
	 * @return array
	**/
	public static function getViewData() {
		static::addViewData('fra', static::getFra() );
		static::addViewData('til', static::getTil() );
		static::addViewData('tab', static::getAction() );
		return parent::getViewData();
	}
	
	public static function ajax() {
		if( is_array( $_POST ) ) {
			static::addResponseData('POST', $_POST );
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
			
			if( in_array( $_POST['subaction'], $supported_actions ) ) {
				## SETUP LOGGER
				global $current_user;
				get_currentuserinfo();
				require_once('UKM/logger.class.php'); 
				UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );

				require_once('ajax/'. $_POST['subaction'] .'.ajax.php');
			} else {
				throw new Exception('Beklager, støtter ikke denne handlingen!');
			}
		} catch( Exception $e ) {
			static::addResponseData('success', false);
			static::addResponseData('message', $e->getMessage() );
			static::addResponseData('code', $e->getCode() );
		}
		
		$data = json_encode( static::getResponseData() );
		echo $data;
		die();
	}
	
	public static function script() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_script('TwigJS');
		wp_enqueue_style( 'UKMVideresending_style', plugin_dir_url( __FILE__ ) .'ukmvideresending.css');
		wp_enqueue_script( 'UKMVideresending_script_emitter', plugin_dir_url( __FILE__ ) .'javascript/emitter.js');
		// JS-app for videresending
		wp_enqueue_script( 'UKMVideresending_script_videresend_app', plugin_dir_url( __FILE__ ) .'javascript/videresendApp.js');
		wp_enqueue_script( 'UKMVideresending_script_videresend_item', plugin_dir_url( __FILE__ ) .'javascript/videresendItem.js?v=2019-03-13-V2');
		// JS-app for medie-håndtering
		wp_enqueue_script( 'UKMVideresending_script_medie_app', plugin_dir_url( __FILE__ ) .'javascript/medieApp.js');
		wp_enqueue_script( 'UKMVideresending_script_medie_item', plugin_dir_url( __FILE__ ) .'javascript/medieItem.js');
		// JS-app for leder-håndtering
		if( isset($_GET['action']) && $_GET['action'] == 'ledere' ) {
			wp_enqueue_script( 'UKMVideresending_script_leder_app', plugin_dir_url( __FILE__ ) .'javascript/lederApp.js');
			wp_enqueue_script( 'UKMVideresending_script_leder_item', plugin_dir_url( __FILE__ ) .'javascript/lederItem.js');
			wp_enqueue_script( 'UKMVideresending_script_leder_overnatting', plugin_dir_url( __FILE__ ) .'javascript/lederOvernatting.js');
		}
		// JS-app for leder-håndtering
		if( isset($_GET['action']) && $_GET['action'] == 'reiseinfo' ) {
			wp_enqueue_script( 'UKMVideresending_script_tilrettelegging', plugin_dir_url( __FILE__ ) .'javascript/tilrettelegging.js');
        }
		wp_enqueue_script( 'UKMVideresending_script_videresending', plugin_dir_url( __FILE__ ) .'ukmvideresending.js');
	}

	/**
	 * Registrer menyer
	 *
	**/
	public static function meny() {
		$page = add_menu_page(
			'Videresending', 
			'Videresending',
			'editor', 
			'UKMVideresending',
			['UKMVideresending','renderAdmin'],
			'dashicons-external',#'//ico.ukm.no/paper-airplane-20.png',
			90
		);
		add_action(
			'admin_print_styles-' . $page,
			['UKMVideresending', 'script']
		);
		
		if( static::getType() == 'fylke') {
			// Legg videresendingsskjemaet som en submenu under Mønstring.
			add_submenu_page(
				'UKMMonstring', 
				'Videresendingsskjema', 
				'Skjema for videresending', 
				'editor', 
				'UKMVideresendingsskjema', 
				['UKMVideresending','skjema']
			);
			#UKM_add_scripts_and_styles(
			#	'UKMVideresending_skjema',
			#	['UKMVideresending', 'skjema_script']
			#);
			// Legg nominasjon som en submenu under Mønstring.
			add_submenu_page(
				'UKMVideresending',
				'Nominasjon',
				'Nominasjoner',
				'ukm_nominasjon',
				'UKMnominasjon',
				['UKMVideresending', 'nominasjon']
			);
			#UKM_add_scripts_and_styles(
			#	'UKMVideresending_nominasjon',
			#	['UKMVideresending', 'nominasjon_script']
			#);
		}
	}
	
	/**
	 * Lar fylkene administrere sitt skjema
	**/
	public static function skjema() {
		## ACTION CONTROLLER
		require_once('controller/skjema_admin.controller.php');
		
		## RENDER
		echo TWIG( 'Skjema/admin.html.twig', static::getViewData() , dirname(__FILE__), true);
		return;
	}
	
	public static function skjema_script() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_script( 'UKMVideresending_script_skjema_admin', plugin_dir_url( __FILE__ ) .'javascript/skjema_admin.js');
	}

	/**
	 * Fylkene har eget menyvalg for nominasjonsskjema
	**/	
	public static function nominasjon() {
		## ACTION CONTROLLER
		require_once('controller/nominasjon.controller.php');
		
		## RENDER
		echo TWIG( 'Nominasjon/forside.html.twig', static::getViewData() , dirname(__FILE__), true);
		return;
	}
	public static function nominasjon_script() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_style( 'UKMVideresending_style', plugin_dir_url( __FILE__ ) .'ukmvideresending.css');
		wp_enqueue_script( 'UKMVideresending_script_nominasjon', plugin_dir_url( __FILE__ ) .'javascript/nominasjon.js');
	}
	
	/**
	 *
	**/
	public static function middagsgjester( $monstring_til, $monstring_fra ) { 
		$middagsgjester = array('ukm'=>0,'fylke1'=>0,'fylke2'=>0);
		$sql = new SQL("SELECT `ledermiddag_ukm`,
								`ledermiddag_fylke1`,
								`ledermiddag_fylke2`
						FROM `smartukm_videresending_ledere_middag`
						WHERE `pl_to` = '#pl_to'
						AND `pl_from` = '#pl_from'",
					array( 'pl_to' => $monstring_til,
							'pl_from' => $monstring_fra
						)
					);
		$res = $sql->run();
		
		if( $res && mysql_num_rows( $res ) > 0 ) {
			$r = SQL::fetch( $res );
			$middagsgjester['ukm'] = $r['ledermiddag_ukm'];
			$middagsgjester['fylke1'] = $r['ledermiddag_fylke1'];
			$middagsgjester['fylke2'] = $r['ledermiddag_fylke2'];
		}
		return $middagsgjester;
	}

	public static function overnattingssteder() {
		return [
			'deltakere' 	=> 'Landsbyen',
			'hotell'		=> 'Lederhotellet',
			'privat'		=> 'Privat/annet'
		];
	}
	
	public static function calcAntallPersoner() {
		$monstring = static::getFra();
		$festivalen = array_pop( UKMVideresending::getTil() );
		
		if( $monstring->getType() != 'fylke' ) {
			throw new Exception('Kun fylkesmønstringer skal beregne antall personer totalt');
		}
		
		$unike_personer = [];
		foreach( $festivalen->getInnslag()->getAll() as $innslag ) {
			if( $innslag->getFylke()->getId() != $monstring->getFylke()->getId() ) {
				continue;
			}
			
			foreach( $innslag->getPersoner()->getAll() as $person ) {
				if( $person->erVideresendtTil( $festivalen ) ) {
					$unike_personer[] = $person->getId();
				}
			}
		}
		$unike_personer = array_unique( $unike_personer );
		
		static::updateInfoskjema(
			'systemet_overnatting_spektrumdeltakere',
			sizeof( $unike_personer )
		);
	}
	
	public static function updateInfoskjema( $field, $value ) {
		$monstring = static::getFra();
		$festivalen = array_pop( UKMVideresending::getTil() );
		
		if( $monstring->getType() != 'fylke' ) {
			throw new Exception('Kun fylkesmønstringer skal benytte infoskjema');
		}


		$sql = new SQL("
			SELECT `#field` AS `field`
			FROM `smartukm_videresending_infoskjema`
			WHERE `pl_id` = '#pl_to'
				AND `pl_id_from` = '#pl_from'",
			[
				'pl_to' => $festivalen->getId(),
				'pl_from' => $monstring->getId(),
				'field' => $field
			]
		);
		$res = $sql->run();
		
		/**
		 * Lik verdi = return true
		**/
		$row = SQL::fetch( $res );
		if( $row['field'] == $value ) {
			return true;	
		}
	
		/**
		 * Finnes ikke i databasen? insert
		**/
		if( SQL::numRows( $res ) == 0 ) {
			$SQLins = new SQLins('smartukm_videresending_infoskjema');
			$SQLins->add('pl_id', $festivalen->getId());
			$SQLins->add('pl_id_from', $monstring->getId());
		} else {
			$SQLins = new SQLins(
				'smartukm_videresending_infoskjema', 
				[
					'pl_id' => $festivalen->getId(),
					'pl_id_from' => $monstring->getId()
				]
			);
		}
		$SQLins->add($field, $value);
		$res = $SQLins->run();
		return $res != -1;
	}
	
	public static function getInfoSkjema( $field ) {
		$monstring = static::getFra();
		$festivalen = array_pop( UKMVideresending::getTil() );
		
		if( $monstring->getType() != 'fylke' ) {
			throw new Exception('Kun fylkesmønstringer skal benytte infoskjema');
		}

		$sql = new SQL("
			SELECT `#field` AS `field`
			FROM `smartukm_videresending_infoskjema`
			WHERE `pl_id` = '#pl_to'
				AND `pl_id_from` = '#pl_from'",
			[
				'pl_to' => $festivalen->getId(),
				'pl_from' => $monstring->getId(),
				'field' => $field
			]
		);
		return $sql->run('field','field');
	}

	public static function checkDocuments( $MESSAGES ) {
		$month = date('n');
		$season = ($month > 7) ? date('Y')+1 : date('Y');
	
		$info1 = get_site_option('UKMFvideresending_info1_'.$season);
	
		if ($month < 6) {
			if( !$info1 || empty($info1) ) {
				$MESSAGES[] = array(	'level' => 'alert-warning', 
										'module' => 'UKMVideresending', 
										'header' => 'Info 1-dokumentet er ikke oppdatert fra i fjor!', 
										'body' => 'Rett dette ved å legge inn rett dokument i Mønstringsmodulen.',
										'link' => '//ukm.no/festivalen/wp-admin/admin.php?page=UKMMonstring' 
									);
				return $MESSAGES;
			}	
		}
		return $MESSAGES;
	}
}


UKMVideresending::init(__DIR__);
UKMVideresending::hook();