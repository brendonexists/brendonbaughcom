<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package _s
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function _s_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	if ( is_page( 'about' ) ) {
		$classes[] = 'bb-page-about';
	}

	return $classes;
}
add_filter( 'body_class', '_s_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function _s_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', '_s_pingback_header' );

/**
 * Brand mark using the uploaded Custom Logo when available.
 *
 * @param string $classes CSS classes for the rendered mark.
 * @return string
 */
function brendon_core_brand_mark( $classes = '' ) {
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		$logo = wp_get_attachment_image(
			$custom_logo_id,
			'full',
			false,
			[
				'class' => trim( $classes . ' bb-mark bb-mark--uploaded' ),
				'alt'   => '',
			]
		);

		if ( $logo ) {
			return $logo;
		}
	}

	return brendon_core_split_heart_svg( trim( $classes . ' bb-mark' ) );
}

/**
 * Inline split-heart brand mark fallback.
 *
 * @param string $classes CSS classes for the SVG element.
 * @return string
 */
function brendon_core_split_heart_svg( $classes = '' ) {
	return sprintf(
		'<svg class="%1$s" viewBox="0 0 64 64" role="img" aria-label="%2$s" xmlns="http://www.w3.org/2000/svg"><path fill="#2975D9" d="M30.2 55.5C14.6 44.8 6 35.6 6 24.6 6 15.8 12.1 9.5 20.2 9.5c4.3 0 7.8 1.9 10 5.2v40.8Z"/><path fill="#F22E2E" d="M33.8 55.5V14.7c2.2-3.3 5.7-5.2 10-5.2C51.9 9.5 58 15.8 58 24.6c0 11-8.6 20.2-24.2 30.9Z"/><path fill="#0D0D0D" d="M30.2 12.8h3.6v45.7h-3.6z"/></svg>',
		esc_attr( $classes ),
		esc_attr__( 'Split heart mark', 'brendon-core' )
	);
}

/**
 * Adds a lightweight SVG favicon when WordPress has no site icon configured.
 */
function brendon_core_brand_favicon() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $logo_url ) {
			printf( '<link rel="icon" href="%s">', esc_url( $logo_url ) );
			return;
		}
	}

	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="#F2EDD0"/><path fill="#2975D9" d="M30.2 55.5C14.6 44.8 6 35.6 6 24.6 6 15.8 12.1 9.5 20.2 9.5c4.3 0 7.8 1.9 10 5.2v40.8Z"/><path fill="#F22E2E" d="M33.8 55.5V14.7c2.2-3.3 5.7-5.2 10-5.2C51.9 9.5 58 15.8 58 24.6c0 11-8.6 20.2-24.2 30.9Z"/><path fill="#0D0D0D" d="M30.2 12.8h3.6v45.7h-3.6z"/></svg>';
	printf( '<link rel="icon" href="data:image/svg+xml,%s">', rawurlencode( $svg ) );
}
add_action( 'wp_head', 'brendon_core_brand_favicon', 5 );

/**
 * Primary navigation fallback.
 */
function brendon_core_primary_menu_fallback() {
	$items = brendon_core_identity_nav_items();

	echo '<ul id="primary-menu" class="bb-primary-nav__list">';
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Footer navigation fallback.
 */
function brendon_core_footer_menu_fallback() {
	$items = brendon_core_identity_nav_items();

	echo '<ul class="bb-footer__links">';
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Default identity navigation used when menus have not been configured.
 *
 * @return array
 */
function brendon_core_identity_nav_items() {
	return [
		[
			'label' => esc_html__( 'Home', 'brendon-core' ),
			'url'   => home_url( '/' ),
		],
		[
			'label' => esc_html__( 'About', 'brendon-core' ),
			'url'   => home_url( '/about' ),
		],
		[
			'label' => esc_html__( 'Writing', 'brendon-core' ),
			'url'   => home_url( '/writing' ),
		],
		[
			'label' => esc_html__( 'Projects', 'brendon-core' ),
			'url'   => home_url( '/projects' ),
		],
		[
			'label' => esc_html__( 'Faith', 'brendon-core' ),
			'url'   => home_url( '/faith' ),
		],
		[
			'label' => esc_html__( 'Contact', 'brendon-core' ),
			'url'   => home_url( '/contact' ),
		],
	];
}

/**
 * Estimated reading time for a post.
 *
 * @param int|null $post_id Post ID. Defaults to the current post.
 * @return int
 */
function brendon_core_reading_minutes( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ?: '' ) ) );

	return max( 1, (int) ceil( $words / 220 ) );
}

