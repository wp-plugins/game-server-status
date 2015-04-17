<?php

/* Establish some needed variables */
$title = apply_filters('widget_title', $instance['title']);
$server = $wpdb->get_row("SELECT * FROM `{$table_name}` WHERE `server_id` = {$instance['server_id']}");


echo $before_widget;

echo $before_title . $title . $after_title;

/* Make sure the server exists, else display a notice message */
if($server) {

	$cache_file = $plugin_path . 'cache/' . $server->server_id . '.js';

	/* Determine what source we need to use ( cached or check again ) */
	if(file_exists($cache_file) && filemtime($cache_file) > time() - $server->cache_time) {

		$server_info = json_decode(file_get_contents($cache_file), true);
		$status = (is_null($server_info)) ? false : true;
		
	} else {

		/* Try to query the server and get the details */
		$query = new Query($server->server_ip, $server->server_query_port, $server->game_engine);
		$status = $query->status;
		$server_info = $query->query();

		/* Save the data as JSON ( if server is offline write the file as empty ) */
		$file = fopen($cache_file, 'w');
		if($query->status) fwrite($file, json_encode($server_info));
		fclose($file);

	}


	/* Display the widget content */
	echo '<strong>' . __('Name:', 'game-server-status') . '</strong> ' . $server->server_name . '<br />'; 
	echo '<strong>' . __('Status:', 'game-server-status') . '</strong> ' . (($status) ? __('Online', 'game-server-status') : __('Offline', 'game-server-status')) . '<br />'; 
	if($status) {
		echo '<strong>' . __('Server:', 'game-server-status') . '</strong> ' . $server->server_ip . ':' . $server->server_connection_port . '<br />'; 
		echo '<strong>' . __('Players:', 'game-server-status') . '</strong> ' . (($server->game_engine == 'source') ? $server_info['general']['online_players']['value'] : $server_info['general']['online_players']) . '/' . (($server->game_engine == 'source') ? $server_info['general']['maximum_online_players']['value'] : $server_info['general']['maximum_online_players']) . '<br />'; 
	}

} else {

	_e('Your selected server does not exist anymore !', 'game-server-status');

}

echo $after_widget;

?>