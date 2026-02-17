<?php

/**
 * Lightweight mobile navigation alternative for the sidebar.
 *
 * @package brendon-core
 */

$fallback_pages = brendon_core_sidebar_fallback_pages();
$social_links = brendon_core_get_sidebar_social_links();
?>

<details class="lg:hidden rounded-2xl border border-border bg-white p-4 shadow-sm transition">
	<summary class="flex items-center justify-between text-sm font-semibold text-slate-900">
		<span class="sr-only"><?php esc_html_e('Open mobile navigation', 'brendon-core'); ?></span>
		<svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<line x1="3" y1="6" x2="21" y2="6"></line>
			<line x1="3" y1="12" x2="21" y2="12"></line>
			<line x1="3" y1="18" x2="21" y2="18"></line>
		</svg>
	</summary>

	<div class="mt-4 space-y-4">
		<nav aria-label="<?php echo esc_attr_x('Mobile sidebar menu', 'aria label', 'brendon-core'); ?>">
			<?php if (has_nav_menu('sidebar')) : ?>
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'sidebar',
						'container'      => '',
						'menu_class'     => 'space-y-2',
						'depth'          => 1,
						'fallback_cb'    => false,
					]
				);
				?>
			<?php else : ?>
				<ul class="space-y-2">
					<?php foreach ($fallback_pages as $page) : ?>
						<li>
							<a class="block rounded-lg border border-border bg-primary/10 px-3 py-2 text-sm font-medium text-slate-900 transition hover:bg-primary/15" href="<?php echo esc_url($page['url']); ?>">
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
			<div class="space-y-2">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
					<?php echo esc_html( $sidebar_secondary_label ); ?>
				</p>
				<nav aria-label="<?php echo esc_attr_x( 'Mobile sidebar secondary menu', 'aria label', 'brendon-core' ); ?>">
					<?php
					wp_nav_menu(
						[
							'theme_location' => 'sidebar-secondary',
							'container'      => '',
							'menu_class'     => 'space-y-2',
							'depth'          => 1,
							'fallback_cb'    => false,
						]
					);
					?>
				</nav>
			</div>
		<?php endif; ?>

		<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="space-y-2">
			<label class="sr-only" for="mobile-menu-search"><?php esc_html_e('Search posts', 'brendon-core'); ?></label>
			<div class="flex gap-2">
				<input id="mobile-menu-search" name="s" type="search" placeholder="<?php esc_attr_e('Search the archive…', 'brendon-core'); ?>" class="flex-1 rounded-lg border border-border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/40" />
				<button type="submit" class="rounded-lg border border-transparent bg-primary px-3 py-2 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-primary-hover"><?php esc_html_e('Go', 'brendon-core'); ?></button>
			</div>
		</form>

		<div class="rounded-lg border border-border bg-surface-2 p-3">
			<p class="text-xs font-semibold uppercase tracking-wide text-slate-500"><?php esc_html_e('Social links', 'brendon-core'); ?></p>
			<div class="mt-3 flex flex-wrap items-center justify-center gap-3 text-slate-700">
				<?php foreach ($social_links as $social) : ?>
					<a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="flex h-11 w-11 items-center justify-center rounded-full border border-border bg-white text-primary transition hover:bg-primary/10">
						<span class="sr-only"><?php echo esc_html($social['label']); ?></span>
						<span class="flex h-5 w-5 items-center justify-center">
							<?php echo brendon_core_get_social_icon_svg($social['icon'], 'h-4 w-4'); ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</details>
