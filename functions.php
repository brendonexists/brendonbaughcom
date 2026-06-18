<?php

/**
 * _s functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package _s
 */

if (! defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

if (! defined('BB_HOME_POSTS_PER_PAGE')) {
	define('BB_HOME_POSTS_PER_PAGE', 14);
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function _s_setup()
{
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on _s, use a find and replace
		* to change '_s' to the name of your theme in all the template files.
		*/

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');
	add_theme_support('responsive-embeds');
	add_theme_support('align-wide');
	add_theme_support('editor-styles');
	add_theme_support('wp-block-styles');
	add_editor_style('style.css');

	add_theme_support(
		'editor-color-palette',
		[
			[
				'name'  => esc_html__('Forest Green', 'brendon-core'),
				'slug'  => 'forest-green',
				'color' => '#315D3B',
			],
			[
				'name'  => esc_html__('Paper Cream', 'brendon-core'),
				'slug'  => 'paper-cream',
				'color' => '#F1E6C8',
			],
			[
				'name'  => esc_html__('Muted Gold', 'brendon-core'),
				'slug'  => 'muted-gold',
				'color' => '#D2A43A',
			],
			[
				'name'  => esc_html__('Deep Ink Green', 'brendon-core'),
				'slug'  => 'deep-ink-green',
				'color' => '#10251F',
			],
		]
	);

	// This theme uses WordPress menus for the primary shell and footer.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', '_s'),
			'footer' => esc_html__('Footer', 'brendon-core'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'_s_custom_background_args',
			array(
				'default-color' => 'F2EDD0',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_image_size('brendon-core-card', 960, 600, true);
	add_image_size('brendon-core-wide', 1440, 810, true);
}
add_action('after_setup_theme', '_s_setup');

/**
 * Load the theme textdomains after init so WordPress hooks translation loading later.
 */
function brendon_core_load_textdomains()
{
	load_theme_textdomain('_s', get_template_directory() . '/languages');
	load_theme_textdomain('brendon-core', get_template_directory() . '/languages');
}
add_action('init', 'brendon_core_load_textdomains');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function _s_content_width()
{
	$GLOBALS['content_width'] = apply_filters('_s_content_width', 840);
}
add_action('after_setup_theme', '_s_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function _s_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', '_s'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', '_s'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', '_s_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function _s_scripts()
{
	$stylesheet_path = get_stylesheet_directory() . '/style.css';
	$stylesheet_ver  = file_exists( $stylesheet_path ) ? filemtime( $stylesheet_path ) : _S_VERSION;
	$font_url = 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Bowlby+One+SC&family=Inter:wght@400;500;600;700;800&family=League+Spartan:wght@700;800&display=swap';
	wp_enqueue_style( 'brendon-core-fonts', $font_url, array(), null );
	wp_enqueue_style('_s-style', get_stylesheet_uri(), array( 'brendon-core-fonts' ), $stylesheet_ver);
	wp_style_add_data('_s-style', 'rtl', 'replace');
	$embeds_css_path = get_template_directory() . '/assets/css/embeds.css';
	$embeds_css_ver  = file_exists( $embeds_css_path ) ? filemtime( $embeds_css_path ) : _S_VERSION;
	wp_enqueue_style('brendon-core-embed-style', get_template_directory_uri() . '/assets/css/embeds.css', array('_s-style'), $embeds_css_ver);
	$brand_theme_deps = array( '_s-style', 'brendon-core-embed-style' );
	if ( is_page_template( 'page-live-now.php' ) ) {
		$live_now_css_path = get_template_directory() . '/assets/css/live-now.css';
		$live_now_css_ver  = file_exists( $live_now_css_path ) ? filemtime( $live_now_css_path ) : _S_VERSION;
		wp_enqueue_style( 'brendon-core-live-now', get_template_directory_uri() . '/assets/css/live-now.css', array( '_s-style' ), $live_now_css_ver );
		$brand_theme_deps[] = 'brendon-core-live-now';
	}
	$brand_theme_css_path = get_template_directory() . '/assets/css/brand-theme.css';
	$brand_theme_css_ver  = file_exists( $brand_theme_css_path ) ? filemtime( $brand_theme_css_path ) : _S_VERSION;
	wp_enqueue_style( 'brendon-core-brand-theme', get_template_directory_uri() . '/assets/css/brand-theme.css', $brand_theme_deps, $brand_theme_css_ver );
	if ( is_front_page() ) {
		$home_retro_css_path = get_template_directory() . '/assets/css/home-retro.css';
		$home_retro_css_ver  = file_exists( $home_retro_css_path ) ? filemtime( $home_retro_css_path ) : _S_VERSION;
		wp_enqueue_style( 'brendon-core-home-retro', get_template_directory_uri() . '/assets/css/home-retro.css', array( 'brendon-core-brand-theme' ), $home_retro_css_ver );
	}
	if ( is_page_template( 'page-at-every-turn.php' ) ) {
		$at_every_turn_css_path = get_template_directory() . '/assets/css/at-every-turn.css';
		$at_every_turn_css_ver  = file_exists( $at_every_turn_css_path ) ? filemtime( $at_every_turn_css_path ) : _S_VERSION;
		wp_enqueue_style( 'brendon-core-at-every-turn-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;0,700;1,300;1,600&family=Barlow+Condensed:wght@300;400;600;700;800&family=Barlow:wght@300;400;500&display=swap', array(), null );
		wp_enqueue_style( 'brendon-core-at-every-turn', get_template_directory_uri() . '/assets/css/at-every-turn.css', array( 'brendon-core-brand-theme', 'brendon-core-at-every-turn-fonts' ), $at_every_turn_css_ver );
	}

	$nav_js_path = get_template_directory() . '/js/navigation.js';
	$nav_js_ver  = file_exists( $nav_js_path ) ? filemtime( $nav_js_path ) : _S_VERSION;
	wp_enqueue_script('_s-navigation', get_template_directory_uri() . '/js/navigation.js', array(), $nav_js_ver, true);
	if (is_singular()) {
		$embeds_js_path = get_template_directory() . '/assets/js/embeds.js';
		$embeds_js_ver  = file_exists( $embeds_js_path ) ? filemtime( $embeds_js_path ) : _S_VERSION;
		wp_enqueue_script('brendon-core-embed-script', get_template_directory_uri() . '/assets/js/embeds.js', array(), $embeds_js_ver, true);
	}

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', '_s_scripts');

/**
 * Load Ultimate Member overrides after plugin assets so the cascade is predictable.
 */
function brendon_core_um_override_assets() {
	$um_profile_css_path = get_template_directory() . '/assets/css/ultimate-member-profile.css';
	if ( file_exists( $um_profile_css_path ) ) {
		wp_enqueue_style(
			'brendon-core-ultimate-member-profile',
			get_template_directory_uri() . '/assets/css/ultimate-member-profile.css',
			array( 'brendon-core-brand-theme' ),
			filemtime( $um_profile_css_path )
		);
	}

	$um_account_css_path = get_template_directory() . '/assets/css/ultimate-member-account.css';
	if ( file_exists( $um_account_css_path ) ) {
		wp_enqueue_style(
			'brendon-core-ultimate-member-account',
			get_template_directory_uri() . '/assets/css/ultimate-member-account.css',
			array( 'brendon-core-brand-theme', 'brendon-core-ultimate-member-profile' ),
			filemtime( $um_account_css_path )
		);
	}

	$um_profile_js_path = get_template_directory() . '/assets/js/ultimate-member-profile.js';
	if ( file_exists( $um_profile_js_path ) ) {
		$deps = wp_script_is( 'um_profile', 'registered' ) ? array( 'um_profile' ) : array();

		wp_enqueue_script(
			'brendon-core-ultimate-member-profile',
			get_template_directory_uri() . '/assets/js/ultimate-member-profile.js',
			$deps,
			filemtime( $um_profile_js_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'brendon_core_um_override_assets', 99 );

/**
 * Show the logged-in user's display name on the primary profile menu item.
 *
 * @param string   $title Menu item title.
 * @param WP_Post  $item  Menu item object.
 * @param stdClass $args  Menu arguments.
 * @param int      $depth Menu depth.
 * @return string
 */
function brendon_core_primary_profile_menu_title( $title, $item, $args, $depth ) {
	if ( ! is_user_logged_in() || empty( $args->theme_location ) || 'menu-1' !== $args->theme_location || 0 !== (int) $depth ) {
		return $title;
	}

	$item_url = isset( $item->url ) ? untrailingslashit( wp_parse_url( $item->url, PHP_URL_PATH ) ?: '' ) : '';

	if ( 'User' !== wp_strip_all_tags( $title ) && '/user' !== $item_url ) {
		return $title;
	}

	$current_user = wp_get_current_user();
	$display_name = $current_user->display_name ?: $current_user->user_login;

	return esc_html( $display_name );
}
add_filter( 'nav_menu_item_title', 'brendon_core_primary_profile_menu_title', 20, 4 );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/live-now.php';
require get_template_directory() . '/inc/at-every-turn.php';
require get_template_directory() . '/inc/bible-study-live.php';
require get_template_directory() . '/inc/prayer-wall.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Register availability of the sidebar menu once translations are ready.
 */
function brendon_core_register_sidebar_menu()
{
	register_nav_menus([
		'sidebar'           => esc_html__('Sidebar Menu', 'brendon-core'),
		'sidebar-secondary' => esc_html__('Sidebar Secondary Menu', 'brendon-core'),
	]);
}
add_action('init', 'brendon_core_register_sidebar_menu');

/**
 * Adds a featured post checkbox to the post edit screen.
 */
function brendon_core_add_featured_meta_box()
{
	add_meta_box(
		'brendon_core_featured_post',
		esc_html__('Feature on Homepage', 'brendon-core'),
		'brendon_core_featured_meta_box_callback',
		'post',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'brendon_core_add_featured_meta_box');

/**
 * Callback for rendering the featured checkbox.
 *
 * @param WP_Post $post
 */
function brendon_core_featured_meta_box_callback($post)
{
	wp_nonce_field('brendon_core_save_featured_meta', 'brendon_core_featured_nonce');
	$value = get_post_meta($post->ID, 'brendon_core_featured_post', true);
	?>
	<label>
		<input type="checkbox" name="brendon_core_featured_post" value="1" <?php checked($value, '1'); ?> />
		<?php esc_html_e('Feature this post in the homepage record block', 'brendon-core'); ?>
	</label>
	<?php
}

/**
 * Saves the featured post flag.
 *
 * @param int $post_id
 */
function brendon_core_save_featured_meta($post_id)
{
	if (!isset($_POST['brendon_core_featured_nonce']) || !wp_verify_nonce($_POST['brendon_core_featured_nonce'], 'brendon_core_save_featured_meta')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	$flag = isset($_POST['brendon_core_featured_post']) ? '1' : '0';
	update_post_meta($post_id, 'brendon_core_featured_post', $flag);
}
add_action('save_post', 'brendon_core_save_featured_meta');

/**
 * Wrap embeds in a styled responsive container.
 *
 * @param string $html    The embed HTML.
 * @param string $url     The source URL.
 * @param array  $attr    Embed attributes.
 * @param int    $post_id Post ID.
 * @return string
 */
function brendon_core_responsive_embed( $html, $url, $attr, $post_id ) {
	if ( false === stripos( $html, '<iframe' ) ) {
		return $html;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	$host = $host ? strtolower( str_replace( 'www.', '', $host ) ) : '';

	$label  = '';

	if ( str_contains( $host, 'spotify' ) ) {
		$label  = esc_html__( 'Spotify', 'brendon-core' );
	} elseif ( str_contains( $host, 'youtu' ) ) {
		$label  = esc_html__( 'YouTube', 'brendon-core' );
	}

	$wide_html = preg_replace( '/(width|height)="\d*"/i', '', $html );
	$enhanced  = preg_replace(
		'/<iframe([^>]*)>/i',
		'<iframe$1 loading="lazy" class="bb-embed__iframe" referrerpolicy="no-referrer" allowfullscreen>',
		$wide_html,
		1
	);

	$label_html = $label
		? sprintf(
			'<span class="bb-embed__label">%s</span>',
			esc_html( $label )
		)
		: '';

	$wrapper_classes = 'bb-embed';

	if ( str_contains( $host, 'spotify' ) ) {
		$wrapper_classes .= ' bb-embed--audio';
	} else {
		$wrapper_classes .= str_contains( $host, 'youtu' ) ? ' bb-embed--wide' : ' bb-embed--video';
	}

	$wrapper = '<div class="%1$s">%2$s<div class="bb-embed__frame">%3$s</div></div>';

	return sprintf(
		$wrapper,
		esc_attr( trim( $wrapper_classes ) ),
		$label_html,
		$enhanced
	);
}
add_filter( 'embed_oembed_html', 'brendon_core_responsive_embed', 20, 4 );
