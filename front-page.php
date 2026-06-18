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

$pillar_items = [
	[
		'label' => esc_html__( 'Notes', 'brendon-core' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M11 18h23v12a10 10 0 0 1-10 10h-3A10 10 0 0 1 11 30V18Zm23 4h3a5 5 0 0 1 0 10h-3M17 12c-2-3 2-4 0-7m8 7c-2-3 2-4 0-7m8 7c-2-3 2-4 0-7M8 40h31" /></svg>',
	],
	[
		'label' => esc_html__( 'Music', 'brendon-core' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="m31 6 5 5-13 13m9-15 7-3 3 3-3 7M21 23a10 10 0 1 0 4 4m-9 2 3 3m-7 2 2 2" /></svg>',
	],
	[
		'label' => esc_html__( 'Faith', 'brendon-core' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M8 12c6-2 11-1 16 3 5-4 10-5 16-3v28c-6-2-11-1-16 3-5-4-10-5-16-3V12Zm16 3v28" /></svg>',
	],
	[
		'label' => esc_html__( 'Builds', 'brendon-core' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M10 36h28M14 36V12h20v24M19 18h3m4 0h3m-10 7h3m4 0h3m-10 7h3m4 0h3M7 40h34" /></svg>',
	],
];

$tile_items = [
	[
		'label' => esc_html__( 'About', 'brendon-core' ),
		'url'   => home_url( '/about' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M24 11a7 7 0 1 0 0 14 7 7 0 0 0 0-14ZM13 38c2.3-7 6-10.5 11-10.5S32.7 31 35 38" /></svg>',
	],
	[
		'label' => esc_html__( 'Writings', 'brendon-core' ),
		'url'   => home_url( '/category/writings/' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M17 10h17l5 5v23H17V10ZM34 10v6h5M22 22h11M22 29h11M22 36h7" /></svg>',
	],
	[
		'label' => esc_html__( 'Projects', 'brendon-core' ),
		'url'   => home_url( '/category/projects/' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M18 18 12 24l6 6M30 18l6 6-6 6M27 13 21 35" /></svg>',
	],
	[
		'label' => esc_html__( 'Bible Study', 'brendon-core' ),
		'url'   => home_url( '/bible-study' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M24 9v30M14 19h20M18 39h12" /></svg>',
	],
	[
		'label' => esc_html__( 'Prayers', 'brendon-core' ),
		'url'   => home_url( '/prayer-wall' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M24 38s-13-7.4-13-17a7 7 0 0 1 13-3.6A7 7 0 0 1 37 21c0 9.6-13 17-13 17ZM24 17v21" /></svg>',
	],
	[
		'label' => esc_html__( 'Contact', 'brendon-core' ),
		'url'   => home_url( '/contact' ),
		'icon'  => '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M10 16h28v18H10V16ZM12 18l12 10 12-10" /></svg>',
	],
];
?>

<main id="primary" class="bb-main bb-home-retro">
	<section class="bb-retro-hero" aria-labelledby="bb-retro-title">
		<div class="bb-retro-wrap">
			<div class="bb-retro-wordmark" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<h1 id="bb-retro-title">BrendonBaugh</h1>
			</div>

			<ul class="bb-retro-pillars" aria-label="<?php echo esc_attr_x( 'Stream hub pillars', 'front page list label', 'brendon-core' ); ?>">
				<?php foreach ( $pillar_items as $index => $item ) : ?>
					<li>
						<span class="bb-retro-pillar__icon">
							<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
								<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
				<p><?php esc_html_e( 'Latest writing', 'brendon-core' ); ?></p>
				<h2 id="bb-retro-latest-title"><?php esc_html_e( 'Field notes from the build.', 'brendon-core' ); ?></h2>
				<a href="<?php echo esc_url( brendon_core_home_setting( 'writing_url' ) ); ?>"><?php esc_html_e( 'All Writing', 'brendon-core' ); ?></a>
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
