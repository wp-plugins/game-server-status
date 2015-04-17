<?php

if(!defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;

$table_name = $wpdb->prefix . 'game_servers';

if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
	$wpdb->query("DROP TABLE `{$table_name}`;");
}

?>