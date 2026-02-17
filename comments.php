<?php

/**
 * The template for displaying comments
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package brendon-core
 */

if (post_password_required()) {
	return;
}

if (! function_exists('brendon_core_comment')) {
	/**
	 * Custom comment markup for the sidebar layout.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param array      $args    Comment arguments.
	 * @param int        $depth   Current depth.
	 */
	function brendon_core_comment($comment, $args, $depth) {
		$commenter = get_comment_author_link($comment);
		$avatar = get_avatar($comment, 48, '', '', ['class' => 'h-12 w-12 rounded-full']);

			echo '<li id="comment-' . esc_attr($comment->comment_ID) . '" class="comment rounded-2xl border border-border bg-white p-6 shadow-sm transition">';
		echo '<div class="flex gap-4">';
		echo '<div class="flex-shrink-0">' . $avatar . '</div>';
		echo '<div class="flex-1">';
		echo '<div class="flex flex-wrap items-center justify-between gap-3">';
		printf('<span class="text-sm font-semibold text-slate-900">%s</span>', $commenter);
		printf('<time class="text-xs uppercase tracking-wider text-slate-500" datetime="%s">%s</time>', esc_attr(get_comment_date('c', $comment)), esc_html(get_comment_date('', $comment)));
		echo '</div>';
			echo '<div class="mt-3 text-sm leading-relaxed text-slate-700">';
		comment_text();
		echo '</div>';
		echo '<div class="mt-4 flex flex-wrap gap-3 text-xs">';
		$comment_action_classes = 'inline-flex items-center rounded-full border border-primary bg-white px-3 py-1 text-xs font-semibold text-primary shadow-sm transition hover:bg-primary/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary';
		comment_reply_link(array_merge($args, [
			'reply_text' => esc_html__('Reply', 'brendon-core'),
			'depth' => $depth,
			'max_depth' => $args['max_depth'],
			'class' => $comment_action_classes,
		]));
		$edit_link = get_edit_comment_link($comment->comment_ID);
		if ($edit_link) {
			echo '<a href="' . esc_url($edit_link) . '" class="' . esc_attr($comment_action_classes) . '">' . esc_html__('Edit', 'brendon-core') . '</a>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</li>';
	}
}
?>

<div id="comments" class="comments-area space-y-6">

	<?php if (have_comments()) : ?>

		<h2 class="text-lg font-semibold tracking-tight">
			<?php
			$comment_count = get_comments_number();
			if (1 === $comment_count) {
				esc_html_e('One thought on this post', 'brendon-core');
			} else {
				printf(
					/* translators: %s: Comment count. */
					esc_html(_nx('%s thought on this post', '%s thoughts on this post', $comment_count, 'comments title', 'brendon-core')),
					number_format_i18n($comment_count)
				);
			}
			?>
		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="space-y-4">
			<?php
			wp_list_comments([
				'style' => 'ol',
				'short_ping' => true,
				'avatar_size' => 0,
				'callback' => 'brendon_core_comment',
			]);
			?>
		</ol>

		<?php the_comments_navigation(); ?>

	<?php endif; ?>

	<?php
	$commenter = wp_get_current_commenter();

	$shared_field_classes = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/40';

	$comment_form_args = [
		'class_form' => 'space-y-4',
		'title_reply_before' => '<h2 class="text-lg font-bold tracking-tight">',
		'title_reply_after' => '</h2>',
		'comment_field' => '<p class="space-y-1"><label class="block text-sm font-medium text-slate-700" for="comment">' . esc_html__('Comment', 'brendon-core') . '</label><textarea id="comment" name="comment" rows="4" class="' . $shared_field_classes . '" placeholder="' . esc_attr__('Share your thoughts...', 'brendon-core') . '"></textarea></p>',
		'fields' => [
			'author' => '<p class="space-y-1"><label class="block text-sm font-medium text-slate-700" for="author">' . esc_html__('Name', 'brendon-core') . '</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author'] ?? '') . '" class="' . $shared_field_classes . '" required /></p>',
			'email' => '<p class="space-y-1"><label class="block text-sm font-medium text-slate-700" for="email">' . esc_html__('Email', 'brendon-core') . '</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email'] ?? '') . '" class="' . $shared_field_classes . '" required /></p>',
			'url' => '<p class="space-y-1"><label class="block text-sm font-medium text-slate-700" for="url">' . esc_html__('Website', 'brendon-core') . '</label><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url'] ?? '') . '" class="' . $shared_field_classes . '" /></p>',
		],
		'submit_button' => '<button type="submit" class="w-full rounded-lg border border-transparent bg-primary px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">' . esc_html__('Post comment', 'brendon-core') . '</button>',
		'submit_field' => '<p class="mt-4">%1$s %2$s</p>',
	];
	?>

	<div class="rounded-2xl border border-border bg-white p-6 shadow-sm">
		<?php comment_form($comment_form_args); ?>
	</div>

</div>
