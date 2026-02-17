<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package _s
 */

get_header();
?>

<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
	<div class="w-full px-6 py-8 space-y-8">

		<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

			<aside class="lg:col-span-3 lg:sticky lg:top-8 self-start">
				<?php get_template_part('template-parts/sidebar-panel'); ?>
			</aside>

			<section class="lg:col-span-9 space-y-6">

				<header class="rounded-3xl border border-border bg-white p-8 shadow-lg">
					<h1 class="text-3xl font-semibold tracking-tight text-slate-900">
						<?php
						/* translators: %s: search query. */
						printf(
							esc_html__('Search results for: %s', 'brendon-core'),
							'<span class="text-primary">' . esc_html(get_search_query()) . '</span>'
						);
						?>
					</h1>
				</header>

				<?php if (have_posts()) : ?>

					<div class="grid grid-cols-1 gap-6">
						<?php
						while (have_posts()) :
							the_post();
							get_template_part('template-parts/content');
						endwhile;
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
						<nav class="mt-10" aria-label="<?php esc_attr_e('Search pagination', 'brendon-core'); ?>">
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

					<div class="rounded-3xl border border-border bg-white p-8 shadow-lg">
						<h2 class="text-2xl font-semibold tracking-tight text-slate-900"><?php esc_html_e('No results found', 'brendon-core'); ?></h2>
						<p class="mt-2 text-slate-600"><?php esc_html_e('Try a different search term and see what comes up.', 'brendon-core'); ?></p>
					</div>

				<?php endif; ?>

			</section>

		</div>

	</div>
</main>

<?php
get_footer();
