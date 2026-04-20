<?php
/**
 * Single post reading template.
 *
 * @package brendon-core
 */

$post_id          = get_the_ID();
$excerpt          = has_excerpt() ? get_the_excerpt() : '';
$reading_minutes  = function_exists( 'brendon_core_reading_minutes' ) ? brendon_core_reading_minutes( $post_id ) : 1;
$writing_url      = function_exists( 'brendon_core_writing_url' ) ? brendon_core_writing_url() : home_url( '/writing' );
$previous_post    = get_previous_post();
$next_post        = get_next_post();
$categories       = get_the_category();
$primary_category = ! empty( $categories ) ? $categories[0] : null;
?>

<main id="primary" class="bb-main bb-log-main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'bb-log-entry' ); ?>>
		<header class="bb-log-hero<?php echo has_post_thumbnail() ? ' bb-log-hero--has-image' : ''; ?>"<?php
			if ( has_post_thumbnail() ) {
				$thumb_url = get_the_post_thumbnail_url( null, 'full' );
				echo ' style="background-image: url(' . esc_url( $thumb_url ) . ');"';
			}
		?>>
			<div class="bb-wrap bb-log-hero__inner">
				<p class="bb-kicker"><?php esc_html_e( 'Log Entry', 'brendon-core' ); ?></p>
				<h1><?php the_title(); ?></h1>

				<div class="bb-log-meta" aria-label="<?php echo esc_attr_x( 'Post details', 'aria label', 'brendon-core' ); ?>">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></time>
					<span aria-hidden="true">/</span>
					<span>
						<?php
						printf(
							/* translators: %s: minutes of reading time. */
							esc_html__( '%s min read', 'brendon-core' ),
							esc_html( number_format_i18n( $reading_minutes ) )
						);
						?>
					</span>
				</div>

				<?php if ( $excerpt ) : ?>
					<p class="bb-log-dek"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="bb-wrap bb-log-shell">

			<div class="bb-log-layout">
				<aside class="bb-log-rail" aria-label="<?php echo esc_attr_x( 'Article details', 'aria label', 'brendon-core' ); ?>">
					<p class="bb-kicker"><?php esc_html_e( 'Article', 'brendon-core' ); ?></p>
					<dl>
						<div>
							<dt><?php esc_html_e( 'Filed', 'brendon-core' ); ?></dt>
							<dd><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></dd>
						</div>
						<div>
							<dt><?php esc_html_e( 'Read', 'brendon-core' ); ?></dt>
							<dd>
								<?php
								printf(
									/* translators: %s: minutes of reading time. */
									esc_html__( '%s min', 'brendon-core' ),
									esc_html( number_format_i18n( $reading_minutes ) )
								);
								?>
							</dd>
						</div>
						<?php if ( $primary_category ) : ?>
							<div>
								<dt><?php esc_html_e( 'Type', 'brendon-core' ); ?></dt>
								<dd><a href="<?php echo esc_url( get_category_link( $primary_category ) ); ?>"><?php echo esc_html( $primary_category->name ); ?></a></dd>
							</div>
						<?php endif; ?>
					</dl>
					<nav class="bb-log-rail__links" aria-label="<?php echo esc_attr_x( 'Article shortcuts', 'aria label', 'brendon-core' ); ?>">
						<a href="#article"><?php esc_html_e( 'Read from top', 'brendon-core' ); ?></a>
						<a href="<?php echo esc_url( $writing_url ); ?>"><?php esc_html_e( 'All Writing', 'brendon-core' ); ?></a>
						<?php if ( comments_open() || get_comments_number() ) : ?>
							<a href="#comments"><?php esc_html_e( 'Comments', 'brendon-core' ); ?></a>
						<?php endif; ?>
					</nav>
				</aside>

				<div id="article" class="bb-log-paper">
					<div class="entry-content bb-content bb-log-content">
						<?php
						the_content();
						wp_link_pages(
							[
								'before' => '<nav class="bb-page-links">' . esc_html__( 'Pages:', 'brendon-core' ),
								'after'  => '</nav>',
							]
						);
						?>
					</div>

					<footer class="bb-log-end">
						<div class="bb-log-end__recap">
							<p class="bb-kicker"><?php esc_html_e( 'End of entry', 'brendon-core' ); ?></p>
							<p><?php esc_html_e( 'A record kept in public. Not polished into something cleaner than it was.', 'brendon-core' ); ?></p>
						</div>

						<?php if ( has_tag() ) : ?>
							<div class="bb-log-tags">
								<?php the_tags( '<span>' . esc_html__( 'Filed under:', 'brendon-core' ) . '</span> ', ', ', '' ); ?>
							</div>
						<?php endif; ?>

						<nav class="bb-log-nav" aria-label="<?php echo esc_attr_x( 'Continue reading', 'aria label', 'brendon-core' ); ?>">
							<a class="bb-log-nav__back" href="<?php echo esc_url( $writing_url ); ?>"><?php esc_html_e( 'Back to writing', 'brendon-core' ); ?></a>

							<div class="bb-log-nav__posts">
								<?php if ( $previous_post ) : ?>
									<a class="bb-log-nav__post" href="<?php echo esc_url( get_permalink( $previous_post ) ); ?>" rel="prev">
										<span><?php esc_html_e( 'Previous Log', 'brendon-core' ); ?></span>
										<strong><?php echo esc_html( get_the_title( $previous_post ) ); ?></strong>
									</a>
								<?php endif; ?>

								<?php if ( $next_post ) : ?>
									<a class="bb-log-nav__post" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" rel="next">
										<span><?php esc_html_e( 'Next Log', 'brendon-core' ); ?></span>
										<strong><?php echo esc_html( get_the_title( $next_post ) ); ?></strong>
									</a>
								<?php endif; ?>
							</div>
						</nav>
					</footer>

				</div>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<section class="bb-log-comments">
						<?php comments_template(); ?>
					</section>
				<?php endif; ?>
			</div>
		</div>
	</article>
</main>
