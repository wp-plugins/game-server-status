<?php
defined('ABSPATH') or die();

/* Internationalization */
load_plugin_textdomain('game-server-status', false, dirname(plugin_basename(__FILE__)) . '/languages/');

/* Create some variables */
$errors = array();
$table_name = $wpdb->prefix . 'game_servers';

/* Load needed classes */
require_once $plugin_path . 'core/classes/Query.php';

/* Custom error display function */
function display_errors($errors) {

	if(!is_array($errors)) $errors = array($errors);

	foreach($errors as $error) {
		echo '<div class="error"><p>' . $error . '</p></div>';
	}

}

?>