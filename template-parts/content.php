<?php
/**
 * Singular post/page content.
 *
 * @package brendon-core
 */

$is_post = 'post' === get_post_type();
$hide_page_featured = is_page() && function_exists( 'brendon_core_hide_page_featured_image' ) && brendon_core_hide_page_featured_image();
$show_featured = has_post_thumbnail() && ! brendon_core_is_video_post() && ! $hide_page_featured;
$post_content_value = get_post_field( 'post_content', get_the_ID() );
$word_count = str_word_count( wp_strip_all_tags( $post_content_value ?: '' ) );
$reading_minutes = max( 1, (int) ceil( $word_count / 200 ) );
$categories = get_the_category();
$primary_category = ! empty( $categories ) ? $categories[0] : null;
$posts_page_id = (int) get_option( 'page_for_posts' );
$writing_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/writing/' );
$previous_post = get_previous_post();
$next_post = get_next_post();
?>

<?php if ( $is_post ) : ?>
	<main id="primary" class="bb-main bb-log-main">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bb-log-entry' ); ?>>
			<header class="bb-log-hero">
				<div class="bb-wrap bb-log-hero__inner">
					<p class="bb-kicker">
						<?php
						echo $primary_category
							? esc_html( $primary_category->name )
							: esc_html__( 'Log entry', 'brendon-core' );
						?>
					</p>
					<h1><?php the_title(); ?></h1>
					<div class="bb-log-meta">
						<span class="bb-log-meta__item">
							<span><?php esc_html_e( 'Filed', 'brendon-core' ); ?></span>
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</span>
						<span aria-hidden="true">/</span>
						<span class="bb-log-meta__item">
							<span><?php esc_html_e( 'By', 'brendon-core' ); ?></span>
							<span><?php echo esc_html( get_the_author() ); ?></span>
						</span>
						<span aria-hidden="true">/</span>
						<span class="bb-log-meta__item">
							<span><?php esc_html_e( 'Read', 'brendon-core' ); ?></span>
							<span>
								<?php
								printf(
									/* translators: %s: minutes of reading time. */
									esc_html__( '%s min', 'brendon-core' ),
									esc_html( number_format_i18n( $reading_minutes ) )
								);
								?>
							</span>
						</span>
					</div>
					<?php if ( has_excerpt() ) : ?>
						<p class="bb-log-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<div class="bb-wrap bb-log-shell">
				<?php if ( $show_featured ) : ?>
					<figure class="bb-log-featured">
						<?php the_post_thumbnail( 'brendon-core-wide' ); ?>
						<?php if ( get_the_post_thumbnail_caption() ) : ?>
							<figcaption><?php echo esc_html( get_the_post_thumbnail_caption() ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<div class="bb-log-layout">
					<aside class="bb-log-rail" aria-label="<?php echo esc_attr_x( 'Article details', 'aria label', 'brendon-core' ); ?>">
						<p class="bb-kicker"><?php esc_html_e( 'Article', 'brendon-core' ); ?></p>
						<dl>
							<div>
								<dt><?php esc_html_e( 'Filed', 'brendon-core' ); ?></dt>
								<dd><?php echo esc_html( get_the_date() ); ?></dd>
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
						<div class="entry-content bb-log-content">
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

							<nav class="bb-log-nav" aria-label="<?php echo esc_attr_x( 'Post navigation', 'aria label', 'brendon-core' ); ?>">
								<a class="bb-log-nav__back" href="<?php echo esc_url( $writing_url ); ?>"><?php esc_html_e( 'All Writing', 'brendon-core' ); ?></a>
								<div class="bb-log-nav__posts">
									<?php if ( $previous_post ) : ?>
										<a class="bb-log-nav__post" href="<?php echo esc_url( get_permalink( $previous_post ) ); ?>">
											<span><?php esc_html_e( 'Previous log', 'brendon-core' ); ?></span>
											<strong><?php echo esc_html( get_the_title( $previous_post ) ); ?></strong>
										</a>
									<?php endif; ?>
									<?php if ( $next_post ) : ?>
										<a class="bb-log-nav__post" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
											<span><?php esc_html_e( 'Next log', 'brendon-core' ); ?></span>
											<strong><?php echo esc_html( get_the_title( $next_post ) ); ?></strong>
										</a>
									<?php endif; ?>
								</div>
							</nav>
						</footer>
					</div>
				</div>
			</div>
		</article>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<section class="bb-log-comments">
				<div class="bb-wrap bb-log-comments__inner">
					<?php comments_template(); ?>
				</div>
			</section>
		<?php endif; ?>
	</main>
<?php return; ?>
<?php endif; ?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<h1><?php the_title(); ?></h1>
		</div>
	</section>

	<section class="bb-section bb-page-content">
		<div class="bb-wrap bb-singular__wrap">
			<?php if ( $show_featured ) : ?>
				<figure class="bb-singular__media">
					<?php the_post_thumbnail( 'brendon-core-wide' ); ?>
				</figure>
			<?php endif; ?>

			<div class="bb-singular__paper">
				<div class="entry-content bb-content">
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
			</div>
		</div>
	</section>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<section class="bb-section bb-comments-section">
			<div class="bb-wrap bb-singular__wrap">
				<?php comments_template(); ?>
			</div>
		</section>
	<?php endif; ?>
</main>