/**
 * Best destination for the writing archive.
 *
 * @return string
 */
function brendon_core_writing_url() {
	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		$posts_page_url = get_permalink( $posts_page_id );

		if ( $posts_page_url ) {
			return $posts_page_url;
		}
	}

	return home_url( '/writing' );
}

/**
 * Default homepage copy and editable settings.
 *
 * @return array
 */
function brendon_core_home_defaults() {
	return [
		'hero_kicker'          => esc_html__( 'Brendon Exists', 'brendon-core' ),
		'hero_heading'         => esc_html__( 'Documenting real life in public, without pretending it is cleaner than it is.', 'brendon-core' ),
		'hero_lede'            => esc_html__( 'Faith, fatherhood, discipline, building, creative work, and the slow practice of becoming a whole person.', 'brendon-core' ),
		'hero_primary_label'   => esc_html__( 'Read the Logs', 'brendon-core' ),
		'hero_primary_url'     => home_url( '/writing' ),
		'hero_secondary_label' => esc_html__( 'Start With the Story', 'brendon-core' ),
		'hero_secondary_url'   => home_url( '/about' ),
		'what_kicker'          => esc_html__( 'What this is', 'brendon-core' ),
		'what_heading'         => esc_html__( 'A home base for the work under the work.', 'brendon-core' ),
		'what_body'            => esc_html__( 'Not a persona. Not a content funnel. A place to keep receipts from real life: conviction, doubt, reps, prayers, unfinished builds, and lessons learned the hard way.', 'brendon-core' ),
		'writing_heading'      => esc_html__( 'Logs from the field.', 'brendon-core' ),
		'writing_url'          => home_url( '/writing' ),
		'projects_kicker'      => esc_html__( 'Projects / Builds', 'brendon-core' ),
		'projects_heading'     => esc_html__( 'The useful things', 'brendon-core' ),
		'projects_subheading'  => esc_html__( 'Get a paper trail.', 'brendon-core' ),
		'projects_category'    => 0,
		'season_kicker'        => esc_html__( 'Current season', 'brendon-core' ),
		'season_heading'       => esc_html__( 'Build the home. Keep the record. Tell the truth.', 'brendon-core' ),
		'season_body'          => esc_html__( 'Less performance. More practice. A steady archive of what is being learned, made, repaired, and carried.', 'brendon-core' ),
	];
}

/**
 * Get a homepage setting with a default fallback.
 *
 * @param string $key Setting key without prefix.
 * @return string|int
 */
function brendon_core_home_setting( $key ) {
	$defaults = brendon_core_home_defaults();

	if ( ! array_key_exists( $key, $defaults ) ) {
		return '';
	}

	return get_theme_mod( "brendon_core_home_{$key}", $defaults[ $key ] );
}

/**
 * Default copy for the site footer.
 *
 * @return array
 */
function brendon_core_footer_defaults() {
	return [
		'eyebrow'   => esc_html__( 'Brendon Exists', 'brendon-core' ),
		'statement' => esc_html__( 'A living record of faith, fatherhood, discipline, building, and becoming in public.', 'brendon-core' ),
		'tagline'   => esc_html__( 'Built as a home base, not a highlight reel.', 'brendon-core' ),
	];
}

/**
 * Get a footer setting with a default fallback.
 *
 * @param string $key Setting key without prefix.
 * @return string
 */
function brendon_core_footer_setting( $key ) {
	$defaults = brendon_core_footer_defaults();

	if ( ! array_key_exists( $key, $defaults ) ) {
		return '';
	}

	return get_theme_mod( "brendon_core_footer_{$key}", $defaults[ $key ] );
}

