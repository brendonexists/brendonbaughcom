<?php
/**
 * Comments template.
 *
 * @package brendon-core
 */

if ( post_password_required() ) {
	return;
}

if ( ! function_exists( 'brendon_core_comment' ) ) {
	/**
	 * Custom comment markup.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param array      $args    Comment arguments.
	 * @param int        $depth   Current depth.
	 */
	function brendon_core_comment( $comment, $args, $depth ) {
		?>
		<li id="comment-<?php echo esc_attr( $comment->comment_ID ); ?>" <?php comment_class( 'bb-comment' ); ?>>
			<article class="bb-comment__body">
				<header class="bb-comment__meta">
					<strong><?php echo wp_kses_post( get_comment_author_link( $comment ) ); ?></strong>
					<span>
						<time datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'M j, Y', $comment ) ); ?></time>
					</span>
				</header>
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="bb-comment__moderation"><?php esc_html_e( 'Your note is awaiting moderation.', 'brendon-core' ); ?></p>
				<?php endif; ?>
				<div class="bb-comment__content">
					<?php comment_text(); ?>
				</div>
				<footer class="bb-comment__actions">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							[
								'reply_text' => esc_html__( 'Reply', 'brendon-core' ),
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
							]
						)
					);

					edit_comment_link( esc_html__( 'Edit', 'brendon-core' ), '', '' );
					?>
				</footer>
			</article>
		</li>
		<?php
	}
}
?>

<div id="comments" class="comments-area">
	<div class="bb-comments-header">
		<p class="bb-kicker"><?php esc_html_e( 'Field notes', 'brendon-core' ); ?></p>
		<h2 class="bb-comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( $comment_count ) {
				printf(
					/* translators: %s: comment count. */
					esc_html( _n( '%s field note', '%s field notes', $comment_count, 'brendon-core' ) ),
					esc_html( number_format_i18n( $comment_count ) )
				);
			} else {
				esc_html_e( 'Leave a field note', 'brendon-core' );
			}
			?>
		</h2>
		<p><?php esc_html_e( 'Useful notes, questions, and honest response.', 'brendon-core' ); ?></p>
	</div>

	<?php if ( have_comments() ) : ?>
		<?php the_comments_navigation(); ?>

		<ol class="bb-comment-list">
			<?php
			wp_list_comments(
				[
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 0,
					'callback'    => 'brendon_core_comment',
				]
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		[
			'class_form'           => 'comment-form bb-comment-form',
			'title_reply'          => esc_html__( 'Leave a note', 'brendon-core' ),
			'title_reply_before'   => '<h3 class="comment-reply-title">',
			'title_reply_after'    => '</h3>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Quiet, useful, human. Your email stays private.', 'brendon-core' ) . '</p>',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'label_submit'         => esc_html__( 'Post Note', 'brendon-core' ),
		]
	);
	?>
</div>
