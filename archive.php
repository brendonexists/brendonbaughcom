<?php
/**
 * Archive template.
 *
 * @package brendon-core
 */

get_header();

$archive_heading = get_the_archive_title();
$archive_heading = wp_strip_all_tags( preg_replace( '/^[^:]+:\s*/', '', $archive_heading ) );
?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<p class="bb-kicker"><?php esc_html_e('Archive', 'brendon-core'); ?></p>
			<h1><?php echo esc_html( $archive_heading ); ?></h1>
			<?php if ( get_the_archive_description() ) : ?>
				<div class="bb-page-hero__description">
					<?php the_archive_description(); ?>
				</div>
			<?php endif; ?>
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
