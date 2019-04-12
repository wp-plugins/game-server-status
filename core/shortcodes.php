<?php

function grohsfabian_game_servers_short($atts, $message = null)	{
 	global $wpdb, $table_name, $plugin_path;

 	$data = shortcode_atts(
				array('server_id' => '1', 'display' => 'all'), 
				$atts
			);

 	$content = null;

	/* Get the server data */
	$server = $wpdb->get_row("SELECT * FROM `{$table_name}` WHERE `server_id` = {$data['server_id']}");
	
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
		switch($data['display']) {
			case 'server_name'		: $content .= $server->server_name; break;
			case 'server_status'	: $content .= (($status) ? __('Online', 'game-server-status') : __('Offline', 'game-server-status')); break;
			case 'server_address'	: $content .= $server->server_ip . ':' . $server->server_connection_port; break;
			case 'server_online_players'			: $content .= $server_info['general']['online_players']['value']; break;
			case 'server_maximum_online_players'	: $content .= $server_info['general']['maximum_online_players']['value']; break;
			
			default:
				$content .= '<strong>' . __('Name:', 'game-server-status') . '</strong> ' . $server->server_name . '<br />'; 
				$content .= '<strong>' . __('Status:', 'game-server-status') . '</strong> ' . (($status) ? __('Online', 'game-server-status') : __('Offline', 'game-server-status')) . '<br />'; 
				if($status) {
					$content .= '<strong>' . __('Server:', 'game-server-status') . '</strong> ' . $server->server_ip . ':' . $server->server_connection_port . '<br />'; 
					$content .= '<strong>' . __('Players:', 'game-server-status') . '</strong> ' . $server_info['general']['online_players'] . '/' . $server_info['general']['maximum_online_players'] . '<br />'; 
				}
			break;
		}

	} else {

		$content .= __('Your selected server does not exist anymore !', 'game-server-status');

	}

	return $content;
 
}

/* Register the ShortCodes */
add_shortcode('game-servers', 'grohsfabian_game_servers_short');

?>
