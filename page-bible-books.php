<?php
/**
 * Template Name: Bible Books Table
 *
 * @package brendon-core
 */

get_header();

$books       = brendon_core_bible_books_table_books();
$completed   = brendon_core_bible_books_table_completed();
$total_books  = count( $books );
$done_count   = count( $completed );
$progress     = $total_books ? round( ( $done_count / $total_books ) * 100 ) : 0;
$page_url     = get_permalink( get_queried_object_id() );
$login_url    = brendon_core_bible_books_table_login_url( $page_url );
$first_book   = $books[0] ?? array();
$old_groups   = array();
$new_groups   = array();

foreach ( $books as $book ) {
	$group_key = sanitize_title( $book['group'] );
	if ( 'Old Testament' === $book['testament'] ) {
		$old_groups[ $group_key ] = $book['group'];
	} else {
		$new_groups[ $group_key ] = $book['group'];
	}
}

$new_group_layout = array(
	'torah'           => array( 'col' => 1, 'row' => 1 ),
	'history'         => array( 'col' => 1, 'row' => 3 ),
	'poetry'          => array( 'col' => 1, 'row' => 5 ),
	'major-prophets'  => array( 'col' => 1, 'row' => 7 ),
	'minor-prophets'  => array( 'col' => 1, 'row' => 9 ),
	'gospels'         => array( 'col' => 1, 'row' => 11 ),
	'acts'            => array( 'col' => 6, 'row' => 11 ),
	'pauls-letters'   => array( 'col' => 1, 'row' => 13 ),
	'general-letters' => array( 'col' => 1, 'row' => 15 ),
	'prophecy'        => array( 'col' => 13, 'row' => 15 ),
);
?>

