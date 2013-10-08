<?php  
/* 
Plugin Name: UKM Videresending
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/

require_once('save.php');
## HOOK MENU AND SCRIPTS
if(is_admin()) {
	global $blog_id;
	if($blog_id != 1)
		add_action('admin_menu', 'UKMVideresending_menu');
	add_action('admin_init', 'UKMVideresending_scriptsandstyles',1000);
	add_action('admin_init', 'UKMV_save',2000);
	require_once('UKM/phaseout_titleinfo.class.php');

}

add_action('wp_ajax_UKMV_rapport_fraktseddel', 'UKMV_rapport_fraktseddel_ajax');
add_action('wp_ajax_UKMVS_question_add', 'UKMVS_question_add');
add_action('wp_ajax_UKMVS_question_order', 'UKMVS_question_order');
add_action('wp_ajax_UKMVS_question_remove', 'UKMVS_question_remove');

function UKMVS_question_remove(){
	require_once('videresendingsskjema.php');
	ajax_question_remove($_POST['removethisquestion']);
	die();	
}
function UKMVS_question_add(){
	require_once('videresendingsskjema.php');
	ajax_question_add(true);
	die();
}

function UKMVS_question_order() {
	require_once('videresendingsskjema.php');
	ajax_question_order();
	die();
}

function UKMVideresending_scriptsandstyles() {
	wp_register_style( 'UKMVideresending_css', plugin_dir_url( __FILE__ ) .'videresending.css');
	wp_register_style('zoombox_css','/wp-content/plugins/UKMvisitorpages/zoombox/zoombox.css');

	wp_register_style('UKMVideresendingsskjema_css', plugin_dir_url( __FILE__ ) .'videresendingsskjema.css');


	wp_enqueue_script('jqueryGoogleUI', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
	
#	wp_register_script('jquery-ui-effects-core', 'http://ukm.no/wp-includes/js/jquery/ui/jquery.effects.core.min.js');
	wp_register_script('zoombox_js','/wp-content/plugins/UKMvisitorpages/zoombox/zoombox.js');
#	wp_register_script('shake', 'http://ukm.no/wp-includes/js/jquery/ui/jquery.effects.shake.min.js');
	wp_register_script('UKMVideresending_js', plugin_dir_url( __FILE__ ) .'videresending.js');
	wp_register_script('UKMVideresendingsskjema_js', plugin_dir_url( __FILE__ ) .'videresendingsskjema.js');
	wp_register_script('UKMNorge_JQprint', plugin_dir_url( __FILE__ ) .'/UKMNorge/js/jquery.print.js');
}
## CREATE A MENU
function UKMVideresending_menu() {
	$page = add_menu_page('Videresending', 'Videresending', 'editor', 'UKMVideresending', 'UKMVideresending', 'http://ico.ukm.no/paper-airplane-20.png',212);
	add_action( 'admin_print_styles-' . $page, 'UKMVideresending_scriptsandstyles_print' );

	if(get_option('site_type')=='fylke') {
		$page2 = add_menu_page('Lag skjema for videresending', 'Lag skjema for videresending', 'editor', 'UKMVideresendingsskjema', 'UKMVideresendingsskjema', 'http://ico.ukm.no/clipboard-20.png',200);
		add_action( 'admin_print_styles-' . $page2, 'UKMVideresendingsskjema_scriptsandstyles_print' );
	}
#	add_action( 'admin_print_styles-' . $subpage, 'UKMVideresending_scriptsandstyles_print' );
}

function UKMVideresendingsskjema(){
	require_once('videresendingsskjema.php');
	question_admin();
}

function UKMV_rapporter() {
	require_once('rapporter/start.php');
	if(isset($_GET['rapport'])) {
		require_once('rapporter/'.$_GET['rapport'].'.php');
		$function = 'UKMV_rapporter_'.$_GET['rapport'];
		$function();
	} else {
		echo UKMV_rapporter_splash();
	}
}

function UKMVideresendingsskjema_scriptsandstyles_print() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-effects-core');
	wp_enqueue_script('zoombox_js');
	wp_enqueue_script('shake');
	wp_enqueue_script('UKMVideresendingsskjema_js');
	wp_enqueue_style('UKMVideresendingsskjema_css');
}


function UKMVideresending_scriptsandstyles_print() {
	wp_enqueue_style( 'UKMVideresending_css');
	wp_enqueue_style('zoombox_css');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-effects-core');
	wp_enqueue_script('zoombox_js');
	wp_enqueue_script('shake');
	wp_enqueue_script('UKMVideresending_js');
	wp_enqueue_script('UKMNorge_JQprint');
}


function UKMVideresending() {
	require_once('UKM/inc/phaseout.ico.inc.php');
	require_once('UKM/sql.class.php');
	require_once('UKM/monstring.class.php');
	require_once('videresending.php');
	echo UKMVideresending_gui();
}

function UKMV_rapport_fraktseddel_ajax() {
	require_once('rapporter/kunsthenting.ajax.php');
}

?>