<?php
/**
 * Forminator-powered prayer requests and prayer wall.
 *
 * @package brendon-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BB_PRAYER_REQUEST_POST_TYPE', 'prayer_request' );

function brendon_core_prayer_wall_register_post_type() {
	register_post_type(
		BB_PRAYER_REQUEST_POST_TYPE,
		array(
			'labels'              => array(
				'name'               => esc_html__( 'Prayer Requests', 'brendon-core' ),
				'singular_name'      => esc_html__( 'Prayer Request', 'brendon-core' ),
				'add_new_item'       => esc_html__( 'Add New Prayer Request', 'brendon-core' ),
				'edit_item'          => esc_html__( 'Edit Prayer Request', 'brendon-core' ),
				'view_item'          => esc_html__( 'View Prayer Request', 'brendon-core' ),
				'search_items'       => esc_html__( 'Search Prayer Requests', 'brendon-core' ),
				'not_found'          => esc_html__( 'No prayer requests found.', 'brendon-core' ),
				'not_found_in_trash' => esc_html__( 'No prayer requests found in Trash.', 'brendon-core' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-heart',
			'menu_position'       => 22,
			'supports'            => array( 'title', 'editor', 'author' ),
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'brendon_core_prayer_wall_register_post_type' );

function brendon_core_prayer_wall_activate() {
	brendon_core_prayer_wall_register_post_type();
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'brendon_core_prayer_wall_activate' );

function brendon_core_prayer_wall_form_id() {
	/*
	 * Set BB_PRAYER_REQUEST_FORM_ID in wp-config.php, or use the
	 * brendon_core_prayer_wall_form_id filter, to bind this to one Forminator form.
	 * Without that, only Forminator forms with "prayer" in the title are handled.
	 */
	$form_id = defined( 'BB_PRAYER_REQUEST_FORM_ID' ) ? (int) BB_PRAYER_REQUEST_FORM_ID : 0;

	return (int) apply_filters( 'brendon_core_prayer_wall_form_id', $form_id );
}

function brendon_core_prayer_wall_field_map() {
	/*
	 * Override these with brendon_core_prayer_wall_field_map if the Forminator
	 * field slugs differ from the defaults below.
	 */
	$defaults = array(
		'request'   => array( 'prayer_request', 'request', 'request_text', 'prayer', 'message', 'textarea-1' ),
		'anonymous' => array( 'anonymous', 'is_anonymous', 'post_anonymously', 'checkbox-1' ),
		'public'    => array( 'public', 'is_public', 'share_publicly', 'prayer_wall', 'checkbox-2' ),
		'praise'    => array( 'praise', 'praise_report', 'is_praise_report', 'testimony', 'checkbox-3' ),
	);

	return apply_filters( 'brendon_core_prayer_wall_field_map', $defaults );
}

function brendon_core_prayer_wall_field_text( $field ) {
	if ( ! is_array( $field ) ) {
		return strtolower( sanitize_key( (string) $field ) );
	}

	$parts = array();
	foreach ( array( 'name', 'id', 'element_id', 'slug', 'label', 'field_label' ) as $key ) {
		if ( isset( $field[ $key ] ) && is_scalar( $field[ $key ] ) ) {
			$parts[] = (string) $field[ $key ];
		}
	}

	return strtolower( implode( ' ', $parts ) );
}

function brendon_core_prayer_wall_field_value( $fields, $keys, $hints = array() ) {
	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$field_text = brendon_core_prayer_wall_field_text( $field );
		foreach ( (array) $keys as $key ) {
			$key = strtolower( (string) $key );
			if ( $key && false !== strpos( $field_text, $key ) ) {
				return $field['value'] ?? '';
			}
		}
	}

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$field_text = brendon_core_prayer_wall_field_text( $field );
		$matched    = true;
		foreach ( $hints as $hint ) {
			if ( false === strpos( $field_text, strtolower( $hint ) ) ) {
				$matched = false;
				break;
			}
		}

		if ( $matched ) {
			return $field['value'] ?? '';
		}
	}

	return '';
}

function brendon_core_prayer_wall_is_target_form( $form_id ) {
	$target_form_id = brendon_core_prayer_wall_form_id();
	if ( $target_form_id ) {
		return (int) $form_id === $target_form_id;
	}

	$form_title = get_the_title( (int) $form_id );

	return $form_title && false !== stripos( $form_title, 'prayer' );
}

