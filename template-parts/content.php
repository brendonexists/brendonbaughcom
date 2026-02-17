<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package brendon-core
 */

$permalink  = get_permalink();
$title      = get_the_title();
$date       = get_the_date();
$author     = get_the_author();
$category_objects = get_the_category();
$category_badges = '';
if ('post' === get_post_type() && ! empty($category_objects)) {
	foreach ($category_objects as $category_object) {
		$category_link = get_category_link($category_object);
		if (is_wp_error($category_link) || ! $category_link) {
			continue;
		}

		$category_badges .= sprintf(
			'<a href="%s" class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-[11px] font-semibold text-slate-900 transition hover:bg-primary/15 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">%s</a>',
			esc_url($category_link),
			esc_html($category_object->name)
		);
	}
}
$has_thumb  = has_post_thumbnail();

$post_content_value = get_post_field('post_content', get_the_ID());
$word_count = str_word_count(wp_strip_all_tags($post_content_value ?: ''));
$reading_minutes = max(1, (int) ceil($word_count / 200));
$reading_time = '';
if ('post' === get_post_type()) {
	$reading_time = sprintf(
		/* translators: %s: minutes of reading time. */
		_n('%s min read', '%s min read', $reading_minutes, 'brendon-core'),
		number_format_i18n($reading_minutes)
	);
}

$card_classes = 'group relative overflow-hidden rounded-2xl border border-border bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-[1px] hover:shadow-lg focus-within:-translate-y-[1px] focus-within:shadow-lg';
$content_card_classes = is_singular() ? $card_classes : $card_classes . ' flex flex-col gap-4 h-full';
if (! is_singular()) {
	$tile_span = get_query_var('content_tile_span', '');
	if ($tile_span) {
		$content_card_classes .= " {$tile_span}";
	}
}

if (is_singular()) :
	$is_video_post = brendon_core_is_video_post();
	$hide_page_featured = is_page() && function_exists( 'brendon_core_hide_page_featured_image' ) && brendon_core_hide_page_featured_image();
	$show_featured_image = $has_thumb && ! $is_video_post && ! $hide_page_featured;
?>

	<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
		<div class="w-full px-6 py-8">
			<div class="mb-6 lg:hidden">
				<?php get_template_part('template-parts/mobile-sidebar-panel'); ?>
			</div>
			<div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">

				<aside class="hidden lg:block lg:sticky lg:top-8 self-start">
					<?php get_template_part('template-parts/sidebar-panel'); ?>
				</aside>

				<section class="space-y-6">

					<article id="post-<?php the_ID(); ?>" <?php post_class($content_card_classes); ?>>

						<div class="flex flex-col gap-3">

							<?php if ($show_featured_image) : ?>
								<div class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm max-h-[420px]">
									<?php the_post_thumbnail('large', ['class' => 'h-full w-full object-cover transition duration-300 group-hover:scale-105']); ?>
								</div>
							<?php elseif (! $is_video_post && ! $hide_page_featured) : ?>
								<div class="flex h-64 items-center justify-center rounded-2xl border border-border bg-surface-2 text-sm font-semibold uppercase tracking-wide text-slate-500">
									<?php esc_html_e('No featured image', 'brendon-core'); ?>
								</div>
							<?php endif; ?>

							<header class="flex flex-col gap-3">
								<div class="flex flex-wrap items-start justify-between gap-3">
									<h1 class="text-3xl font-bold tracking-tight leading-tight text-slate-900 transition-colors duration-200">
										<?php echo esc_html($title); ?>
									</h1>

									<?php if ($category_badges) : ?>
										<div class="ml-auto flex flex-wrap justify-end gap-2 text-right">
											<?php echo wp_kses_post($category_badges); ?>
										</div>
									<?php endif; ?>
								</div>

								<?php if ('post' === get_post_type()) : ?>
									<div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
										<span><?php echo esc_html($date); ?></span>
										<span class="text-slate-300">•</span>
										<span><?php echo esc_html($author); ?></span>
										<?php if ($reading_time) : ?>
											<span class="text-slate-300">•</span>
											<span><?php echo esc_html($reading_time); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<div class="mt-3 h-px w-full bg-danger"></div>
							</header>

							<div class="entry-content prose prose-lg max-w-none text-slate-600 leading-relaxed prose-headings:font-bold prose-headings:text-slate-900 prose-blockquote:border-border prose-blockquote:border-l-4 prose-blockquote:bg-surface-2 prose-blockquote:text-slate-800 prose-blockquote:px-4 prose-blockquote:py-2 prose-blockquote:rounded-lg prose-a:text-primary prose-a:hover:text-primary-hover [&_iframe]:w-full [&_iframe]:max-w-full [&_iframe]:rounded-2xl [&_iframe]:border [&_iframe]:border-border [&_iframe]:shadow-lg [&_iframe]:bg-white [&_iframe]:mx-auto [&_iframe]:transition [&_iframe]:duration-300 [&_iframe]:ease-in-out [&_video]:w-full [&_video]:rounded-2xl [&_video]:shadow-lg prose-p:mt-0 prose-p:mb-2 prose-figure:mt-2 prose-figure:mb-2 prose-iframe:mt-2 prose-iframe:mb-2">
								<?php
								the_content();
								wp_link_pages(
									[
										'before' => '<div class="mt-6 text-sm text-slate-600">' . esc_html__('Pages:', 'brendon-core') . ' ',
										'after'  => '</div>',
									]
								);
								?>
							</div>

							<?php if ('post' === get_post_type()) : ?>
								<footer class="pt-2 text-sm text-slate-600">
									<?php the_tags('Tags: ', ', ', ''); ?>
								</footer>
							<?php endif; ?>

						</div>

					</article>

					<?php if (comments_open() || get_comments_number()) : ?>
						<div class="mt-10">
							<?php comments_template(); ?>
						</div>
					<?php endif; ?>

				</section>

			</div>
		</div>
	</main>

