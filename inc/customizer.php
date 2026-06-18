<?php
/**
 * _s Theme Customizer
 *
 * @package _s
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function _s_customize_register( $wp_customize ) {
	foreach ( [ 'blogname', 'blogdescription', 'header_textcolor' ] as $setting_id ) {
		$setting = $wp_customize->get_setting( $setting_id );

		if ( $setting ) {
			$setting->transport = 'postMessage';
		}
	}

	$wp_customize->add_panel(
		'brendon_core_theme',
		[
			'title'       => esc_html__( 'Brendon Core', 'brendon-core' ),
			'description' => esc_html__( 'Theme-managed content and settings for the custom sections on the site.', 'brendon-core' ),
			'priority'    => 125,
		]
	);

	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'header_image' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'custom_css' );

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => '_s_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => '_s_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', '_s_customize_register' );

/**
 * Disable Customizer widgets using the supported component filter.
 *
 * @param array $components Customizer components to load.
 * @return array
 */
function brendon_core_customize_loaded_components( $components ) {
	return array_values( array_diff( $components, [ 'widgets' ] ) );
}
add_filter( 'customize_loaded_components', 'brendon_core_customize_loaded_components' );

/**
 * Register homepage identity controls.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function brendon_core_customize_homepage( $wp_customize ) {
	$wp_customize->add_section(
		'brendon_core_homepage',
		[
			'title'       => esc_html__( 'Homepage', 'brendon-core' ),
			'description' => esc_html__( 'Manage the retro homepage hub: wordmark, pillar labels, quick-link tiles, and latest-writing copy.', 'brendon-core' ),
			'priority'    => 130,
			'panel'       => 'brendon_core_theme',
		]
	);

	$defaults = brendon_core_home_defaults();
	$icons    = brendon_core_home_icon_choices();
	$controls = [
		'wordmark'             => [
			'label'       => esc_html__( 'Wordmark text', 'brendon-core' ),
			'description' => esc_html__( 'Large text displayed at the top of the homepage.', 'brendon-core' ),
			'type'        => 'text',
		],
		'latest_kicker'        => [
			'label' => esc_html__( 'Latest writing kicker', 'brendon-core' ),
			'type'  => 'text',
		],
		'latest_heading'       => [
			'label' => esc_html__( 'Latest writing heading', 'brendon-core' ),
			'type'  => 'text',
		],
		'latest_archive_label' => [
			'label' => esc_html__( 'Latest writing link label', 'brendon-core' ),
			'type'  => 'text',
		],
		'writing_url'          => [
			'label' => esc_html__( 'Latest writing link URL', 'brendon-core' ),
			'type'  => 'url',
		],
	];

	foreach ( $controls as $key => $control ) {
		$wp_customize->add_setting(
			"brendon_core_home_{$key}",
			[
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'url' === $control['type'] ? 'esc_url_raw' : 'sanitize_text_field',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_{$key}",
			[
				'label'       => $control['label'],
				'description' => $control['description'] ?? '',
				'section'     => 'brendon_core_homepage',
				'type'        => $control['type'],
			]
		);
	}

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'brendon_core_home_pillars_heading',
			[
				'label'       => esc_html__( 'Pillar labels', 'brendon-core' ),
				'description' => esc_html__( 'These labels appear below the homepage wordmark. Icons are fixed by the theme.', 'brendon-core' ),
				'section'     => 'brendon_core_homepage',
				'type'        => 'hidden',
			]
		)
	);

	foreach ( brendon_core_home_pillar_defaults() as $index => $pillar ) {
		$wp_customize->add_setting(
			"brendon_core_home_pillar_{$index}_label",
			[
				'default'           => $pillar['label'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_pillar_{$index}_label",
			[
				'label'   => sprintf(
					/* translators: %d: pillar number. */
					esc_html__( 'Pillar %d label', 'brendon-core' ),
					$index + 1
				),
				'section' => 'brendon_core_homepage',
				'type'    => 'text',
			]
		);

		$wp_customize->add_setting(
			"brendon_core_home_pillar_{$index}_icon",
			[
				'default'           => $pillar['icon'],
				'sanitize_callback' => 'brendon_core_sanitize_home_icon',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_pillar_{$index}_icon",
			[
				'label'       => sprintf(
					/* translators: %d: pillar number. */
					esc_html__( 'Pillar %d icon', 'brendon-core' ),
					$index + 1
				),
				'description' => esc_html__( 'Select an icon from the theme icon library.', 'brendon-core' ),
				'section'     => 'brendon_core_homepage',
				'type'        => 'select',
				'choices'     => $icons,
			]
		);
	}

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'brendon_core_home_quick_links_heading',
			[
				'label'       => esc_html__( 'Quick-link tiles', 'brendon-core' ),
				'description' => esc_html__( 'These six tiles appear in the Explore area. Leave a label or URL empty to hide that tile.', 'brendon-core' ),
				'section'     => 'brendon_core_homepage',
				'type'        => 'hidden',
			]
		)
	);

	foreach ( brendon_core_home_quick_link_defaults() as $index => $tile ) {
		$number = $index + 1;

		$wp_customize->add_setting(
			"brendon_core_home_quick_link_{$index}_label",
			[
				'default'           => $tile['label'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_quick_link_{$index}_label",
			[
				'label'   => sprintf(
					/* translators: %d: tile number. */
					esc_html__( 'Tile %d label', 'brendon-core' ),
					$number
				),
				'section' => 'brendon_core_homepage',
				'type'    => 'text',
			]
		);

		$wp_customize->add_setting(
			"brendon_core_home_quick_link_{$index}_url",
			[
				'default'           => $tile['url'],
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_quick_link_{$index}_url",
			[
				'label'   => sprintf(
					/* translators: %d: tile number. */
					esc_html__( 'Tile %d URL', 'brendon-core' ),
					$number
				),
				'section' => 'brendon_core_homepage',
				'type'    => 'url',
			]
		);

		$wp_customize->add_setting(
			"brendon_core_home_quick_link_{$index}_icon",
			[
				'default'           => $tile['icon'],
				'sanitize_callback' => 'brendon_core_sanitize_home_icon',
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_quick_link_{$index}_icon",
			[
				'label'       => sprintf(
					/* translators: %d: tile number. */
					esc_html__( 'Tile %d icon', 'brendon-core' ),
					$number
				),
				'description' => esc_html__( 'Select an icon from the theme icon library.', 'brendon-core' ),
				'section'     => 'brendon_core_homepage',
				'type'        => 'select',
				'choices'     => $icons,
			]
		);
	}

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'brendon_core_home_menu_note',
			[
				'label'       => esc_html__( 'Navigation', 'brendon-core' ),
				'description' => esc_html__( 'Header and footer navigation are managed in Appearance > Menus using the Primary and Footer menu locations.', 'brendon-core' ),
				'section'     => 'brendon_core_homepage',
				'type'        => 'hidden',
			]
		)
	);
}
add_action( 'customize_register', 'brendon_core_customize_homepage' );

/**
 * Register footer copy controls.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function brendon_core_customize_footer( $wp_customize ) {
	$wp_customize->add_section(
		'brendon_core_footer',
		[
			'title'       => esc_html__( 'Footer', 'brendon-core' ),
			'description' => esc_html__( 'Manage the editable copy shown in the site footer.', 'brendon-core' ),
			'priority'    => 145,
			'panel'       => 'brendon_core_theme',
		]
	);

	$defaults = brendon_core_footer_defaults();
	$labels   = [
		'eyebrow'   => esc_html__( 'Footer eyebrow', 'brendon-core' ),
		'statement' => esc_html__( 'Footer statement', 'brendon-core' ),
		'tagline'   => esc_html__( 'Footer tagline', 'brendon-core' ),
	];

	foreach ( $defaults as $key => $default ) {
		$wp_customize->add_setting(
			"brendon_core_footer_{$key}",
			[
				'default'           => $default,
				'sanitize_callback' => 'sanitize_textarea_field',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			"brendon_core_footer_{$key}",
			[
				'label'   => $labels[ $key ] ?? $key,
				'section' => 'brendon_core_footer',
				'type'    => 'textarea',
			]
		);
	}
}
add_action( 'customize_register', 'brendon_core_customize_footer' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function _s_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function _s_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function _s_customize_preview_js() {
	wp_enqueue_script( '_s-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', '_s_customize_preview_js' );
