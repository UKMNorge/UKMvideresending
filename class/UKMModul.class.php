<?php
class UKMmodul {
	public static $view_data = null;
	public static $ajax_response = null;
	public static $action = null;
	
	public static function init( $pl_id ) {
		if( isset( $_GET['action'] ) ) {
			self::$action = $_GET['action'];
		}
	}

	/**
	 * Hent hvilken viewAction som er aktive
	 *
	 * @return string
	**/
	public static function getAction() {
		return self::$action;
	}
		
	/**
	 * Hent alle view-data
	 *
	 * @return array
	**/
	public static function getViewData() {
		return self::$view_data;
	}

	/**
	 * Legg til viewdata
	 * 
	 * Tar i mot array med flere keys (ett parameter)
	 * eller key, value (to parameter)
	 *
	 * @param [string|array] key eller [key => val]
	 * @param [null|array] data hvis oppgitt key som string
	 * @return void
	**/
	public static function addViewData( $key_or_array, $data=null ) {
		if( is_array( $key_or_array ) ) {
			self::$view_data = array_merge( self::$view_data, $key_or_array );
		} else {
			self::$view_data[ $key_or_array ] = $data;
		}
	}
	
	/**
	 * Hent alle ajax response-data
	 *
	 * @return array
	**/
	public static function getResponseData() {
		return self::$ajax_response;
	}

	/**
	 * Legg til ajax respons-data
	 * 
	 * Tar i mot array med flere keys (ett parameter)
	 * eller key, value (to parameter)
	 *
	 * @param [string|array] key eller [key => val]
	 * @param [null|array] data hvis oppgitt key som string
	 * @return void
	**/
	public static function addResponseData( $key_or_array, $data=null ) {
		if( is_array( $key_or_array ) ) {
			self::$ajax_response = array_merge( self::$ajax_response, $key_or_array );
		} else {
			self::$ajax_response[ $key_or_array ] = $data;
		}
	}

}