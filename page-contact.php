<?php
/**
 * Template Name: Contact
 *
 * @package brendon-core
 */

get_header();

$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$status_message = '';
$status_classes = '';

if ( 'success' === $status ) {
	$status_message = esc_html__( 'Thanks! Your message has been sent.', 'brendon-core' );
	$status_classes = 'border border-primary/30 bg-primary/10 text-slate-900';
} elseif ( 'invalid' === $status ) {
	$status_message = esc_html__( 'Please fill out all required fields with a valid email.', 'brendon-core' );
	$status_classes = 'border border-danger/30 bg-danger/10 text-slate-900';
} elseif ( 'error' === $status ) {
	$status_message = esc_html__( 'Something went wrong while sending your message. Please try again.', 'brendon-core' );
	$status_classes = 'border border-danger/30 bg-danger/10 text-slate-900';
}

while ( have_posts() ) :
	the_post();
	?>

	<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
		<div class="w-full px-6 py-8">
			<div class="mb-6 lg:hidden">
				<?php get_template_part( 'template-parts/mobile-sidebar-panel' ); ?>
			</div>
			<div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">

				<aside class="hidden lg:block lg:sticky lg:top-8 self-start">
					<?php get_template_part( 'template-parts/sidebar-panel' ); ?>
				</aside>

				<section class="space-y-6">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'group relative overflow-hidden rounded-2xl border border-border bg-white p-6 shadow-sm transition duration-300' ); ?>>
						<div class="flex flex-col gap-6">
							<header class="flex flex-col gap-3">
								<h1 class="text-3xl font-bold tracking-tight leading-tight text-slate-900 transition-colors duration-200">
									<?php the_title(); ?>
								</h1>
								<div class="mt-1 h-px w-full bg-danger"></div>
							</header>

							<?php if ( $status_message ) : ?>
								<div class="rounded-2xl px-4 py-3 text-sm <?php echo esc_attr( $status_classes ); ?>" role="status" aria-live="polite">
									<?php echo esc_html( $status_message ); ?>
								</div>
							<?php endif; ?>

							<?php if ( '' !== trim( get_the_content() ) ) : ?>
								<div class="entry-content prose prose-lg max-w-none text-slate-600 leading-relaxed prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-primary prose-a:hover:text-primary-hover">
									<?php the_content(); ?>
								</div>
							<?php endif; ?>

							<form class="space-y-4" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<?php wp_nonce_field( 'brendon_core_contact_form', 'brendon_core_contact_nonce' ); ?>
								<input type="hidden" name="action" value="brendon_core_contact_form">
								<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">

								<div class="hidden" aria-hidden="true">
									<label for="contact_company"><?php esc_html_e( 'Company', 'brendon-core' ); ?></label>
									<input id="contact_company" name="contact_company" type="text" autocomplete="off" tabindex="-1" />
								</div>

								<div class="grid gap-4 md:grid-cols-2">
									<div class="space-y-2">
										<label class="text-sm font-semibold text-slate-900" for="contact_name"><?php esc_html_e( 'Name', 'brendon-core' ); ?></label>
										<input class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/40" id="contact_name" name="contact_name" type="text" required />
									</div>
									<div class="space-y-2">
										<label class="text-sm font-semibold text-slate-900" for="contact_email"><?php esc_html_e( 'Email', 'brendon-core' ); ?></label>
										<input class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/40" id="contact_email" name="contact_email" type="email" required />
									</div>
								</div>

								<div class="space-y-2">
									<label class="text-sm font-semibold text-slate-900" for="contact_subject"><?php esc_html_e( 'Subject', 'brendon-core' ); ?></label>
									<input class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/40" id="contact_subject" name="contact_subject" type="text" />
								</div>

								<div class="space-y-2">
									<label class="text-sm font-semibold text-slate-900" for="contact_message"><?php esc_html_e( 'Message', 'brendon-core' ); ?></label>
									<textarea class="min-h-[160px] w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/40" id="contact_message" name="contact_message" required></textarea>
								</div>

								<button class="inline-flex items-center gap-2 rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" type="submit">
									<?php esc_html_e( 'Send message', 'brendon-core' ); ?>
									<span aria-hidden="true">→</span>
								</button>
							</form>
						</div>
					</article>
				</section>
			</div>
		</div>
	</main>

<?php endwhile; ?>

<?php
get_footer();
