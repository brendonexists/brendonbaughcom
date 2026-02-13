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

<div class="sidebar-panel flex flex-col gap-6 rounded-2xl border border-[#F2A25C]/30 bg-white p-6 shadow-sm dark:bg-slate-900 dark:border-[#F2A25C]/20">
	<button type="button" data-theme-toggle class="sidebar-theme-toggle">
		<span class="sr-only"><?php esc_html_e( 'Toggle light and dark mode', 'brendon-core' ); ?></span>
		<span class="inline-flex items-center gap-2">
			<svg class="h-5 w-5 text-[#F26D3D] dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"></circle><path d="M12 1v2M12 21v2M4.22 4.22l1.41 1.41M18.36 18.36l1.41 1.41M1 12h2M21 12h2M4.22 19.78l1.41-1.41M18.36 5.64l1.41-1.41"></path></svg>
			<svg class="hidden h-5 w-5 text-[#F26D3D] dark:inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 0111.21 3 7 7 0 1019 12.79z"></path></svg>
			<span class="sr-only"><?php esc_html_e( 'Toggle theme', 'brendon-core' ); ?></span>
		</span>
	</button>

	<div class="sidebar-panel__content">
	<?php
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) :
		$logo_img = wp_get_attachment_image(
			$custom_logo_id,
			'medium',
			false,
			[
				'class' => 'h-full w-full object-cover',
				'alt'   => esc_attr( $site_title ),
			]
		);
		?>
		<div class="flex justify-center">
			<a class="sidebar-avatar h-[180px] w-[180px] overflow-hidden rounded-full border border-[#F2A25C]/20 bg-white shadow-sm dark:border-[#F2A25C]/30 dark:bg-slate-900" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $site_title ); ?>">
				<?php echo $logo_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<div class="text-center space-y-1">
			<h2 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white"><?php echo esc_html( $site_title ); ?></h2>
			<?php if ( $site_description ) : ?>
				<p class="text-sm text-slate-600 dark:text-slate-300"><?php echo esc_html( $site_description ); ?></p>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="space-y-1">
			<h2 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white"><?php echo esc_html( $site_title ); ?></h2>
			<?php if ( $site_description ) : ?>
				<p class="text-sm text-slate-600 dark:text-slate-300"><?php echo esc_html( $site_description ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<nav class="space-y-2" aria-label="<?php echo esc_attr_x('Sidebar menu', 'aria label', 'brendon-core'); ?>">
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

	<?php if ( has_nav_menu( 'sidebar-secondary' ) ) : ?>
		<?php
		$sidebar_secondary_label = brendon_core_get_menu_label_by_location( 'sidebar-secondary' );
		if ( ! $sidebar_secondary_label ) {
			$sidebar_secondary_label = esc_html__( 'More links', 'brendon-core' );
		}
		?>
		<div class="space-y-2">
			<p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
				<?php echo esc_html( $sidebar_secondary_label ); ?>
			</p>
			<nav class="space-y-2" aria-label="<?php echo esc_attr_x( 'Sidebar secondary menu', 'aria label', 'brendon-core' ); ?>">
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

	<form role="search" method="get" class="space-y-2" action="<?php echo esc_url(home_url('/')); ?>">
	<label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300" for="sidebar-search"><?php esc_html_e('Search posts', 'brendon-core'); ?></label>
		<div class="flex">
			<input id="sidebar-search" name="s" type="search" placeholder="<?php esc_attr_e('Search the archive…', 'brendon-core'); ?>" class="w-full rounded-l-lg border border-[#F2A25C]/30 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#F2A25C] focus:ring-2 focus:ring-[#F2A25C]/40 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-700 dark:placeholder:text-slate-500" />
			<button type="submit" class="rounded-r-lg border border-transparent bg-[#F26D3D] px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-[#F24E29] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F2A25C]/70"><?php esc_html_e('Go', 'brendon-core'); ?></button>
		</div>
	</form>

		<div class="flex items-center justify-between">
			<p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e('Socials', 'brendon-core'); ?></p>
		</div>
		<ul class="sidebar-socials__list mt-3 text-sm text-slate-700 dark:text-slate-200">
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

	<div class="mt-4 text-xs text-center text-slate-500 dark:text-slate-400">
		<?php printf( wp_kses_post( __( 'Theme made by <a class="font-semibold text-[#F26D3D] hover:text-[#F24E29]" href="%1$s">Brendon Baugh</a>.', 'brendon-core' ) ), esc_url( 'https://brendonbaugh.com' ) ); ?>
	</div>

	</div>
</div>
<script>
	(function () {
		if ( window.__brendonThemeToggleInitialized ) {
			return;
		}
		window.__brendonThemeToggleInitialized = true;
		const root = document.documentElement;
		const STORAGE_KEY = 'brendon_theme_mode';
		const switches = Array.from( document.querySelectorAll( '[data-theme-toggle]' ) );
		if ( ! switches.length ) {
			return;
		}
		const switchToLight = '<?php echo esc_js( esc_html__( 'Switch to light mode', 'brendon-core' ) ); ?>';
		const switchToDark  = '<?php echo esc_js( esc_html__( 'Switch to dark mode', 'brendon-core' ) ); ?>';
		const applyMode = ( mode ) => {
			const isDark = 'dark' === mode;
			root.classList.toggle( 'dark', isDark );
			localStorage.setItem( STORAGE_KEY, mode );
			switches.forEach( ( button ) => {
				button.setAttribute( 'aria-label', isDark ? switchToLight : switchToDark );
			} );
		};
		let initial = localStorage.getItem( STORAGE_KEY );
		if ( ! initial ) {
			const prefersDark = window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches;
			initial = prefersDark ? 'dark' : 'light';
		}
		applyMode( initial );
		switches.forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				const nextMode = root.classList.contains( 'dark' ) ? 'light' : 'dark';
				applyMode( nextMode );
			} );
		} );
	} )();
</script>
