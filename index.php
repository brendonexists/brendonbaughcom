<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package brendon-core
 */

get_header();
?>

<main id="primary" class="site-main min-h-screen bg-[#F2F2F2] text-slate-900 dark:bg-slate-950 dark:text-slate-100">
	<div class="w-full px-6 py-8">

		<div class="lg:hidden mb-2">
			<?php get_template_part('template-parts/mobile-sidebar-panel'); ?>
		</div>

		<div class="grid grid-cols-1 gap-8 lg:grid-cols-[330px_1fr]">

			<aside class="hidden lg:block">
				<?php get_template_part('template-parts/sidebar-panel'); ?>
			</aside>

			<section class="space-y-6">

				<?php
				$slider_limit   = defined( 'BB_HOME_SLIDER_POSTS' ) ? (int) BB_HOME_SLIDER_POSTS : 3;
				$featured_args  = [
					'posts_per_page'      => $slider_limit,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'meta_query'          => [
						[
							'key'     => 'brendon_core_featured_post',
							'value'   => '1',
							'compare' => '=',
						],
					],
				];
				$featured_query = new WP_Query($featured_args);
				$slider_posts  = $featured_query->have_posts() ? $featured_query->posts : [];
				$featured_ids  = wp_list_pluck($slider_posts, 'ID');
				wp_reset_postdata();

				if (count($slider_posts) < $slider_limit) {
					$excluded       = wp_list_pluck($slider_posts, 'ID');
					$fill_query     = new WP_Query(
						[
							'posts_per_page'      => $slider_limit - count($slider_posts),
							'post_status'         => 'publish',
							'ignore_sticky_posts' => true,
							'post__not_in'        => $excluded,
						]
					);
					if ($fill_query->have_posts()) {
						$slider_posts = array_merge($slider_posts, $fill_query->posts);
					}
					wp_reset_postdata();
				}

				$slider_posts = array_slice($slider_posts, 0, $slider_limit);
				?>

				<?php if ($slider_posts) : ?>
					<section class="space-y-4 w-full">
						<!-- <div class="flex items-center justify-end">
							<div class="flex gap-2">
								<button data-slider-prev class="rounded-full border border-[#F2A25C]/40 bg-white p-2 text-[#F26D3D] shadow-sm transition hover:bg-[#F2EB8D]/60 dark:bg-slate-800 dark:border-[#F2A25C]/30">
									<span class="sr-only"><?php esc_html_e('Previous featured post', 'brendon-core'); ?></span>
									<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="15 18 9 12 15 6"></polyline>
									</svg>
								</button>
								<button data-slider-next class="rounded-full border border-[#F2A25C]/40 bg-white p-2 text-[#F26D3D] shadow-sm transition hover:bg-[#F2EB8D]/60 dark:bg-slate-800 dark:border-[#F2A25C]/30">
									<span class="sr-only"><?php esc_html_e('Next featured post', 'brendon-core'); ?></span>
									<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="9 18 15 12 9 6"></polyline>
									</svg>
								</button>
							</div>
						</div> -->

						<div class="relative w-full" role="region" aria-label="<?php esc_attr_e('Featured posts slider', 'brendon-core'); ?>">
							<div data-slider-track class="flex w-full flex-nowrap gap-4 overflow-x-auto pb-0 scroll-smooth snap-x snap-mandatory touch-pan-x scrollbar-hidden bg-transparent">
								<?php foreach ($slider_posts as $post) : ?>
									<?php setup_postdata($post); ?>
									<?php $thumb_url = has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'large') : ''; ?>
									<article class="group relative flex-none w-full basis-full min-w-full snap-start rounded-3xl border border-[#F2A25C]/30 bg-white p-6 transition hover:-translate-y-1 dark:border-[#F2A25C]/20 dark:bg-slate-900 animate-featuredWave" style="flex: 0 0 100%; width: 100%; min-width: 100%;">
										<span class="absolute top-5 right-5 z-20 rounded-full bg-[#F26D3D]/90 px-3 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-white">
											<?php esc_html_e('Featured', 'brendon-core'); ?>
										</span>
										<?php if ($thumb_url) : ?>
											<div class="mb-4 overflow-hidden rounded-2xl bg-slate-100 z-0">
												<div class="h-96 w-full overflow-hidden">
													<img class="h-full w-full object-cover object-center transition duration-300 group-hover:scale-105" src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" />
												</div>
											</div>
										<?php endif; ?>
										<div class="flex flex-col gap-2">
											<div class="text-xs font-semibold uppercase tracking-wider text-[#F2A25C]">
												<?php echo get_the_category_list(' · ', '', $post->ID); ?>
											</div>
											<h3 class="text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
												<a href="<?php echo esc_url(get_permalink($post)); ?>" class="hover:text-[#F26D3D]"><?php echo esc_html(get_the_title($post)); ?></a>
											</h3>
											<p class="text-sm text-slate-600 dark:text-slate-300">
												<?php echo wp_trim_words(get_the_excerpt($post), 30, '…'); ?>
											</p>
										</div>
									</article>
								<?php endforeach; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						</div>

						<div class="flex items-center">
							<div aria-hidden="true" class="w-full border-t border-gray-300 dark:border-white/15"></div>
							<div class="relative flex justify-center">
								<span class="pl-3 pr-2 text-base font-semibold text-gray-900 dark:text-white">Latest</span>
								<span class="pr-3 text-base font-semibold text-gray-900 dark:text-white">Posts</span>
							</div>
							<div aria-hidden="true" class="w-full border-t border-gray-300 dark:border-white/15"></div>
						</div>

					</section>
					<script>
						(function() {
							const track = document.querySelector('[data-slider-track]');
							if (!track) {
								return;
							}

							const prev = document.querySelector('[data-slider-prev]');
							const next = document.querySelector('[data-slider-next]');

							const getStep = () => Math.max(track.clientWidth * 0.9, 320);
							let amount = getStep();

							window.addEventListener('resize', () => {
								amount = getStep();
							});

							const scrollStep = (direction = 1) => {
								const maxScroll = track.scrollWidth - track.clientWidth;
								let target = track.scrollLeft + direction * amount;

								if (target > maxScroll) {
									target = 0;
								}

								if (target < 0) {
									target = maxScroll;
								}

								track.scrollTo({
									left: target,
									behavior: 'smooth'
								});
							};

							let auto = setInterval(() => scrollStep(1), 6000);

							const resetAuto = () => {
								clearInterval(auto);
								auto = setInterval(() => scrollStep(1), 6000);
							};

							const handleNav = (direction) => {
								scrollStep(direction);
								resetAuto();
							};

							if (prev) {
								prev.addEventListener('click', () => handleNav(-1));
							}
							if (next) {
								next.addEventListener('click', () => handleNav(1));
							}

							track.addEventListener('mouseenter', () => clearInterval(auto));
							track.addEventListener('mouseleave', () => resetAuto());
							document.addEventListener('visibilitychange', () => {
								if (document.hidden) {
									clearInterval(auto);
									return;
								}
								resetAuto();
							});

						})();
					</script>
				<?php endif; ?>

				<?php if (have_posts()) : ?>

					<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
						<?php
						$post_index = 0;
						while (have_posts()) :
							the_post();
							if ($featured_ids && in_array(get_the_ID(), $featured_ids, true)) {
								continue;
							}
							$post_index++;
							$span_class = 0 === $post_index % 5 ? 'lg:col-span-2' : '';
							set_query_var('grid_span', $span_class);
							get_template_part('template-parts/card-grid');
						endwhile;
						set_query_var('grid_span', '');
						?>
					</div>

					<?php
					$pagination = paginate_links(
						[
							'type'      => 'array',
							'prev_text' => esc_html__('← Previous', 'brendon-core'),
							'next_text' => esc_html__('Next →', 'brendon-core'),
						]
					);
					?>

					<?php if (! empty($pagination) && is_array($pagination)) : ?>
					<nav class="mt-10" aria-label="<?php esc_attr_e('Posts pagination', 'brendon-core'); ?>">
						<ul class="flex flex-wrap gap-2 justify-center">
								<?php foreach ($pagination as $link) : ?>
									<li class="[&>a]:inline-flex [&>a]:items-center [&>a]:rounded-lg [&>a]:border [&>a]:border-[#F2A25C]/30 [&>a]:bg-white [&>a]:px-3 [&>a]:py-2 [&>a]:text-sm [&>a]:text-slate-700 [&>a]:shadow-sm [&>a]:transition [&>a]:hover:bg-[#F2EB8D]/60 [&>a]:hover:text-slate-900 [&>a]:focus-visible:outline [&>a]:focus-visible:outline-2 [&>a]:focus-visible:outline-offset-2 [&>a]:focus-visible:outline-[#F2A25C]/70 [&>span]:inline-flex [&>span]:items-center [&>span]:rounded-lg [&>span]:border [&>span]:border-[#F24E29] [&>span]:bg-[#F24E29] [&>span]:px-3 [&>span]:py-2 [&>span]:text-sm [&>span]:text-white [&>span]:shadow-sm">
										<?php echo wp_kses_post($link); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

				<?php else : ?>

					<div class="rounded-xl border border-[#F2A25C]/30 bg-white p-8 shadow-sm dark:bg-slate-900 dark:border-[#F2A25C]/20">
						<h2 class="text-2xl font-semibold tracking-tight"><?php esc_html_e('No posts yet', 'brendon-core'); ?></h2>
						<p class="mt-2 text-slate-600 dark:text-slate-300"><?php esc_html_e('Once you publish posts, they’ll show up here.', 'brendon-core'); ?></p>
					</div>

				<?php endif; ?>

			</section>

		</div>

	</div>
</main>

<?php
get_footer();
