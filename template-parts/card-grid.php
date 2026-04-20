<?php
/**
 * Editorial post card.
 *
 * @package brendon-core
 */

$categories = get_the_category();
$category = ! empty( $categories ) ? $categories[0] : null;
$post_content_value = get_post_field( 'post_content', get_the_ID() );
$word_count = str_word_count( wp_strip_all_tags( $post_content_value ?: '' ) );
$reading_minutes = max( 1, (int) ceil( $word_count / 200 ) );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bb-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="bb-post-card__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php the_post_thumbnail( 'brendon-core-card' ); ?>
		</a>
	<?php endif; ?>

	<div class="bb-post-card__body">
		<div class="bb-post-card__meta">
			<?php if ( $category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<span aria-hidden="true">/</span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>

		<h2 class="bb-post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<p class="bb-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '...' ) ); ?></p>

		<div class="bb-post-card__footer">
			<span>
				<?php
				printf(
					/* translators: %s: minutes of reading time. */
					esc_html__( '%s min read', 'brendon-core' ),
					esc_html( number_format_i18n( $reading_minutes ) )
				);
				?>
			</span>
			<a href="<?php the_permalink(); ?>"><?php esc_html_e('Read', 'brendon-core'); ?></a>
		</div>
	</div>
</article>
