<?php  
/* 
Plugin Name: UKM Videresending
Plugin URI: http://www.ukm-norge.no
Description: Videresendingsfunksjoner for alle mønstringer.
Author: UKM Norge / M Mandal / A Hustad
Version: 3.0 
Author URI: http://mariusmandal.no
*/
require_once('class/UKMModul.class.php');


class UKMVideresending extends UKMmodul {
	public static $monstring = null;
	public static $til = null;
	
	/**
	 * Initier Videresending-objektet
	 *
	**/
	public static function init( $pl_id=null ) {
		self::$view_data = [];
		self::$monstring = new monstring_v2( $pl_id );
		self::$action = 'informasjon';
		
		parent::init( $pl_id );
	}
	
	/**
	 * Get type mønstring vi videresender fra
	 *
	 * @return string (kommune|fylke|land)
	**/	
	public static function getType() {
		return self::$monstring->getType();
	}
	
	/**
	 * Get mønstringen vi videresender fra
	 *
	 * @return monstring_v2 $mønstring
	**/
	public static function getFra() {
		return self::$monstring;
	}
	
	/**
	 * Hent alle mønstringer vi kan videresende til
	 * 
	 * @return array[ monstring_v2 ]
	**/
	public static function getTil() {
		require_once('UKM/monstringer.class.php');
		if( self::getType() == 'fylke' )  {
			$monstringer = [
				monstringer_v2::land( self::getFra()->getSesong() )
			];
		} else {
			$monstringer = self::getFra()->getFylkesmonstringer();
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
		if( isset( $_GET['fylke'] ) && self::getFra()->getType() == 'kommune' && sizeof( self::getTil() ) > 1 ) {
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
		
		self::addViewData('valgt_til', $valgt_til );
		return $valgt_til;
	}
	
	
	/**
	 * Hent alle view-data
	 *
	 * @return array
	**/
	public static function getViewData() {
		self::addViewData('fra', self::getFra() );
		self::addViewData('til', self::getTil() );
		self::addViewData('tab', self::getAction() );
		return parent::getViewData();
	}
	
	public static function ajax() {
		if( is_array( $_POST ) ) {
			self::addResponseData('POST', $_POST );
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
			self::addResponseData('success', false);
			self::addResponseData('message', $e->getMessage() );
			self::addResponseData('code', $e->getCode() );
		}
		
		$data = json_encode( self::getResponseData() );
		echo $data;
		die();
	}

	/**
	 * Generer admin-GUI
	 *
	 * @return void, echo GUI.
	**/
	public static function admin() {
		## SETUP LOGGER
		global $current_user;
		get_currentuserinfo();
		require_once('UKM/logger.class.php'); 
		UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );

		## ACTION CONTROLLER
		require_once('controller/'. self::getAction() .'.controller.php');
		
		## RENDER
		echo TWIG( ucfirst(self::getAction()) .'/forside.html.twig', self::getViewData() , dirname(__FILE__), true);
		echo TWIGjs( dirname(__FILE__) );
		return;
	}
	public static function script() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_script('TwigJS');
		wp_enqueue_style( 'UKMVideresending_style', plugin_dir_url( __FILE__ ) .'ukmvideresending.css');
		wp_enqueue_script( 'UKMVideresending_script_emitter', plugin_dir_url( __FILE__ ) .'javascript/emitter.js');
		// JS-app for videresending
		wp_enqueue_script( 'UKMVideresending_script_videresend_app', plugin_dir_url( __FILE__ ) .'javascript/videresendApp.js');
		wp_enqueue_script( 'UKMVideresending_script_videresend_item', plugin_dir_url( __FILE__ ) .'javascript/videresendItem.js?v=2019-03-13');
		// JS-app for medie-håndtering
		wp_enqueue_script( 'UKMVideresending_script_medie_app', plugin_dir_url( __FILE__ ) .'javascript/medieApp.js');
		wp_enqueue_script( 'UKMVideresending_script_medie_item', plugin_dir_url( __FILE__ ) .'javascript/medieItem.js');
		// JS-app for leder-håndtering
		if( isset($_GET['action']) && $_GET['action'] == 'ledere' ) {
			wp_enqueue_script( 'UKMVideresending_script_leder_app', plugin_dir_url( __FILE__ ) .'javascript/lederApp.js');
			wp_enqueue_script( 'UKMVideresending_script_leder_item', plugin_dir_url( __FILE__ ) .'javascript/lederItem.js');
			wp_enqueue_script( 'UKMVideresending_script_leder_overnatting', plugin_dir_url( __FILE__ ) .'javascript/lederOvernatting.js');
		}

		wp_enqueue_script( 'UKMVideresending_script_videresending', plugin_dir_url( __FILE__ ) .'ukmvideresending.js');
	}
	
