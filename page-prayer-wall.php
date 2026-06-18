<?php
/**
 * Template Name: Prayer Wall
 *
 * @package brendon-core
 */

get_header();

$page_url           = get_permalink( get_queried_object_id() );
$submission_content = '';
if ( is_user_logged_in() && have_posts() ) {
	the_post();
	$submission_content = trim( apply_filters( 'the_content', get_the_content() ) );
	rewind_posts();
}

$prayer_query = new WP_Query(
	array(
		'post_type'      => BB_PRAYER_REQUEST_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => 24,
		'meta_query'     => array(
			array(
				'key'   => '_bb_prayer_is_public',
				'value' => '1',
			),
		),
	)
);
?>

<main id="primary" class="bb-main bb-prayer-wall">
	<section class="bb-page-hero bb-section bb-prayer-wall__hero">
		<div class="bb-wrap bb-prayer-wall__hero-inner">
			<p class="bb-kicker"><?php esc_html_e( 'Prayer Wall', 'brendon-core' ); ?></p>
			<h1><?php esc_html_e( 'Pray With Us', 'brendon-core' ); ?></h1>
			<p><?php esc_html_e( 'Requests shared by the community, carried together in public or anonymously.', 'brendon-core' ); ?></p>
		</div>
	</section>

	<section class="bb-section bb-prayer-wall__submit-section">
		<div class="bb-wrap">
			<?php if ( is_user_logged_in() ) : ?>
				<div class="bb-prayer-wall__submit">
					<div>
						<p class="bb-kicker"><?php esc_html_e( 'Share a request', 'brendon-core' ); ?></p>
						<h2><?php esc_html_e( 'Post a prayer request', 'brendon-core' ); ?></h2>
					</div>

					<?php if ( $submission_content ) : ?>
						<div class="bb-prayer-wall__form entry-content">
							<?php echo $submission_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="bb-prayer-wall__auth">
					<div>
						<p class="bb-kicker"><?php esc_html_e( 'Join the wall', 'brendon-core' ); ?></p>
						<h2><?php esc_html_e( 'Sign in to share a prayer request.', 'brendon-core' ); ?></h2>
						<p><?php esc_html_e( 'Members can post under their display name or choose Anonymous on the form.', 'brendon-core' ); ?></p>
					</div>
					<div class="bb-actions">
						<a class="bb-button" href="<?php echo esc_url( brendon_core_prayer_wall_login_url( $page_url ) ); ?>"><?php esc_html_e( 'Sign in', 'brendon-core' ); ?></a>
						<a class="bb-button bb-button--ghost" href="<?php echo esc_url( brendon_core_prayer_wall_register_url( $page_url ) ); ?>"><?php esc_html_e( 'Register', 'brendon-core' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="bb-section bb-prayer-wall__section">
		<div class="bb-wrap">
			<?php if ( $prayer_query->have_posts() ) : ?>
				<div class="bb-prayer-wall__grid">
					<?php
					while ( $prayer_query->have_posts() ) :
						$prayer_query->the_post();
						$post_id     = get_the_ID();
						$is_praise   = '1' === get_post_meta( $post_id, '_bb_prayer_is_praise', true );
						$count       = (int) get_post_meta( $post_id, '_bb_prayer_count', true );
						$has_prayed  = brendon_core_prayer_wall_user_has_prayed( $post_id );
						$button_text = $has_prayed ? __( 'Prayed', 'brendon-core' ) : __( 'I prayed', 'brendon-core' );
						?>
						<article <?php post_class( 'bb-prayer-card' ); ?>>
							<header class="bb-prayer-card__header">
								<span class="bb-prayer-card__type">
									<?php echo esc_html( $is_praise ? __( 'Praise report', 'brendon-core' ) : __( 'Prayer request', 'brendon-core' ) ); ?>
								</span>
								<span class="bb-prayer-card__date">
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</span>
							</header>

							<div class="bb-prayer-card__content">
								<?php echo wp_kses_post( wpautop( get_post_meta( $post_id, '_bb_prayer_request_text', true ) ?: get_the_content() ) ); ?>
							</div>

							<footer class="bb-prayer-card__footer">
								<div>
									<span class="bb-prayer-card__label"><?php esc_html_e( 'Shared by', 'brendon-core' ); ?></span>
									<strong><?php echo esc_html( brendon_core_prayer_wall_author_name( $post_id ) ); ?></strong>
								</div>

								<div class="bb-prayer-card__action">
									<span class="bb-prayer-card__count" data-prayer-count="<?php echo esc_attr( $post_id ); ?>">
										<?php
										printf(
											/* translators: %s: prayer count. */
											esc_html( _n( '%s prayer', '%s prayers', $count, 'brendon-core' ) ),
											esc_html( number_format_i18n( $count ) )
										);
										?>
									</span>
									<?php if ( is_user_logged_in() ) : ?>
										<button
											class="bb-button bb-prayer-card__button"
											type="button"
											data-prayer-button
											data-post-id="<?php echo esc_attr( $post_id ); ?>"
											<?php disabled( $has_prayed ); ?>
										>
											<?php echo esc_html( $button_text ); ?>
										</button>
									<?php else : ?>
										<a class="bb-button bb-prayer-card__button" href="<?php echo esc_url( brendon_core_prayer_wall_login_url( $page_url ) ); ?>">
											<?php esc_html_e( 'Log in to pray', 'brendon-core' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</footer>
						</article>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="bb-prayer-wall__empty">
					<p class="bb-kicker"><?php esc_html_e( 'Quiet for now', 'brendon-core' ); ?></p>
					<h2><?php esc_html_e( 'No public prayer requests have been approved yet.', 'brendon-core' ); ?></h2>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
