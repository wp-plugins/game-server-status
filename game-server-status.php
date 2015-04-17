<?php
/*
Plugin Name: Game Server Status
Plugin URI: http://codecanyon.net/user/GrohsFabian/portfolio
Description: Get the live status and details of your game server ( minecraft, counter strike, sa:mp ). Widget + Shortcodes
Author: GrohsFabian
Version: 1.0
Author URI: http://codecanyon.net/user/GrohsFabian/portfolio
Text Domain: game-server-status
Domain Path: /languages/
*/
defined('ABSPATH') or die('Get out.');

/* Define a path variable */
$plugin_path = plugin_dir_path(__FILE__);

/* Update the database table when the plugin is activated */
function grohsfabian_update_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'game_servers';

	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			`server_id` INT( 11 ) NOT NULL AUTO_INCREMENT,
			`server_name` varchar(64) NOT NULL,
			`server_ip` varchar(32) NOT NULL,
			`server_connection_port` int(11) NOT NULL,
			`server_query_port` int(11) NOT NULL,
			`cache_time` int(11) NOT NULL,
			`game_engine` varchar(16) NOT NULL,
			PRIMARY KEY (`server_id`)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

register_activation_hook(__FILE__, 'grohsfabian_update_table');


/* Include all the required files */
require_once $plugin_path . 'core/init.php';

require_once $plugin_path . 'core/widget.php';

require_once $plugin_path . 'core/shortcodes.php';

require_once $plugin_path . 'core/settings.php';



?>