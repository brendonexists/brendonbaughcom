<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package brendon-core
 */

get_header();

while (have_posts()) :
	the_post();
	if ( 'post' === get_post_type() ) {
		get_template_part( 'template-parts/content', 'single' );
	} else {
		get_template_part( 'template-parts/content', get_post_type() );
	}
endwhile;

get_footer();
