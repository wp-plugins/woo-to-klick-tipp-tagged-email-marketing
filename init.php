<?php

/*

Plugin Name: WOO to Klick-Tipp Tagged Email Marketing
URI: https://wordpress.org/woo-to-klick-tipp-tagged-email-marketing/
Description: Sync customers data between WooCommerce and Klick-Tipp (tag based e-mail marketing)
Version: 1.1
Author: Tobias B. Conrad
Author URI: https://wordpress.org/support/profile/tobias_conrad

*/


register_activation_hook(__FILE__,'activate');

function activate(){
   global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');    /* file included for database */
	$reoccurence		=	'12perhour';
	wp_schedule_event( time(), $reoccurence, 'klicktip_cron_event');
	wp_schedule_event( time(), $reoccurence, 'klicktip_cron_event_cpanel');
	
}

register_deactivation_hook( __FILE__, 'klicktip_deactivation' );

/* On deactivation, remove all functions from the scheduled action hook. */
function klicktip_deactivation() {
	wp_clear_scheduled_hook( 'klicktip_cron_event' );
	wp_clear_scheduled_hook( 'klicktip_cron_event_cpanel' );
}

add_filter('cron_schedules','klicktip_cron_definer');

function klicktip_cron_definer(){
	global $wpdb;
	$hourresult	=	'12';
	$per_tym	=	'hour';
		$intrate	= $hourresult.'perhour';		
		$inttime	=	3600/$hourresult;		
		$schedules[$intrate] = array(
		  'interval'=> $inttime,
		  'display'=>  __( $hourresult.'times/hour')
		);
	return $schedules;
}

add_action('klicktip_cron_event', 'klicktip_cron_event_fun');
function klicktip_cron_event_fun(){
	if(get_option('klicktip_wordpress_cron')=='1'){
		include 'cron_execute.php';
	}
}

add_action('klicktip_cron_event_cpanel', 'klicktip_cron_event_cpanel_fun');
function klicktip_cron_event_cpanel_fun(){
	include 'cron_execute.php';
}


/* Start Function add menus  */
add_action('admin_menu','add_menus');

function add_menus(){
	add_menu_page( "Klick-Tipp Bridge", "Klick-Tipp Bridge", "administrator", "klicktippbridge", "klicktippbridge_fun");	
	//add_submenu_page( "edit.php?post_type=contest", "Buyers", "Buyers", "administrator", "rn_corporate_buyers", "rn_corporate_buyers_fun" );
}



add_action('admin_enqueue_scripts', 'klictip_admin_files');



function klictip_admin_files()

{

	wp_enqueue_script('jquery');

	wp_register_style('jquery-core-ui-css', plugins_url('jquery-ui.css', __FILE__));

	wp_enqueue_style('jquery-core-ui-css' );

	wp_register_style('jquery-google-font-css', 'http://fonts.googleapis.com/css?family=Kristi|Crafty+Girls|Yesteryear|Finger+Paint|Press+Start+2P|Spirax|Bonbon|Over+the+Rainbow');

	wp_enqueue_style('jquery-google-font-css');

	

	wp_register_style('prefix-style-source-css', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro');

	wp_enqueue_style('prefix-style-source-css' );



	wp_register_style('prefix-style-roboto-css', 'http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' );

	wp_enqueue_style('prefix-style-roboto-css' );

		wp_register_style('prefix-style-custom-css', plugins_url('custom-style.css', __FILE__) );

	wp_enqueue_style('prefix-style-custom-css' );



	wp_register_style('prefix-style-awesome-css', plugins_url('css/font-awesome.css', __FILE__) );

	wp_enqueue_style('prefix-style-awesome-css' );

	

	wp_enqueue_script('custom-js', plugin_dir_url( __FILE__ ).'js/custom-js.js');

}





function klicktippbridge_fun(){

	include 'plugin.php';	

}



function pre($arr){

	echo '<pre>';print_r($arr);echo '</pre>';

}

?>