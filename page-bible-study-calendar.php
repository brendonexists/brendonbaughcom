<?php
/**
 * Template Name: Bible Study Calendar
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="bb-bible-study bb-bible-study--calendar">
	<div class="bb-bible-study__wrap">
		<header class="bb-bible-study__hero">
			<p class="bb-bible-study__kicker"><?php echo esc_html__( 'Bible Study', 'brendon-core' ); ?></p>
			<h1><?php echo esc_html__( 'Study Calendar', 'brendon-core' ); ?></h1>
			<p><?php echo esc_html__( 'Browse upcoming scheduled Bible studies and revisit previous sessions from one place.', 'brendon-core' ); ?></p>
		</header>

		<?php
		brendon_core_bible_study_render_schedule(
			array(
				'title'       => __( 'Upcoming and Past Studies', 'brendon-core' ),
				'description' => __( 'This schedule uses the same published Bible Study Sessions that power the live page, video archive, resources, and chat replay.', 'brendon-core' ),
				'limit'       => 72,
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