function brendon_core_prayer_wall_member_page_url( $slug, $redirect_url = '' ) {
	$page = get_page_by_path( $slug );
	$url  = $page ? get_permalink( $page ) : '';

	if ( ! $url ) {
		return 'login' === $slug ? wp_login_url( $redirect_url ) : wp_registration_url();
	}

	if ( $redirect_url ) {
		$url = add_query_arg( 'redirect_to', rawurlencode( $redirect_url ), $url );
	}

	return $url;
}

function brendon_core_prayer_wall_login_url( $redirect_url = '' ) {
	return brendon_core_prayer_wall_member_page_url( 'login', $redirect_url );
}

function brendon_core_prayer_wall_register_url( $redirect_url = '' ) {
	return brendon_core_prayer_wall_member_page_url( 'register', $redirect_url );
}

function brendon_core_prayer_wall_string_value( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( "\n", array_map( 'strval', $value ) );
	}

	return (string) $value;
}

function brendon_core_prayer_wall_bool( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( ' ', array_map( 'strval', $value ) );
	}

	$value = strtolower( trim( (string) $value ) );

	if ( '' === $value ) {
		return false;
	}

	return ! in_array( $value, array( '0', 'false', 'no', 'n', 'off', 'unchecked' ), true );
}

function brendon_core_prayer_wall_request_title( $request_text, $is_praise ) {
	$prefix = $is_praise ? __( 'Praise Report', 'brendon-core' ) : __( 'Prayer Request', 'brendon-core' );
	$words  = wp_trim_words( wp_strip_all_tags( $request_text ), 8, '' );

	return trim( $prefix . ( $words ? ': ' . $words : '' ) );
}

