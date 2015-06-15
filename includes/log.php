<?php

function orbis_log( $message ) {
	global $wpdb;

	$data = array(
		'created'    => current_time( 'mysql' ),
		'wp_user_id' => get_current_user_id(),
		'message'    => $message,
	);

	$format = array(
		'created' => '%s',
		'user_id' => '%d',
		'message' => '%s',
	);

	$result = $wpdb->insert( $wpdb->orbis_log, $data, $format );

	return $result;
}

function orbis_get_logs() {
	global $wpdb;

	$query = "
		SELECT
			created,
			message
		FROM
			$wpdb->orbis_log
		ORDER BY
			created DESC
		LIMIT
			0, 10
	";

	$logs = $wpdb->get_results( $query );

	return $logs;
}

/**
 * Log widget
 */
class Orbis_Log_Widget extends WP_Widget {
	/**
	 * Register this widget
	 */
	public static function register() {
		register_widget( __CLASS__ );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Constructs and initializes this widget
	 */
	public function Orbis_Log_Widget() {
		parent::WP_Widget( 'orbis-log', __( 'Orbis Log', 'orbis' ) );
	}

	function widget( $args, $instance ) {
		$defaults = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		// @codingStandardsIgnoreStart
		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}

		$logs = orbis_get_logs();

		?>
		<div class="content">
			<ul class="no-disc">
				<?php foreach ( $logs as $log ) : ?>

					<li>
						<?php /* <span class="label label-success">Werk</span> */ ?>
						<span><?php echo mysql2date( 'H:i', $log->created ); ?></span>
						<?php echo $log->message; ?>
						<?php /* <span>?</span> */ ?>
					</li>

				<?php endforeach; ?>
			</ul>
		</div>
		<?php

		echo $args['after_widget'];
		// @codingStandardsIgnoreEnd
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];

		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php _e( 'Title:', 'orbis' ); ?>
			</label>

			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php
	}
}

function orbis_log_widget_init() {
	register_widget( 'Orbis_Log_Widget' );
}

add_action( 'widgets_init', 'orbis_log_widget_init' );

function orbis_log_save_post( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Revision
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Ok
	$current_user = wp_get_current_user();

	if ( 0 === $current_user->ID ) {
		$name = __( 'Anonymous', 'orbis' );
	} else {
		$name = $current_user->display_name;
	}

	if ( 'publish' !== $post->post_status ) {
		$url = get_edit_post_link( $post_id );
	} else {
		$url = get_permalink( $post_id );
	}

	$message = sprintf(
		__( '%s updated the "%s" post.', 'orbis' ),
		$name,
		sprintf(
			'<a href="%s">%s</a>',
			$url,
			$post->post_title
		)
	);

	orbis_log( $message );
}

add_action( 'save_post', 'orbis_log_save_post', 10, 2 );
