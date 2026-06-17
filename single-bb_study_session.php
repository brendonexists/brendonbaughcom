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

			.bb-bible-study__hero {
				margin-bottom: 0.9rem;
				max-width: 780px;
			}

			.bb-bible-study__kicker {
				color: #d9b46f;
				font-size: 0.78rem;
				font-weight: 700;
				letter-spacing: 0.14em;
				margin: 0 0 0.6rem;
				text-transform: uppercase;
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

			.bb-bible-study__video-panel,
			.bb-bible-study__panel,
			.bb-bible-study__login {
				background: rgba(255, 249, 236, 0.075);
				border: 1px solid rgba(247, 238, 220, 0.16);
				border-radius: 8px;
				box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.24);
			}

			.bb-bible-study__video-panel {
				overflow: hidden;
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

			.bb-bible-study__empty,
			.bb-bible-study__panel,
			.bb-bible-study__login {
				color: #d8cbb6;
				line-height: 1.65;
				padding: clamp(1rem, 3vw, 1.5rem);
			}

			.bb-bible-study__member-grid {
				align-items: start;
				display: grid;
				gap: 1rem;
				grid-template-columns: minmax(0, 0.85fr) minmax(320px, 1.15fr);
				margin-top: 1rem;
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

			.bb-bible-study__chat-state {
				color: #d9b46f;
				font-size: 0.8rem;
				font-weight: 800;
				text-transform: uppercase;
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

			.bb-bible-study-chat__body p,
			.bb-bible-study-chat__notice {
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
				border-radius: 8px;
				color: #17130e;
				height: 3.6rem;
				min-height: 3.6rem;
				padding: 0.85rem 0.95rem;
				resize: vertical;
				width: 100%;
			}

			.bb-bible-study-chat__form .bb-bible-study__button {
				border-radius: 8px;
				height: 3.6rem;
				min-height: 3.6rem;
				padding-inline: 1.35rem;
			}

			@media (max-width: 900px) {
				.bb-bible-study__member-grid {
					grid-template-columns: 1fr;
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
		</style>

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
