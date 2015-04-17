<?php
/* Define some variables */
$server_name = $server_ip = $server_connection_port = $server_query_port = $cache_time = $game_engine = null;

/* Check if the GET server id is passed */
if(!empty($_GET['server_id'])) {

	/* Try to get the server data */
	$server = $wpdb->get_row("SELECT * FROM `{$table_name}` WHERE `server_id` = {$_GET['server_id']}", ARRAY_A);

	/* If the server exists, extract the data, if not then delete the GET parameter */
	if($server) {
		extract($server);
	} else {
		unset($_GET['server_id']);
	}

}


if(!empty($_POST)) {

	/* Possible error checks */
	$fields = array('server_name', 'server_ip', 'server_connection_port', 'server_query_port', 'cache_time', 'game_engine');

	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true) {
			$errors[] = __('All the fields are required !', 'game-server-status');
			break 1;
		}
	}

	/* Try to query the server and see if its online or not */
	$query = new Query($_POST['server_ip'], $_POST['server_query_port'], $_POST['game_engine']);
	$info  = $query->query();

	if(!$info && $query->status) {
		$errors[] = __('The server is currently online but it did not respond to the query ! Please read the documentation !', 'game-server-status');
	} else 
	if(!$info && !$query->status) {
		$errors[] = __('The server you submitted is currently offline !', 'game-server-status');
	}
	
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'add-server')) {
		$errors[] = __('Your nonce did not verify !', 'game-server-status');
	}

	/* If there are no errors then proceed */
	if(empty($errors)) {

		if(!empty($_GET['server_id'])) {

			$wpdb->update(
				$table_name,
				array(
					'server_name' => $_POST['server_name'],
					'server_ip' => $_POST['server_ip'],
					'server_connection_port' => $_POST['server_connection_port'],
					'server_query_port' => $_POST['server_query_port'],
					'cache_time' => $_POST['cache_time'],
					'game_engine' => $_POST['game_engine']
				),
				array(
					'server_id' => $_GET['server_id']
				)
			);

			/* Refresh the current variables */
			$server = $wpdb->get_row("SELECT * FROM `{$table_name}` WHERE `server_id` = {$_GET['server_id']}", ARRAY_A);
			extract($server);

			/* Display a success message */
			echo '<div class="updated"><p>' . __('The server was successfully updated !', 'game-server-status') . '</p></div>';

		} else {

			$wpdb->insert(
				$table_name,
				array(
					'server_name' => $_POST['server_name'],
					'server_ip' => $_POST['server_ip'],
					'server_connection_port' => $_POST['server_connection_port'],
					'server_query_port' => $_POST['server_query_port'],
					'cache_time' => $_POST['cache_time'],
					'game_engine' => $_POST['game_engine']
				)
			);

			/* Display a success message */
			echo '<div class="updated"><p>' . __('The server was successfully added to the database !', 'game-server-status') . '</p></div>';

		}

	} else {
		display_errors($errors);
	}
}
?>

<div class="wrap">
	<h2><?php echo (!empty($_GET['server_id'])) ? __('Edit Server', 'game-server-status') : __('Add Game Server', 'game-server-status'); ?></h2>

	<form method="post" action="">
		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Server Name', 'game-server-status'); ?></label>
					<td>
						<input type="text" name="server_name" class="regular-text" value="<?php echo $server_name; ?>" />
					</td>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Server IP', 'game-server-status'); ?></label>
					<td>
						<input type="text" name="server_ip" class="regular-text" value="<?php echo $server_ip; ?>" />
					</td>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Server Connection Port', 'game-server-status'); ?></label>
					<td>
						<input type="text" name="server_connection_port" class="regular-text" value="<?php echo $server_connection_port; ?>" />
					</td>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Server Query Port ( Usually the same as the connection port )', 'game-server-status'); ?></label>
					<td>
						<input type="text" name="server_query_port" class="regular-text" value="<?php echo $server_query_port; ?>" />
					</td>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Query again after X Seconds', 'game-server-status'); ?></label>
					<td>
						<input type="text" name="cache_time" class="regular-text" value="<?php echo $cache_time; ?>" />
					</td>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for=""><?php _e('Game Engine', 'game-server-status'); ?></label>
					<td>
						<select name="game_engine" class="regular-text">
							<option value="minecraft" <?php echo ($game_engine == 'minecraft') ? 'selected' : null; ?> ><?php _e('Minecraft', 'game-server-status'); ?></option>
							<option value="samp" <?php echo ($game_engine == 'samp') ? 'selected' : null; ?> ><?php _e('SA:MP', 'game-server-status'); ?></option>
							<option value="source" <?php echo ($game_engine == 'source') ? 'selected' : null; ?> ><?php _e('Source', 'game-server-status'); ?></option>
						</select>
					</td>
				</th>
			</tr>

		</table>

		<?php wp_nonce_field('add-server'); ?>
		<?php submit_button(__('Submit', 'game-server-status')); ?>
	</form>

</div>