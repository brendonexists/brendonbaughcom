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
	<div class="bb-bible-study__wrap">
		<header class="bb-bible-study__hero">
			<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Bible Study Archive', 'brendon-core' ); ?></p>
			<h1><?php echo esc_html__( 'Past Live Bible Studies', 'brendon-core' ); ?></h1>
			<p><?php echo esc_html__( 'Rewatch previous studies, then log in on a study page to access member resources and discussion replay.', 'brendon-core' ); ?></p>
		</header>

		<?php
		brendon_core_bible_study_render_schedule(
			array(
				'title'       => __( 'Bible Study Schedule', 'brendon-core' ),
				'description' => __( 'A calendar view of upcoming sessions and past studies.', 'brendon-core' ),
				'limit'       => 48,
			)
		);
		?>

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