/**
 * Core pillars for the homepage framework section.
 *
 * @return array
 */
function brendon_core_home_pillars() {
	return [
		esc_html__( 'Faith', 'brendon-core' ),
		esc_html__( 'Fatherhood', 'brendon-core' ),
		esc_html__( 'Discipline', 'brendon-core' ),
		esc_html__( 'Building', 'brendon-core' ),
		esc_html__( 'Creative Process', 'brendon-core' ),
		esc_html__( 'Growth in Public', 'brendon-core' ),
	];
}

/**
 * Fallback project/build links for the homepage when no category posts exist.
 *
 * @return array
 */
function brendon_core_default_project_links() {
	return [
		[
			'label'       => esc_html__( 'Systems', 'brendon-core' ),
			'description' => esc_html__( 'Personal tools, web work, experiments, and repairs.', 'brendon-core' ),
			'url'         => home_url( '/projects' ),
		],
		[
			'label'       => esc_html__( 'Reflection', 'brendon-core' ),
			'description' => esc_html__( 'Notes on conviction, surrender, practice, and becoming.', 'brendon-core' ),
			'url'         => home_url( '/faith' ),
		],
		[
			'label'       => esc_html__( 'Connection', 'brendon-core' ),
			'description' => esc_html__( 'A direct path for thoughtful work and real conversation.', 'brendon-core' ),
			'url'         => home_url( '/contact' ),
		],
	];
}

/**
 * Retrieve project/build preview items.
 *
 * Uses posts from the selected category when configured, otherwise falls back
 * to three editable navigation-style links. This keeps the CMS simple.
 *
 * @param int $limit Number of items to return.
 * @return array
 */
function brendon_core_get_project_items( $limit = 3 ) {
	$category_id = absint( brendon_core_home_setting( 'projects_category' ) );
	$items       = [];

	if ( $category_id ) {
		$project_query = new WP_Query(
			[
				'cat'                 => $category_id,
				'posts_per_page'      => $limit,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
			]
		);

		while ( $project_query->have_posts() ) {
			$project_query->the_post();
			$items[] = [
				'label'       => get_the_title(),
				'description' => wp_trim_words( get_the_excerpt(), 18, '...' ),
				'url'         => get_permalink(),
			];
		}

		wp_reset_postdata();
	}

	if ( $items ) {
		return $items;
	}

	$defaults = brendon_core_default_project_links();

	foreach ( $defaults as $index => $default ) {
		$number  = $index + 1;
		$label   = get_theme_mod( "brendon_core_project_{$number}_label", $default['label'] );
		$summary = get_theme_mod( "brendon_core_project_{$number}_description", $default['description'] );
		$url     = get_theme_mod( "brendon_core_project_{$number}_url", $default['url'] );

		if ( ! $label || ! $url ) {
			continue;
		}

		$items[] = [
			'label'       => $label,
			'description' => $summary,
			'url'         => $url,
		];
	}

	return array_slice( $items, 0, $limit );
}

/**
 * Retrieve the sidebar menu items assigned to the sidebar location.
 *
 * @return array Array of WP_Post items representing the menu.
 */
function brendon_core_get_sidebar_menu_items() {
	$locations = get_nav_menu_locations();

	if ( empty( $locations['sidebar'] ) ) {
		return [];
	}

	$menu = wp_get_nav_menu_object( $locations['sidebar'] );

	if ( ! $menu ) {
		return [];
	}

	return wp_get_nav_menu_items( $menu->term_id ) ?: [];
}

/**
 * Retrieve a nav menu label assigned to a location.
 *
 * @param string $location Menu location key.
 * @return string
 */
function brendon_core_get_menu_label_by_location( $location ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return '';
	}

	$menu = wp_get_nav_menu_object( $locations[ $location ] );

	if ( ! $menu || empty( $menu->name ) ) {
		return '';
	}

	return $menu->name;
}

/**
 * Default sidebar social links and icon metadata.
 *
 * @return array
 */
