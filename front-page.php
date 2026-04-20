<?php
/**
 * Front page identity hub.
 *
 * @package brendon-core
 */

get_header();

$latest_query = new WP_Query(
	[
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	]
);

$featured_query = new WP_Query(
	[
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'meta_query'          => [
			[
				'key'     => 'brendon_core_featured_post',
				'value'   => '1',
				'compare' => '=',
			],
		],
	]
);

$project_items = brendon_core_get_project_items( 3 );
?>

<main id="primary" class="bb-main">
	<section class="bb-hero bb-section">
		<div class="bb-wrap bb-hero__grid">
			<div class="bb-hero__content">
				<p class="bb-kicker"><?php echo esc_html( brendon_core_home_setting( 'hero_kicker' ) ); ?></p>
				<h1><?php echo esc_html( brendon_core_home_setting( 'hero_heading' ) ); ?></h1>
				<p class="bb-hero__lede"><?php echo esc_html( brendon_core_home_setting( 'hero_lede' ) ); ?></p>
				<div class="bb-actions">
					<a class="bb-button" href="<?php echo esc_url( brendon_core_home_setting( 'hero_primary_url' ) ); ?>"><?php echo esc_html( brendon_core_home_setting( 'hero_primary_label' ) ); ?></a>
					<a class="bb-button bb-button--ghost" href="<?php echo esc_url( brendon_core_home_setting( 'hero_secondary_url' ) ); ?>"><?php echo esc_html( brendon_core_home_setting( 'hero_secondary_label' ) ); ?></a>
				</div>
			</div>

			<aside class="bb-hero__identity" aria-label="<?php echo esc_attr_x( 'Identity note', 'front page card label', 'brendon-core' ); ?>">
				<span class="bb-hero__mark" aria-hidden="true">
					<?php echo brendon_core_brand_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<p><?php esc_html_e( 'Faith. Fatherhood. Discipline. Building. Creative work. Growth in public.', 'brendon-core' ); ?></p>
			</aside>
		</div>
	</section>

	<section class="bb-section bb-section--black">
		<div class="bb-wrap bb-manifesto">
			<p class="bb-kicker"><?php echo esc_html( brendon_core_home_setting( 'what_kicker' ) ); ?></p>
			<h2><?php echo esc_html( brendon_core_home_setting( 'what_heading' ) ); ?></h2>
			<p><?php echo esc_html( brendon_core_home_setting( 'what_body' ) ); ?></p>
		</div>
	</section>

	<section class="bb-section bb-pillars-section">
		<div class="bb-wrap">
			<div class="bb-section-heading">
				<p class="bb-kicker"><?php esc_html_e( 'Lived code', 'brendon-core' ); ?></p>
				<h2><?php esc_html_e( 'The framework is simple. The practice is not.', 'brendon-core' ); ?></h2>
			</div>
			<ul class="bb-principles" aria-label="<?php echo esc_attr_x( 'Brand foundations', 'front page list label', 'brendon-core' ); ?>">
				<?php foreach ( brendon_core_home_pillars() as $pillar ) : ?>
					<li><?php echo esc_html( $pillar ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="bb-section bb-logs-section">
		<div class="bb-wrap">
			<div class="bb-section-heading">
				<p class="bb-kicker"><?php esc_html_e( 'Latest writing', 'brendon-core' ); ?></p>
				<h2><?php echo esc_html( brendon_core_home_setting( 'writing_heading' ) ); ?></h2>
				<a class="bb-text-link" href="<?php echo esc_url( brendon_core_home_setting( 'writing_url' ) ); ?>"><?php esc_html_e( 'All Writing', 'brendon-core' ); ?></a>
			</div>

			<?php if ( $latest_query->have_posts() ) : ?>
				<div class="bb-card-grid bb-card-grid--three">
					<?php
					while ( $latest_query->have_posts() ) :
						$latest_query->the_post();
						get_template_part( 'template-parts/card-grid' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<div class="bb-empty">
					<h3><?php esc_html_e( 'No logs published yet.', 'brendon-core' ); ?></h3>
					<p><?php esc_html_e( 'The record starts when the first post goes live.', 'brendon-core' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="bb-section bb-builds">
		<div class="bb-wrap bb-builds__grid">
			<div>
				<p class="bb-kicker"><?php echo esc_html( brendon_core_home_setting( 'projects_kicker' ) ); ?></p>
				<h2>
					<span><?php echo esc_html( brendon_core_home_setting( 'projects_heading' ) ); ?></span>
					<em><?php echo esc_html( brendon_core_home_setting( 'projects_subheading' ) ); ?></em>
				</h2>
			</div>
			<div class="bb-build-list">
				<?php foreach ( $project_items as $project ) : ?>
					<a href="<?php echo esc_url( $project['url'] ); ?>">
						<span><?php echo esc_html( $project['label'] ); ?></span>
						<strong><?php echo esc_html( $project['description'] ); ?></strong>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="bb-section bb-season">
		<div class="bb-wrap bb-season__grid">
			<div>
				<p class="bb-kicker"><?php echo esc_html( brendon_core_home_setting( 'season_kicker' ) ); ?></p>
				<h2><?php echo esc_html( brendon_core_home_setting( 'season_heading' ) ); ?></h2>
			</div>
			<p><?php echo esc_html( brendon_core_home_setting( 'season_body' ) ); ?></p>
		</div>
	</section>

	<?php if ( $featured_query->have_posts() ) : ?>
		<section class="bb-section bb-section--redline">
			<div class="bb-wrap">
				<?php
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					?>
					<article class="bb-featured">
						<div>
							<p class="bb-kicker"><?php esc_html_e( 'Featured record', 'brendon-core' ); ?></p>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34, '...' ) ); ?></p>
						</div>
						<a class="bb-button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read it', 'brendon-core' ); ?></a>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php
get_footer();
