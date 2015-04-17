<?php

/* Check if the GET server id is passed */
if(!empty($_GET['server_id'])) {

	/* Security check */
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'delete-server')) {
		$errors[] = __('Your nonce did not verify !', 'game-server-status');
	}

	/* If there are no errors then proceed */
	if(empty($errors)) {

		$wpdb->delete(
			$table_name,
			array('server_id' => $_GET['server_id'])
		);

		/* Display a success message */
		echo '<div class="updated"><p>' . __('The server was successfully deleted from the database !', 'game-server-status') . '</p></div>';

	} else {
		display_errors($errors);
	}
}

/* Get the servers from the database */
$servers = $wpdb->get_results("SELECT * FROM `{$table_name}` ORDER BY `server_id` DESC ");


?>

<div class="wrap">
	<h2>
		<?php _e('Game Servers List', 'game-server-status'); ?>
		<a href="<?php echo admin_url() . 'admin.php?page=grohsfabian-add-game-servers'; ?>" class="add-new-h2"><?php _e('Add New', 'game-server-status'); ?></a>
	</h2>


<?php if($servers) { ?>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('Server ID', 'game-server-status'); ?></th>
				<th><?php _e('Name', 'game-server-status'); ?></th>
				<th><?php _e('Server IP', 'game-server-status'); ?></th>
				<th><?php _e('Connection Port', 'game-server-status'); ?></th>
				<th><?php _e('Query Port', 'game-server-status'); ?></th>
				<th><?php _e('Cache Time', 'game-server-status'); ?></th>
				<th><?php _e('Game Engine', 'game-server-status'); ?></th>
				<th><?php _e('Actions', 'game-server-status'); ?></th>
			</tr>
		</thead>

		<tbody>

		<?php foreach($servers as $server) { ?>
			<tr>
				<td><?php echo $server->server_id; ?></td>
				<td><?php echo $server->server_name; ?></td>
				<td><?php echo $server->server_ip; ?></td>
				<td><?php echo $server->server_connection_port; ?></td>
				<td><?php echo $server->server_query_port; ?></td>
				<td><?php echo $server->cache_time; ?></td>
				<td><?php echo $server->game_engine; ?></td>
				<td><a href="<?php echo admin_url() . 'admin.php?page=grohsfabian-add-game-servers&server_id=' . $server->server_id; ?>"><?php _e('Edit', 'game-server-status'); ?></a> | <a href="<?php echo wp_nonce_url(admin_url() . 'admin.php?page=grohsfabian-game-servers&server_id=' . $server->server_id, 'delete-server'); ?>" onclick="confirm('<?php _e('Are you sure you want to do this ?', 'game-server-status'); ?>');"><?php _e('Delete', 'game-server-status'); ?></a></td>
			</tr>	
		<?php } ?>

		</tbody>
	</table>
<?php } else echo '<div class="error"><p>' . __('There are no servers added in the database at the moment !', 'game-server-status') . '</p></div>'; ?>

     
</div>