function brendon_core_default_social_links() {
	return [
		[
			'label' => esc_html__( 'Twitter', 'brendon-core' ),
			'url'   => 'https://twitter.com/brendonbaugh',
			'icon'  => 'twitter',
		],
		[
			'label' => esc_html__( 'LinkedIn', 'brendon-core' ),
			'url'   => 'https://www.linkedin.com/in/brendonbaugh',
			'icon'  => 'linkedin',
		],
		[
			'label' => esc_html__( 'GitHub', 'brendon-core' ),
			'url'   => 'https://github.com/brendonbaugh',
			'icon'  => 'github',
		],
		[
			'label' => esc_html__( 'Instagram', 'brendon-core' ),
			'url'   => 'https://www.instagram.com/',
			'icon'  => 'instagram',
		],
	];
}

/**
 * Available social icon choices used in the Customizer.
 *
 * @return array
 */
function brendon_core_get_social_icon_choices() {
	return [
		'twitter'   => esc_html__( 'Twitter', 'brendon-core' ),
		'linkedin'  => esc_html__( 'LinkedIn', 'brendon-core' ),
		'github'    => esc_html__( 'GitHub', 'brendon-core' ),
		'instagram' => esc_html__( 'Instagram', 'brendon-core' ),
		'youtube'   => esc_html__( 'YouTube', 'brendon-core' ),
		'twitch'    => esc_html__( 'Twitch', 'brendon-core' ),
		'facebook'  => esc_html__( 'Facebook', 'brendon-core' ),
		'pinterest' => esc_html__( 'Pinterest', 'brendon-core' ),
		'dribbble'  => esc_html__( 'Dribbble', 'brendon-core' ),
		'behance'   => esc_html__( 'Behance', 'brendon-core' ),
		'tik-tok'   => esc_html__( 'TikTok', 'brendon-core' ),
		'link'      => esc_html__( 'Link', 'brendon-core' ),
	];
}

/**
 * Validates the selected icon name.
 *
 * @param string $value
 * @return string
 */
function brendon_core_sanitize_social_icon( $value ) {
	$choices = array_keys( brendon_core_get_social_icon_choices() );

	return in_array( $value, $choices, true ) ? $value : $choices[0];
}

/**
 * Retrieves the social links the sidebar should render.
 *
 * @return array
 */
function brendon_core_get_sidebar_social_links() {
	$defaults = brendon_core_default_social_links();
	$choices  = array_keys( brendon_core_get_social_icon_choices() );
	$links    = [];

	foreach ( $defaults as $index => $default ) {
		$label = get_theme_mod( "brendon_core_social_{$index}_label", $default['label'] );
		$url   = get_theme_mod( "brendon_core_social_{$index}_url", $default['url'] );
		$icon  = get_theme_mod( "brendon_core_social_{$index}_icon", $default['icon'] );

		if ( ! $label || ! $url ) {
			continue;
		}

		if ( ! in_array( $icon, $choices, true ) ) {
			$icon = $default['icon'];
		}

		$links[] = [
			'label' => sanitize_text_field( $label ),
			'url'   => esc_url_raw( $url ),
			'icon'  => $icon,
		];
	}

	return $links ?: $defaults;
}

/**
 * Provides default fallback pages for the sidebar when no menu is set.
 *
 * @return array
 */
function brendon_core_sidebar_fallback_pages() {
	return [
		[
			'label' => esc_html__( 'Home', 'brendon-core' ),
			'url'   => home_url( '/' ),
		],
		[
			'label' => esc_html__( 'Writing', 'brendon-core' ),
			'url'   => home_url( '/writing' ),
		],
		[
			'label' => esc_html__( 'Work', 'brendon-core' ),
			'url'   => home_url( '/work' ),
		],
		[
			'label' => esc_html__( 'About', 'brendon-core' ),
			'url'   => home_url( '/about' ),
		],
	];
}

/**
 * Returns SVG markup for a social icon.
 *
 * @param string $icon
 * @param string $classes
 * @return string
 */
