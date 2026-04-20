<?php
/**
 * Pagination component.
 *
 * @package brendon-core
 */

$pagination = paginate_links(
	[
		'type'      => 'array',
		'prev_text' => esc_html__('Previous', 'brendon-core'),
		'next_text' => esc_html__('Next', 'brendon-core'),
	]
);

if ( empty( $pagination ) || ! is_array( $pagination ) ) {
	return;
}
?>

<nav class="bb-pagination" aria-label="<?php echo esc_attr_x('Pagination', 'pagination label', 'brendon-core'); ?>">
	<ul>
		<?php foreach ( $pagination as $link ) : ?>
			<li><?php echo wp_kses_post( $link ); ?></li>
		<?php endforeach; ?>
	</ul>
</nav>
