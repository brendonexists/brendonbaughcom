<?php

/**
 * Sidebar panel with site info, navigation, and search.
 *
 * @package brendon-core
 */

$site_title = get_bloginfo('name');
$site_description = get_bloginfo('description');
$custom_logo_id = get_theme_mod('custom_logo');
$logo_img = '';
$has_logo = false;
$flap_width = 12;

if ( $custom_logo_id ) {
	$has_logo = true;
	$logo_img = wp_get_attachment_image(
		$custom_logo_id,
		'medium',
		false,
		[
			'class' => 'h-full w-full object-cover',
			'alt'   => esc_attr( $site_title ),
		]
	);
}
$fallback_pages = brendon_core_sidebar_fallback_pages();
$social_links = brendon_core_get_sidebar_social_links();
?>

<div class="sidebar-panel flex flex-col gap-6 rounded-2xl border border-border bg-white p-6 shadow-sm">

	<div class="sidebar-panel__content">
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

		<nav class="bb-nav bb-nav--primary pt-6" aria-label="<?php echo esc_attr_x('Sidebar menu', 'aria label', 'brendon-core'); ?>">
			<?php if (has_nav_menu('sidebar')) : ?>
				<?php
				wp_nav_menu([
					'theme_location' => 'sidebar',
					'container' => false,
					'menu_class' => 'bb-nav__list',
					'depth' => 1,
					'fallback_cb' => false,
				]);
				?>
			<?php else : ?>
				<?php $sidebar_menu_link_classes = brendon_core_sidebar_menu_base_classes(); ?>
				<ul class="bb-nav__list">
					<?php foreach ($fallback_pages as $page) : ?>
						<li class="bb-nav__item">
							<a href="<?php echo esc_url($page['url']); ?>" class="<?php echo esc_attr($sidebar_menu_link_classes); ?>">
								<?php echo esc_html($page['label']); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>

		<?php if (has_nav_menu('sidebar-secondary')) : ?>
			<?php
			$sidebar_secondary_label = brendon_core_get_menu_label_by_location('sidebar-secondary');
			if (! $sidebar_secondary_label) {
				$sidebar_secondary_label = esc_html__('More links', 'brendon-core');
			}
			?>
			<div class="bb-nav__section">
				<p class="bb-nav__meta">
					<?php echo esc_html($sidebar_secondary_label); ?>
				</p>
				<nav class="bb-nav" aria-label="<?php echo esc_attr_x('Sidebar secondary menu', 'aria label', 'brendon-core'); ?>">
					<?php
					wp_nav_menu([
						'theme_location' => 'sidebar-secondary',
						'container' => false,
						'menu_class' => 'bb-nav__list',
						'depth' => 1,
						'fallback_cb' => false,
					]);
					?>
				</nav>
			</div>
		<?php endif; ?>


		<!-- <div class="flex items-center justify-between">
			<p class="text-xs font-semibold uppercase tracking-wide text-slate-500"><?php esc_html_e('Socials', 'brendon-core'); ?></p>
		</div> -->
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
		<div class="flex justify-center mt-0 pt-0 mb-0 pb-0">
			<a class="sidebar-avatar h-[180px] w-[180px] overflow-hidden rounded-full border border-border bg-white shadow-sm" data-crt-avatar href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_title); ?>">
				<?php echo $logo_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<div class="mt-4 text-xs text-center text-slate-500">
			<?php printf(wp_kses_post(__('Theme made by <a class="font-semibold text-primary hover:text-primary-hover" href="%1$s">Brendon Baugh</a>.', 'brendon-core')), esc_url('https://brendonbaugh.com')); ?>
		</div>

	</div>
</div>