	/**
	 * Registrer conditions for menyen
	**/
	public static function menu_conditions( $_CONDITIONS ) {
		return array_merge( $_CONDITIONS, 
			['UKMVideresending' => 'monstring_er_startet']
		);
	}

	/**
	 * Registrer menyer
	 *
	**/
	public static function meny() {
		UKM_add_menu_page(
			'monstring', 
			'Videresending', 
			'Videresending',
			'editor', 
			'UKMVideresending',
			['UKMVideresending','admin'],
			'//ico.ukm.no/paper-airplane-20.png',
			20
		);
		UKM_add_scripts_and_styles(
			'UKMVideresending_admin',	# Page-hook
			['UKMVideresending', 'script']	# Script-funksjon
		);

		if( self::getType() == 'fylke') {
			// Legg videresendingsskjemaet som en submenu under Mønstring.
			UKM_add_submenu_page(
				'UKMMonstring', 
				'Videresendingsskjema', 
				'Skjema for videresending', 
				'editor', 
				'UKMVideresendingsskjema', 
				['UKMVideresending','skjema']
			);
			UKM_add_scripts_and_styles(
				'UKMVideresending_skjema',
				['UKMVideresending', 'skjema_script']
			);
			// Legg nominasjon som en submenu under Mønstring.
			UKM_add_submenu_page(
				'UKMVideresending',
				'Nominasjon',
				'Nominasjoner',
				'ukm_nominasjon',
				'UKMnominasjon',
				['UKMVideresending', 'nominasjon']
			);
			UKM_add_scripts_and_styles(
				'UKMVideresending_nominasjon',
				['UKMVideresending', 'nominasjon_script']
			);

			// Hookes inn under fylkets mønstring-meny
			// da dette er det mest logiske stedet å vise det, selv
			// om informasjonen brukes kun til videresendingen for lokalkontakter
			UKM_add_submenu_page(	'UKMMonstring', 
									'Infotekst om videresending', 
									'Infotekst om videresending', 
									'editor', 
									'UKMmonstring_videresending_info',
									['UKMVideresending','info_fra_fylket']
								);
		}
	}
	
	/**
	 * Lar fylkene administrere sitt skjema
	**/
	public static function skjema() {
		## ACTION CONTROLLER
		require_once('controller/skjema_admin.controller.php');
		
		## RENDER
		echo TWIG( 'Skjema/admin.html.twig', self::getViewData() , dirname(__FILE__), true);
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
		echo TWIG( 'Nominasjon/forside.html.twig', self::getViewData() , dirname(__FILE__), true);
		return;
	}
	public static function nominasjon_script() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_style( 'UKMVideresending_style', plugin_dir_url( __FILE__ ) .'ukmvideresending.css');
		wp_enqueue_script( 'UKMVideresending_script_nominasjon', plugin_dir_url( __FILE__ ) .'javascript/nominasjon.js');
	}

	public static function info_fra_fylket() {
		$option_name = 'videresending_info_pl'.get_option('pl_id');
		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$TWIG['saved'] = update_site_option($option_name, $_POST['videresending_editor'] );
		}
		$TWIGdata = array('UKM_HOSTNAME' => UKM_HOSTNAME);
		echo TWIG('Informasjon/editor_pre.html.twig', $TWIGdata, dirname(__FILE__) );
		wp_editor( stripslashes(get_site_option($option_name)), 'videresending_editor', $settings = array() );
		echo TWIG('Informasjon/editor_post.html.twig', $TWIGdata, dirname(__FILE__) );
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
		$monstring = self::getFra();
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
		
		self::updateInfoskjema(
			'systemet_overnatting_spektrumdeltakere',
			sizeof( $unike_personer )
		);
	}
	
	public static function updateInfoskjema( $field, $value ) {
		$monstring = self::getFra();
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
		$monstring = self::getFra();
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



## HOOK MENU AND SCRIPTS
if(is_admin()) {
	
	# Kun initier på mønstringssider
	if( is_numeric( get_option('pl_id') )	 ) {
		UKMVideresending::init( get_option('pl_id') );
		if( get_option('site_type') == 'fylke' || get_option('site_type') == 'kommune' ) {
			add_action('UKM_admin_menu', ['UKMVideresending', 'meny'], 101);
			add_filter('UKM_admin_menu_conditions', ['UKMvideresending','menu_conditions']);
			add_action('wp_ajax_UKMVideresending_ajax', ['UKMVideresending', 'ajax']);
		}
	}

	# Network dash kjører uten mønstringside
	add_filter( 'UKMWPNETWDASH_messages', ['UKMVideresending', 'checkDocuments'] );
}