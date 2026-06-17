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

	const dropdownItems = Array.from( menu.querySelectorAll( '.menu-item-has-children' ) );

	dropdownItems.forEach( ( item, index ) => {
		const link = item.querySelector( ':scope > a' );
		const submenu = item.querySelector( ':scope > .sub-menu' );

		if ( ! link || ! submenu ) {
			return;
		}

		const submenuId = submenu.id || `bb-submenu-${ index + 1 }`;
		submenu.id = submenuId;

		const toggle = document.createElement( 'button' );
		toggle.className = 'bb-submenu-toggle';
		toggle.type = 'button';
		toggle.setAttribute( 'aria-expanded', 'false' );
		toggle.setAttribute( 'aria-controls', submenuId );
		toggle.innerHTML = `<span class="screen-reader-text">Open ${ link.textContent.trim() } submenu</span><span aria-hidden="true"></span>`;

		link.insertAdjacentElement( 'afterend', toggle );
	} );

	const closeSubmenus = ( exceptItem = null ) => {
		dropdownItems.forEach( ( item ) => {
			if ( item === exceptItem ) {
				return;
			}

			item.classList.remove( 'is-submenu-open' );
			const toggle = item.querySelector( ':scope > .bb-submenu-toggle' );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	};

	const closeMenu = () => {
		navigation.classList.remove( 'is-open' );
		button.setAttribute( 'aria-expanded', 'false' );
		closeSubmenus();
	};

	button.addEventListener( 'click', () => {
		const isOpen = navigation.classList.toggle( 'is-open' );
		button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );

	menu.addEventListener( 'click', ( event ) => {
		const toggle = event.target.closest( '.bb-submenu-toggle' );

		if ( ! toggle || ! menu.contains( toggle ) ) {
			return;
		}

		const item = toggle.closest( '.menu-item-has-children' );
		const isOpen = item.classList.toggle( 'is-submenu-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );

		if ( isOpen ) {
			closeSubmenus( item );
		}
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

	navigation.addEventListener( 'focusout', ( event ) => {
		if ( event.relatedTarget && navigation.contains( event.relatedTarget ) ) {
			return;
		}

		closeSubmenus();
	} );
}() );
