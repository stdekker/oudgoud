/**
 * Manage the responsive navigation content and scroll-aware header.
 */
( function () {
	'use strict';

	const HEADER_HIDE_DISTANCE = 32;
	const HEADER_REVEAL_DISTANCE = 512;
	const MOBILE_MENU_QUERY = '(max-width: 599px)';
	let headerActionsId = 0;
	let submenuId = 0;

	/**
	 * Enable hover presentation only after deliberate pointer input.
	 *
	 * A cursor retained over a header action during navigation can make the
	 * browser paint :hover before the first frame. Waiting for real pointer
	 * input keeps the server-rendered resting state stable during page load.
	 */
	function enablePointerInteractionStyles() {
		const root = document.documentElement;
		const markPointerInteraction = ( event ) => {
			if ( ! event.isTrusted || event.pointerType === 'touch' ) {
				return;
			}

			root.classList.add( 'has-pointer-interaction' );
			window.removeEventListener(
				'pointermove',
				markPointerInteraction
			);
			window.removeEventListener(
				'pointerdown',
				markPointerInteraction
			);
		};

		window.addEventListener( 'pointermove', markPointerInteraction, {
			passive: true,
		} );
		window.addEventListener( 'pointerdown', markPointerInteraction, {
			passive: true,
		} );
	}

	/**
	 * Give cloned controls unique IDs and update attributes that reference them.
	 *
	 * @param {HTMLElement} clone Cloned header actions.
	 */
	function renewCloneIds( clone ) {
		const idMap = new Map();

		clone.querySelectorAll( '[id]' ).forEach( ( element ) => {
			const originalId = element.id;
			const mobileId = `oudgoud-mobile-action-${ headerActionsId }-${ originalId }`;

			idMap.set( originalId, mobileId );
			element.id = mobileId;
		} );

		[ 'for', 'aria-controls', 'aria-describedby', 'aria-labelledby' ].forEach(
			( attributeName ) => {
				clone
					.querySelectorAll( `[${ attributeName }]` )
					.forEach( ( element ) => {
						const references = element
							.getAttribute( attributeName )
							.split( /\s+/ )
							.map(
								( reference ) =>
									idMap.get( reference ) || reference
							);

						element.setAttribute(
							attributeName,
							references.join( ' ' )
						);
					} );
			}
		);
	}

	/**
	 * Add a hidden mobile copy without moving the visible desktop controls.
	 *
	 * @param {HTMLElement} desktopActions    Server-rendered desktop actions.
	 * @param {HTMLElement} responsiveContent Native Navigation overlay content.
	 * @return {HTMLElement} Mobile action group.
	 */
	function mountMobileHeaderActions( desktopActions, responsiveContent ) {
		const existingActions = responsiveContent.querySelector(
			'.header-actions--mobile'
		);

		if ( existingActions ) {
			return existingActions;
		}

		headerActionsId += 1;

		const mobileActions = desktopActions.cloneNode( true );

		mobileActions.classList.remove( 'header-actions--desktop' );
		mobileActions.classList.add( 'header-actions--mobile' );
		renewCloneIds( mobileActions );
		responsiveContent.append( mobileActions );

		return mobileActions;
	}

	/**
	 * Add independent mobile toggles without changing Core's desktop controls.
	 *
	 * @param {HTMLElement} navigation Navigation block wrapper.
	 */
	function mountMobileSubmenuToggles( navigation ) {
		navigation
			.querySelectorAll(
				'.wp-block-navigation-item.has-child > .wp-block-navigation-submenu__toggle'
			)
			.forEach( ( coreToggle ) => {
				const nextElement = coreToggle.nextElementSibling;

				if (
					nextElement?.classList.contains( 'oudgoud-submenu-toggle' )
				) {
					return;
				}

				const submenu = nextElement;

				if (
					! submenu?.classList.contains(
						'wp-block-navigation__submenu-container'
					)
				) {
					return;
				}

				const mobileToggle = coreToggle.cloneNode( true );

				Array.from( mobileToggle.attributes ).forEach( ( attribute ) => {
					if ( attribute.name.startsWith( 'data-wp-' ) ) {
						mobileToggle.removeAttribute( attribute.name );
					}
				} );

				if ( ! submenu.id ) {
					submenuId += 1;
					submenu.id = `oudgoud-mobile-submenu-${ submenuId }`;
				}

				mobileToggle.classList.add( 'oudgoud-submenu-toggle' );
				mobileToggle.setAttribute( 'aria-controls', submenu.id );
				mobileToggle.setAttribute( 'aria-expanded', 'false' );
				mobileToggle.setAttribute( 'type', 'button' );
				mobileToggle.addEventListener( 'click', () => {
					const isExpanded =
						mobileToggle.getAttribute( 'aria-expanded' ) === 'true';

					mobileToggle.setAttribute(
						'aria-expanded',
						String( ! isExpanded )
					);
				} );

				coreToggle.after( mobileToggle );
			} );
	}

	/**
	 * Mount direction-aware sticky-header behavior.
	 *
	 * @param {HTMLElement} header Header template-part element.
	 */
	function mountScrollAwareHeader( header ) {
		const hoverCapable = window.matchMedia(
			'(hover: hover) and (pointer: fine)'
		);
		let accumulatedDistance = 0;
		let headerFrame = null;
		let lastScrollY = Math.max( window.scrollY, 0 );
		let scrollDirection = null;

		const showHeader = () => {
			header.classList.remove( 'is-scroll-hidden' );
		};
		const resetScrollTracking = () => {
			accumulatedDistance = 0;
			lastScrollY = Math.max( window.scrollY, 0 );
			scrollDirection = null;
		};
		const revealForInteraction = () => {
			showHeader();
			resetScrollTracking();
		};
		const hasActiveInteraction = () =>
			( hoverCapable.matches && header.matches( ':hover' ) ) ||
			header.matches( ':focus-within' ) ||
			Boolean(
				header.querySelector(
					'.header-search[open], .wp-block-navigation-submenu__toggle[aria-expanded="true"], .wp-block-navigation__responsive-container.is-menu-open'
				)
			);

		const updateHeaderVisibility = () => {
			headerFrame = null;

			const currentScrollY = Math.max( window.scrollY, 0 );
			const scrollDelta = currentScrollY - lastScrollY;
			lastScrollY = currentScrollY;

			if ( currentScrollY <= header.offsetHeight || hasActiveInteraction() ) {
				showHeader();
				accumulatedDistance = 0;
				scrollDirection = null;
				return;
			}

			if ( scrollDelta === 0 ) {
				return;
			}

			const nextDirection = scrollDelta > 0 ? 'down' : 'up';

			if ( nextDirection !== scrollDirection ) {
				accumulatedDistance = 0;
				scrollDirection = nextDirection;
			}

			accumulatedDistance += Math.abs( scrollDelta );

			if (
				nextDirection === 'down' &&
				accumulatedDistance >= HEADER_HIDE_DISTANCE
			) {
				header.classList.add( 'is-scroll-hidden' );
				accumulatedDistance = 0;
			} else if (
				nextDirection === 'up' &&
				accumulatedDistance >= HEADER_REVEAL_DISTANCE
			) {
				showHeader();
				accumulatedDistance = 0;
			}
		};
		const scheduleHeaderUpdate = () => {
			if ( headerFrame === null ) {
				headerFrame = window.requestAnimationFrame(
					updateHeaderVisibility
				);
			}
		};
		const handleViewportResize = () => {
			resetScrollTracking();
		};

		header.addEventListener( 'pointerenter', revealForInteraction );
		header.addEventListener( 'focusin', revealForInteraction );
		header.addEventListener(
			'toggle',
			() => {
				if ( hasActiveInteraction() ) {
					revealForInteraction();
				} else {
					resetScrollTracking();
				}
			},
			true
		);
		window.addEventListener( 'resize', handleViewportResize, {
			passive: true,
		} );
		window.addEventListener( 'scroll', scheduleHeaderUpdate, {
			passive: true,
		} );
	}

	/**
	 * Mount behavior that extends WordPress's responsive navigation.
	 *
	 * @param {HTMLElement} header Header template-part element.
	 */
	function mountResponsiveNavigation( header ) {
		const navigation = header.querySelector( '.site-navigation' );

		if ( ! navigation ) {
			return;
		}

		const desktopActions = header.querySelector(
			'.header-actions--desktop'
		);
		const responsiveContainer = navigation.querySelector(
			'.wp-block-navigation__responsive-container'
		);
		const responsiveContent = navigation.querySelector(
			'.wp-block-navigation__responsive-container-content'
		);
		const menuCloseButton = navigation.querySelector(
			'.wp-block-navigation__responsive-container-close'
		);

		mountMobileSubmenuToggles( navigation );

		if ( desktopActions && responsiveContent ) {
			mountMobileHeaderActions( desktopActions, responsiveContent );
		}

		if ( ! responsiveContainer ) {
			return;
		}

		const mobileMenu = window.matchMedia( MOBILE_MENU_QUERY );
		const isResponsiveMenuOpen = () =>
			responsiveContainer.classList.contains( 'is-menu-open' );
		const resetMobileMenu = () => {
			header
				.querySelectorAll( '.header-search[open]' )
				.forEach( ( search ) => {
					search.open = false;
				} );

			responsiveContainer
				.querySelectorAll(
					'.oudgoud-submenu-toggle[aria-expanded="true"]'
				)
				.forEach( ( toggle ) => {
					toggle.setAttribute( 'aria-expanded', 'false' );
				} );
		};
		const resetMobileMenuWhenHidden = () => {
			if ( mobileMenu.matches && ! isResponsiveMenuOpen() ) {
				resetMobileMenu();
			}
		};
		const resetMobileMenuWhenClosed = () => {
			if ( ! isResponsiveMenuOpen() ) {
				resetMobileMenu();
			}
		};
		const handleMediaChange = () => {
			if ( ! mobileMenu.matches ) {
				if ( isResponsiveMenuOpen() ) {
					menuCloseButton?.click();
				}

				resetMobileMenu();
			} else {
				resetMobileMenuWhenHidden();
			}
		};

		const menuStateObserver = new MutationObserver(
			resetMobileMenuWhenClosed
		);
		menuStateObserver.observe( responsiveContainer, {
			attributeFilter: [ 'class' ],
			attributes: true,
		} );
		mobileMenu.addEventListener( 'change', handleMediaChange );
		resetMobileMenuWhenHidden();
	}

	/**
	 * Mount all behavior for one rendered header.
	 *
	 * @param {HTMLElement} header Header template-part element.
	 */
	function mountHeader( header ) {
		if ( header.dataset.oudgoudHeaderMounted ) {
			return;
		}

		header.dataset.oudgoudHeaderMounted = 'true';
		mountScrollAwareHeader( header );
		mountResponsiveNavigation( header );
	}

	function mountHeaders() {
		document
			.querySelectorAll( 'header.wp-block-template-part' )
			.forEach( mountHeader );
	}

	enablePointerInteractionStyles();

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', mountHeaders, {
			once: true,
		} );
	} else {
		mountHeaders();
	}
} )();
