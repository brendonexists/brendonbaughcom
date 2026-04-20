( function() {
	const navigation = document.getElementById( 'site-navigation' );

	if ( ! navigation ) {
		return;
	}

	const button = navigation.querySelector( '.bb-menu-toggle' );
	const menu = navigation.querySelector( '.bb-primary-nav__list' );

	if ( ! button || ! menu ) {
		return;
	}

	const closeMenu = () => {
		navigation.classList.remove( 'is-open' );
		button.setAttribute( 'aria-expanded', 'false' );
	};

	button.addEventListener( 'click', () => {
		const isOpen = navigation.classList.toggle( 'is-open' );
		button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );

	document.addEventListener( 'click', ( event ) => {
		if ( ! navigation.contains( event.target ) ) {
			closeMenu();
		}
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' ) {
			closeMenu();
		}
	} );
}() );
