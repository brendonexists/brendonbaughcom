/* global wp, jQuery */
/**
 * Customizer live preview bindings for Brendon Core.
 */

( function( $ ) {
	var bindText = function( setting, selector ) {
		wp.customize( setting, function( value ) {
			value.bind( function( nextValue ) {
				$( selector ).text( nextValue );
			} );
		} );
	};

	var bindAttr = function( setting, selector, attr ) {
		wp.customize( setting, function( value ) {
			value.bind( function( nextValue ) {
				$( selector ).attr( attr, nextValue );
			} );
		} );
	};

	bindText( 'brendon_core_home_wordmark', '.bb-retro-wordmark h1' );
	bindText( 'brendon_core_home_latest_kicker', '.bb-retro-section-heading p' );
	bindText( 'brendon_core_home_latest_heading', '.bb-retro-section-heading h2' );
	bindText( 'brendon_core_home_latest_archive_label', '.bb-retro-section-heading a' );
	bindAttr( 'brendon_core_home_writing_url', '.bb-retro-section-heading a', 'href' );

	[ 0, 1, 2, 3 ].forEach( function( index ) {
		bindText(
			'brendon_core_home_pillar_' + index + '_label',
			'.bb-retro-pillars li:not(.bb-retro-pillars__dot):eq(' + index + ') span:last-child'
		);
	} );

	[ 0, 1, 2, 3, 4, 5 ].forEach( function( index ) {
		bindText(
			'brendon_core_home_quick_link_' + index + '_label',
			'.bb-retro-tile:eq(' + index + ') span:last-child'
		);
		bindAttr(
			'brendon_core_home_quick_link_' + index + '_url',
			'.bb-retro-tile:eq(' + index + ')',
			'href'
		);
	} );

	bindText( 'brendon_core_footer_eyebrow', '.bb-footer__eyebrow' );
	bindText( 'brendon_core_footer_statement', '.bb-footer__statement' );
	bindText( 'brendon_core_footer_tagline', '.bb-footer__small p:last-child' );
}( jQuery ) );
