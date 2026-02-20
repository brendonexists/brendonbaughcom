/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	// Return early if the button doesn't exist.
	if ( 'undefined' === typeof button ) {
		return;
	}

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	}

	// Toggle focus each time a menu link with children receive a touch event.
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );

( function() {
	const activeSelector = [
		'.bb-nav li.current-menu-item > a',
		'.bb-nav li.current_page_item > a',
		'.bb-nav li:not(.current_page_parent):not(.current_page_ancestor) > a[aria-current="page"]',
		'.bb-nav li:not(.current_page_parent):not(.current_page_ancestor) > a.bb-nav__link--active'
	].join( ', ' );

	const activeLinks = document.querySelectorAll( activeSelector );
	if ( ! activeLinks.length ) {
		// Still allow hover lava even when no active link is found.
	}

	const prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	const instances = new Map();
	const activeSet = new Set( activeLinks );

	const createInstance = ( link ) => {
		if ( link.querySelector( 'canvas.bb-nav__lava' ) ) {
			return;
		}

		const canvas = document.createElement( 'canvas' );
		canvas.className = 'bb-nav__lava';
		canvas.setAttribute( 'aria-hidden', 'true' );
		link.appendChild( canvas );

		const instance = createLavaLamp( canvas, prefersReducedMotion.matches );
		instances.set( link, instance );
	};

	activeLinks.forEach( createInstance );

	if ( typeof prefersReducedMotion.addEventListener === 'function' ) {
		prefersReducedMotion.addEventListener( 'change', ( event ) => {
			instances.forEach( ( instance ) => {
				if ( instance && typeof instance.setReduced === 'function' ) {
					instance.setReduced( event.matches );
				}
			} );
		} );
	}

	const hoverLinks = document.querySelectorAll( '.bb-nav .bb-nav__link' );
	const enableHover = ( link ) => {
		if ( activeSet.has( link ) || link.classList.contains( 'is-lava-hover' ) ) {
			return;
		}
		link.classList.add( 'is-lava-hover' );
		if ( link.querySelector( 'canvas.bb-nav__lava' ) ) {
			return;
		}
		const canvas = document.createElement( 'canvas' );
		canvas.className = 'bb-nav__lava';
		canvas.setAttribute( 'aria-hidden', 'true' );
		link.appendChild( canvas );
		const instance = createLavaLamp( canvas, prefersReducedMotion.matches );
		instances.set( link, instance );
	};

	const disableHover = ( link ) => {
		if ( activeSet.has( link ) ) {
			return;
		}
		link.classList.remove( 'is-lava-hover' );
		const instance = instances.get( link );
		if ( instance && typeof instance.destroy === 'function' ) {
			instance.destroy();
		}
		instances.delete( link );
		const canvas = link.querySelector( 'canvas.bb-nav__lava' );
		if ( canvas ) {
			canvas.remove();
		}
	};

	hoverLinks.forEach( ( link ) => {
		link.addEventListener( 'pointerenter', ( event ) => {
			if ( event.pointerType === 'touch' ) {
				return;
			}
			enableHover( link );
		} );
		link.addEventListener( 'pointerleave', () => disableHover( link ) );
		link.addEventListener( 'focusin', () => enableHover( link ) );
		link.addEventListener( 'focusout', () => disableHover( link ) );
	} );

	function createLavaLamp( canvas, reducedMotion ) {
		const ctx = canvas.getContext( '2d' );
		if ( ! ctx ) {
			return { setReduced() {} };
		}

		const palette = {
			blue: [ 41, 117, 217 ],
			red: [ 242, 46, 46 ]
		};
		const baseAlpha = {
			strong: 0.7,
			soft: 0.35
		};
		let startTime = performance.now();
		let width = 0;
		let height = 0;
		let dpr = window.devicePixelRatio || 1;
		let lamp = null;
		let rafId = 0;
		let running = false;
		let resizeRaf = 0;
		let resizeObserver = null;
		let usesWindowResize = false;

		const Point = function( x, y ) {
			this.x = x;
			this.y = y;
			this.magnitude = x * x + y * y;
			this.computed = 0;
			this.force = 0;
		};

		Point.prototype.add = function( point ) {
			return new Point( this.x + point.x, this.y + point.y );
		};

		const Ball = function( parent ) {
			const min = 0.2;
			const max = 1.0;
			const speedX = 0.02 + Math.random() * 0.07;
			const speedY = 0.02 + Math.random() * 0.08;
			this.vel = new Point(
				( Math.random() > 0.5 ? 1 : -1 ) * speedX,
				( Math.random() > 0.5 ? 1 : -1 ) * speedY
			);
			const baseSize = parent.wh / 10;
			this.size = baseSize + ( Math.random() * ( max - min ) + min ) * baseSize;
			const pad = Math.max( this.size * 1.2, parent.wh * 0.06 );
			const maxX = Math.max( pad, parent.width - pad );
			const maxY = Math.max( pad, parent.height - pad );
			const minX = Math.min( pad, maxX );
			const minY = Math.min( pad, maxY );
			this.pos = new Point(
				minX + Math.random() * Math.max( 1, maxX - minX ),
				minY + Math.random() * Math.max( 1, maxY - minY )
			);
			this.width = parent.width;
			this.height = parent.height;
		};

		Ball.prototype.move = function() {
			const jitter = 0.0025;
			const maxSpeed = 0.12;
			this.vel.x = Math.max( -maxSpeed, Math.min( maxSpeed, this.vel.x + ( Math.random() - 0.5 ) * jitter ) );
			this.vel.y = Math.max( -maxSpeed, Math.min( maxSpeed, this.vel.y + ( Math.random() - 0.5 ) * jitter ) );

			if ( this.pos.x >= this.width - this.size ) {
				if ( this.vel.x > 0 ) {
					this.vel.x = -this.vel.x;
				}
				this.pos.x = this.width - this.size;
			} else if ( this.pos.x <= this.size ) {
				if ( this.vel.x < 0 ) {
					this.vel.x = -this.vel.x;
				}
				this.pos.x = this.size;
			}

			if ( this.pos.y >= this.height - this.size ) {
				if ( this.vel.y > 0 ) {
					this.vel.y = -this.vel.y;
				}
				this.pos.y = this.height - this.size;
			} else if ( this.pos.y <= this.size ) {
				if ( this.vel.y < 0 ) {
					this.vel.y = -this.vel.y;
				}
				this.pos.y = this.size;
			}

			this.pos = this.pos.add( this.vel );
		};

		const makeColor = function( rgb, alpha ) {
			return `rgba(${rgb[ 0 ]}, ${rgb[ 1 ]}, ${rgb[ 2 ]}, ${alpha})`;
		};

		const createRadialGradient = function( w, h, radius, c0, c1 ) {
			const gradient = ctx.createRadialGradient( w, h, 0, w, h, radius );
			gradient.addColorStop( 0, c0 );
			gradient.addColorStop( 1, c1 );
			return gradient;
		};

		const buildGradient = function( time ) {
			const phase = ( time - startTime ) * 0.00008;
			const mix = 0.5 + 0.5 * Math.sin( phase );
			const driftX = 0.55 + 0.08 * Math.sin( phase * 0.8 );
			const driftY = 0.45 + 0.08 * Math.cos( phase * 0.7 );
			const blueStrong = baseAlpha.strong + 0.14 * ( 1 - mix );
			const blueSoft = baseAlpha.soft + 0.1 * mix;

			return createRadialGradient(
				width * driftX,
				height * driftY,
				Math.max( width, height ),
				makeColor( palette.blue, blueStrong.toFixed( 3 ) ),
				makeColor( palette.blue, blueSoft.toFixed( 3 ) )
			);
		};

		const LavaLamp = function( w, h, numBalls ) {
			this.step = 6;
			this.width = w;
			this.height = h;
			this.wh = Math.min( w, h );
			this.sx = Math.floor( this.width / this.step );
			this.sy = Math.floor( this.height / this.step );
			this.paint = false;
			this.metaFill = null;
			this.plx = [ 0, 0, 1, 0, 1, 1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0 ];
			this.ply = [ 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 0, 1, 0, 1 ];
			this.mscases = [ 0, 3, 0, 3, 1, 3, 0, 3, 2, 2, 0, 2, 1, 1, 0 ];
			this.ix = [ 1, 0, -1, 0, 0, 1, 0, -1, -1, 0, 1, 0, 0, 1, 1, 0, 0, 0, 1, 1 ];
			this.grid = [];
			this.balls = [];
			this.iter = 0;
			this.sign = 1;

			for ( let i = 0; i < ( this.sx + 2 ) * ( this.sy + 2 ); i++ ) {
				this.grid[ i ] = new Point(
					( i % ( this.sx + 2 ) ) * this.step,
					( Math.floor( i / ( this.sx + 2 ) ) ) * this.step
				);
			}

			for ( let i = 0; i < numBalls; i++ ) {
				this.balls[ i ] = new Ball( this );
			}
		};

		LavaLamp.prototype.computeForce = function( x, y, idx ) {
			const id = idx || x + y * ( this.sx + 2 );
			let force = 0;

			if ( x === 0 || y === 0 || x === this.sx || y === this.sy ) {
				force = 0.6 * this.sign;
			} else {
				const cell = this.grid[ id ];
				for ( let i = 0; i < this.balls.length; i++ ) {
					const ball = this.balls[ i ];
					force += ball.size * ball.size / ( -2 * cell.x * ball.pos.x - 2 * cell.y * ball.pos.y + ball.pos.magnitude + cell.magnitude );
				}
				force *= this.sign;
			}

			this.grid[ id ].force = force;
			return force;
		};

		LavaLamp.prototype.marchingSquares = function( next ) {
			const x = next[ 0 ];
			const y = next[ 1 ];
			const pdir = next[ 2 ];
			const id = x + y * ( this.sx + 2 );
			if ( this.grid[ id ].computed === this.iter ) {
				return false;
			}

			let dir;
			let mscase = 0;
			for ( let i = 0; i < 4; i++ ) {
				const idn = ( x + this.ix[ i + 12 ] ) + ( y + this.ix[ i + 16 ] ) * ( this.sx + 2 );
				let force = this.grid[ idn ].force;
				if ( ( force > 0 && this.sign < 0 ) || ( force < 0 && this.sign > 0 ) || ! force ) {
					force = this.computeForce( x + this.ix[ i + 12 ], y + this.ix[ i + 16 ], idn );
				}
				if ( Math.abs( force ) > 0.85 ) {
					mscase += Math.pow( 2, i );
				}
			}

			if ( mscase === 15 ) {
				return [ x, y - 1, false ];
			}

			if ( mscase === 5 ) {
				dir = ( pdir === 2 ) ? 3 : 1;
			} else if ( mscase === 10 ) {
				dir = ( pdir === 3 ) ? 0 : 2;
			} else {
				dir = this.mscases[ mscase ];
				this.grid[ id ].computed = this.iter;
			}

			const ix = this.step / (
				Math.abs( Math.abs( this.grid[ ( x + this.plx[ 4 * dir + 2 ] ) + ( y + this.ply[ 4 * dir + 2 ] ) * ( this.sx + 2 ) ].force ) - 1 ) /
				Math.abs( Math.abs( this.grid[ ( x + this.plx[ 4 * dir + 3 ] ) + ( y + this.ply[ 4 * dir + 3 ] ) * ( this.sx + 2 ) ].force ) - 1 ) + 1
			);

			ctx.lineTo(
				this.grid[ ( x + this.plx[ 4 * dir ] ) + ( y + this.ply[ 4 * dir ] ) * ( this.sx + 2 ) ].x + this.ix[ dir ] * ix,
				this.grid[ ( x + this.plx[ 4 * dir + 1 ] ) + ( y + this.ply[ 4 * dir + 1 ] ) * ( this.sx + 2 ) ].y + this.ix[ dir + 4 ] * ix
			);

			this.paint = true;
			return [ x + this.ix[ dir + 4 ], y + this.ix[ dir + 8 ], dir ];
		};

		LavaLamp.prototype.renderMetaballs = function() {
			for ( let i = 0; i < this.balls.length; i++ ) {
				this.balls[ i ].move();
			}

			this.iter++;
			this.sign = -this.sign;
			this.paint = false;
			ctx.fillStyle = this.metaFill;
			ctx.beginPath();

			for ( let i = 0; i < this.balls.length; i++ ) {
				let next = [
					Math.round( this.balls[ i ].pos.x / this.step ),
					Math.round( this.balls[ i ].pos.y / this.step ),
					false
				];
				do {
					next = this.marchingSquares( next );
				} while ( next );

				if ( this.paint ) {
					ctx.fill();
					ctx.closePath();
					ctx.beginPath();
					this.paint = false;
				}
			}
		};

		const resize = () => {
			const rect = canvas.getBoundingClientRect();
			const nextWidth = Math.round( rect.width );
			const nextHeight = Math.round( rect.height );
			if ( ! nextWidth || ! nextHeight ) {
				return;
			}
			if ( lamp && nextWidth === width && nextHeight === height ) {
				return;
			}

			dpr = window.devicePixelRatio || 1;
			width = nextWidth;
			height = nextHeight;
			canvas.width = Math.round( width * dpr );
			canvas.height = Math.round( height * dpr );
			ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
			startTime = performance.now();
			lamp = new LavaLamp( width, height, 6 );
			updateGradient( startTime );
		};

		const updateGradient = ( time ) => {
			if ( ! lamp ) {
				return;
			}
			lamp.metaFill = buildGradient( time );
		};

		const draw = ( time = startTime ) => {
			if ( ! lamp ) {
				return;
			}
			updateGradient( time );
			ctx.clearRect( 0, 0, width, height );
			lamp.renderMetaballs();
		};

		const tick = ( time ) => {
			draw( time );
			rafId = window.requestAnimationFrame( tick );
		};

		const start = () => {
			if ( running ) {
				return;
			}
			running = true;
			rafId = window.requestAnimationFrame( tick );
		};

		const stop = () => {
			running = false;
			if ( rafId ) {
				window.cancelAnimationFrame( rafId );
				rafId = 0;
			}
		};

		resize();

		const scheduleResize = () => {
			if ( resizeRaf ) {
				return;
			}
			resizeRaf = window.requestAnimationFrame( () => {
				resizeRaf = 0;
				resize();
			} );
		};

		if ( typeof ResizeObserver === 'function' ) {
			resizeObserver = new ResizeObserver( scheduleResize );
			resizeObserver.observe( canvas.parentElement || canvas );
		} else {
			usesWindowResize = true;
			window.addEventListener( 'resize', scheduleResize, { passive: true } );
		}

		if ( reducedMotion ) {
			draw();
		} else {
			start();
		}

		return {
			setReduced( nextReduced ) {
				if ( nextReduced ) {
					stop();
					draw();
				} else {
					start();
				}
			},
			destroy() {
				stop();
				if ( resizeObserver ) {
					resizeObserver.disconnect();
					resizeObserver = null;
				}
				if ( usesWindowResize ) {
					window.removeEventListener( 'resize', scheduleResize );
				}
				if ( resizeRaf ) {
					window.cancelAnimationFrame( resizeRaf );
					resizeRaf = 0;
				}
			}
		};
	}
}() );

