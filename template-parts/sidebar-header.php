<?php

/**
 * Shared site title header for sidebar + mobile menu.
 *
 * @package brendon-core
 */

$header_args = wp_parse_args(
	$args ?? [],
	[
		'site_title'       => get_bloginfo( 'name' ),
		'site_description' => get_bloginfo( 'description' ),
		'has_logo'         => false,
		'flap_width'       => 12,
	]
);

$site_title       = $header_args['site_title'];
$site_description = $header_args['site_description'];
$has_logo         = (bool) $header_args['has_logo'];
$flap_width       = (int) $header_args['flap_width'];
if ( $flap_width < 1 ) {
	$flap_width = 12;
}
?>

<div class="bb-site-header">
	<?php if ( $has_logo ) : ?>
		<div class="text-center">
			<h1 class="site-title text-2xl font-extrabold tracking-[0.04em] leading-tight text-slate-900 font-display">
				<a class="site-title__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="site-title__name uppercase"><?php echo esc_html( $site_title ); ?></span>
					<span class="bb-flap" data-bb-splitflap data-bb-width="<?php echo esc_attr( $flap_width ); ?>" aria-hidden="true"></span>
					<span class="sr-only"><?php printf( esc_html__( '%s exists', 'brendon-core' ), esc_html( $site_title ) ); ?></span>
				</a>
			</h1>
		</div>
		<?php if ( $site_description ) : ?>
			<p class="text-sm text-slate-600 text-center mt-0"><?php echo esc_html( $site_description ); ?></p>
		<?php endif; ?>
	<?php else : ?>
		<div class="space-y-1">
			<h1 class="site-title text-2xl font-extrabold tracking-[0.04em] leading-tight text-slate-900 font-display">
				<a class="site-title__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="site-title__name uppercase"><?php echo esc_html( $site_title ); ?></span>
					<span class="bb-flap" data-bb-splitflap data-bb-width="<?php echo esc_attr( $flap_width ); ?>" aria-hidden="true"></span>
					<span class="sr-only"><?php printf( esc_html__( '%s exists', 'brendon-core' ), esc_html( $site_title ) ); ?></span>
				</a>
			</h1>
			<?php if ( $site_description ) : ?>
				<p class="text-sm text-slate-600"><?php echo esc_html( $site_description ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