function brendon_core_get_social_icon_svg( $icon, $classes = 'h-4 w-4' ) {
	switch ( $icon ) {
		case 'youtube':
			$path = 'M21.8 8.01c-.1-1.46-.69-2.58-1.93-3.23C17.49 4.21 12 4.21 12 4.21s-5.5 0-7.87.57c-1.24.65-1.82 1.77-1.93 3.23-.1 1.46-.1 4.48-.1 4.48s0 3.02.1 4.48c.11 1.46.69 2.58 1.93 3.23C6.5 19.59 12 19.59 12 19.59s5.49 0 7.88-.57c1.24-.65 1.82-1.77 1.93-3.23.1-1.46.1-4.48.1-4.48s0-3.02-.1-4.48zM10 15.02V9.09l5 2.96-5 2.97z';
			break;
		case 'facebook':
			$path = 'M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.99 3.66 9.13 8.44 9.87v-6.99H8.29V12h2.15v-2.2c0-1.33.68-2.2 1.98-2.2 1.1 0 1.63.08 1.91.12v2.21h-1.31c-1.03 0-1.23.49-1.23 1.21V12h2.5l-.41 2.88h-2.09v6.99C18.34 21.13 22 16.99 22 12z';
			break;
		case 'pinterest':
			$path = 'M12 2c-5.52 0-10 4.48-10 10 0 4.32 2.8 7.99 6.68 9.28-.09-.78-.17-1.97.04-2.82.18-.75 1.16-4.77 1.16-4.77s-.3-.61-.3-1.52c0-1.43.83-2.5 1.87-2.5.88 0 1.3.66 1.3 1.45 0 .88-.56 2.2-.85 3.43-.24 1.04.5 1.88 1.48 1.88 1.78 0 3.15-1.88 3.15-4.58 0-2.39-1.72-4.06-4.18-4.06-2.85 0-4.54 2.13-4.54 4.33 0 .86.33 1.78.74 2.28.08.1.09.21.07.32-.08.35-.26 1.11-.29 1.26-.04.21-.16.26-.37.16-1.37-.63-2.24-2.65-2.24-4.27 0-3.48 2.53-6.68 7.3-6.68 3.83 0 6.31 2.74 6.31 6.4 0 3.82-2.41 6.9-5.74 6.9-1.12 0-2.18-.58-2.54-1.26l-.69 2.63c-.25.95-.93 2.14-1.4 2.86C9.59 21.96 10.8 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z';
			break;
		case 'dribbble':
			$path = 'M21.77 8.08a8.96 8.96 0 014.24 5.48A17 17 0 0018.02 9.7a29.85 29.85 0 00-1.56-2.97 9 9 0 015.31-1.31zm-13.55-2.86a9 9 0 017.13-2.31c-.73.98-1.42 1.99-2.04 3.01-2.56-.46-4.88-.99-7.09-1.2.65-.78 1.49-1.39 2.5-1.5zm-2.81 6.78c0-1.89.76-3.61 2-4.82a41.3 41.3 0 016.18 6.3 32.25 32.25 0 00-6.73 2.63 9 9 0 00-1.45-4.11zm2.5 5.4a31.9 31.9 0 016.53-2.53 30.6 30.6 0 011.42 3.83 8.94 8.94 0 01-4.98 1.73c-1.68 0-3.24-.63-4.45-1.72.5-.6.95-1.26 1.48-2.31zm12.79 2.34c-.75-.95-1.45-2.12-1.98-3.37 1.87-.47 3.62-1.19 5.21-2.2a9.04 9.04 0 01.77 3.4c0 1.08-.23 2.1-.64 3.03-1.32.63-2.82.98-4.36.61z';
			break;
		case 'behance':
			$path = 'M2 6h8c1.1 0 2 .9 2 2s-.9 2-2 2H3v2h-1v-6zm9.5 0h5c1.66 0 3 1.34 3 3s-1.34 3-3 3h-4.5V6zm1.5 2v2h3c.55 0 1-.45 1-1s-.45-1-1-1h-3zm-9.5 5h9c1.1 0 2 .9 2 2s-.9 2-2 2H3v-4h-1zm17-9H15v10h5c1.66 0 3-1.34 3-3V6z';
			break;
		case 'twitch':
			$path = 'M5 3l-1 4v11h5v4l4-4h6l4-4V3H5zm15 9-2 2H11l-3 3V14H5V6h15v6zM8 9h3v4H8zm5 0h3v4h-3z';
			break;
		case 'tik-tok':
			$path = 'M22 7.7a5.47 5.47 0 01-1.58-.22 3.92 3.92 0 001.72-2.16 7.64 7.64 0 01-2.46.93A3.82 3.82 0 0014.5 6.5V2.88h-3.6v13.03a4.1 4.1 0 01-2.4-3.66 4.18 4.18 0 004.2 4.19 4.15 4.15 0 004.15-4.15V7.7h.05z';
			break;
		case 'twitter':
			$path = 'M22 4.01a8.15 8.15 0 01-2.357.646 4.127 4.127 0 001.806-2.27 8.18 8.18 0 01-2.605.996 4.091 4.091 0 00-6.982 3.732A11.604 11.604 0 014.27 3.504a4.09 4.09 0 001.266 5.459 4.082 4.082 0 01-1.852-.512v.052a4.09 4.09 0 003.28 4.009 4.11 4.11 0 01-1.848.07 4.09 4.09 0 003.823 2.837 8.195 8.195 0 01-2.36.647c.36 2.233 2.51 3.86 4.74 3.91-1.77 1.36-3.99 1.87-6.23 1.29A11.6 11.6 0 004.5 19.54C8 21.7 11.9 22.5 15.78 21.05 20.65 19.22 23.5 14.24 23.5 9.27c0-.18 0-.36-.01-.54A7.9 7.9 0 0022 4.01z';
			break;
		case 'linkedin':
			$path = 'M6.94 6.94a3.5 3.5 0 11-.002 7.002 3.5 3.5 0 01.002-7.002zM3.5 8.5h3v11h-3zM11 12.5v7.5h3v-7c0-1.78 1.441-3 3.25-3 1.808 0 3 1.141 3 3.054v6.946h3v-7.05C23.25 10.5 20.735 8 17.25 8 14.55 8 13 9.67 12.5 11.15h-.041V8.5h-3z';
			break;
		case 'github':
			$path = 'M12 0.5C5.64.5.5 5.64.5 12c0 5.07 3.29 9.36 7.86 10.88.58.1.79-.25.79-.56v-2.04c-3.2.7-3.87-1.53-3.87-1.53-.53-1.37-1.29-1.73-1.29-1.73-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.76 2.7 1.25 3.36.96.1-.75.4-1.25.72-1.54-2.55-.29-5.23-1.3-5.23-5.78 0-1.28.46-2.33 1.2-3.15-.12-.3-.52-1.51.12-3.15 0 0 .97-.31 3.18 1.21a10.94 10.94 0 015.78 0c2.21-1.53 3.18-1.21 3.18-1.21.64 1.64.24 2.85.12 3.15.74.82 1.2 1.87 1.2 3.15 0 4.49-2.69 5.49-5.25 5.78.41.35.77 1.01.77 2.04v3.02c0 .31.21.67.79.56A11.51 11.51 0 0023.5 12C23.5 5.64 18.36.5 12 .5z';
			break;
		case 'instagram':
			$path = 'M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm0 2h10c1.654 0 3 1.346 3 3v10c0 1.654-1.346 3-3 3H7c-1.654 0-3-1.346-3-3V7c0-1.654 1.346-3 3-3zm10 1.5a1 1 0 100 2 1 1 0 000-2zM12 7a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z';
			break;
		default:
			$path = 'M12 4a4 4 0 100 8 4 4 0 000-8zm0-2a6 6 0 100 12 6 6 0 000-12z';
			break;
	}

	return sprintf(
		'<svg viewBox="0 0 24 24" role="presentation" class="%s" fill="currentColor" aria-hidden="true"><path d="%s" /></svg>',
		esc_attr( $classes ),
		esc_attr( $path )
	);
}