( function() {
	const mobileMenu = document.querySelector( '[data-bb-mobile-menu]' );
	if ( ! mobileMenu ) {
		return;
	}

	const summary = mobileMenu.querySelector( 'summary' );
	const panel = mobileMenu.querySelector( '#bb-mobile-menu-panel' );
	const backdrop = mobileMenu.querySelector( '[data-bb-mobile-backdrop]' );
	const closeButton = mobileMenu.querySelector( '[data-bb-mobile-close]' );
	if ( ! summary || ! panel ) {
		return;
	}

	const setOpen = ( nextOpen ) => {
		if ( nextOpen ) {
			mobileMenu.setAttribute( 'open', '' );
		} else {
			mobileMenu.removeAttribute( 'open' );
		}
	};

	const updateExpanded = () => {
		const isOpen = mobileMenu.open;
		summary.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		document.body.classList.toggle( 'bb-mobile-menu-open', isOpen );
		document.documentElement.classList.toggle( 'bb-mobile-menu-open', isOpen );
	};

	const closeMenu = () => {
		if ( ! mobileMenu.open ) {
			return;
		}
		setOpen( false );
		summary.focus();
	};

	updateExpanded();
	mobileMenu.addEventListener( 'toggle', updateExpanded );

	if ( backdrop ) {
		backdrop.addEventListener( 'click', closeMenu );
	}
	if ( closeButton ) {
		closeButton.addEventListener( 'click', closeMenu );
	}

	document.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' ) {
			closeMenu();
		}
	} );
}() );