function brendon_core_prayer_wall_handle_forminator_submission( $entry, $form_id, $field_data_array ) {
	if ( ! brendon_core_prayer_wall_is_target_form( $form_id ) ) {
		return;
	}

	if ( ! is_user_logged_in() ) {
		return;
	}

	$field_map    = brendon_core_prayer_wall_field_map();
	$request_text = brendon_core_prayer_wall_field_value( $field_data_array, $field_map['request'] ?? array(), array( 'prayer' ) );
	$request_text = trim( wp_kses_post( brendon_core_prayer_wall_string_value( $request_text ) ) );

	if ( '' === wp_strip_all_tags( $request_text ) ) {
		return;
	}

	$is_anonymous = brendon_core_prayer_wall_bool( brendon_core_prayer_wall_field_value( $field_data_array, $field_map['anonymous'] ?? array(), array( 'anonymous' ) ) );
	$is_public    = apply_filters( 'brendon_core_prayer_wall_default_public', true, $form_id, $field_data_array );
	$is_praise    = brendon_core_prayer_wall_bool( brendon_core_prayer_wall_field_value( $field_data_array, $field_map['praise'] ?? array(), array( 'praise' ) ) );
	$post_author  = get_current_user_id();

	$post_id = wp_insert_post(
		array(
			'post_type'    => BB_PRAYER_REQUEST_POST_TYPE,
			'post_status'  => 'publish',
			'post_title'   => brendon_core_prayer_wall_request_title( $request_text, $is_praise ),
			'post_content' => $request_text,
			'post_author'  => $post_author,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_bb_prayer_request_text', $request_text );
	update_post_meta( $post_id, '_bb_prayer_is_anonymous', $is_anonymous ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_is_public', $is_public ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_is_praise', $is_praise ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_count', 0 );
	update_post_meta( $post_id, '_bb_prayer_form_id', (int) $form_id );
}
add_action( 'forminator_custom_form_submit_before_set_fields', 'brendon_core_prayer_wall_handle_forminator_submission', 10, 3 );

function brendon_core_prayer_wall_author_name( $post_id ) {
	if ( '1' === get_post_meta( $post_id, '_bb_prayer_is_anonymous', true ) ) {
		return __( 'Anonymous', 'brendon-core' );
	}

	$author_id = (int) get_post_field( 'post_author', $post_id );
	if ( $author_id ) {
		$user = get_userdata( $author_id );
		if ( $user ) {
			return $user->display_name ?: $user->user_login;
		}
	}

	return __( 'Community member', 'brendon-core' );
}

function brendon_core_prayer_wall_user_has_prayed( $post_id, $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	$prayed = get_post_meta( $post_id, '_bb_prayer_user_ids', true );
	$prayed = is_array( $prayed ) ? array_map( 'intval', $prayed ) : array();

	return in_array( $user_id, $prayed, true );
}

function brendon_core_prayer_wall_ajax_prayed() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to mark that you prayed.', 'brendon-core' ) ), 401 );
	}

	check_ajax_referer( 'bb_prayer_wall_prayed', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	if ( ! $post_id || BB_PRAYER_REQUEST_POST_TYPE !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Prayer request not found.', 'brendon-core' ) ), 404 );
	}

	$user_id = get_current_user_id();
	$prayed  = get_post_meta( $post_id, '_bb_prayer_user_ids', true );
	$prayed  = is_array( $prayed ) ? array_values( array_unique( array_map( 'intval', $prayed ) ) ) : array();

	if ( ! in_array( $user_id, $prayed, true ) ) {
		$prayed[] = $user_id;
		update_post_meta( $post_id, '_bb_prayer_user_ids', $prayed );
		update_post_meta( $post_id, '_bb_prayer_count', count( $prayed ) );
	}

	wp_send_json_success(
		array(
			'count'   => count( $prayed ),
			'message' => __( 'Thank you for praying.', 'brendon-core' ),
		)
	);
}
add_action( 'wp_ajax_bb_prayer_wall_prayed', 'brendon_core_prayer_wall_ajax_prayed' );

function brendon_core_prayer_wall_assets() {
	if ( ! is_page( 'prayer-wall' ) && ! is_page_template( 'page-prayer-wall.php' ) ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/css/prayer-wall.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'brendon-core-prayer-wall',
			get_template_directory_uri() . '/assets/css/prayer-wall.css',
			array( 'brendon-core-brand-theme' ),
			filemtime( $css_path )
		);
	}

	$js_path = get_template_directory() . '/assets/js/prayer-wall.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'brendon-core-prayer-wall',
			get_template_directory_uri() . '/assets/js/prayer-wall.js',
			array(),
			filemtime( $js_path ),
			true
		);

		wp_localize_script(
			'brendon-core-prayer-wall',
			'bbPrayerWall',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'bb_prayer_wall_prayed' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'brendon_core_prayer_wall_assets', 30 );

function brendon_core_prayer_wall_meta_box() {
	add_meta_box(
		'brendon_core_prayer_wall_details',
		esc_html__( 'Prayer Details', 'brendon-core' ),
		'brendon_core_prayer_wall_meta_box_callback',
		BB_PRAYER_REQUEST_POST_TYPE,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'brendon_core_prayer_wall_meta_box' );

function brendon_core_prayer_wall_meta_box_callback( $post ) {
	wp_nonce_field( 'brendon_core_prayer_wall_save_meta', 'brendon_core_prayer_wall_nonce' );
	$count = (int) get_post_meta( $post->ID, '_bb_prayer_count', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="bb_prayer_is_anonymous" value="1" <?php checked( get_post_meta( $post->ID, '_bb_prayer_is_anonymous', true ), '1' ); ?> />
			<?php esc_html_e( 'Anonymous', 'brendon-core' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="bb_prayer_is_public" value="1" <?php checked( get_post_meta( $post->ID, '_bb_prayer_is_public', true ), '1' ); ?> />
			<?php esc_html_e( 'Show on prayer wall', 'brendon-core' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="bb_prayer_is_praise" value="1" <?php checked( get_post_meta( $post->ID, '_bb_prayer_is_praise', true ), '1' ); ?> />
			<?php esc_html_e( 'Praise report', 'brendon-core' ); ?>
		</label>
	</p>
	<p>
		<strong><?php esc_html_e( 'Prayer count:', 'brendon-core' ); ?></strong>
		<?php echo esc_html( number_format_i18n( $count ) ); ?>
	</p>
	<?php
}

function brendon_core_prayer_wall_save_meta( $post_id ) {
	if ( ! isset( $_POST['brendon_core_prayer_wall_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brendon_core_prayer_wall_nonce'] ) ), 'brendon_core_prayer_wall_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_bb_prayer_is_anonymous', isset( $_POST['bb_prayer_is_anonymous'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_is_public', isset( $_POST['bb_prayer_is_public'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_is_praise', isset( $_POST['bb_prayer_is_praise'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_bb_prayer_request_text', wp_kses_post( get_post_field( 'post_content', $post_id ) ) );
}
add_action( 'save_post_' . BB_PRAYER_REQUEST_POST_TYPE, 'brendon_core_prayer_wall_save_meta' );
