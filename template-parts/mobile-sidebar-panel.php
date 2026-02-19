<?php

/**
 * Lightweight mobile navigation alternative for the sidebar.
 *
 * @package brendon-core
 */

$site_title = get_bloginfo( 'name' );
$site_description = get_bloginfo( 'description' );
$custom_logo_id = get_theme_mod( 'custom_logo' );
$has_logo = false;
$flap_width = 10;

if ( $custom_logo_id ) {
	$has_logo = true;
}

$fallback_pages = brendon_core_sidebar_fallback_pages();
$social_links = brendon_core_get_sidebar_social_links();
?>

<details class="bb-mobile-panel lg:hidden rounded-2xl border border-border bg-white p-6 shadow-sm transition" data-bb-mobile-menu>
	<summary class="bb-mobile-panel__summary flex items-center justify-between text-sm font-semibold text-slate-900" aria-controls="bb-mobile-menu-panel" aria-expanded="false">
		<span class="sr-only"><?php esc_html_e('Open mobile navigation', 'brendon-core'); ?></span>
		<svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<line x1="3" y1="6" x2="21" y2="6"></line>
			<line x1="3" y1="12" x2="21" y2="12"></line>
			<line x1="3" y1="18" x2="21" y2="18"></line>
		</svg>
	</summary>

	<div class="bb-mobile-panel__body" id="bb-mobile-menu-panel">
		<?php
		get_template_part(
			'template-parts/sidebar-header',
			null,
			[
				'site_title'       => $site_title,
				'site_description' => $site_description,
				'has_logo'         => $has_logo,
				'flap_width'       => $flap_width,
			]
		);
		?>

		<nav class="bb-nav bb-nav--primary pt-6" aria-label="<?php echo esc_attr_x('Mobile sidebar menu', 'aria label', 'brendon-core'); ?>">
			<?php if (has_nav_menu('sidebar')) : ?>
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'sidebar',
						'container'      => '',
						'menu_class'     => 'bb-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					]
				);
				?>
			<?php else : ?>
				<?php $sidebar_menu_link_classes = brendon_core_sidebar_menu_base_classes(); ?>
				<ul class="bb-nav__list">
					<?php foreach ($fallback_pages as $page) : ?>
						<li class="bb-nav__item">
							<a class="<?php echo esc_attr($sidebar_menu_link_classes); ?>" href="<?php echo esc_url($page['url']); ?>">
								<?php echo esc_html($page['label']); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>

		<?php if ( has_nav_menu( 'sidebar-secondary' ) ) : ?>
			<?php
			$sidebar_secondary_label = brendon_core_get_menu_label_by_location( 'sidebar-secondary' );
			if ( ! $sidebar_secondary_label ) {
				$sidebar_secondary_label = esc_html__( 'More links', 'brendon-core' );
			}
			?>
			<div class="bb-nav__section">
				<p class="bb-nav__meta">
					<?php echo esc_html( $sidebar_secondary_label ); ?>
				</p>
				<nav class="bb-nav" aria-label="<?php echo esc_attr_x( 'Mobile sidebar secondary menu', 'aria label', 'brendon-core' ); ?>">
					<?php
					wp_nav_menu(
						[
							'theme_location' => 'sidebar-secondary',
							'container'      => '',
							'menu_class'     => 'bb-nav__list',
							'depth'          => 1,
							'fallback_cb'    => false,
						]
					);
					?>
				</nav>
			</div>
		<?php endif; ?>

		<ul class="sidebar-socials__list mt-3 text-sm text-slate-700">
			<?php foreach ($social_links as $social) : ?>
				<li class="sidebar-socials__item">
					<a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="sidebar-socials__link">
						<span class="sidebar-socials__icon">
							<?php echo brendon_core_get_social_icon_svg($social['icon'], 'h-4 w-4'); ?>
						</span>
						<span class="sr-only"><?php echo esc_html($social['label']); ?></span>
						<span class="sidebar-socials__tooltip" role="tooltip"><?php echo esc_html($social['label']); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</details>
