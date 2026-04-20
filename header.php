<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _s
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', '_s'); ?></a>

		<header class="bb-header" role="banner">
			<div class="bb-wrap bb-header__inner">
				<a class="bb-brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
					<span class="bb-brand__mark" aria-hidden="true">
						<?php echo brendon_core_brand_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="bb-brand__text">
						<span class="bb-brand__name"><?php bloginfo('name'); ?></span>
						<span class="bb-brand__line"><?php bloginfo('description'); ?></span>
					</span>
				</a>

				<nav id="site-navigation" class="bb-primary-nav" aria-label="<?php echo esc_attr_x('Primary navigation', 'aria label', 'brendon-core'); ?>">
					<button class="bb-menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
						<span class="bb-menu-toggle__bars" aria-hidden="true"></span>
						<span><?php esc_html_e('Menu', 'brendon-core'); ?></span>
					</button>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'bb-primary-nav__list',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'brendon_core_primary_menu_fallback',
						)
					);
					?>
				</nav>
			</div>
		</header>
