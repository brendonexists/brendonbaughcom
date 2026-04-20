<?php
/**
 * Search results template.
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<p class="bb-kicker"><?php esc_html_e('Search', 'brendon-core'); ?></p>
			<h1>
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Results for "%s"', 'brendon-core' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
			<?php get_search_form(); ?>
		</div>
	</section>

	<section class="bb-section">
		<div class="bb-wrap">
			<?php if ( have_posts() ) : ?>
				<div class="bb-card-grid bb-card-grid--archive">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/card-grid' );
					endwhile;
					?>
				</div>

				<?php get_template_part( 'template-parts/pagination' ); ?>
			<?php else : ?>
				<div class="bb-empty">
					<h2><?php esc_html_e('Nothing matched that search.', 'brendon-core'); ?></h2>
					<p><?php esc_html_e('Try a simpler phrase or browse the writing archive.', 'brendon-core'); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
