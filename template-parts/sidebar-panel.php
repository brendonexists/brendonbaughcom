<?php

/**
 * Sidebar panel with site info, navigation, and search.
 *
 * @package brendon-core
 */

$site_title = get_bloginfo('name');
$site_description = get_bloginfo('description');
$fallback_pages = brendon_core_sidebar_fallback_pages();
$social_links = brendon_core_get_sidebar_social_links();
?>

<div class="sidebar-panel flex flex-col gap-6 rounded-2xl border border-border bg-white p-6 shadow-sm">

	<div class="sidebar-panel__content">
		<?php
		$custom_logo_id = get_theme_mod('custom_logo');
		if ($custom_logo_id) :
			$logo_img = wp_get_attachment_image(
				$custom_logo_id,
				'medium',
				false,
				[
					'class' => 'h-full w-full object-cover',
					'alt'   => esc_attr($site_title),
				]
			);
		?>

			<div class="text-center">
				<h1 class="site-title text-2xl font-extrabold tracking-[0.04em] leading-tight text-slate-900 font-display">
					<a class="site-title__link" href="<?php echo esc_url(home_url('/')); ?>">
						<span class="site-title__name uppercase"><?php echo esc_html($site_title); ?></span><span class="bb-flap" aria-hidden="true"></span>
						<span class="sr-only" id="bbFlapSR"><?php printf(esc_html__('%s exists', 'brendon-core'), esc_html($site_title)); ?></span>
					</a>
				</h1>
			</div>
			<?php if ($site_description) : ?>
				<p class="text-sm text-slate-600 text-center mt-0"><?php echo esc_html($site_description); ?></p>
			<?php endif; ?>

		<?php else : ?>
			<div class="space-y-1">
				<h1 class="site-title text-2xl font-extrabold tracking-[0.04em] leading-tight text-slate-900 font-display">
					<a class="site-title__link" href="<?php echo esc_url(home_url('/')); ?>">
						<span class="site-title__name uppercase"><?php echo esc_html($site_title); ?></span><span class="bb-flap" aria-hidden="true"></span>
						<span class="sr-only" id="bbFlapSR"><?php printf(esc_html__('%s exists', 'brendon-core'), esc_html($site_title)); ?></span>
					</a>
				</h1>
				<?php if ($site_description) : ?>
					<p class="text-sm text-slate-600"><?php echo esc_html($site_description); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<nav class="space-y-2 pt-6" aria-label="<?php echo esc_attr_x('Sidebar menu', 'aria label', 'brendon-core'); ?>">
			<?php if (has_nav_menu('sidebar')) : ?>
				<?php
				wp_nav_menu([
					'theme_location' => 'sidebar',
					'container' => false,
					'menu_class' => 'space-y-2',
					'depth' => 1,
					'fallback_cb' => false,
				]);
				?>
			<?php else : ?>
				<?php $sidebar_menu_link_classes = brendon_core_sidebar_menu_base_classes(); ?>
				<ul class="space-y-2">
					<?php foreach ($fallback_pages as $page) : ?>
						<li>
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
			<div class="space-y-2">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
					<?php echo esc_html($sidebar_secondary_label); ?>
				</p>
				<nav class="space-y-2" aria-label="<?php echo esc_attr_x('Sidebar secondary menu', 'aria label', 'brendon-core'); ?>">
					<?php
					wp_nav_menu([
						'theme_location' => 'sidebar-secondary',
						'container' => false,
						'menu_class' => 'space-y-2',
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
				<?php echo $logo_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</a>
		</div>
		<div class="mt-4 text-xs text-center text-slate-500">
			<?php printf(wp_kses_post(__('Theme made by <a class="font-semibold text-primary hover:text-primary-hover" href="%1$s">Brendon Baugh</a>.', 'brendon-core')), esc_url('https://brendonbaugh.com')); ?>
		</div>

	</div>
</div>
