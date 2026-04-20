<?php
/**
 * Template Name: Projects
 *
 * Used automatically for a page with the slug "projects".
 *
 * @package brendon-core
 */

get_header();

$project_items = brendon_core_get_project_items( 12 );
?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<p class="bb-kicker"><?php esc_html_e( 'Projects / Builds', 'brendon-core' ); ?></p>
			<h1><?php esc_html_e( 'The useful things.', 'brendon-core' ); ?></h1>
			<p><?php esc_html_e( 'Systems, experiments, repairs, and practical work with enough paper trail to be useful later.', 'brendon-core' ); ?></p>
		</div>
	</section>

	<section class="bb-section">
		<div class="bb-wrap">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="bb-content bb-projects-intro">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			<?php endwhile; ?>

			<?php if ( $project_items ) : ?>
				<div class="bb-build-list bb-build-list--grid">
					<?php foreach ( $project_items as $project ) : ?>
						<a href="<?php echo esc_url( $project['url'] ); ?>">
							<span><?php echo esc_html( $project['label'] ); ?></span>
							<strong><?php echo esc_html( $project['description'] ); ?></strong>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="bb-empty">
					<h2><?php esc_html_e( 'No builds logged yet.', 'brendon-core' ); ?></h2>
					<p><?php esc_html_e( 'When project records are published, they will collect here.', 'brendon-core' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
