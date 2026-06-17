<?php
/**
 * Bible Study Live sessions, settings, assets, and chat.
 *
 * @package brendon-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BB_BIBLE_STUDY_DB_VERSION', '1.0.1' );
define( 'BB_BIBLE_STUDY_POST_TYPE', 'bb_study_session' );
define( 'BB_BIBLE_STUDY_MANAGE_CAP', 'manage_bible_study_chat' );
define( 'BB_BIBLE_STUDY_MODERATE_CAP', 'moderate_bible_study_chat' );

function brendon_core_bible_study_messages_table() {
	global $wpdb;

	return $wpdb->prefix . 'bb_bible_study_messages';
}

function brendon_core_bible_study_mutes_table() {
	global $wpdb;

	return $wpdb->prefix . 'bb_bible_study_mutes';
}

function brendon_core_bible_study_install() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();
	$messages_table  = brendon_core_bible_study_messages_table();
	$mutes_table     = brendon_core_bible_study_mutes_table();

	dbDelta(
		"CREATE TABLE {$messages_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			session_id bigint(20) unsigned NOT NULL,
			user_id bigint(20) unsigned NOT NULL,
			message text NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'visible',
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY session_status_id (session_id,status,id),
			KEY user_id (user_id)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE {$mutes_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			session_id bigint(20) unsigned NOT NULL,
			user_id bigint(20) unsigned NOT NULL,
			muted_by bigint(20) unsigned NOT NULL,
			muted_until datetime NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY session_user (session_id,user_id),
			KEY muted_until (muted_until)
		) {$charset_collate};"
	);

	$admin = get_role( 'administrator' );
	if ( $admin ) {
		$admin->add_cap( BB_BIBLE_STUDY_MANAGE_CAP );
		$admin->add_cap( BB_BIBLE_STUDY_MODERATE_CAP );
	}

	update_option( 'bb_bible_study_db_version', BB_BIBLE_STUDY_DB_VERSION );

	if ( ! post_type_exists( BB_BIBLE_STUDY_POST_TYPE ) ) {
		brendon_core_bible_study_register_post_type();
	}

	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'brendon_core_bible_study_install' );

function brendon_core_bible_study_maybe_install() {
	if ( get_option( 'bb_bible_study_db_version' ) !== BB_BIBLE_STUDY_DB_VERSION ) {
		brendon_core_bible_study_install();
	}
}
add_action( 'init', 'brendon_core_bible_study_maybe_install', 20 );
add_action( 'admin_init', 'brendon_core_bible_study_maybe_install' );

function brendon_core_bible_study_register_post_type() {
	register_post_type(
		BB_BIBLE_STUDY_POST_TYPE,
		array(
			'labels'       => array(
				'name'          => esc_html__( 'Bible Study Sessions', 'brendon-core' ),
				'singular_name' => esc_html__( 'Bible Study Session', 'brendon-core' ),
				'add_new_item'  => esc_html__( 'Add New Bible Study Session', 'brendon-core' ),
				'edit_item'     => esc_html__( 'Edit Bible Study Session', 'brendon-core' ),
				'view_item'     => esc_html__( 'View Bible Study Session', 'brendon-core' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-book-alt',
			'menu_position' => 21,
			'rewrite'      => array(
				'slug'       => 'bible-study/session',
				'with_front' => false,
			),
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'brendon_core_bible_study_register_post_type' );

function brendon_core_bible_study_default_settings() {
	return array(
		'youtube_api_key'      => '',
		'youtube_channel_id'   => '',
		'fallback_playlist_id' => '',
	);
}

function brendon_core_bible_study_youtube_channel_id( $value ) {
	$value = trim( sanitize_text_field( $value ) );

	if ( '' === $value ) {
		return '';
	}

	$parts = wp_parse_url( $value );
	if ( ! empty( $parts['path'] ) ) {
		$path = trim( $parts['path'], '/' );

		if ( 0 === strpos( $path, 'channel/' ) ) {
			return sanitize_text_field( basename( $path ) );
		}
	}

	return $value;
}

function brendon_core_bible_study_youtube_playlist_id( $value ) {
	$value = trim( sanitize_text_field( $value ) );

	if ( '' === $value ) {
		return '';
	}

	$parts = wp_parse_url( $value );
	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );

		if ( ! empty( $query['list'] ) ) {
			return sanitize_text_field( $query['list'] );
		}
	}

	if ( ! empty( $parts['path'] ) ) {
		$path = trim( $parts['path'], '/' );

		if ( 0 === strpos( $path, 'playlist/' ) ) {
			return sanitize_text_field( basename( $path ) );
		}
	}

	return $value;
}

function brendon_core_bible_study_youtube_video_id( $value ) {
	$value = trim( sanitize_text_field( $value ) );

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $value ) ) {
		return $value;
	}

	$parts = wp_parse_url( $value );
	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );

		if ( ! empty( $query['v'] ) && preg_match( '/^[A-Za-z0-9_-]{11}$/', $query['v'] ) ) {
			return sanitize_text_field( $query['v'] );
		}
	}

	if ( ! empty( $parts['host'] ) && false !== strpos( $parts['host'], 'youtu.be' ) && ! empty( $parts['path'] ) ) {
		$id = trim( $parts['path'], '/' );

		return preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ? sanitize_text_field( $id ) : '';
	}

	if ( ! empty( $parts['path'] ) ) {
		$path = trim( $parts['path'], '/' );

		if ( preg_match( '#(?:embed|live|shorts|video)/([A-Za-z0-9_-]{11})#', $path, $matches ) ) {
			return sanitize_text_field( $matches[1] );
		}
	}

	return '';
}

function brendon_core_bible_study_sanitize_settings( $settings ) {
	$settings = is_array( $settings ) ? $settings : array();
	$defaults = brendon_core_bible_study_default_settings();

	return array(
		'youtube_api_key'      => sanitize_text_field( $settings['youtube_api_key'] ?? $defaults['youtube_api_key'] ),
		'youtube_channel_id'   => brendon_core_bible_study_youtube_channel_id( $settings['youtube_channel_id'] ?? $defaults['youtube_channel_id'] ),
		'fallback_playlist_id' => brendon_core_bible_study_youtube_playlist_id( $settings['fallback_playlist_id'] ?? $defaults['fallback_playlist_id'] ),
	);
}

function brendon_core_bible_study_settings() {
	$settings = get_option( 'brendon_core_bible_study_settings', array() );

	return wp_parse_args( is_array( $settings ) ? $settings : array(), brendon_core_bible_study_default_settings() );
}

function brendon_core_bible_study_setting( $key ) {
	$settings = brendon_core_bible_study_settings();

	return $settings[ $key ] ?? '';
}

function brendon_core_bible_study_member_page_url( $slug, $redirect_url = '' ) {
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

function brendon_core_bible_study_login_url( $redirect_url = '' ) {
	return brendon_core_bible_study_member_page_url( 'login', $redirect_url );
}

function brendon_core_bible_study_register_url( $redirect_url = '' ) {
	return brendon_core_bible_study_member_page_url( 'register', $redirect_url );
}

function brendon_core_bible_study_live_window_before() {
	return 30 * MINUTE_IN_SECONDS;
}

function brendon_core_bible_study_live_window_after() {
	return 3 * HOUR_IN_SECONDS;
}

function brendon_core_bible_study_forced_live_until( $session_id ) {
	return absint( get_post_meta( $session_id, '_bb_bible_study_force_live_until', true ) );
}

function brendon_core_bible_study_is_forced_live( $session_id ) {
	$forced_until = brendon_core_bible_study_forced_live_until( $session_id );

	return $forced_until && $forced_until > time();
}

function brendon_core_bible_study_schedule_timezone() {
	$timezone_string = get_option( 'timezone_string' );

	if ( $timezone_string ) {
		return wp_timezone();
	}

	return new DateTimeZone( 'America/Chicago' );
}

function brendon_core_bible_study_datetime_to_timestamp( $datetime ) {
	$datetime = trim( (string) $datetime );

	if ( '' === $datetime ) {
		return 0;
	}

	try {
		$date = new DateTimeImmutable( $datetime, brendon_core_bible_study_schedule_timezone() );
		return $date->getTimestamp();
	} catch ( Exception $error ) {
		return 0;
	}
}

function brendon_core_bible_study_format_session_time( $session_id ) {
	$meta      = brendon_core_bible_study_session_meta( $session_id );
	$timestamp = brendon_core_bible_study_datetime_to_timestamp( $meta['scheduled_start'] );

	if ( ! $timestamp ) {
		return '';
	}

	return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp, brendon_core_bible_study_schedule_timezone() );
}

function brendon_core_bible_study_datetime_input_value( $datetime ) {
	$datetime = trim( (string) $datetime );

	if ( '' === $datetime ) {
		return '';
	}

	if ( 10 === strlen( $datetime ) ) {
		return $datetime . 'T00:00';
	}

	return substr( str_replace( ' ', 'T', $datetime ), 0, 16 );
}

function brendon_core_bible_study_query_sessions() {
	return get_posts(
		array(
			'post_type'      => BB_BIBLE_STUDY_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_key'       => '_bb_bible_study_scheduled_start',
		)
	);
}

function brendon_core_bible_study_past_sessions( $limit = 6 ) {
	$now      = time();
	$sessions = array();

	foreach ( brendon_core_bible_study_query_sessions() as $session ) {
		$meta      = brendon_core_bible_study_session_meta( $session->ID );
		$timestamp = brendon_core_bible_study_datetime_to_timestamp( $meta['scheduled_start'] );

		if ( ! $timestamp || $timestamp > $now || empty( $meta['archive_video_id'] ) ) {
			continue;
		}

		$sessions[] = $session;
	}

	usort(
		$sessions,
		function ( $first, $second ) {
			$first_time  = brendon_core_bible_study_datetime_to_timestamp( brendon_core_bible_study_session_meta( $first->ID )['scheduled_start'] );
			$second_time = brendon_core_bible_study_datetime_to_timestamp( brendon_core_bible_study_session_meta( $second->ID )['scheduled_start'] );

			return $second_time <=> $first_time;
		}
	);

	return array_slice( $sessions, 0, absint( $limit ) ?: 6 );
}

function brendon_core_bible_study_get_scheduled_context() {
	$now      = time();
	$current  = null;
	$upcoming = null;

	foreach ( brendon_core_bible_study_query_sessions() as $session ) {
		$meta      = brendon_core_bible_study_session_meta( $session->ID );
		$timestamp = brendon_core_bible_study_datetime_to_timestamp( $meta['scheduled_start'] );

		if ( ! $timestamp ) {
			continue;
		}

		if ( $now >= ( $timestamp - brendon_core_bible_study_live_window_before() ) && $now <= ( $timestamp + brendon_core_bible_study_live_window_after() ) ) {
			$current = $session;
			break;
		}

		if ( $timestamp > $now && ( ! $upcoming || $timestamp < brendon_core_bible_study_datetime_to_timestamp( brendon_core_bible_study_session_meta( $upcoming->ID )['scheduled_start'] ) ) ) {
			$upcoming = $session;
		}
	}

	return array(
		'current'  => $current,
		'upcoming' => $upcoming,
	);
}

function brendon_core_bible_study_fallback_playlist_id() {
	$fallback_playlist_id = brendon_core_bible_study_youtube_playlist_id( brendon_core_bible_study_setting( 'fallback_playlist_id' ) );
	$youtube_channel_id   = brendon_core_bible_study_youtube_channel_id( brendon_core_bible_study_setting( 'youtube_channel_id' ) );

	if ( $fallback_playlist_id ) {
		return $fallback_playlist_id;
	}

	if ( 0 === strpos( $youtube_channel_id, 'UC' ) ) {
		return 'UU' . substr( $youtube_channel_id, 2 );
	}

	return '';
}

function brendon_core_bible_study_channel_live_embed_url() {
	$youtube_channel_id = brendon_core_bible_study_youtube_channel_id( brendon_core_bible_study_setting( 'youtube_channel_id' ) );

	if ( ! $youtube_channel_id ) {
		return '';
	}

	return add_query_arg(
		array(
			'channel'  => $youtube_channel_id,
			'autoplay' => 1,
			'rel'      => 0,
		),
		'https://www.youtube.com/embed/live_stream'
	);
}

function brendon_core_bible_study_youtube_status( $session_id = 0 ) {
	$youtube_api_key    = sanitize_text_field( brendon_core_bible_study_setting( 'youtube_api_key' ) );
	$youtube_channel_id = brendon_core_bible_study_youtube_channel_id( brendon_core_bible_study_setting( 'youtube_channel_id' ) );
	$meta               = $session_id ? brendon_core_bible_study_session_meta( $session_id ) : array();
	$scheduled_video_id = ! empty( $meta['scheduled_video_id'] ) ? $meta['scheduled_video_id'] : '';
	$cache_key          = 'bb_bible_study_youtube_v3_' . md5( $youtube_api_key . '|' . $youtube_channel_id . '|' . $scheduled_video_id );
	$status             = get_transient( $cache_key );

	if ( false !== $status ) {
		return $status;
	}

	$status = array(
		'is_live'  => false,
		'video_id' => '',
		'state'    => 'offline',
		'error'    => '',
	);

	if ( ! $youtube_api_key ) {
		$status['error'] = __( 'Missing YouTube API key.', 'brendon-core' );
		set_transient( $cache_key, $status, 2 * MINUTE_IN_SECONDS );
		return $status;
	}

	if ( $scheduled_video_id ) {
		$request_url = add_query_arg(
			array(
				'part' => 'id,snippet,liveStreamingDetails',
				'id'   => $scheduled_video_id,
				'key'  => $youtube_api_key,
			),
			'https://www.googleapis.com/youtube/v3/videos'
		);

		$response = wp_remote_get( esc_url_raw( $request_url ), array( 'timeout' => 8 ) );

		if ( is_wp_error( $response ) ) {
			$status['error'] = $response->get_error_message();
		} else {
			$body = wp_remote_retrieve_body( $response );
			$json = json_decode( $body, true );
			$item = is_array( $json ) && ! empty( $json['items'][0] ) ? $json['items'][0] : array();

			if ( ! empty( $item['liveStreamingDetails']['actualEndTime'] ) ) {
				$status['video_id'] = sanitize_text_field( $scheduled_video_id );
				$status['state']    = 'ended';
				set_transient( $cache_key, $status, 2 * MINUTE_IN_SECONDS );
				return $status;
			}

			if (
				( ! empty( $item['liveStreamingDetails']['actualStartTime'] ) && empty( $item['liveStreamingDetails']['actualEndTime'] ) )
				|| ( ! empty( $item['snippet']['liveBroadcastContent'] ) && 'live' === $item['snippet']['liveBroadcastContent'] )
			) {
				$status['is_live']  = true;
				$status['video_id'] = sanitize_text_field( $scheduled_video_id );
				$status['state']    = 'live';
			} elseif ( ! empty( $item['snippet']['liveBroadcastContent'] ) && 'upcoming' === $item['snippet']['liveBroadcastContent'] ) {
				$status['video_id'] = sanitize_text_field( $scheduled_video_id );
				$status['state']    = 'upcoming';
			}
		}

		if ( $status['is_live'] ) {
			set_transient( $cache_key, $status, 2 * MINUTE_IN_SECONDS );
			return $status;
		}
	}

	if ( ! $youtube_channel_id ) {
		$status['error'] = __( 'Missing YouTube channel ID.', 'brendon-core' );
		set_transient( $cache_key, $status, 2 * MINUTE_IN_SECONDS );
		return $status;
	}

	$request_url = add_query_arg(
		array(
			'part'       => 'id,snippet',
			'channelId'  => $youtube_channel_id,
			'eventType'  => 'live',
			'type'       => 'video',
			'maxResults' => 1,
			'key'        => $youtube_api_key,
		),
		'https://www.googleapis.com/youtube/v3/search'
	);

	$response = wp_remote_get( esc_url_raw( $request_url ), array( 'timeout' => 8 ) );

	if ( is_wp_error( $response ) ) {
		$status['error'] = $response->get_error_message();
	} else {
		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( $body, true );

		if ( is_array( $json ) && ! empty( $json['items'][0]['id']['videoId'] ) ) {
			$status['is_live']  = true;
			$status['video_id'] = sanitize_text_field( $json['items'][0]['id']['videoId'] );
			$status['state']    = 'live';
			$status['error']    = '';
		}
	}

	set_transient( $cache_key, $status, 2 * MINUTE_IN_SECONDS );
	return $status;
}

function brendon_core_bible_study_page_context() {
	$scheduled = brendon_core_bible_study_get_scheduled_context();
	$current   = $scheduled['current'];
	$upcoming  = $scheduled['upcoming'];

	if ( $current ) {
		$youtube_status = brendon_core_bible_study_youtube_status( $current->ID );
		$current_meta   = brendon_core_bible_study_session_meta( $current->ID );

		if ( brendon_core_bible_study_is_forced_live( $current->ID ) && ( $current_meta['scheduled_video_id'] || $current_meta['archive_video_id'] ) ) {
			return array(
				'state'          => 'live',
				'session'        => $current,
				'upcoming'       => $upcoming,
				'youtube_status' => array(
					'is_live'  => true,
					'video_id' => sanitize_text_field( $current_meta['scheduled_video_id'] ? $current_meta['scheduled_video_id'] : $current_meta['archive_video_id'] ),
					'state'    => 'forced',
					'error'    => '',
				),
			);
		}

		if ( ! empty( $youtube_status['is_live'] ) && ! empty( $youtube_status['video_id'] ) ) {
			update_post_meta( $current->ID, '_bb_bible_study_archive_video_id', sanitize_text_field( $youtube_status['video_id'] ) );

			return array(
				'state'          => 'live',
				'session'        => $current,
				'upcoming'       => $upcoming,
				'youtube_status' => $youtube_status,
			);
		}

		if ( 'ended' === ( $youtube_status['state'] ?? '' ) && ! empty( $youtube_status['video_id'] ) ) {
			update_post_meta( $current->ID, '_bb_bible_study_archive_video_id', sanitize_text_field( $youtube_status['video_id'] ) );

			return array(
				'state'          => 'offline',
				'session'        => $current,
				'upcoming'       => $upcoming,
				'youtube_status' => $youtube_status,
			);
		}

		return array(
			'state'          => 'waiting',
			'session'        => $current,
			'upcoming'       => $upcoming,
			'youtube_status' => $youtube_status,
		);
	}

	return array(
		'state'          => $upcoming ? 'upcoming' : 'offline',
		'session'        => null,
		'upcoming'       => $upcoming,
		'youtube_status' => array(
			'is_live'  => false,
			'video_id' => '',
			'state'    => 'offline',
			'error'    => '',
		),
	);
}

function brendon_core_bible_study_session_context( $session_id ) {
	$session_id = absint( $session_id );
	$session    = get_post( $session_id );

	if ( ! $session || BB_BIBLE_STUDY_POST_TYPE !== $session->post_type ) {
		return array(
			'state'          => 'offline',
			'session'        => null,
			'youtube_status' => array(
				'is_live'  => false,
				'video_id' => '',
				'state'    => 'offline',
				'error'    => '',
			),
		);
	}

	$meta      = brendon_core_bible_study_session_meta( $session_id );
	$timestamp = brendon_core_bible_study_datetime_to_timestamp( $meta['scheduled_start'] );
	$now       = time();

	if ( ! $timestamp ) {
		return array(
			'state'          => 'offline',
			'session'        => $session,
			'youtube_status' => array(
				'is_live'  => false,
				'video_id' => '',
				'state'    => 'offline',
				'error'    => '',
			),
		);
	}

	if ( $now < ( $timestamp - brendon_core_bible_study_live_window_before() ) ) {
		return array(
			'state'          => 'upcoming',
			'session'        => $session,
			'youtube_status' => array(
				'is_live'  => false,
				'video_id' => '',
				'state'    => 'offline',
				'error'    => '',
			),
		);
	}

	if ( $now <= ( $timestamp + brendon_core_bible_study_live_window_after() ) ) {
		$youtube_status = brendon_core_bible_study_youtube_status( $session_id );

		if ( brendon_core_bible_study_is_forced_live( $session_id ) && ( $meta['scheduled_video_id'] || $meta['archive_video_id'] ) ) {
			return array(
				'state'          => 'live',
				'session'        => $session,
				'youtube_status' => array(
					'is_live'  => true,
					'video_id' => sanitize_text_field( $meta['scheduled_video_id'] ? $meta['scheduled_video_id'] : $meta['archive_video_id'] ),
					'state'    => 'forced',
					'error'    => '',
				),
			);
		}

		if ( ! empty( $youtube_status['is_live'] ) && ! empty( $youtube_status['video_id'] ) ) {
			update_post_meta( $session_id, '_bb_bible_study_archive_video_id', sanitize_text_field( $youtube_status['video_id'] ) );

			return array(
				'state'          => 'live',
				'session'        => $session,
				'youtube_status' => $youtube_status,
			);
		}

		if ( 'ended' === ( $youtube_status['state'] ?? '' ) && ! empty( $youtube_status['video_id'] ) ) {
			update_post_meta( $session_id, '_bb_bible_study_archive_video_id', sanitize_text_field( $youtube_status['video_id'] ) );

			return array(
				'state'          => 'archived',
				'session'        => $session,
				'youtube_status' => $youtube_status,
			);
		}

		return array(
			'state'          => 'waiting',
			'session'        => $session,
			'youtube_status' => $youtube_status,
		);
	}

	return array(
		'state'          => $meta['archive_video_id'] ? 'archived' : 'offline',
		'session'        => $session,
		'youtube_status' => array(
			'is_live'  => false,
			'video_id' => '',
			'state'    => 'offline',
			'error'    => '',
		),
	);
}

function brendon_core_bible_study_register_settings() {
	register_setting(
		'brendon_core_bible_study',
		'brendon_core_bible_study_settings',
		array(
			'sanitize_callback' => 'brendon_core_bible_study_sanitize_settings',
			'default'           => brendon_core_bible_study_default_settings(),
		)
	);
}
add_action( 'admin_init', 'brendon_core_bible_study_register_settings' );

function brendon_core_bible_study_ajax_status() {
	$context = brendon_core_bible_study_page_context();

	wp_send_json_success(
		array(
			'state'   => sanitize_text_field( $context['state'] ),
			'videoId' => ! empty( $context['youtube_status']['video_id'] ) ? sanitize_text_field( $context['youtube_status']['video_id'] ) : '',
		)
	);
}
add_action( 'wp_ajax_bb_bible_study_status', 'brendon_core_bible_study_ajax_status' );
add_action( 'wp_ajax_nopriv_bb_bible_study_status', 'brendon_core_bible_study_ajax_status' );

function brendon_core_bible_study_quick_video_update() {
	$session_id = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$redirect   = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/bible-study/' );

	if (
		! $session_id
		|| BB_BIBLE_STUDY_POST_TYPE !== get_post_type( $session_id )
		|| ! current_user_can( 'edit_post', $session_id )
		|| ! isset( $_POST['bb_bible_study_quick_video_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bb_bible_study_quick_video_nonce'] ) ), 'bb_bible_study_quick_video_' . $session_id )
	) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$video_raw = isset( $_POST['youtube_video'] ) ? sanitize_text_field( wp_unslash( $_POST['youtube_video'] ) ) : '';
	$video_id  = brendon_core_bible_study_youtube_video_id( $video_raw );

	if ( $video_id ) {
		update_post_meta( $session_id, '_bb_bible_study_scheduled_video_raw', $video_raw );
		update_post_meta( $session_id, '_bb_bible_study_scheduled_video_id', $video_id );
		update_post_meta( $session_id, '_bb_bible_study_archive_video_raw', $video_raw );
		update_post_meta( $session_id, '_bb_bible_study_archive_video_id', $video_id );
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_bb_bible_study_quick_video', 'brendon_core_bible_study_quick_video_update' );

function brendon_core_bible_study_force_live_update() {
	$session_id = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$mode       = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '';
	$redirect   = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/bible-study/' );

	if (
		! $session_id
		|| BB_BIBLE_STUDY_POST_TYPE !== get_post_type( $session_id )
		|| ! current_user_can( 'edit_post', $session_id )
		|| ! isset( $_POST['bb_bible_study_force_live_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bb_bible_study_force_live_nonce'] ) ), 'bb_bible_study_force_live_' . $session_id )
	) {
		wp_safe_redirect( $redirect );
		exit;
	}

	if ( 'start' === $mode ) {
		update_post_meta( $session_id, '_bb_bible_study_force_live_until', time() + brendon_core_bible_study_live_window_after() );
	} elseif ( 'end' === $mode ) {
		delete_post_meta( $session_id, '_bb_bible_study_force_live_until' );
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_bb_bible_study_force_live', 'brendon_core_bible_study_force_live_update' );

function brendon_core_bible_study_add_settings_page() {
	add_options_page(
		esc_html__( 'Bible Study Live', 'brendon-core' ),
		esc_html__( 'Bible Study Live', 'brendon-core' ),
		'manage_options',
		'brendon-core-bible-study',
		'brendon_core_bible_study_render_settings_page'
	);
}
add_action( 'admin_menu', 'brendon_core_bible_study_add_settings_page' );

function brendon_core_bible_study_session_options() {
	return get_posts(
		array(
			'post_type'      => BB_BIBLE_STUDY_POST_TYPE,
			'post_status'    => array( 'publish', 'draft', 'future', 'private' ),
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

function brendon_core_bible_study_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = brendon_core_bible_study_settings();
	$context  = brendon_core_bible_study_page_context();
	$warnings = array();

	if ( empty( $settings['youtube_api_key'] ) ) {
		$warnings[] = __( 'YouTube API key is missing.', 'brendon-core' );
	}

	if ( empty( $settings['youtube_channel_id'] ) ) {
		$warnings[] = __( 'YouTube channel ID is missing.', 'brendon-core' );
	}

	if ( ! brendon_core_bible_study_fallback_playlist_id() ) {
		$warnings[] = __( 'Fallback playlist is missing and no uploads playlist can be derived from the channel ID.', 'brendon-core' );
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Bible Study Live Settings', 'brendon-core' ); ?></h1>
		<div class="card" style="max-width: 760px;">
			<h2><?php echo esc_html__( 'Bible Study Live Status', 'brendon-core' ); ?></h2>
			<p>
				<strong><?php echo esc_html__( 'Current state:', 'brendon-core' ); ?></strong>
				<?php echo esc_html( ucfirst( $context['state'] ) ); ?>
			</p>
			<?php if ( $context['session'] ) : ?>
				<p>
					<strong><?php echo esc_html__( 'Current session:', 'brendon-core' ); ?></strong>
					<a href="<?php echo esc_url( get_edit_post_link( $context['session']->ID ) ); ?>"><?php echo esc_html( get_the_title( $context['session'] ) ); ?></a>
					<?php echo esc_html( brendon_core_bible_study_format_session_time( $context['session']->ID ) ); ?>
				</p>
			<?php elseif ( $context['upcoming'] ) : ?>
				<p>
					<strong><?php echo esc_html__( 'Next session:', 'brendon-core' ); ?></strong>
					<a href="<?php echo esc_url( get_edit_post_link( $context['upcoming']->ID ) ); ?>"><?php echo esc_html( get_the_title( $context['upcoming'] ) ); ?></a>
					<?php echo esc_html( brendon_core_bible_study_format_session_time( $context['upcoming']->ID ) ); ?>
				</p>
			<?php else : ?>
				<p><?php echo esc_html__( 'No upcoming published Bible Study Sessions were found.', 'brendon-core' ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $context['youtube_status']['error'] ) ) : ?>
				<p><strong><?php echo esc_html__( 'YouTube:', 'brendon-core' ); ?></strong> <?php echo esc_html( $context['youtube_status']['error'] ); ?></p>
			<?php endif; ?>
			<?php if ( $warnings ) : ?>
				<ul style="list-style: disc; padding-left: 1.25rem;">
					<?php foreach ( $warnings as $warning ) : ?>
						<li><?php echo esc_html( $warning ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p><?php echo esc_html__( 'Core setup looks ready.', 'brendon-core' ); ?></p>
			<?php endif; ?>
		</div>
		<form method="post" action="options.php">
			<?php settings_fields( 'brendon_core_bible_study' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="brendon-core-bible-study-youtube-api-key"><?php echo esc_html__( 'YouTube API Key', 'brendon-core' ); ?></label>
					</th>
					<td>
						<input id="brendon-core-bible-study-youtube-api-key" class="regular-text" type="password" name="brendon_core_bible_study_settings[youtube_api_key]" value="<?php echo esc_attr( $settings['youtube_api_key'] ); ?>" autocomplete="off">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="brendon-core-bible-study-youtube-channel-id"><?php echo esc_html__( 'YouTube Channel ID', 'brendon-core' ); ?></label>
					</th>
					<td>
						<input id="brendon-core-bible-study-youtube-channel-id" class="regular-text" type="text" name="brendon_core_bible_study_settings[youtube_channel_id]" value="<?php echo esc_attr( $settings['youtube_channel_id'] ); ?>">
						<p class="description"><?php echo esc_html__( 'Use the channel ID that starts with UC, or paste a /channel/ URL.', 'brendon-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="brendon-core-bible-study-fallback-playlist-id"><?php echo esc_html__( 'Fallback Playlist ID', 'brendon-core' ); ?></label>
					</th>
					<td>
						<input id="brendon-core-bible-study-fallback-playlist-id" class="regular-text" type="text" name="brendon_core_bible_study_settings[fallback_playlist_id]" value="<?php echo esc_attr( $settings['fallback_playlist_id'] ); ?>">
						<p class="description"><?php echo esc_html__( 'Use the playlist ID that starts with PL, or paste a YouTube playlist URL.', 'brendon-core' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function brendon_core_bible_study_add_meta_boxes() {
	add_meta_box(
		'bb-bible-study-session-details',
		esc_html__( 'Bible Study Session Details', 'brendon-core' ),
		'brendon_core_bible_study_render_session_meta_box',
		BB_BIBLE_STUDY_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brendon_core_bible_study_add_meta_boxes' );

function brendon_core_bible_study_session_meta( $post_id ) {
	$asset_ids = get_post_meta( $post_id, '_bb_bible_study_asset_ids', true );
	$scheduled_video_raw = get_post_meta( $post_id, '_bb_bible_study_scheduled_video_raw', true );
	$scheduled_video_id  = get_post_meta( $post_id, '_bb_bible_study_scheduled_video_id', true );
	$archive_video_raw   = get_post_meta( $post_id, '_bb_bible_study_archive_video_raw', true );
	$archive_video_id    = get_post_meta( $post_id, '_bb_bible_study_archive_video_id', true );

	if ( ! is_array( $asset_ids ) ) {
		$asset_ids = array();
	}

	$scheduled_video_id = brendon_core_bible_study_youtube_video_id( $scheduled_video_id ) ?: brendon_core_bible_study_youtube_video_id( $scheduled_video_raw );
	$archive_video_id   = brendon_core_bible_study_youtube_video_id( $archive_video_id ) ?: brendon_core_bible_study_youtube_video_id( $archive_video_raw ) ?: $scheduled_video_id;

	return array(
		'scheduled_start'     => get_post_meta( $post_id, '_bb_bible_study_scheduled_start', true ) ?: get_post_meta( $post_id, '_bb_bible_study_date', true ),
		'scheduled_video_id'  => $scheduled_video_id,
		'scheduled_video_raw' => $scheduled_video_raw,
		'archive_video_id'    => $archive_video_id,
		'archive_video_raw'   => $archive_video_raw,
		'asset_ids'           => array_values( array_filter( array_map( 'absint', $asset_ids ) ) ),
		'chat_locked'         => (bool) get_post_meta( $post_id, '_bb_bible_study_chat_locked', true ),
	);
}

function brendon_core_bible_study_render_session_meta_box( $post ) {
	$meta = brendon_core_bible_study_session_meta( $post->ID );
	wp_nonce_field( 'bb_bible_study_save_session', 'bb_bible_study_session_nonce' );
	?>
	<p>
		<label for="bb-bible-study-scheduled-start"><strong><?php echo esc_html__( 'Scheduled Start', 'brendon-core' ); ?></strong></label><br>
		<input id="bb-bible-study-scheduled-start" type="datetime-local" name="bb_bible_study_scheduled_start" value="<?php echo esc_attr( brendon_core_bible_study_datetime_input_value( $meta['scheduled_start'] ) ); ?>">
		<span class="description"><?php echo esc_html__( 'Uses the WordPress timezone, or America/Chicago when the site timezone is unset.', 'brendon-core' ); ?></span>
	</p>
	<p>
		<label for="bb-bible-study-scheduled-video"><strong><?php echo esc_html__( 'Scheduled YouTube Live URL or ID', 'brendon-core' ); ?></strong></label><br>
		<input id="bb-bible-study-scheduled-video" class="widefat" type="text" name="bb_bible_study_scheduled_video" value="<?php echo esc_attr( $meta['scheduled_video_raw'] ? $meta['scheduled_video_raw'] : $meta['scheduled_video_id'] ); ?>">
		<span class="description"><?php echo esc_html__( 'Optional. If blank, live detection falls back to the configured channel.', 'brendon-core' ); ?></span>
	</p>
	<p>
		<label for="bb-bible-study-archive-video"><strong><?php echo esc_html__( 'Archive YouTube Video URL or ID', 'brendon-core' ); ?></strong></label><br>
		<input id="bb-bible-study-archive-video" class="widefat" type="text" name="bb_bible_study_archive_video" value="<?php echo esc_attr( $meta['archive_video_raw'] ? $meta['archive_video_raw'] : $meta['archive_video_id'] ); ?>">
		<span class="description"><?php echo esc_html__( 'Optional. This can be auto-filled from the live video when the session goes live.', 'brendon-core' ); ?></span>
	</p>
	<p>
		<label for="bb-bible-study-asset-ids"><strong><?php echo esc_html__( 'Study Assets', 'brendon-core' ); ?></strong></label><br>
		<input id="bb-bible-study-asset-ids" class="widefat" type="hidden" name="bb_bible_study_asset_ids" value="<?php echo esc_attr( implode( ',', $meta['asset_ids'] ) ); ?>">
		<button type="button" class="button" id="bb-bible-study-select-assets"><?php echo esc_html__( 'Select Assets', 'brendon-core' ); ?></button>
	</p>
	<ul id="bb-bible-study-assets-list">
		<?php foreach ( $meta['asset_ids'] as $asset_id ) : ?>
			<li data-id="<?php echo esc_attr( $asset_id ); ?>"><?php echo esc_html( get_the_title( $asset_id ) ?: wp_basename( get_attached_file( $asset_id ) ) ); ?></li>
		<?php endforeach; ?>
	</ul>
	<p>
		<label>
			<input type="checkbox" name="bb_bible_study_chat_locked" value="1" <?php checked( $meta['chat_locked'] ); ?>>
			<?php echo esc_html__( 'Lock live chat for this session', 'brendon-core' ); ?>
		</label>
	</p>
	<?php
}

function brendon_core_bible_study_save_session_meta( $post_id ) {
	if ( ! isset( $_POST['bb_bible_study_session_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bb_bible_study_session_nonce'] ) ), 'bb_bible_study_save_session' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$scheduled_start = isset( $_POST['bb_bible_study_scheduled_start'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_bible_study_scheduled_start'] ) ) : '';
	update_post_meta( $post_id, '_bb_bible_study_scheduled_start', $scheduled_start );

	$scheduled_raw = isset( $_POST['bb_bible_study_scheduled_video'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_bible_study_scheduled_video'] ) ) : '';
	$scheduled_video_id = brendon_core_bible_study_youtube_video_id( $scheduled_raw );
	update_post_meta( $post_id, '_bb_bible_study_scheduled_video_raw', $scheduled_raw );
	update_post_meta( $post_id, '_bb_bible_study_scheduled_video_id', $scheduled_video_id );

	$archive_raw = isset( $_POST['bb_bible_study_archive_video'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_bible_study_archive_video'] ) ) : '';
	update_post_meta( $post_id, '_bb_bible_study_archive_video_raw', $archive_raw );
	update_post_meta( $post_id, '_bb_bible_study_archive_video_id', brendon_core_bible_study_youtube_video_id( $archive_raw ) ?: $scheduled_video_id );

	$asset_ids = isset( $_POST['bb_bible_study_asset_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_bible_study_asset_ids'] ) ) : '';
	$asset_ids = array_values( array_filter( array_map( 'absint', explode( ',', $asset_ids ) ) ) );
	update_post_meta( $post_id, '_bb_bible_study_asset_ids', $asset_ids );

	update_post_meta( $post_id, '_bb_bible_study_chat_locked', isset( $_POST['bb_bible_study_chat_locked'] ) ? '1' : '' );
}
add_action( 'save_post_' . BB_BIBLE_STUDY_POST_TYPE, 'brendon_core_bible_study_save_session_meta' );

function brendon_core_bible_study_admin_assets( $hook ) {
	global $post;

	if ( ! $post || BB_BIBLE_STUDY_POST_TYPE !== $post->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'brendon-core-bible-study-admin',
		get_template_directory_uri() . '/assets/js/bible-study-admin.js',
		array( 'jquery' ),
		filemtime( get_template_directory() . '/assets/js/bible-study-admin.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'brendon_core_bible_study_admin_assets' );

function brendon_core_bible_study_get_session_assets( $session_id ) {
	$meta = brendon_core_bible_study_session_meta( $session_id );

	return $meta['asset_ids'];
}

function brendon_core_bible_study_user_is_muted( $session_id, $user_id ) {
	global $wpdb;

	$table = brendon_core_bible_study_mutes_table();

	$muted_until = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT muted_until FROM {$table} WHERE session_id = %d AND user_id = %d AND muted_until > %s",
			$session_id,
			$user_id,
			current_time( 'mysql' )
		)
	);

	return $muted_until ? $muted_until : false;
}

function brendon_core_bible_study_chat_allowed( $session_id ) {
	$meta = brendon_core_bible_study_session_meta( $session_id );
	$session_context = brendon_core_bible_study_session_context( $session_id );

	return $session_id && 'live' === $session_context['state'] && ! $meta['chat_locked'] && is_user_logged_in() && ! brendon_core_bible_study_user_is_muted( $session_id, get_current_user_id() );
}

function brendon_core_bible_study_message_payload( $message ) {
	$user = get_userdata( absint( $message->user_id ) );

	return array(
		'id'         => absint( $message->id ),
		'userId'     => absint( $message->user_id ),
		'author'     => $user ? $user->display_name : __( 'Member', 'brendon-core' ),
		'avatar'     => get_avatar_url( absint( $message->user_id ), array( 'size' => 64 ) ),
		'message'    => esc_html( $message->message ),
		'createdAt'  => mysql2date( get_option( 'time_format' ), $message->created_at ),
		'canModerate' => current_user_can( BB_BIBLE_STUDY_MODERATE_CAP ),
	);
}

function brendon_core_bible_study_fetch_message_rows( $session_id, $after_id = 0, $limit = 80 ) {
	global $wpdb;

	$table    = brendon_core_bible_study_messages_table();
	$after_id = absint( $after_id );
	$limit    = min( 100, max( 1, absint( $limit ) ) );

	if ( $after_id ) {
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE session_id = %d AND status = 'visible' AND id > %d ORDER BY id ASC LIMIT %d",
				$session_id,
				$after_id,
				$limit
			)
		);
	}

	return array_reverse(
		$wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE session_id = %d AND status = 'visible' ORDER BY id DESC LIMIT %d",
				$session_id,
				$limit
			)
		)
	);
}

function brendon_core_bible_study_ajax_fetch_messages() {
	check_ajax_referer( 'bb_bible_study_chat', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to view chat.', 'brendon-core' ) ), 401 );
	}

	$session_id = isset( $_POST['sessionId'] ) ? absint( $_POST['sessionId'] ) : 0;
	$after_id   = isset( $_POST['afterId'] ) ? absint( $_POST['afterId'] ) : 0;

	if ( BB_BIBLE_STUDY_POST_TYPE !== get_post_type( $session_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid Bible study session.', 'brendon-core' ) ), 400 );
	}

	$messages = array_map( 'brendon_core_bible_study_message_payload', brendon_core_bible_study_fetch_message_rows( $session_id, $after_id ) );

	wp_send_json_success(
		array(
			'messages' => $messages,
			'muted'    => (bool) brendon_core_bible_study_user_is_muted( $session_id, get_current_user_id() ),
		)
	);
}
add_action( 'wp_ajax_bb_bible_study_fetch_messages', 'brendon_core_bible_study_ajax_fetch_messages' );

function brendon_core_bible_study_ajax_send_message() {
	global $wpdb;

	check_ajax_referer( 'bb_bible_study_chat', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to chat.', 'brendon-core' ) ), 401 );
	}

	$session_id = isset( $_POST['sessionId'] ) ? absint( $_POST['sessionId'] ) : 0;
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$message    = trim( function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 500 ) : substr( $message, 0, 500 ) );

	if ( BB_BIBLE_STUDY_POST_TYPE !== get_post_type( $session_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid Bible study session.', 'brendon-core' ) ), 400 );
	}

	if ( ! brendon_core_bible_study_chat_allowed( $session_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Chat is not available right now.', 'brendon-core' ) ), 403 );
	}

	if ( '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'Write a message first.', 'brendon-core' ) ), 400 );
	}

	$now = current_time( 'mysql' );
	$wpdb->insert(
		brendon_core_bible_study_messages_table(),
		array(
			'session_id' => $session_id,
			'user_id'    => get_current_user_id(),
			'message'    => $message,
			'status'     => 'visible',
			'created_at' => $now,
			'updated_at' => $now,
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s' )
	);

	wp_send_json_success(
		array(
			'message' => brendon_core_bible_study_message_payload(
				$wpdb->get_row(
					$wpdb->prepare(
						'SELECT * FROM ' . brendon_core_bible_study_messages_table() . ' WHERE id = %d',
						$wpdb->insert_id
					)
				)
			),
		)
	);
}
add_action( 'wp_ajax_bb_bible_study_send_message', 'brendon_core_bible_study_ajax_send_message' );

function brendon_core_bible_study_ajax_moderate_message() {
	global $wpdb;

	check_ajax_referer( 'bb_bible_study_chat', 'nonce' );

	if ( ! current_user_can( BB_BIBLE_STUDY_MODERATE_CAP ) ) {
		wp_send_json_error( array( 'message' => __( 'You cannot moderate this chat.', 'brendon-core' ) ), 403 );
	}

	$message_id = isset( $_POST['messageId'] ) ? absint( $_POST['messageId'] ) : 0;
	$status     = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'hidden';

	if ( ! in_array( $status, array( 'hidden', 'deleted' ), true ) ) {
		$status = 'hidden';
	}

	$wpdb->update(
		brendon_core_bible_study_messages_table(),
		array(
			'status'     => $status,
			'updated_at' => current_time( 'mysql' ),
		),
		array( 'id' => $message_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	wp_send_json_success();
}
add_action( 'wp_ajax_bb_bible_study_moderate_message', 'brendon_core_bible_study_ajax_moderate_message' );

function brendon_core_bible_study_ajax_mute_user() {
	global $wpdb;

	check_ajax_referer( 'bb_bible_study_chat', 'nonce' );

	if ( ! current_user_can( BB_BIBLE_STUDY_MODERATE_CAP ) ) {
		wp_send_json_error( array( 'message' => __( 'You cannot mute users.', 'brendon-core' ) ), 403 );
	}

	$session_id = isset( $_POST['sessionId'] ) ? absint( $_POST['sessionId'] ) : 0;
	$user_id    = isset( $_POST['userId'] ) ? absint( $_POST['userId'] ) : 0;
	$minutes    = isset( $_POST['minutes'] ) ? absint( $_POST['minutes'] ) : 15;
	$minutes    = min( 1440, max( 1, $minutes ) );

	if ( ! $session_id || ! $user_id ) {
		wp_send_json_error( array( 'message' => __( 'Missing mute details.', 'brendon-core' ) ), 400 );
	}

	$wpdb->replace(
		brendon_core_bible_study_mutes_table(),
		array(
			'session_id'   => $session_id,
			'user_id'      => $user_id,
			'muted_by'     => get_current_user_id(),
			'muted_until'  => wp_date( 'Y-m-d H:i:s', time() + ( $minutes * MINUTE_IN_SECONDS ) ),
			'created_at'   => current_time( 'mysql' ),
		),
		array( '%d', '%d', '%d', '%s', '%s' )
	);

	wp_send_json_success();
}
add_action( 'wp_ajax_bb_bible_study_mute_user', 'brendon_core_bible_study_ajax_mute_user' );

function brendon_core_bible_study_enqueue_chat( $session_id, $mode = 'live' ) {
	wp_enqueue_script(
		'brendon-core-bible-study-chat',
		get_template_directory_uri() . '/assets/js/bible-study-chat.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/bible-study-chat.js' ),
		true
	);

	wp_localize_script(
		'brendon-core-bible-study-chat',
		'bbBibleStudyChat',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'bb_bible_study_chat' ),
			'sessionId'  => absint( $session_id ),
			'mode'       => $mode,
			'pollMs'     => 3000,
			'canSend'    => 'live' === $mode && brendon_core_bible_study_chat_allowed( $session_id ),
			'canModerate' => current_user_can( BB_BIBLE_STUDY_MODERATE_CAP ),
		)
	);
}

function brendon_core_bible_study_render_assets( $session_id ) {
	$asset_ids = brendon_core_bible_study_get_session_assets( $session_id );
	?>
	<article class="bb-bible-study__panel bb-bible-study__panel--assets">
		<h3><?php echo esc_html__( 'Study Assets', 'brendon-core' ); ?></h3>
		<?php if ( $asset_ids ) : ?>
			<ul class="bb-bible-study__asset-list">
				<?php foreach ( $asset_ids as $asset_id ) : ?>
					<?php $url = wp_get_attachment_url( $asset_id ); ?>
					<?php if ( $url ) : ?>
						<li>
							<a class="bb-bible-study__button" href="<?php echo esc_url( $url ); ?>" download>
								<?php echo esc_html( get_the_title( $asset_id ) ?: wp_basename( $url ) ); ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html__( 'No downloads have been added for this study yet.', 'brendon-core' ); ?></p>
		<?php endif; ?>
	</article>
	<?php
}

function brendon_core_bible_study_render_chat( $session_id, $mode = 'live' ) {
	brendon_core_bible_study_enqueue_chat( $session_id, $mode );
	$can_send = 'live' === $mode && brendon_core_bible_study_chat_allowed( $session_id );
	?>
	<article class="bb-bible-study__panel bb-bible-study__panel--chat">
		<div class="bb-bible-study__chat-heading">
			<h3><?php echo 'live' === $mode ? esc_html__( 'Live Discussion', 'brendon-core' ) : esc_html__( 'Discussion Replay', 'brendon-core' ); ?></h3>
			<span class="bb-bible-study__chat-state"><?php echo 'live' === $mode ? esc_html__( 'Refreshing live', 'brendon-core' ) : esc_html__( 'Archive', 'brendon-core' ); ?></span>
		</div>
		<div class="bb-bible-study-chat" data-session-id="<?php echo esc_attr( $session_id ); ?>">
			<div class="bb-bible-study-chat__messages" data-chat-messages aria-live="polite"></div>
			<?php if ( $can_send ) : ?>
				<form class="bb-bible-study-chat__form" data-chat-form>
					<label class="screen-reader-text" for="bb-bible-study-chat-message"><?php echo esc_html__( 'Chat message', 'brendon-core' ); ?></label>
					<textarea id="bb-bible-study-chat-message" data-chat-input maxlength="500" rows="3" placeholder="<?php echo esc_attr__( 'Share a thought or question...', 'brendon-core' ); ?>"></textarea>
					<button class="bb-bible-study__button" type="submit"><?php echo esc_html__( 'Send', 'brendon-core' ); ?></button>
				</form>
			<?php else : ?>
				<p class="bb-bible-study-chat__notice"><?php echo esc_html__( 'Chat is locked or unavailable right now.', 'brendon-core' ); ?></p>
			<?php endif; ?>
			<p class="bb-bible-study-chat__notice" data-chat-notice></p>
		</div>
	</article>
	<?php
}

function brendon_core_bible_study_video_embed_url( $video_id ) {
	$video_id = brendon_core_bible_study_youtube_video_id( $video_id );

	if ( ! $video_id ) {
		return '';
	}

	return add_query_arg(
		array(
			'rel' => 0,
		),
		'https://www.youtube.com/embed/' . rawurlencode( $video_id )
	);
}
