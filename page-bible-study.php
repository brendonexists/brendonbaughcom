<?php
/**
 * Template Name: Bible Study Live
 *
 * @package brendon-core
 */

get_header();

$context     = brendon_core_bible_study_page_context();
$state       = $context['state'];
$session     = $context['session'];
$upcoming    = $context['upcoming'];
$past_sessions = brendon_core_bible_study_past_sessions( 6 );
$admin_video_session = $session ?: ( $upcoming ?: ( $past_sessions[0] ?? null ) );
$admin_video_meta = $admin_video_session ? brendon_core_bible_study_session_meta( $admin_video_session->ID ) : array();
$is_live_preview = false;

if (
	$admin_video_session
	&& current_user_can( 'edit_post', $admin_video_session->ID )
	&& isset( $_GET['bb_bible_study_preview'] )
	&& 'live' === sanitize_key( wp_unslash( $_GET['bb_bible_study_preview'] ) )
) {
	$preview_video_id = $admin_video_meta['scheduled_video_id'] ?: $admin_video_meta['archive_video_id'];

	if ( $preview_video_id ) {
		$is_live_preview = true;
		$session         = $admin_video_session;
		$state           = 'live';
		$context['state'] = 'live';
		$context['session'] = $session;
		$context['youtube_status'] = array(
			'is_live'  => true,
			'video_id' => $preview_video_id,
			'state'    => 'preview',
			'error'    => '',
		);
	}
}

$session_meta = $session ? brendon_core_bible_study_session_meta( $session->ID ) : array();
$playlist_id = brendon_core_bible_study_fallback_playlist_id();
$playlist_src = $playlist_id ? add_query_arg(
	array(
		'list'  => $playlist_id,
		'rel'   => 0,
		'index' => 0,
	),
	'https://www.youtube.com/embed/videoseries'
) : '';
$live_src = 'live' === $state && ! empty( $context['youtube_status']['video_id'] )
	? add_query_arg(
		array(
			'autoplay' => 1,
			'rel'      => 0,
		),
		'https://www.youtube.com/embed/' . rawurlencode( $context['youtube_status']['video_id'] )
	)
	: '';
$waiting_channel_src = 'waiting' === $state
	? brendon_core_bible_study_channel_live_embed_url()
	: '';
?>