/**
 * Base Tailwind classes for the sidebar menu links.
 *
 * @param bool $is_active Whether the menu item is the current page or ancestor.
 * @return string
 */
function brendon_core_sidebar_menu_base_classes( $is_active = false ) {
	$classes = [ 'bb-nav__link' ];

	if ( $is_active ) {
		$classes[] = 'bb-nav__link--active';
	}

	return implode( ' ', $classes );
}

/**
 * Add Tailwind-friendly classes to the sidebar menu items.
 *
 * @param array    $classes CSS classes applied to the menu item's <li>.
 * @param WP_Post  $item    The current menu item instance.
 * @param stdClass $args    An object of wp_nav_menu() arguments.
 * @param int      $depth   Depth of the menu item.
 * @return array
 */
function brendon_core_sidebar_menu_item_classes( $classes, $item, $args, $depth ) {
	if ( isset( $args->theme_location ) && in_array( $args->theme_location, [ 'sidebar', 'sidebar-secondary' ], true ) ) {
		$classes = array_filter( $classes, function ( $class ) {
			return false === strpos( $class, 'menu-' );
		} );
		$classes[] = 'list-none';
		$classes[] = 'bb-nav__item';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'brendon_core_sidebar_menu_item_classes', 20, 4 );

/**
 * Add Tailwind classes to sidebar menu anchor tags.
 *
 * @param array    $atts   Attributes for the menu item.
 * @param WP_Post  $item   The current menu item.
 * @param stdClass $args   An object of wp_nav_menu() arguments.
 * @param int      $depth  Depth of the menu item.
 * @return array
 */
function brendon_core_sidebar_menu_link_attributes( $atts, $item, $args, $depth ) {
	if ( isset( $args->theme_location ) && in_array( $args->theme_location, [ 'sidebar', 'sidebar-secondary' ], true ) ) {
		$is_current = in_array( 'current-menu-item', (array) $item->classes, true )
			|| in_array( 'current_page_parent', (array) $item->classes, true )
			|| in_array( 'current_page_ancestor', (array) $item->classes, true );

		$atts['class'] = brendon_core_sidebar_menu_base_classes( $is_current );

		if ( $is_current ) {
			$atts['aria-current'] = 'page';
		}
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'brendon_core_sidebar_menu_link_attributes', 20, 4 );

/**
 * Helper to determine whether a post is marked as a video post.
 *
 * @param int $post_id
 * @return bool
 */
function brendon_core_is_video_post( $post_id = 0 ) {
	$post_id = absint( $post_id ?: get_the_ID() );

	if ( ! $post_id ) {
		return false;
	}

	return '1' === get_post_meta( $post_id, 'brendon_core_is_video_post', true );
}

/**
 * Registers the meta box that lets editors flag a post as a video.
 */
function brendon_core_register_video_post_meta_box() {
	add_meta_box(
		'brendon_core_video_post',
		esc_html__( 'Video post', 'brendon-core' ),
		'brendon_core_render_video_post_meta_box',
		'post',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'brendon_core_register_video_post_meta_box' );

/**
 * Renders the video post checkbox UI.
 *
 * @param WP_Post $post
 */
function brendon_core_render_video_post_meta_box( $post ) {
	wp_nonce_field( 'brendon_core_video_post_meta', 'brendon_core_video_post_nonce' );

	printf(
		'<p><label><input type="checkbox" id="brendon_core_video_post_flag" name="brendon_core_video_post_flag" value="1" %s /> %s</label></p>',
		checked( brendon_core_is_video_post( $post->ID ), true, false ),
		esc_html__( 'Hide the featured image on the single post view.', 'brendon-core' )
	);

	echo '<p class="description">' . esc_html__( 'Use this when a post primarily features an embed/video rather than a cover image.', 'brendon-core' ) . '</p>';
}

/**
 * Saves the video post flag when the post is saved.
 *
 * @param int $post_id
 */
function brendon_core_save_video_post_meta_box( $post_id ) {
	if ( ! isset( $_POST['brendon_core_video_post_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['brendon_core_video_post_nonce'] ), 'brendon_core_video_post_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$is_video = isset( $_POST['brendon_core_video_post_flag'] );

	update_post_meta( $post_id, 'brendon_core_is_video_post', $is_video ? '1' : '0' );
}
add_action( 'save_post', 'brendon_core_save_video_post_meta_box' );

/**
 * Helper to determine whether a page should hide its featured image.
 *
 * @param int $post_id
 * @return bool
 */
function brendon_core_hide_page_featured_image( $post_id = 0 ) {
	$post_id = absint( $post_id ?: get_the_ID() );

	if ( ! $post_id ) {
		return false;
	}

	return '1' === get_post_meta( $post_id, 'brendon_core_hide_page_featured_image', true );
}

/**
 * Registers the meta box that lets editors hide a page featured image.
 */
function brendon_core_register_page_featured_image_meta_box() {
	add_meta_box(
		'brendon_core_page_featured_image',
		esc_html__( 'Featured image', 'brendon-core' ),
		'brendon_core_render_page_featured_image_meta_box',
		'page',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'brendon_core_register_page_featured_image_meta_box' );

/**
 * Renders the featured image checkbox UI for pages.
 *
 * @param WP_Post $post
 */
function brendon_core_render_page_featured_image_meta_box( $post ) {
	wp_nonce_field( 'brendon_core_page_featured_image_meta', 'brendon_core_page_featured_image_nonce' );

	printf(
		'<p><label><input type="checkbox" id="brendon_core_hide_page_featured_image" name="brendon_core_hide_page_featured_image" value="1" %s /> %s</label></p>',
		checked( brendon_core_hide_page_featured_image( $post->ID ), true, false ),
		esc_html__( 'Hide the featured image on this page.', 'brendon-core' )
	);
}

/**
 * Saves the page featured image flag when the page is saved.
 *
 * @param int $post_id
 */
function brendon_core_save_page_featured_image_meta_box( $post_id ) {
	if ( ! isset( $_POST['brendon_core_page_featured_image_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['brendon_core_page_featured_image_nonce'] ), 'brendon_core_page_featured_image_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$hide_featured = isset( $_POST['brendon_core_hide_page_featured_image'] );

	update_post_meta( $post_id, 'brendon_core_hide_page_featured_image', $hide_featured ? '1' : '0' );
}
add_action( 'save_post', 'brendon_core_save_page_featured_image_meta_box' );

/**
 * Handle contact form submissions.
 */
function brendon_core_handle_contact_form() {
	if ( ! isset( $_POST['brendon_core_contact_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['brendon_core_contact_nonce'] ), 'brendon_core_contact_form' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	$redirect_to = $redirect_to ? $redirect_to : home_url( '/' );

	$honeypot = isset( $_POST['contact_company'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) : '';
	if ( $honeypot ) {
		wp_safe_redirect( add_query_arg( 'contact', 'success', $redirect_to ) );
		exit;
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_subject'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	if ( ! $name || ! $email || ! is_email( $email ) || ! $message ) {
		wp_safe_redirect( add_query_arg( 'contact', 'invalid', $redirect_to ) );
		exit;
	}

	$subject = $subject ? $subject : sprintf(
		/* translators: %s: sender name. */
		esc_html__( 'New message from %s', 'brendon-core' ),
		$name
	);

	$body = sprintf(
		"Name: %s\nEmail: %s\n\nMessage:\n%s\n",
		$name,
		$email,
		$message
	);

	$headers = array(
		sprintf( 'Reply-To: %s <%s>', $name, $email ),
	);

	$sent = wp_mail( 'brendonbaughray@gmail.com', $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'contact', $sent ? 'success' : 'error', $redirect_to ) );
	exit;
}
add_action( 'admin_post_nopriv_brendon_core_contact_form', 'brendon_core_handle_contact_form' );
add_action( 'admin_post_brendon_core_contact_form', 'brendon_core_handle_contact_form' );
