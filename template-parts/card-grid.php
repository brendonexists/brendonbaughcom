<?php
/**
 * Card markup used in the featured grid on the home and archive templates.
 *
 * @package brendon-core
 */

$grid_span = get_query_var( 'grid_span', '' );
$accent_class = ! empty( $grid_span ) ? 'post-card--accent' : '';
$mosaic_item_class = '';

if ( isset( $args ) && is_array( $args ) && ! empty( $args['mosaic_item_class'] ) ) {
	$mosaic_item_class = $args['mosaic_item_class'];
}

$card_classes = trim( "post-card relative flex h-full flex-col gap-3 rounded-3xl border border-border bg-white p-5 shadow-lg transition hover:-translate-y-0.5 hover:shadow-2xl {$grid_span} {$accent_class} {$mosaic_item_class}" );
?>

<article class="<?php echo esc_attr( $card_classes ); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-card__media overflow-hidden rounded-2xl">
			<a class="group block" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'large', [ 'class' => 'post-card__image h-full w-full object-cover transition duration-300 group-hover:scale-105' ] ); ?>
			</a>
		</div>
	<?php endif; ?>

	<header class="post-card__body flex flex-1 flex-col gap-2">
		<div class="text-xs font-semibold uppercase tracking-wider text-primary">
			<?php the_category( ' · ' ); ?>
		</div>
		<h3 class="line-clamp-2 text-xl font-bold leading-tight">
			<a class="text-slate-900 transition-colors hover:text-primary" href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<div class="line-clamp-4 text-sm text-slate-600">
			<?php echo wp_trim_words( get_the_excerpt(), 35, '…' ); ?>
		</div>
	</header>

	<div class="mt-auto flex items-center justify-between text-xs uppercase tracking-wide text-slate-500">
		<span><?php echo esc_html( get_the_date() ); ?></span>
		<span><?php echo esc_html( get_the_author() ); ?></span>
	</div>
</article>
