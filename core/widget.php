<?php
/* Create the Widget */
class grohsfabian_game_server_status extends WP_Widget {

	function __construct() {

		parent::__construct(
			'grohsfabian_game_server_status',
			__('Game Server Status', 'game-server-status'),
			array('description' => __( 'Display live statistics for your game server', 'game-server-status' ))
		);

	}


	/* Displaying the form in the admin panel */
	function form($instance) {
		global $wpdb, $table_name;

		$defaults = array(
			'title'						=> '',
			'server_id'					=> ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'game-server-status'); ?></label>	
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('server_id'); ?>"><?php _e('Server', 'game-server-status'); ?></label>	
			<select id="<?php echo $this->get_field_id('server_id'); ?>" name="<?php echo $this->get_field_name('server_id'); ?>" class="widefat">
			<?php
			$servers = $wpdb->get_results("SELECT `server_id`, `server_name` FROM `{$table_name}` ORDER BY `server_id` DESC");

			foreach($servers as $server) {
				echo '<option value="' . $server->server_id . '" ' . (($instance['server_id'] == $server->server_id) ? 'selected' : '') . '>' . $server->server_name . '</option>';
			}
			?>
			</select>
		</p>

		<?php
	}


	/* The update function */
	function update($new_instance, $old_instance) {

		return array_map('esc_attr', $new_instance);

	}


	/* Displaying the widget */
	function widget($args, $instance) {
		global $wpdb, $table_name, $plugin_path;
		extract($args);

		include $plugin_path . 'core/templates/widget.php';

	}
}

/* Register the widget */
add_action('widgets_init', function() {
	register_widget('grohsfabian_game_server_status');
});


?>