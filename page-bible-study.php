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
	<style>
		.bb-bible-study {
			background:
				radial-gradient(circle at top left, rgba(184, 141, 74, 0.22), transparent 34rem),
				linear-gradient(135deg, #17130e 0%, #221a12 54%, #0f0d0a 100%);
			color: #f7eedc;
			min-height: 100vh;
		}

		.bb-bible-study * {
			box-sizing: border-box;
		}

		.bb-bible-study__wrap {
			margin: 0 auto;
			max-width: 1180px;
			padding: clamp(1.25rem, 3.5vw, 2.5rem) 1.25rem;
		}

		.bb-bible-study__grid,
		.bb-bible-study__live-layout {
			display: grid;
			gap: 1.25rem;
		}

		.bb-bible-study__live-layout {
			align-items: start;
			grid-template-columns: minmax(0, 0.85fr) minmax(320px, 1.15fr);
		}

		.bb-bible-study__panel,
		.bb-bible-study__video-panel,
		.bb-bible-study__login {
			background: rgba(255, 249, 236, 0.075);
			border: 1px solid rgba(247, 238, 220, 0.16);
			border-radius: 8px;
			box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.24);
		}

		.bb-bible-study__panel,
		.bb-bible-study__login {
			padding: clamp(1rem, 3vw, 1.5rem);
		}

		.bb-bible-study__video-panel {
			overflow: hidden;
		}

		.bb-bible-study__video-header {
			align-items: center;
			border-bottom: 1px solid rgba(247, 238, 220, 0.12);
			display: flex;
			justify-content: space-between;
			padding: 1rem clamp(1rem, 3vw, 1.5rem);
		}

		.bb-bible-study h1,
		.bb-bible-study h2,
		.bb-bible-study h3,
		.bb-bible-study p {
			margin-top: 0;
		}

		.bb-bible-study h1 {
			color: #fff9ec;
			font-size: clamp(2rem, 5vw, 4rem);
			line-height: 1;
			margin-bottom: 0.75rem;
		}

		.bb-bible-study h2,
		.bb-bible-study h3 {
			color: #fff9ec;
			margin-bottom: 0.65rem;
		}

		.bb-bible-study p,
		.bb-bible-study-chat__notice {
			color: #d8cbb6;
			line-height: 1.65;
		}

		.bb-bible-study__kicker,
		.bb-bible-study__chat-state {
			color: #d9b46f;
			font-size: 0.78rem;
			font-weight: 800;
			letter-spacing: 0.14em;
			text-transform: uppercase;
		}

		.bb-bible-study__status {
			align-items: center;
			background: rgba(247, 238, 220, 0.08);
			border: 1px solid rgba(217, 180, 111, 0.28);
			border-radius: 999px;
			color: #fff4df;
			display: inline-flex;
			font-size: 0.9rem;
			font-weight: 700;
			gap: 0.6rem;
			margin-bottom: 1rem;
			padding: 0.6rem 0.9rem;
		}

		.bb-bible-study__status-dot {
			background: #d9b46f;
			border-radius: 50%;
			box-shadow: 0 0 0 0.35rem rgba(217, 180, 111, 0.16);
			height: 0.65rem;
			width: 0.65rem;
		}

		.bb-bible-study__status-dot--live {
			background: #ff5a4f;
			box-shadow: 0 0 0 0.35rem rgba(255, 90, 79, 0.18);
		}

		.bb-bible-study__video-frame {
			aspect-ratio: 16 / 9;
			background: #050403;
			position: relative;
			width: 100%;
		}

		.bb-bible-study__video-frame iframe {
			border: 0;
			height: 100%;
			inset: 0;
			position: absolute;
			width: 100%;
		}

		.bb-bible-study__empty {
			align-items: center;
			aspect-ratio: 16 / 9;
			background: rgba(5, 4, 3, 0.9);
			color: #d8cbb6;
			display: flex;
			line-height: 1.65;
			padding: clamp(1.25rem, 4vw, 2rem);
		}

		.bb-bible-study__admin-note {
			background: rgba(217, 180, 111, 0.12);
			border: 1px solid rgba(217, 180, 111, 0.42);
			border-radius: 8px;
			color: #fff4df;
			line-height: 1.55;
			margin-top: 1rem;
			padding: 1rem;
		}

		.bb-bible-study__admin-note a {
			color: #d9b46f;
			font-weight: 800;
		}

		.bb-bible-study__quick-video {
			display: grid;
			gap: 0.75rem;
			grid-template-columns: minmax(0, 1fr) auto;
			margin-top: 0.85rem;
		}

		.bb-bible-study__quick-video input {
			background: #fff4df;
			border: 1px solid rgba(217, 180, 111, 0.72);
			border-radius: 8px;
			color: #17130e;
			min-height: 2.8rem;
			padding: 0.75rem 0.9rem;
			width: 100%;
		}

		.bb-bible-study__admin-controls {
			display: flex;
			flex-wrap: wrap;
			gap: 0.75rem;
			margin-top: 0.75rem;
		}

		.bb-bible-study__actions,
		.bb-bible-study__asset-list {
			display: flex;
			flex-wrap: wrap;
			gap: 0.75rem;
			list-style: none;
			margin: 1.25rem 0 0;
			padding: 0;
		}

		.bb-bible-study__button {
			align-items: center;
			background: #d9b46f;
			border: 1px solid #d9b46f;
			border-radius: 999px;
			color: #17130e;
			cursor: pointer;
			display: inline-flex;
			font-weight: 800;
			justify-content: center;
			min-height: 2.8rem;
			padding: 0.75rem 1.1rem;
			text-decoration: none;
		}

		.bb-bible-study__button--ghost {
			background: transparent;
			border-color: rgba(247, 238, 220, 0.35);
			color: #fff4df;
		}

		.bb-bible-study__chat-heading {
			align-items: center;
			display: flex;
			justify-content: space-between;
			gap: 1rem;
		}

		.bb-bible-study-chat__messages {
			background: rgba(5, 4, 3, 0.38);
			border: 1px solid rgba(247, 238, 220, 0.12);
			border-radius: 8px;
			display: grid;
			gap: 0.85rem;
			height: min(54vh, 520px);
			overflow: auto;
			padding: 1rem;
		}

		.bb-bible-study-chat__message {
			display: grid;
			gap: 0.75rem;
			grid-template-columns: 2.4rem minmax(0, 1fr);
		}

		.bb-bible-study-chat__avatar {
			border-radius: 50%;
			height: 2.4rem;
			width: 2.4rem;
		}

		.bb-bible-study-chat__meta {
			align-items: baseline;
			color: #f7eedc;
			display: flex;
			gap: 0.75rem;
			margin-bottom: 0.2rem;
		}

		.bb-bible-study-chat__meta span {
			color: #b9ab94;
			font-size: 0.82rem;
		}

		.bb-bible-study-chat__body p {
			color: #d8cbb6;
			line-height: 1.5;
			margin-bottom: 0;
		}

		.bb-bible-study-chat__tools {
			display: flex;
			gap: 0.5rem;
			margin-top: 0.5rem;
		}

		.bb-bible-study-chat__tools button {
			background: rgba(255, 249, 236, 0.08);
			border: 1px solid rgba(247, 238, 220, 0.16);
			border-radius: 999px;
			color: #fff4df;
			cursor: pointer;
			font-size: 0.75rem;
			padding: 0.35rem 0.6rem;
		}

		.bb-bible-study-chat__form {
			display: grid;
			gap: 0.75rem;
			margin-top: 0.75rem;
		}

		.bb-bible-study-chat__form textarea {
			background: rgba(255, 249, 236, 0.1);
			border: 1px solid rgba(247, 238, 220, 0.18);
			border-radius: 8px;
			color: #fff9ec;
			padding: 0.85rem;
			resize: vertical;
			width: 100%;
		}

		.bb-bible-study__panel h3 {
			color: #fff4df;
			font-size: 1rem;
			letter-spacing: 0.02em;
			margin-bottom: 1rem;
		}

		.bb-bible-study__panel {
			background: rgba(24, 19, 13, 0.82);
		}

		.bb-bible-study__panel--assets {
			align-self: start;
		}

		.bb-bible-study__asset-list {
			display: grid;
			gap: 0.7rem;
			margin-top: 0.8rem;
		}

		.bb-bible-study__asset-list li {
			min-width: 0;
		}

		.bb-bible-study__asset-list .bb-bible-study__button {
			border-radius: 8px;
			justify-content: flex-start;
			line-height: 1.25;
			min-height: 3.25rem;
			overflow-wrap: anywhere;
			padding: 0.8rem 0.95rem;
			text-align: left;
			width: 100%;
		}

		.bb-bible-study__panel--chat {
			display: grid;
			gap: 0.9rem;
		}

		.bb-bible-study-chat__messages {
			align-content: start;
			background: rgba(5, 4, 3, 0.52);
			height: min(38vh, 390px);
			min-height: 18rem;
		}

		.bb-bible-study-chat__message {
			background: rgba(255, 249, 236, 0.045);
			border: 1px solid rgba(247, 238, 220, 0.08);
			border-radius: 8px;
			padding: 0.75rem;
		}

		.bb-bible-study-chat__form {
			align-items: end;
			display: grid;
			gap: 0.65rem;
			grid-template-columns: minmax(0, 1fr) auto;
			margin-top: 0;
		}

		.bb-bible-study-chat__form textarea {
			background: #fff4df;
			border: 1px solid rgba(217, 180, 111, 0.72);
			color: #17130e;
			height: 3.6rem;
			min-height: 3.6rem;
			padding: 0.85rem 0.95rem;
		}

		.bb-bible-study-chat__form .bb-bible-study__button {
			border-radius: 8px;
			height: 3.6rem;
			min-height: 3.6rem;
			padding-inline: 1.35rem;
		}

		@media (max-width: 900px) {
			.bb-bible-study__live-layout {
				grid-template-columns: 1fr;
			}

			.bb-bible-study__video-header,
			.bb-bible-study__chat-heading {
				align-items: flex-start;
				flex-direction: column;
			}

			.bb-bible-study-chat__form {
				grid-template-columns: 1fr;
			}
		}

		.bb-bible-study__panel--chat {
			overflow: hidden;
		}

		.bb-bible-study-chat__messages {
			overflow-x: hidden;
			padding: 0.75rem;
		}

		.bb-bible-study-chat__message {
			grid-template-columns: 2.25rem minmax(0, 1fr);
			max-width: 100%;
			min-width: 0;
		}

		.bb-bible-study-chat__body {
			min-width: 0;
		}

		.bb-bible-study-chat__tools {
			flex-wrap: wrap;
		}

		.bb-bible-study-chat__form {
			align-items: stretch;
			background: #fff4df;
			border: 1px solid rgba(217, 180, 111, 0.72);
			border-radius: 8px;
			gap: 0;
			grid-template-columns: minmax(0, 1fr) 5.25rem;
			overflow: hidden;
		}

		.bb-bible-study-chat__form textarea {
			background: transparent;
			border: 0;
			border-radius: 0;
			box-shadow: none;
			height: 3.25rem;
			min-height: 3.25rem;
			padding: 0.8rem 0.95rem;
			resize: none;
		}

		.bb-bible-study-chat__form .bb-bible-study__button {
			border: 0;
			border-left: 1px solid rgba(23, 19, 14, 0.12);
			border-radius: 0;
			box-shadow: none;
			height: auto;
			min-height: 3.25rem;
			padding: 0 0.9rem;
		}

		@media (max-width: 560px) {
			.bb-bible-study-chat__form {
				grid-template-columns: 1fr;
			}

			.bb-bible-study-chat__form .bb-bible-study__button {
				border-left: 0;
				border-top: 1px solid rgba(23, 19, 14, 0.12);
			}
		}

		.bb-bible-study__panel h3 {
			font-size: clamp(1.05rem, 1.5vw, 1.25rem);
			line-height: 1.15;
			margin: 0;
		}

		.bb-bible-study__panel--assets {
			padding: clamp(1.4rem, 2.5vw, 2rem);
		}

		.bb-bible-study__panel--assets h3 {
			margin-bottom: 1.35rem;
		}

		.bb-bible-study__panel--chat {
			background: rgba(17, 13, 8, 0.9);
			display: grid;
			gap: 0;
			overflow: hidden;
			padding: 0;
		}

		.bb-bible-study__chat-heading {
			align-items: center;
			border-bottom: 1px solid rgba(247, 238, 220, 0.1);
			display: flex;
			gap: 1rem;
			justify-content: space-between;
			padding: clamp(1.3rem, 2.3vw, 1.8rem) clamp(1.25rem, 2.5vw, 1.8rem);
		}

		.bb-bible-study__chat-state {
			color: #d9b46f;
			flex: 0 0 auto;
			font-size: 0.74rem;
			font-weight: 900;
			letter-spacing: 0.16em;
			text-transform: uppercase;
		}

		.bb-bible-study-chat {
			display: grid;
			grid-template-rows: minmax(18rem, 1fr) auto auto;
			min-height: 34rem;
		}

		.bb-bible-study-chat__messages {
			align-content: start;
			background: rgba(5, 4, 3, 0.48);
			border: 0;
			border-radius: 0;
			display: grid;
			gap: 0.75rem;
			height: auto;
			min-height: 18rem;
			overflow-x: hidden;
			overflow-y: auto;
			padding: clamp(1rem, 2.2vw, 1.35rem);
		}

		.bb-bible-study-chat__message {
			background: rgba(255, 249, 236, 0.055);
			border: 1px solid rgba(247, 238, 220, 0.11);
			border-radius: 8px;
			display: grid;
			gap: 0.75rem;
			grid-template-columns: 2.35rem minmax(0, 1fr);
			min-width: 0;
			padding: 0.85rem;
		}

		.bb-bible-study-chat__avatar {
			background: rgba(255, 249, 236, 0.85);
			border: 1px solid rgba(255, 249, 236, 0.2);
			border-radius: 50%;
			height: 2.35rem;
			object-fit: cover;
			width: 2.35rem;
		}

		.bb-bible-study-chat__body {
			min-width: 0;
		}

		.bb-bible-study-chat__meta {
			align-items: baseline;
			display: flex;
			flex-wrap: wrap;
			gap: 0.45rem 0.7rem;
			line-height: 1.2;
			margin: 0 0 0.35rem;
		}

		.bb-bible-study-chat__meta strong {
			color: #fff9ec;
			font-size: 0.98rem;
		}

		.bb-bible-study-chat__meta span {
			color: #b8aa92;
			font-size: 0.82rem;
		}

		.bb-bible-study-chat__body p {
			color: #e3d7c3;
			font-size: 0.98rem;
			line-height: 1.45;
			margin: 0;
			overflow-wrap: anywhere;
		}

		.bb-bible-study-chat__tools {
			display: flex;
			flex-wrap: wrap;
			gap: 0.45rem;
			margin-top: 0.65rem;
		}

		.bb-bible-study-chat__tools button {
			background: rgba(255, 249, 236, 0.08);
			border: 1px solid rgba(247, 238, 220, 0.18);
			border-radius: 999px;
			color: #fff4df;
			cursor: pointer;
			font-size: 0.76rem;
			line-height: 1;
			padding: 0.45rem 0.7rem;
		}

		.bb-bible-study-chat__form {
			align-items: stretch;
			background: rgba(255, 244, 223, 0.98);
			border: 0;
			border-top: 1px solid rgba(217, 180, 111, 0.45);
			border-radius: 0;
			display: grid;
			gap: 0;
			grid-template-columns: minmax(0, 1fr) 5.8rem;
			margin: 0;
			overflow: hidden;
		}

		.bb-bible-study-chat__form textarea {
			background: transparent;
			border: 0;
			border-radius: 0;
			box-shadow: none;
			color: #17130e;
			font: inherit;
			height: 4.25rem;
			line-height: 1.4;
			min-height: 4.25rem;
			padding: 1rem 1.1rem;
			resize: none;
			width: 100%;
		}

		.bb-bible-study-chat__form textarea::placeholder {
			color: rgba(23, 19, 14, 0.48);
		}

		.bb-bible-study-chat__form .bb-bible-study__button {
			background: #061f18;
			border: 0;
			border-left: 1px solid rgba(23, 19, 14, 0.14);
			border-radius: 0;
			box-shadow: none;
			color: #fff4df;
			height: auto;
			min-height: 4.25rem;
			padding: 0 1rem;
		}

		.bb-bible-study-chat__notice {
			color: #d8cbb6;
			font-size: 0.9rem;
			margin: 0;
			padding: 0.85rem 1.15rem 1rem;
		}

		.bb-bible-study-chat__notice:empty {
			display: none;
		}

		@media (max-width: 900px) {
			.bb-bible-study__chat-heading {
				align-items: flex-start;
				flex-direction: column;
			}
		}

		@media (max-width: 560px) {
			.bb-bible-study-chat {
				min-height: 30rem;
			}

			.bb-bible-study-chat__form {
				grid-template-columns: 1fr;
			}

			.bb-bible-study-chat__form .bb-bible-study__button {
				border-left: 0;
				border-top: 1px solid rgba(23, 19, 14, 0.14);
				min-height: 3.25rem;
			}
		}

		.bb-bible-study__archive-list {
			display: grid;
			gap: 0.9rem;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			margin-top: 1rem;
		}

		.bb-bible-study__archive-card {
			background: rgba(17, 13, 8, 0.86);
			border: 1px solid rgba(247, 238, 220, 0.14);
			border-radius: 8px;
			color: #fff9ec;
			display: grid;
			gap: 0.65rem;
			min-width: 0;
			padding: 1rem;
			text-decoration: none;
		}

		.bb-bible-study__archive-card:hover,
		.bb-bible-study__archive-card:focus {
			border-color: rgba(217, 180, 111, 0.55);
			color: #fff9ec;
			transform: translateY(-1px);
		}

		.bb-bible-study__archive-card h3 {
			color: #fff9ec;
			font-size: 1.05rem;
			line-height: 1.2;
			margin: 0;
			overflow-wrap: anywhere;
		}

		.bb-bible-study__archive-card p {
			color: #d8cbb6;
			font-size: 0.9rem;
			line-height: 1.45;
			margin: 0;
		}

		.bb-bible-study__archive-action {
			color: #d9b46f;
			font-size: 0.76rem;
			font-weight: 900;
			letter-spacing: 0.14em;
			text-transform: uppercase;
		}

		@media (max-width: 900px) {
			.bb-bible-study__archive-list {
				grid-template-columns: 1fr;
			}

			.bb-bible-study__quick-video {
				grid-template-columns: 1fr;
			}
		}
	</style>

	<div class="bb-bible-study__wrap">
		<div class="bb-bible-study__status">
			<span class="bb-bible-study__status-dot<?php echo 'live' === $state ? ' bb-bible-study__status-dot--live' : ''; ?>" aria-hidden="true"></span>
			<span>
				<?php
				echo esc_html(
					array(
						'live'     => __( 'Live now', 'brendon-core' ),
						'waiting'  => __( 'Starting soon', 'brendon-core' ),
						'upcoming' => __( 'Next study scheduled', 'brendon-core' ),
						'offline'  => __( 'Past studies', 'brendon-core' ),
					)[ $state ] ?? __( 'Bible Study', 'brendon-core' )
				);
				?>
			</span>
		</div>

		<div class="bb-bible-study__grid">
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
					<?php $admin_video_meta = brendon_core_bible_study_session_meta( $admin_video_session->ID ); ?>
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
				<?php if ( $upcoming ) : ?>
					<section class="bb-bible-study__panel">
						<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Next Bible Study', 'brendon-core' ); ?></p>
						<h1><?php echo esc_html( get_the_title( $upcoming ) ); ?></h1>
						<p><?php echo esc_html( brendon_core_bible_study_format_session_time( $upcoming->ID ) ); ?></p>
					</section>
				<?php endif; ?>

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
		</div>
	</div>

	<?php if ( in_array( $state, array( 'live', 'waiting' ), true ) ) : ?>
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
