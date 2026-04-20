<?php
/**
 * Blog index template.
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<p class="bb-kicker"><?php esc_html_e('Writing / Journal / Logs', 'brendon-core'); ?></p>
			<h1><?php esc_html_e('The public record.', 'brendon-core'); ?></h1>
			<p><?php esc_html_e('Notes from faith, family, discipline, work, building, and the creative process.', 'brendon-core'); ?></p>
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
				<?php get_template_part( 'template-parts/content-none' ); ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
