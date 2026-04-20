<?php
/**
 * 404 template.
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="bb-main">
	<section class="bb-page-hero bb-section">
		<div class="bb-wrap">
			<p class="bb-kicker"><?php esc_html_e('404', 'brendon-core'); ?></p>
			<h1><?php esc_html_e('That page is not in the record.', 'brendon-core'); ?></h1>
			<p><?php esc_html_e('The link may be old, moved, or mistyped. Search the archive or head back home.', 'brendon-core'); ?></p>
			<div class="bb-actions">
				<a class="bb-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Return home', 'brendon-core'); ?></a>
				<a class="bb-button bb-button--ghost" href="<?php echo esc_url( home_url( '/writing' ) ); ?>"><?php esc_html_e('Browse writing', 'brendon-core'); ?></a>
			</div>
			<?php get_search_form(); ?>
		</div>
	</section>
</main>

<?php
get_footer();
