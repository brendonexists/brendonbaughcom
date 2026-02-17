<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
	<div class="w-full px-6 py-8 space-y-8">

		<div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">

			<aside class="lg:sticky lg:top-8 self-start">
				<?php get_template_part('template-parts/sidebar-panel'); ?>
			</aside>

			<section class="space-y-6">



				<?php if (have_posts()) : ?>

					<?php
					if (is_category()) {
						$archive_heading = single_cat_title('', false);
					} elseif (is_tag()) {
						$archive_heading = single_tag_title('', false);
					} elseif (is_tax()) {
						$archive_heading = single_term_title('', false);
					} elseif (is_post_type_archive()) {
						$archive_heading = post_type_archive_title('', false);
					} elseif (is_author()) {
						$author_obj = get_queried_object();
						$archive_heading = $author_obj ? ($author_obj->display_name ?? '') : '';
					} else {
						$archive_heading = get_the_archive_title();
					}

					if (! $archive_heading) {
						$archive_heading = get_the_archive_title();
					}
					?>

					<header class="rounded-3xl border border-border bg-white p-8 shadow-lg">
						<!-- <p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary"><?php esc_html_e('Archive', 'brendon-core'); ?></p> -->
						<h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
							<?php echo esc_html($archive_heading); ?>
						</h1>
						<?php if (is_category() || is_tag() || is_post_type_archive()) : ?>
							<p class="mt-2 text-base text-slate-600">
								<?php the_archive_description(); ?>
							</p>
						<?php endif; ?>
					</header>

					<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
						<?php
						$post_index = 0;
						while (have_posts()) :
							the_post();
							$post_index++;
							$span_class = 0 === $post_index % 5 ? 'lg:col-span-2' : '';
							set_query_var('grid_span', $span_class);
							get_template_part('template-parts/card-grid');
						endwhile;
						set_query_var('grid_span', '');
						?>
					</div>

					<?php
					$pagination = paginate_links([
						'type' => 'array',
						'prev_text' => esc_html__('← Previous', 'brendon-core'),
						'next_text' => esc_html__('Next →', 'brendon-core'),
					]);

					if (!empty($pagination) && is_array($pagination)) :
					?>
						<nav class="mt-10" aria-label="<?php esc_attr_e('Archive pagination', 'brendon-core'); ?>">
							<ul class="flex flex-wrap gap-2">
								<?php foreach ($pagination as $link) : ?>
									<li class="[&>a]:inline-flex [&>a]:items-center [&>a]:rounded-lg [&>a]:border [&>a]:border-border [&>a]:bg-white [&>a]:px-3 [&>a]:py-2 [&>a]:text-sm [&>a]:text-slate-700 [&>a]:shadow-sm [&>a:hover]:bg-primary/10 [&>span]:inline-flex [&>span]:items-center [&>span]:rounded-lg [&>span]:border [&>span]:border-primary [&>span]:bg-primary [&>span]:px-3 [&>span]:py-2 [&>span]:text-sm [&>span]:text-white [&>span]:shadow-sm">
										<?php echo wp_kses_post($link); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

				<?php else : ?>

					<div class="rounded-xl border border-border bg-white p-8 shadow-sm">
						<h2 class="text-2xl font-semibold tracking-tight"><?php esc_html_e('Nothing found', 'brendon-core'); ?></h2>
						<p class="mt-2 text-slate-600"><?php esc_html_e('It seems we can’t find what you’re looking for.', 'brendon-core'); ?></p>
					</div>

				<?php endif; ?>

			</section>

		</div>

	</div>
</main>

<?php
get_footer();
