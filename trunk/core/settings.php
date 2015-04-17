<?php

/* Default settings page */
function grohsfabian_game_servers() {
	global $wpdb, $plugin_path, $table_name;

	include $plugin_path . 'core/templates/game_servers.php';
}

function grohsfabian_add_game_servers() {
	global $wpdb, $plugin_path, $table_name;
	
	include $plugin_path . 'core/templates/add_game_servers.php';
}

function grohsfabian_game_servers_documentation() {
	global $plugin_path;
	
	include $plugin_path . 'core/templates/game_servers_documentation.php';
}

/* Register the plugin settings page */
add_action('admin_menu', function() {

	add_menu_page(__('Game Servers', 'game-server-status'), __('Game Servers', 'game-server-status'), 'manage_options', 'grohsfabian-game-servers', 'grohsfabian_game_servers');
	add_submenu_page('grohsfabian-game-servers', __('Add Server', 'game-server-status'), __('Add Server', 'game-server-status'), 'manage_options', 'grohsfabian-add-game-servers', 'grohsfabian_add_game_servers');
	add_submenu_page('grohsfabian-game-servers', __('Documentation', 'game-server-status'), __('Documentation', 'game-server-status'), 'manage_options', 'grohsfabian-game-servers-documentation', 'grohsfabian_game_servers_documentation');
});
?>