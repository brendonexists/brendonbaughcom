<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package _s
 */

get_header();
?>

<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
	<div class="w-full px-6 py-8 space-y-8">
		<section class="mx-auto flex max-w-5xl flex-col gap-10">
			<div class="rounded-3xl border border-border bg-white p-8 shadow-lg transition">
				<p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary">404 error</p>
				<h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">
					Sorry, we couldn&rsquo;t find that page.
				</h1>
				<p class="mt-4 text-lg text-slate-600">
					It looks like the URL you entered doesn&rsquo;t exist anymore. Try searching, jump back home, or explore some recent posts.
				</p>
				<div class="mt-6 flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center justify-center rounded-2xl border border-border bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:translate-y-0.5 hover:bg-primary-hover">
						Return to homepage
					</a>
					<span class="text-center sm:text-right text-slate-500">
						Or explore the suggestions below.
					</span>
				</div>
			</div>

			<div class="grid gap-6 lg:grid-cols-3">
				<article class="flex min-h-[220px] flex-col justify-between rounded-3xl border border-border bg-white p-6 shadow-lg">
					<header class="space-y-1">
						<p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary">Search</p>
						<h2 class="text-2xl font-semibold text-slate-900">Find what you need</h2>
					</header>
					<form role="search" method="get" class="mt-6 flex w-full flex-col gap-3" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
							<label class="sr-only" for="search-input-404">Search</label>
							<input id="search-input-404" class="min-w-0 flex-1 rounded-2xl border border-border bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search the blog" />
							<button type="submit" class="flex h-12 items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-hover sm:w-auto">
								Search
							</button>
						</div>
					</form>
				</article>

				<article class="flex min-h-[220px] flex-col justify-between rounded-3xl border border-border bg-white p-6 shadow-lg">
					<header class="space-y-1">
						<p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary">Recent</p>
						<h2 class="text-2xl font-semibold text-slate-900">Fresh posts</h2>
					</header>
					<ul class="mt-6 space-y-4 text-sm text-slate-600">
						<?php
						$recent_query = new WP_Query(
							[
								'posts_per_page'      => 5,
								'post_status'         => 'publish',
								'ignore_sticky_posts' => true,
							]
						);

						if ( $recent_query->have_posts() ) :
							while ( $recent_query->have_posts() ) :
								$recent_query->the_post();
								?>
								<li>
									<a href="<?php the_permalink(); ?>" class="font-semibold text-slate-900 transition hover:text-primary">
										<?php the_title(); ?>
									</a>
									<p class="text-xs font-medium uppercase tracking-wide text-slate-400">
										<?php echo esc_html( get_the_date() ); ?>
									</p>
								</li>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<li class="text-sm text-slate-500"><?php esc_html_e( 'No recent posts available right now.', 'brendon-core' ); ?></li>
						<?php endif; ?>
					</ul>
				</article>

				<article class="flex min-h-[220px] flex-col justify-between rounded-3xl border border-border bg-white p-6 shadow-lg">
					<header class="space-y-1">
						<p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary">Browse</p>
						<h2 class="text-2xl font-semibold text-slate-900">Categories & archives</h2>
					</header>
					<div class="mt-6 space-y-6 text-sm text-slate-600">
						<div>
							<p class="text-sm font-semibold text-slate-900">Top categories</p>
							<ul class="mt-3 space-y-2 text-slate-600">
								<?php
								wp_list_categories(
									[
										'orderby'    => 'count',
										'order'      => 'DESC',
										'show_count' => 1,
										'title_li'   => '',
										'number'     => 6,
										'depth'      => 1,
									]
								);
								?>
							</ul>
						</div>
						<div>
							<p class="text-sm font-semibold text-slate-900">Monthly archives</p>
							<ul class="mt-3 space-y-2 text-slate-600">
								<?php
								wp_get_archives(
									[
										'type'            => 'monthly',
										'limit'           => 6,
										'show_post_count' => true,
									]
								);
								?>
							</ul>
						</div>
					</div>
				</article>
			</div>
		</section>
	</div>
</main><!-- #main -->

<?php
get_footer();