<main id="primary" class="bb-bible-study">
	<div class="bb-bible-study__wrap">
		<div class="bb-bible-study__status">
			<span class="bb-bible-study__status-dot<?php echo 'live' === $state ? ' bb-bible-study__status-dot--live' : ''; ?>" aria-hidden="true"></span>
			<span>
				<?php
				if ( $is_live_preview ) {
					echo esc_html__( 'Live preview', 'brendon-core' );
				} else {
					echo esc_html(
						array(
							'live'     => __( 'Live now', 'brendon-core' ),
							'waiting'  => __( 'Starting soon', 'brendon-core' ),
							'upcoming' => __( 'Next study scheduled', 'brendon-core' ),
							'offline'  => __( 'Past studies', 'brendon-core' ),
						)[ $state ] ?? __( 'Bible Study', 'brendon-core' )
					);
				}
				?>
			</span>
		</div>

		<div class="bb-bible-study__grid">
			<?php if ( $admin_video_session && current_user_can( 'edit_post', $admin_video_session->ID ) ) : ?>
				<section class="bb-bible-study__admin-note" aria-label="<?php echo esc_attr__( 'Admin live preview', 'brendon-core' ); ?>">
					<strong><?php echo esc_html__( 'Admin preview:', 'brendon-core' ); ?></strong>
					<?php if ( $is_live_preview ) : ?>
						<?php echo esc_html__( 'You are previewing the live page layout. This does not change the real livestream state.', 'brendon-core' ); ?>
						<div class="bb-bible-study__admin-controls">
							<a class="bb-bible-study__button bb-bible-study__button--ghost" href="<?php echo esc_url( remove_query_arg( 'bb_bible_study_preview' ) ); ?>"><?php echo esc_html__( 'Exit Live Preview', 'brendon-core' ); ?></a>
						</div>
					<?php elseif ( $admin_video_meta['scheduled_video_id'] || $admin_video_meta['archive_video_id'] ) : ?>
						<?php echo esc_html__( 'Preview the live layout using the selected session video without changing the real livestream state.', 'brendon-core' ); ?>
						<div class="bb-bible-study__admin-controls">
							<a class="bb-bible-study__button bb-bible-study__button--ghost" href="<?php echo esc_url( add_query_arg( 'bb_bible_study_preview', 'live' ) ); ?>"><?php echo esc_html__( 'Preview Live Layout', 'brendon-core' ); ?></a>
						</div>
					<?php else : ?>
						<?php echo esc_html__( 'Add a scheduled or archive YouTube URL to a Bible Study Session, then the live preview switch will appear here.', 'brendon-core' ); ?>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( $admin_video_session && current_user_can( 'edit_post', $admin_video_session->ID ) && 'live' !== $state ) : ?>
				<section class="bb-bible-study__admin-note" aria-label="<?php echo esc_attr__( 'Admin YouTube video update', 'brendon-core' ); ?>">
					<strong><?php echo esc_html__( 'Admin live check:', 'brendon-core' ); ?></strong>
					<?php echo esc_html__( 'If you are live but this page is not showing it, paste the current YouTube live URL here. This updates the selected Bible Study Session immediately.', 'brendon-core' ); ?>
					<form class="bb-bible-study__quick-video" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="bb_bible_study_quick_video">
						<input type="hidden" name="session_id" value="<?php echo esc_attr( $admin_video_session->ID ); ?>">
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">
						<?php wp_nonce_field( 'bb_bible_study_quick_video_' . $admin_video_session->ID, 'bb_bible_study_quick_video_nonce' ); ?>
						<label class="screen-reader-text" for="bb-bible-study-quick-video"><?php echo esc_html__( 'Current YouTube live URL', 'brendon-core' ); ?></label>
						<input id="bb-bible-study-quick-video" type="url" name="youtube_video" placeholder="<?php echo esc_attr__( 'Paste current YouTube live URL', 'brendon-core' ); ?>">
						<button class="bb-bible-study__button" type="submit"><?php echo esc_html__( 'Update Stream', 'brendon-core' ); ?></button>
					</form>
					<?php if ( $admin_video_meta['scheduled_video_id'] || $admin_video_meta['archive_video_id'] ) : ?>
						<form class="bb-bible-study__admin-controls" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="bb_bible_study_force_live">
							<input type="hidden" name="session_id" value="<?php echo esc_attr( $admin_video_session->ID ); ?>">
							<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">
							<input type="hidden" name="mode" value="start">
							<?php wp_nonce_field( 'bb_bible_study_force_live_' . $admin_video_session->ID, 'bb_bible_study_force_live_nonce' ); ?>
							<button class="bb-bible-study__button bb-bible-study__button--ghost" type="submit"><?php echo esc_html__( 'Force Live Now', 'brendon-core' ); ?></button>
						</form>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( $session && current_user_can( 'edit_post', $session->ID ) && 'live' === $state && brendon_core_bible_study_is_forced_live( $session->ID ) ) : ?>
				<section class="bb-bible-study__admin-note" aria-label="<?php echo esc_attr__( 'Admin live override', 'brendon-core' ); ?>">
					<strong><?php echo esc_html__( 'Admin override active:', 'brendon-core' ); ?></strong>
					<?php echo esc_html__( 'This session is being forced live because YouTube did not report the live state correctly.', 'brendon-core' ); ?>
					<form class="bb-bible-study__admin-controls" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="bb_bible_study_force_live">
						<input type="hidden" name="session_id" value="<?php echo esc_attr( $session->ID ); ?>">
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">
						<input type="hidden" name="mode" value="end">
						<?php wp_nonce_field( 'bb_bible_study_force_live_' . $session->ID, 'bb_bible_study_force_live_nonce' ); ?>
						<button class="bb-bible-study__button bb-bible-study__button--ghost" type="submit"><?php echo esc_html__( 'End Live Override', 'brendon-core' ); ?></button>
					</form>
				</section>
			<?php endif; ?>

			<?php if ( 'live' === $state && $live_src ) : ?>
				<section class="bb-bible-study__video-panel" aria-labelledby="bible-study-video-title">
					<div class="bb-bible-study__video-header">
						<h2 id="bible-study-video-title"><?php echo esc_html__( 'Live Stream', 'brendon-core' ); ?></h2>
						<span class="bb-bible-study__kicker"><?php echo esc_html__( 'On Air', 'brendon-core' ); ?></span>
					</div>
					<div class="bb-bible-study__video-frame">
						<iframe title="<?php echo esc_attr__( 'Live Bible study stream', 'brendon-core' ); ?>" src="<?php echo esc_url( $live_src ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
					</div>
				</section>

				<?php if ( is_user_logged_in() ) : ?>
					<section class="bb-bible-study__live-layout" aria-label="<?php echo esc_attr__( 'Bible study live resources', 'brendon-core' ); ?>">
						<?php brendon_core_bible_study_render_assets( $session->ID ); ?>
						<?php brendon_core_bible_study_render_chat( $session->ID, 'live' ); ?>
					</section>
				<?php else : ?>
					<section class="bb-bible-study__login" aria-labelledby="bible-study-login-title">
						<h2 id="bible-study-login-title"><?php echo esc_html__( 'Join the Study', 'brendon-core' ); ?></h2>
						<p><?php echo esc_html__( 'Log in to download study assets and join the discussion.', 'brendon-core' ); ?></p>
						<div class="bb-bible-study__actions">
							<a class="bb-bible-study__button" href="<?php echo esc_url( brendon_core_bible_study_login_url( get_permalink() ) ); ?>"><?php echo esc_html__( 'Login', 'brendon-core' ); ?></a>
							<a class="bb-bible-study__button bb-bible-study__button--ghost" href="<?php echo esc_url( brendon_core_bible_study_register_url( get_permalink() ) ); ?>"><?php echo esc_html__( 'Register', 'brendon-core' ); ?></a>
						</div>
					</section>
				<?php endif; ?>
			<?php elseif ( 'waiting' === $state && $session ) : ?>
				<section class="bb-bible-study__panel">
					<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Scheduled Bible Study', 'brendon-core' ); ?></p>
					<h1><?php echo esc_html( get_the_title( $session ) ); ?></h1>
					<p><?php echo esc_html( brendon_core_bible_study_format_session_time( $session->ID ) ); ?></p>
					<p><?php echo esc_html__( 'We are inside the scheduled live window and waiting for YouTube to report the livestream as live. Chat and study assets will appear here automatically once the stream starts.', 'brendon-core' ); ?></p>
					<?php if ( current_user_can( 'edit_post', $session->ID ) && empty( $session_meta['scheduled_video_id'] ) ) : ?>
						<div class="bb-bible-study__admin-note">
							<?php
							printf(
								wp_kses(
									/* translators: %s: edit session URL */
									__( 'Admin note: this session does not have a scheduled YouTube live URL saved. Channel detection can miss unlisted or newly-started streams. <a href="%s">Edit this Bible Study Session</a> and paste the current YouTube live URL for reliable video, chat, and assets.', 'brendon-core' ),
									array(
										'a' => array(
											'href' => array(),
										),
									)
								),
								esc_url( get_edit_post_link( $session->ID ) )
							);
							?>
						</div>
					<?php endif; ?>
				</section>

				<?php if ( $waiting_channel_src ) : ?>
					<section class="bb-bible-study__video-panel" aria-labelledby="bible-study-channel-live-title">
						<div class="bb-bible-study__video-header">
							<h2 id="bible-study-channel-live-title"><?php echo esc_html__( 'YouTube Live Player', 'brendon-core' ); ?></h2>
							<span class="bb-bible-study__kicker"><?php echo esc_html__( 'Channel Fallback', 'brendon-core' ); ?></span>
						</div>
						<div class="bb-bible-study__video-frame">
							<iframe title="<?php echo esc_attr__( 'Current YouTube channel livestream', 'brendon-core' ); ?>" src="<?php echo esc_url( $waiting_channel_src ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
						</div>
					</section>
				<?php endif; ?>
			<?php else : ?>
				<section class="bb-bible-study__video-panel" aria-labelledby="bible-study-archive-title">
					<div class="bb-bible-study__video-header">
						<h2 id="bible-study-archive-title"><?php echo esc_html__( 'Past Bible Studies', 'brendon-core' ); ?></h2>
						<span class="bb-bible-study__kicker"><?php echo esc_html__( 'Archive', 'brendon-core' ); ?></span>
					</div>
					<?php if ( $playlist_src ) : ?>
						<div class="bb-bible-study__video-frame">
							<iframe title="<?php echo esc_attr__( 'Past Bible study streams playlist', 'brendon-core' ); ?>" src="<?php echo esc_url( $playlist_src ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
						</div>
					<?php else : ?>
						<div class="bb-bible-study__empty">
							<p><?php echo esc_html__( 'Bible Study Live needs a fallback playlist, or a YouTube channel ID that starts with UC so the archive playlist can be detected.', 'brendon-core' ); ?></p>
						</div>
					<?php endif; ?>
				</section>

				<?php if ( $past_sessions ) : ?>
					<section class="bb-bible-study__panel" aria-labelledby="bible-study-session-archive-title">
						<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Session Archive', 'brendon-core' ); ?></p>
						<h2 id="bible-study-session-archive-title"><?php echo esc_html__( 'Past Live Bible Studies', 'brendon-core' ); ?></h2>
						<div class="bb-bible-study__archive-list">
							<?php foreach ( $past_sessions as $past_session ) : ?>
								<a class="bb-bible-study__archive-card" href="<?php echo esc_url( get_permalink( $past_session ) ); ?>">
									<h3><?php echo esc_html( get_the_title( $past_session ) ); ?></h3>
									<?php $session_time = brendon_core_bible_study_format_session_time( $past_session->ID ); ?>
									<?php if ( $session_time ) : ?>
										<p><?php echo esc_html( $session_time ); ?></p>
									<?php endif; ?>
									<span class="bb-bible-study__archive-action"><?php echo esc_html__( 'View Study', 'brendon-core' ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( in_array( $state, array( 'live', 'waiting' ), true ) && $past_sessions ) : ?>
				<section class="bb-bible-study__panel" aria-labelledby="bible-study-session-archive-title">
					<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Session Archive', 'brendon-core' ); ?></p>
					<h2 id="bible-study-session-archive-title"><?php echo esc_html__( 'Past Live Bible Studies', 'brendon-core' ); ?></h2>
					<div class="bb-bible-study__archive-list">
						<?php foreach ( $past_sessions as $past_session ) : ?>
							<a class="bb-bible-study__archive-card" href="<?php echo esc_url( get_permalink( $past_session ) ); ?>">
								<h3><?php echo esc_html( get_the_title( $past_session ) ); ?></h3>
								<?php $session_time = brendon_core_bible_study_format_session_time( $past_session->ID ); ?>
								<?php if ( $session_time ) : ?>
									<p><?php echo esc_html( $session_time ); ?></p>
								<?php endif; ?>
								<span class="bb-bible-study__archive-action"><?php echo esc_html__( 'View Study', 'brendon-core' ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			brendon_core_bible_study_render_schedule(
				array(
					'title'       => __( 'Study Calendar', 'brendon-core' ),
					'description' => __( 'Upcoming sessions and recent studies from the published Bible Study Session schedule.', 'brendon-core' ),
					'limit'       => 48,
				)
			);
			?>
		</div>
	</div>

	<?php if ( ! $is_live_preview && in_array( $state, array( 'live', 'waiting' ), true ) ) : ?>
		<script>
			(function () {
				var currentState = <?php echo wp_json_encode( $state ); ?>;
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
get_footer();
