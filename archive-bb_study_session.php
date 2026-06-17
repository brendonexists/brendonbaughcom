<?php
/**
 * Bible Study Session archive index.
 *
 * @package brendon-core
 */

get_header();

$past_sessions = brendon_core_bible_study_past_sessions( 24 );
?>

<main id="primary" class="bb-bible-study bb-bible-study--archive-index">
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
			padding: clamp(1.5rem, 4vw, 3rem) 1.25rem;
		}

		.bb-bible-study__hero {
			margin-bottom: clamp(1.25rem, 3vw, 2rem);
			max-width: 760px;
		}

		.bb-bible-study__kicker {
			color: #d9b46f;
			font-size: 0.78rem;
			font-weight: 800;
			letter-spacing: 0.14em;
			margin: 0 0 0.65rem;
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
			font-size: clamp(2.35rem, 5vw, 4.5rem);
			line-height: 1;
			margin-bottom: 0.8rem;
		}

		.bb-bible-study__hero p {
			color: #d8cbb6;
			font-size: 1.02rem;
			line-height: 1.6;
		}

		.bb-bible-study__archive-list {
			display: grid;
			gap: 1rem;
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}

		.bb-bible-study__archive-card {
			background: rgba(17, 13, 8, 0.88);
			border: 1px solid rgba(247, 238, 220, 0.14);
			border-radius: 8px;
			box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.22);
			color: #fff9ec;
			display: grid;
			gap: 0.75rem;
			min-width: 0;
			padding: clamp(1rem, 2vw, 1.25rem);
			text-decoration: none;
		}

		.bb-bible-study__archive-card:hover,
		.bb-bible-study__archive-card:focus {
			border-color: rgba(217, 180, 111, 0.55);
			color: #fff9ec;
			transform: translateY(-1px);
		}

		.bb-bible-study__archive-card h2 {
			color: #fff9ec;
			font-size: clamp(1.15rem, 2vw, 1.45rem);
			line-height: 1.16;
			margin: 0;
			overflow-wrap: anywhere;
		}

		.bb-bible-study__archive-card p {
			color: #d8cbb6;
			line-height: 1.5;
			margin: 0;
		}

		.bb-bible-study__archive-action {
			color: #d9b46f;
			font-size: 0.76rem;
			font-weight: 900;
			letter-spacing: 0.14em;
			text-transform: uppercase;
		}

		.bb-bible-study__empty {
			background: rgba(255, 249, 236, 0.075);
			border: 1px solid rgba(247, 238, 220, 0.16);
			border-radius: 8px;
			color: #d8cbb6;
			line-height: 1.6;
			padding: clamp(1rem, 3vw, 1.5rem);
		}

		@media (max-width: 900px) {
			.bb-bible-study__archive-list {
				grid-template-columns: 1fr;
			}
		}
	</style>

	<div class="bb-bible-study__wrap">
		<header class="bb-bible-study__hero">
			<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Bible Study Archive', 'brendon-core' ); ?></p>
			<h1><?php echo esc_html__( 'Past Live Bible Studies', 'brendon-core' ); ?></h1>
			<p><?php echo esc_html__( 'Rewatch previous studies, then log in on a study page to access member resources and discussion replay.', 'brendon-core' ); ?></p>
		</header>

		<?php if ( $past_sessions ) : ?>
			<section class="bb-bible-study__archive-list" aria-label="<?php echo esc_attr__( 'Past Bible Study Sessions', 'brendon-core' ); ?>">
				<?php foreach ( $past_sessions as $past_session ) : ?>
					<a class="bb-bible-study__archive-card" href="<?php echo esc_url( get_permalink( $past_session ) ); ?>">
						<h2><?php echo esc_html( get_the_title( $past_session ) ); ?></h2>
						<?php $session_time = brendon_core_bible_study_format_session_time( $past_session->ID ); ?>
						<?php if ( $session_time ) : ?>
							<p><?php echo esc_html( $session_time ); ?></p>
						<?php endif; ?>
						<span class="bb-bible-study__archive-action"><?php echo esc_html__( 'View Study', 'brendon-core' ); ?></span>
					</a>
				<?php endforeach; ?>
			</section>
		<?php else : ?>
			<section class="bb-bible-study__empty">
				<p><?php echo esc_html__( 'No archived Bible studies are ready yet. Once a scheduled session has a YouTube archive video, it will show up here automatically.', 'brendon-core' ); ?></p>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
