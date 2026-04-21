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
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

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
	$wp_customize->remove_panel( 'widgets' );
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
 * Register controls to manage the sidebar socials list.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function brendon_core_customize_social_links( $wp_customize ) {
	$wp_customize->add_section(
		'brendon_core_social_links',
		[
			'title'       => esc_html__( 'Sidebar Social Links', 'brendon-core' ),
			'description' => esc_html__( 'Update which links appear in the sidebar card.', 'brendon-core' ),
			'priority'    => 140,
			'panel'       => 'brendon_core_theme',
		]
	);

	$defaults = brendon_core_default_social_links();
	$choices  = brendon_core_get_social_icon_choices();

	foreach ( $defaults as $index => $link ) {
		$wp_customize->add_setting(
			"brendon_core_social_{$index}_label",
			[
				'default'           => $link['label'],
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		$wp_customize->add_control(
			"brendon_core_social_{$index}_label",
			[
				'label'       => sprintf( esc_html__( 'Link %d label', 'brendon-core' ), $index + 1 ),
				'section'     => 'brendon_core_social_links',
				'type'        => 'text',
			]
		);

		$wp_customize->add_setting(
			"brendon_core_social_{$index}_url",
			[
				'default'           => $link['url'],
				'sanitize_callback' => 'esc_url_raw',
			]
		);

		$wp_customize->add_control(
			"brendon_core_social_{$index}_url",
			[
				'label'       => sprintf( esc_html__( 'Link %d URL', 'brendon-core' ), $index + 1 ),
				'section'     => 'brendon_core_social_links',
				'type'        => 'url',
			]
		);

		$wp_customize->add_setting(
			"brendon_core_social_{$index}_icon",
			[
				'default'           => $link['icon'],
				'sanitize_callback' => 'brendon_core_sanitize_social_icon',
			]
		);

		$wp_customize->add_control(
			"brendon_core_social_{$index}_icon",
			[
				'label'       => sprintf( esc_html__( 'Link %d icon', 'brendon-core' ), $index + 1 ),
				'description' => esc_html__( 'Select the icon that appears before the label.', 'brendon-core' ),
				'section'     => 'brendon_core_social_links',
				'type'        => 'select',
				'choices'     => $choices,
			]
		);
	}
}
add_action( 'customize_register', 'brendon_core_customize_social_links' );

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
			'description' => esc_html__( 'Manage the homepage copy, including the Current Season section and fallback project links.', 'brendon-core' ),
			'priority'    => 130,
			'panel'       => 'brendon_core_theme',
		]
	);

	$defaults = brendon_core_home_defaults();
	$textareas = [
		'hero_heading',
		'hero_lede',
		'what_heading',
		'what_body',
		'projects_heading',
		'projects_subheading',
		'season_heading',
		'season_body',
	];
	$urls = [
		'hero_primary_url',
		'hero_secondary_url',
		'writing_url',
	];
	$labels = [
		'hero_kicker'          => esc_html__( 'Hero kicker', 'brendon-core' ),
		'hero_heading'         => esc_html__( 'Hero heading', 'brendon-core' ),
		'hero_lede'            => esc_html__( 'Hero support copy', 'brendon-core' ),
		'hero_primary_label'   => esc_html__( 'Primary CTA label', 'brendon-core' ),
		'hero_primary_url'     => esc_html__( 'Primary CTA URL', 'brendon-core' ),
		'hero_secondary_label' => esc_html__( 'Secondary CTA label', 'brendon-core' ),
		'hero_secondary_url'   => esc_html__( 'Secondary CTA URL', 'brendon-core' ),
		'what_kicker'          => esc_html__( 'What This Is kicker', 'brendon-core' ),
		'what_heading'         => esc_html__( 'What This Is heading', 'brendon-core' ),
		'what_body'            => esc_html__( 'What This Is body', 'brendon-core' ),
		'writing_heading'      => esc_html__( 'Latest Writing heading', 'brendon-core' ),
		'writing_url'          => esc_html__( 'All Writing URL', 'brendon-core' ),
		'projects_kicker'      => esc_html__( 'Projects kicker', 'brendon-core' ),
		'projects_heading'     => esc_html__( 'Projects heading', 'brendon-core' ),
		'projects_subheading'  => esc_html__( 'Projects subheading', 'brendon-core' ),
		'season_kicker'        => esc_html__( 'Current Season kicker', 'brendon-core' ),
		'season_heading'       => esc_html__( 'Current Season heading', 'brendon-core' ),
		'season_body'          => esc_html__( 'Current Season body', 'brendon-core' ),
	];

	foreach ( $defaults as $key => $default ) {
		if ( 'projects_category' === $key ) {
			continue;
		}

		$wp_customize->add_setting(
			"brendon_core_home_{$key}",
			[
				'default'           => $default,
				'sanitize_callback' => in_array( $key, $urls, true ) ? 'esc_url_raw' : ( in_array( $key, $textareas, true ) ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
			]
		);

		$wp_customize->add_control(
			"brendon_core_home_{$key}",
			[
				'label'   => $labels[ $key ] ?? $key,
				'section' => 'brendon_core_homepage',
				'type'    => in_array( $key, $textareas, true ) ? 'textarea' : ( in_array( $key, $urls, true ) ? 'url' : 'text' ),
			]
		);
	}

	$wp_customize->add_setting(
		'brendon_core_home_projects_category',
		[
			'default'           => 0,
			'sanitize_callback' => 'absint',
		]
	);

	$category_choices = [ 0 => esc_html__( 'Use fallback links', 'brendon-core' ) ];
	foreach ( get_categories( [ 'hide_empty' => false ] ) as $category ) {
		$category_choices[ $category->term_id ] = $category->name;
	}

	$wp_customize->add_control(
		'brendon_core_home_projects_category',
		[
			'label'       => esc_html__( 'Projects category', 'brendon-core' ),
			'description' => esc_html__( 'Posts in this category will populate the Projects / Builds section. Leave empty to use fallback links.', 'brendon-core' ),
			'section'     => 'brendon_core_homepage',
			'type'        => 'select',
			'choices'     => $category_choices,
		]
	);

	$project_defaults = brendon_core_default_project_links();
	foreach ( $project_defaults as $index => $project ) {
		$number = $index + 1;
		foreach ( [ 'label', 'description', 'url' ] as $field ) {
			$setting = "brendon_core_project_{$number}_{$field}";
			$wp_customize->add_setting(
				$setting,
				[
					'default'           => $project[ $field ],
					'sanitize_callback' => 'url' === $field ? 'esc_url_raw' : 'sanitize_text_field',
				]
			);
			$wp_customize->add_control(
				$setting,
				[
					'label'   => sprintf(
						/* translators: 1: project number, 2: field label. */
						esc_html__( 'Fallback project %1$d %2$s', 'brendon-core' ),
						$number,
						$field
					),
					'section' => 'brendon_core_homepage',
					'type'    => 'description' === $field ? 'textarea' : ( 'url' === $field ? 'url' : 'text' ),
				]
			);
		}
	}
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
 * Customize the Live Now page settings.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function brendon_core_customize_live_now( $wp_customize ) {
	$wp_customize->add_section(
		'brendon_core_live_now',
		array(
			'title'       => esc_html__( 'Live Now', 'brendon-core' ),
			'description' => esc_html__( 'Configure the Twitch embeds and schedule.', 'brendon-core' ),
			'priority'    => 150,
			'panel'       => 'brendon_core_theme',
		)
	);

	$wp_customize->add_setting(
		'bb_live_twitch_channel',
		array(
			'default'           => 'mr__brights1de',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'bb_live_twitch_channel',
		array(
			'label'       => esc_html__( 'Twitch channel', 'brendon-core' ),
			'section'     => 'brendon_core_live_now',
			'type'        => 'text',
			'description' => esc_html__( 'Channel used for player and chat embeds. Env TWITCH_CHANNEL overrides this.', 'brendon-core' ),
		)
	);

	$wp_customize->add_setting(
		'bb_live_parent_domain',
		array(
			'default'           => 'brendonbaugh.com',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'bb_live_parent_domain',
		array(
			'label'       => esc_html__( 'Embed parent domain', 'brendon-core' ),
			'section'     => 'brendon_core_live_now',
			'type'        => 'text',
			'description' => esc_html__( 'Used for Twitch embed parent parameter. Env TWITCH_PARENT overrides this.', 'brendon-core' ),
		)
	);

	$wp_customize->add_setting(
		'bb_live_twitch_client_id',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'bb_live_twitch_client_id',
		array(
			'label'   => esc_html__( 'Twitch API Client ID', 'brendon-core' ),
			'section' => 'brendon_core_live_now',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'bb_live_twitch_client_secret',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'bb_live_twitch_client_secret',
		array(
			'label'   => esc_html__( 'Twitch API Client Secret', 'brendon-core' ),
			'section' => 'brendon_core_live_now',
			'type'    => 'password',
		)
	);

	$wp_customize->add_setting(
		'bb_live_schedule_json',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'bb_live_schedule_json',
		array(
			'label'       => esc_html__( 'Schedule JSON', 'brendon-core' ),
			'section'     => 'brendon_core_live_now',
			'type'        => 'textarea',
			'description' => esc_html__( 'Array of day/time/title objects, e.g. [{"day":"Wed","time":"8PM ET","title":"Cozy Chat"}].', 'brendon-core' ),
		)
	);
}
add_action( 'customize_register', 'brendon_core_customize_live_now' );

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
