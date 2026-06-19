<?php
/**
 * Single Bible Study Session archive.
 *
 * @package brendon-core
 */

get_header();

while ( have_posts() ) :
	the_post();

	$session_id = get_the_ID();
	$meta       = brendon_core_bible_study_session_meta( $session_id );
	$context    = brendon_core_bible_study_session_context( $session_id );
	$is_live_session = 'live' === $context['state'];
	$video_id   = $is_live_session && ! empty( $context['youtube_status']['video_id'] ) ? $context['youtube_status']['video_id'] : $meta['archive_video_id'];
	$video_src  = brendon_core_bible_study_video_embed_url( $video_id );
	$page_label = $is_live_session ? esc_html__( 'Bible Study Live', 'brendon-core' ) : esc_html__( 'Bible Study Archive', 'brendon-core' );
	$video_label = $is_live_session ? esc_attr__( 'Live Bible study video', 'brendon-core' ) : esc_attr__( 'Archived Bible study video', 'brendon-core' );
	?>

	<main id="primary" class="bb-bible-study bb-bible-study--archive">
		<div class="bb-bible-study__wrap">
			<header class="bb-bible-study__hero">
				<p class="bb-bible-study__kicker"><?php echo esc_html( $page_label ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( $meta['scheduled_start'] ) : ?>
					<p><?php echo esc_html( brendon_core_bible_study_format_session_time( $session_id ) ); ?></p>
				<?php endif; ?>
			</header>

			<section class="bb-bible-study__video-panel" aria-label="<?php echo esc_attr( $video_label ); ?>">
				<?php if ( $video_src ) : ?>
					<div class="bb-bible-study__video-frame">
						<iframe
							title="<?php echo esc_attr( get_the_title() ); ?>"
							src="<?php echo esc_url( $video_src ); ?>"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
							allowfullscreen
							loading="lazy"
							referrerpolicy="strict-origin-when-cross-origin"
						></iframe>
					</div>
				<?php else : ?>
					<div class="bb-bible-study__empty">
						<p><?php echo esc_html__( 'The video is not ready yet. Check the scheduled YouTube URL or wait for the livestream to be detected.', 'brendon-core' ); ?></p>
					</div>
				<?php endif; ?>
			</section>

			<?php if ( is_user_logged_in() ) : ?>
				<section class="bb-bible-study__member-grid" aria-label="<?php echo esc_attr__( 'Archived Bible study resources', 'brendon-core' ); ?>">
					<?php brendon_core_bible_study_render_assets( $session_id ); ?>
					<?php brendon_core_bible_study_render_chat( $session_id, $is_live_session ? 'live' : 'archive' ); ?>
				</section>
			<?php else : ?>
				<section class="bb-bible-study__login">
					<h2><?php echo esc_html__( 'Member Resources', 'brendon-core' ); ?></h2>
					<p><?php echo esc_html__( 'Log in to download study assets and view the discussion replay.', 'brendon-core' ); ?></p>
					<div class="bb-bible-study__actions">
						<a class="bb-bible-study__button" href="<?php echo esc_url( brendon_core_bible_study_login_url( get_permalink() ) ); ?>"><?php echo esc_html__( 'Login', 'brendon-core' ); ?></a>
						<a class="bb-bible-study__button bb-bible-study__button--ghost" href="<?php echo esc_url( brendon_core_bible_study_register_url( get_permalink() ) ); ?>"><?php echo esc_html__( 'Register', 'brendon-core' ); ?></a>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<?php if ( in_array( $context['state'], array( 'live', 'waiting' ), true ) ) : ?>
			<script>
				(function () {
					var currentState = <?php echo wp_json_encode( $context['state'] ); ?>;
					var currentVideoId = <?php echo wp_json_encode( $context['youtube_status']['video_id'] ?? '' ); ?>;
					var endpoint = <?php echo wp_json_encode( add_query_arg( 'action', 'bb_bible_study_status', admin_url( 'admin-ajax.php' ) ) ); ?>;

					function checkStatus() {
						fetch(endpoint, {
							credentials: 'same-origin'
						}).then(function (response) {
							return response.json();
						}).then(function (result) {
							if (!result || !result.success || !result.data) {
								return;
							}

							if (result.data.state !== currentState || (result.data.videoId && result.data.videoId !== currentVideoId)) {
								window.location.reload();
							}
						}).catch(function () {});
					}

					window.setInterval(checkStatus, 30000);
				})();
			</script>
		<?php endif; ?>
	</main>

	<?php
endwhile;

get_footer();
