<?php
/**
 * Settings and helpers for the At Every Turn Bible Study page.
 *
 * @package brendon-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings for the Bible study page.
 *
 * @return array<string, string>
 */
function bb_aet_default_settings() {
	return array(
		'join_url'      => '',
		'join_label'    => __( 'Join the Study', 'brendon-core' ),
		'when'          => __( 'Coming soon', 'brendon-core' ),
		'where'         => __( 'Google Meet', 'brendon-core' ),
		'platform_note' => __( 'No cost · No account needed · Google Meet', 'brendon-core' ),
	);
}

/**
 * Return a saved Bible study setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function bb_aet_setting( $key ) {
	$defaults = bb_aet_default_settings();
	$value    = get_option( 'bb_aet_' . $key, $defaults[ $key ] ?? '' );

	return is_string( $value ) ? $value : '';
}

/**
 * Register settings used by the At Every Turn admin page.
 *
 * @return void
 */
function bb_aet_register_settings() {
	register_setting(
		'bb_aet_settings',
		'bb_aet_join_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);

	foreach ( array( 'join_label', 'when', 'where', 'platform_note' ) as $key ) {
		register_setting(
			'bb_aet_settings',
			'bb_aet_' . $key,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => bb_aet_default_settings()[ $key ],
			)
		);
	}
}
add_action( 'admin_init', 'bb_aet_register_settings' );

/**
 * Add a small admin settings screen for the Bible study page.
 *
 * @return void
 */
function bb_aet_add_settings_page() {
	add_options_page(
		__( 'At Every Turn', 'brendon-core' ),
		__( 'At Every Turn', 'brendon-core' ),
		'manage_options',
		'bb-at-every-turn',
		'bb_aet_render_settings_page'
	);
}
add_action( 'admin_menu', 'bb_aet_add_settings_page' );

/**
 * Render a text input field.
 *
 * @param string $key   Setting key without the bb_aet_ prefix.
 * @param string $label Field label.
 * @param string $type  Input type.
 * @return void
 */
function bb_aet_render_field( $key, $label, $type = 'text' ) {
	$option_name = 'bb_aet_' . $key;
	$value       = bb_aet_setting( $key );
	?>
	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $option_name ); ?>"><?php echo esc_html( $label ); ?></label>
		</th>
		<td>
			<input
				name="<?php echo esc_attr( $option_name ); ?>"
				id="<?php echo esc_attr( $option_name ); ?>"
				type="<?php echo esc_attr( $type ); ?>"
				class="regular-text"
				value="<?php echo esc_attr( $value ); ?>"
			/>
		</td>
	</tr>
	<?php
}

/**
 * Render the Bible study settings page.
 *
 * @return void
 */
function bb_aet_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'At Every Turn', 'brendon-core' ); ?></h1>
		<p><?php esc_html_e( 'Update the public Bible study meeting details without editing code.', 'brendon-core' ); ?></p>

		<form action="options.php" method="post">
			<?php settings_fields( 'bb_aet_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php
				bb_aet_render_field( 'join_url', __( 'Join link', 'brendon-core' ), 'url' );
				bb_aet_render_field( 'join_label', __( 'Button text', 'brendon-core' ) );
				bb_aet_render_field( 'when', __( 'When', 'brendon-core' ) );
				bb_aet_render_field( 'where', __( 'Where', 'brendon-core' ) );
				bb_aet_render_field( 'platform_note', __( 'Small note under button', 'brendon-core' ) );
				?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
