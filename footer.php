<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _s
 */

?>

		<footer class="bb-footer" role="contentinfo">
			<div class="bb-wrap bb-footer__grid">
				<div class="bb-footer__brand">
					<span class="bb-footer__mark" aria-hidden="true">
						<?php echo brendon_core_brand_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<div>
						<p class="bb-footer__eyebrow"><?php echo esc_html( brendon_core_footer_setting( 'eyebrow' ) ); ?></p>
						<p class="bb-footer__statement"><?php echo esc_html( brendon_core_footer_setting( 'statement' ) ); ?></p>
					</div>
				</div>

				<nav class="bb-footer__nav" aria-label="<?php echo esc_attr_x('Footer navigation', 'aria label', 'brendon-core'); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'bb-footer__links',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'brendon_core_footer_menu_fallback',
						)
					);
					?>
				</nav>

				<div class="bb-footer__small">
					<p>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?>.</p>
					<p><?php echo esc_html( brendon_core_footer_setting( 'tagline' ) ); ?></p>
				</div>
			</div>
		</footer>
	</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
