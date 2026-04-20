<?php
/**
 * Template Name: Contact
 *
 * @package brendon-core
 */

get_header();

$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$status_message = '';
$status_class = '';

if ( 'success' === $status ) {
	$status_message = esc_html__( 'Message sent. Thank you for reaching out.', 'brendon-core' );
	$status_class = 'bb-notice bb-notice--success';
} elseif ( 'invalid' === $status ) {
	$status_message = esc_html__( 'Please fill out the required fields with a valid email address.', 'brendon-core' );
	$status_class = 'bb-notice bb-notice--error';
} elseif ( 'error' === $status ) {
	$status_message = esc_html__( 'Something went wrong while sending the message. Please try again.', 'brendon-core' );
	$status_class = 'bb-notice bb-notice--error';
}

while ( have_posts() ) :
	the_post();
	?>

	<main id="primary" class="bb-main">
		<section class="bb-page-hero bb-section">
			<div class="bb-wrap">
				<p class="bb-kicker"><?php esc_html_e('Get in touch', 'brendon-core'); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e('For thoughtful work, honest questions, and conversations that do not need to be louder than they are true.', 'brendon-core'); ?></p>
			</div>
		</section>

		<section class="bb-section bb-page-content">
			<div class="bb-wrap">
			<div class="bb-singular__paper bb-contact">
				<div class="bb-contact__intro">
					<?php if ( '' !== trim( get_the_content() ) ) : ?>
						<div class="entry-content bb-content">
							<?php the_content(); ?>
						</div>
					<?php else : ?>
						<h2><?php esc_html_e('Send a note.', 'brendon-core'); ?></h2>
						<p><?php esc_html_e('Keep it plain. Tell me what you are building, asking, carrying, or trying to understand.', 'brendon-core'); ?></p>
					<?php endif; ?>
				</div>

				<form class="bb-contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'brendon_core_contact_form', 'brendon_core_contact_nonce' ); ?>
					<input type="hidden" name="action" value="brendon_core_contact_form">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">

					<?php if ( $status_message ) : ?>
						<div class="<?php echo esc_attr( $status_class ); ?>" role="status" aria-live="polite">
							<?php echo esc_html( $status_message ); ?>
						</div>
					<?php endif; ?>

					<div class="bb-honeypot" aria-hidden="true">
						<label for="contact_company"><?php esc_html_e( 'Company', 'brendon-core' ); ?></label>
						<input id="contact_company" name="contact_company" type="text" autocomplete="off" tabindex="-1" />
					</div>

					<p>
						<label for="contact_name"><?php esc_html_e( 'Name', 'brendon-core' ); ?></label>
						<input id="contact_name" name="contact_name" type="text" required>
					</p>

					<p>
						<label for="contact_email"><?php esc_html_e( 'Email', 'brendon-core' ); ?></label>
						<input id="contact_email" name="contact_email" type="email" required>
					</p>

					<p>
						<label for="contact_subject"><?php esc_html_e( 'Subject', 'brendon-core' ); ?></label>
						<input id="contact_subject" name="contact_subject" type="text">
					</p>

					<p>
						<label for="contact_message"><?php esc_html_e( 'Message', 'brendon-core' ); ?></label>
						<textarea id="contact_message" name="contact_message" rows="7" required></textarea>
					</p>

					<button type="submit"><?php esc_html_e( 'Send message', 'brendon-core' ); ?></button>
				</form>
			</div>
			</div>
		</section>
	</main>

<?php endwhile; ?>

<?php
get_footer();
