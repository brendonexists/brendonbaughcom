<?php
/**
 * Front page retro stream hub.
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

$pillar_items = brendon_core_home_pillar_items();
$tile_items   = brendon_core_home_quick_link_items();
?>

<main id="primary" class="bb-main bb-home-retro">
	<section class="bb-retro-hero" aria-labelledby="bb-retro-title">
		<div class="bb-retro-wrap">
			<div class="bb-retro-wordmark" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<h1 id="bb-retro-title"><?php echo esc_html( brendon_core_home_setting( 'wordmark' ) ); ?></h1>
			</div>

			<ul class="bb-retro-pillars" aria-label="<?php echo esc_attr_x( 'Stream hub pillars', 'front page list label', 'brendon-core' ); ?>">
				<?php foreach ( $pillar_items as $index => $item ) : ?>
					<li>
						<span class="bb-retro-pillar__icon">
							<?php echo brendon_core_home_icon_svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span><?php echo esc_html( $item['label'] ); ?></span>
					</li>
					<?php if ( $index < count( $pillar_items ) - 1 ) : ?>
						<li class="bb-retro-pillars__dot" aria-hidden="true">&bull;</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>

			<nav class="bb-retro-twitch" aria-label="<?php echo esc_attr_x( 'Homepage quick links', 'front page navigation label', 'brendon-core' ); ?>">
				<h2><?php esc_html_e( 'Explore', 'brendon-core' ); ?></h2>
				<div class="bb-retro-tiles">
					<?php foreach ( $tile_items as $item ) : ?>
						<a class="bb-retro-tile" href="<?php echo esc_url( $item['url'] ); ?>">
							<span class="bb-retro-tile__icon">
								<?php echo brendon_core_home_icon_svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<span><?php echo esc_html( $item['label'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</nav>
		</div>
	</section>

	<section class="bb-retro-latest" aria-labelledby="bb-retro-latest-title">
		<div class="bb-retro-wrap">
			<div class="bb-retro-section-heading">
				<p><?php echo esc_html( brendon_core_home_setting( 'latest_kicker' ) ); ?></p>
				<h2 id="bb-retro-latest-title"><?php echo esc_html( brendon_core_home_setting( 'latest_heading' ) ); ?></h2>
				<a href="<?php echo esc_url( brendon_core_home_setting( 'writing_url' ) ); ?>"><?php echo esc_html( brendon_core_home_setting( 'latest_archive_label' ) ); ?></a>
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
				<div class="bb-retro-empty">
					<h3><?php esc_html_e( 'No logs published yet.', 'brendon-core' ); ?></h3>
					<p><?php esc_html_e( 'The record starts when the first post goes live.', 'brendon-core' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