<main id="primary" class="bb-main bb-books">
	<section class="bb-page-hero bb-section bb-books__hero">
		<div class="bb-wrap bb-books__hero-inner">
			<div>
				<p class="bb-kicker"><?php esc_html_e( 'Bible Books', 'brendon-core' ); ?></p>
				<h1><?php esc_html_e( 'Books of the Bible', 'brendon-core' ); ?></h1>
				<p><?php esc_html_e( 'A periodic-table style map for moving through Scripture book by book.', 'brendon-core' ); ?></p>
			</div>

			<div class="bb-books__progress" data-progress-card>
				<span><?php esc_html_e( 'Member progress', 'brendon-core' ); ?></span>
				<strong><span data-progress-count><?php echo esc_html( number_format_i18n( $done_count ) ); ?></span> / <?php echo esc_html( number_format_i18n( $total_books ) ); ?></strong>
				<div class="bb-books__meter" aria-hidden="true">
					<span data-progress-meter style="width: <?php echo esc_attr( $progress ); ?>%;"></span>
				</div>
				<?php if ( ! is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Sign in to save progress', 'brendon-core' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="bb-section bb-books__workspace">
		<div class="bb-wrap bb-books__layout">
			<div class="bb-books__table-shell">
				<div class="bb-books__filters" aria-label="<?php echo esc_attr_x( 'Bible book filters', 'aria label', 'brendon-core' ); ?>">
					<button type="button" class="is-active" data-book-filter="all"><?php esc_html_e( 'All', 'brendon-core' ); ?></button>
					<button type="button" data-book-filter="Old Testament"><?php esc_html_e( 'Old Testament', 'brendon-core' ); ?></button>
					<button type="button" data-book-filter="New Testament"><?php esc_html_e( 'New Testament', 'brendon-core' ); ?></button>
					<button type="button" data-book-filter="completed"><?php esc_html_e( 'Completed', 'brendon-core' ); ?></button>
				</div>

				<div class="bb-books__scroller" tabindex="0">
					<div class="bb-books__grid">
						<?php foreach ( $old_groups as $group_key => $group ) : ?>
							<?php $label_layout = $new_group_layout[ $group_key ] ?? array( 'col' => 1, 'row' => 1 ); ?>
							<span
								class="bb-books__section-label"
								style="--label-col: <?php echo esc_attr( (int) $label_layout['col'] ); ?>; --label-row: <?php echo esc_attr( (int) $label_layout['row'] ); ?>;"
								data-group="<?php echo esc_attr( $group_key ); ?>"
							>
								<?php echo esc_html( $group ); ?>
							</span>
						<?php endforeach; ?>

						<?php foreach ( $new_groups as $group_key => $group ) : ?>
							<?php $label_layout = $new_group_layout[ $group_key ] ?? array( 'col' => 1, 'span' => 1 ); ?>
							<span
								class="bb-books__section-label"
								style="--label-col: <?php echo esc_attr( (int) $label_layout['col'] ); ?>; --label-row: <?php echo esc_attr( (int) $label_layout['row'] ); ?>;"
								data-group="<?php echo esc_attr( $group_key ); ?>"
							>
								<?php echo esc_html( $group ); ?>
							</span>
						<?php endforeach; ?>

						<?php foreach ( $books as $book ) : ?>
							<?php
							$is_completed = in_array( $book['slug'], $completed, true );
							$group_key    = sanitize_title( $book['group'] );
							?>
							<button
								type="button"
								class="bb-book-tile <?php echo $is_completed ? 'is-completed' : ''; ?>"
								style="--book-col: <?php echo esc_attr( (int) $book['col'] ); ?>; --book-row: <?php echo esc_attr( (int) $book['row'] ); ?>;"
								data-book-tile
								data-book="<?php echo esc_attr( $book['slug'] ); ?>"
								data-abbr="<?php echo esc_attr( $book['abbr'] ); ?>"
								data-title="<?php echo esc_attr( $book['title'] ); ?>"
								data-testament="<?php echo esc_attr( $book['testament'] ); ?>"
								data-group="<?php echo esc_attr( $group_key ); ?>"
								data-group-label="<?php echo esc_attr( $book['group'] ); ?>"
								data-chapters="<?php echo esc_attr( (int) $book['chapters'] ); ?>"
								data-summary="<?php echo esc_attr( $book['summary'] ); ?>"
								aria-pressed="<?php echo $is_completed ? 'true' : 'false'; ?>"
							>
								<span class="bb-book-tile__abbr"><?php echo esc_html( $book['abbr'] ); ?></span>
								<span class="bb-book-tile__name"><?php echo esc_html( $book['title'] ); ?></span>
								<span class="bb-book-tile__chapters">
									<?php
									printf(
										/* translators: %s: chapter count. */
										esc_html( _n( '%s chapter', '%s chapters', (int) $book['chapters'], 'brendon-core' ) ),
										esc_html( number_format_i18n( (int) $book['chapters'] ) )
									);
									?>
								</span>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<aside class="bb-books__detail" data-book-detail>
				<p class="bb-kicker" data-book-detail-group><?php echo esc_html( $first_book['group'] ?? '' ); ?></p>
				<h2 data-book-detail-title><?php echo esc_html( $first_book['title'] ?? '' ); ?></h2>
				<div class="bb-books__detail-meta">
					<span data-book-detail-testament><?php echo esc_html( $first_book['testament'] ?? '' ); ?></span>
					<span data-book-detail-chapters>
						<?php
						if ( $first_book ) {
							printf(
								/* translators: %s: chapter count. */
								esc_html( _n( '%s chapter', '%s chapters', (int) $first_book['chapters'], 'brendon-core' ) ),
								esc_html( number_format_i18n( (int) $first_book['chapters'] ) )
							);
						}
						?>
					</span>
				</div>
				<p data-book-detail-summary><?php echo esc_html( $first_book['summary'] ?? '' ); ?></p>

				<?php if ( is_user_logged_in() ) : ?>
					<button class="bb-button bb-books__complete-button" type="button" data-book-complete>
						<?php esc_html_e( 'Mark complete', 'brendon-core' ); ?>
					</button>
				<?php else : ?>
					<a class="bb-button bb-books__complete-button" href="<?php echo esc_url( $login_url ); ?>">
						<?php esc_html_e( 'Sign in', 'brendon-core' ); ?>
					</a>
				<?php endif; ?>
			</aside>
		</div>
	</section>
</main>

<?php
get_footer();