<?php else : ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class($content_card_classes); ?>>
		<div class="flex flex-col gap-4">

			<?php if ($has_thumb) : ?>
				<a class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm transition duration-300 group-hover:shadow-lg h-64" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
					<?php the_post_thumbnail('large', ['class' => 'h-full w-full object-cover transition duration-300 group-hover:scale-105']); ?>
				</a>
			<?php else : ?>
				<div class="flex h-64 items-center justify-center rounded-2xl border border-border bg-surface-2 text-sm font-semibold uppercase tracking-wide text-slate-500">
					<?php esc_html_e('No featured image', 'brendon-core'); ?>
				</div>
			<?php endif; ?>

			<header class="flex flex-col gap-3">
				<?php if ($category_badges) : ?>
					<div class="flex flex-wrap gap-2">
						<?php echo wp_kses_post($category_badges); ?>
					</div>
				<?php endif; ?>

				<h2 class="text-2xl font-bold tracking-tight leading-tight">
					<a class="text-slate-900 transition-colors duration-200 hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" href="<?php echo esc_url($permalink); ?>" rel="bookmark">
						<?php echo esc_html($title); ?>
					</a>
				</h2>

				<?php if ('post' === get_post_type()) : ?>
					<div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
						<span><?php echo esc_html($date); ?></span>
						<span class="text-slate-300">•</span>
						<span><?php echo esc_html($author); ?></span>
						<?php if ($reading_time) : ?>
							<span class="text-slate-300">•</span>
							<span><?php echo esc_html($reading_time); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			<div class="prose prose-lg max-w-none text-slate-700 leading-relaxed prose-headings:text-slate-900 prose-headings:font-bold prose-blockquote:border-border prose-blockquote:border-l-4 prose-blockquote:bg-surface-2 prose-blockquote:text-slate-800 prose-blockquote:px-4 prose-blockquote:py-2 prose-blockquote:rounded-lg prose-a:text-primary prose-a:hover:text-primary-hover prose-p:mt-0 prose-p:mb-2 prose-figure:mt-2 prose-figure:mb-2 prose-iframe:mt-2 prose-iframe:mb-2">
				<?php $archive_excerpt = wp_trim_words(get_the_excerpt(), 16, '…'); ?>
				<p><?php echo esc_html($archive_excerpt); ?></p>
			</div>

			<div>
				<a class="inline-flex items-center gap-2 rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" href="<?php echo esc_url($permalink); ?>">
					<?php esc_html_e('Read more', 'brendon-core'); ?>
					<span aria-hidden="true">→</span>
				</a>
			</div>

		</div>
	</article>

<?php endif; ?>
