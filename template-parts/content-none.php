<?php
/**
 * Empty state template.
 *
 * @package brendon-core
 */
?>

<section class="bb-empty no-results not-found">
	<h2><?php esc_html_e( 'Nothing in the record yet.', 'brendon-core' ); ?></h2>

	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %s: new post URL. */
					__( 'The archive starts with the first entry. <a href="%s">Write it now</a>.', 'brendon-core' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( admin_url( 'post-new.php' ) )
			);
			?>
		</p>
	<?php elseif ( is_search() ) : ?>
		<p><?php esc_html_e( 'That search did not match anything public. Try a simpler phrase.', 'brendon-core' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There is nothing published here yet. The page is ready when the work is.', 'brendon-core' ); ?></p>
	<?php endif; ?>
</section>
