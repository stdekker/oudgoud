/**
 * Manage the responsive navigation content and scroll-aware header.
 */
( function () {
	'use strict';

	const HEADER_HIDE_DISTANCE = 32;
	const HEADER_REVEAL_DISTANCE = 512;
	const MOBILE_MENU_QUERY = '(max-width: 599px)';
	let submenuId = 0;

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
	 * Mount all behavior for one rendered header.
	 *
	 * @param {HTMLElement} header Header template-part element.
	 */
	function mountHeader( header ) {
		if ( header.dataset.oudgoudHeaderMounted ) {
			return;
		}

		const controls = header.querySelector( '.site-header__controls' );
		const navigation = controls?.querySelector( '.site-navigation' );
		const actions = controls?.querySelector( '.header-actions' );
		const responsiveContainer = navigation?.querySelector(
			'.wp-block-navigation__responsive-container'
		);
		const responsiveContent = navigation?.querySelector(
			'.wp-block-navigation__responsive-container-content'
		);
		const menuOpenButton = navigation?.querySelector(
			'.wp-block-navigation__responsive-container-open'
		);

		if (
			! controls ||
			! navigation ||
			! actions ||
			! responsiveContainer ||
			! responsiveContent
		) {
			return;
		}

		header.dataset.oudgoudHeaderMounted = 'true';
		mountMobileSubmenuToggles( navigation );

		const hoverCapable = window.matchMedia(
			'(hover: hover) and (pointer: fine)'
		);
		const mobileMenu = window.matchMedia( MOBILE_MENU_QUERY );
		const search = actions.querySelector( '.header-search' );
		let accumulatedDistance = 0;
		let headerFrame = null;
		let lastScrollY = Math.max( window.scrollY, 0 );
		let menuLayoutFrame = null;
		let scrollDirection = null;

		const isResponsiveMenuOpen = () =>
			responsiveContainer.classList.contains( 'is-menu-open' );
		const isMobileMenuOpen = () =>
			mobileMenu.matches && isResponsiveMenuOpen();
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

		const clearMobileMenuLayout = () => {
			if ( menuLayoutFrame !== null ) {
				window.cancelAnimationFrame( menuLayoutFrame );
				menuLayoutFrame = null;
			}

			[
				'--oudgoud-mobile-menu-top',
				'--oudgoud-mobile-menu-toggle-top',
				'--oudgoud-mobile-menu-toggle-right',
				'--oudgoud-mobile-menu-toggle-width',
				'--oudgoud-mobile-menu-toggle-height',
			].forEach( ( property ) => {
				responsiveContainer.style.removeProperty( property );
			} );
		};
		const updateMobileMenuLayout = () => {
			menuLayoutFrame = null;

			if ( ! menuOpenButton || ! isMobileMenuOpen() ) {
				return;
			}

			const headerBounds = header.getBoundingClientRect();
			const toggleBounds = menuOpenButton.getBoundingClientRect();
			const viewportWidth = document.documentElement.clientWidth;

			responsiveContainer.style.setProperty(
				'--oudgoud-mobile-menu-top',
				`${ Math.max( headerBounds.bottom, 0 ) }px`
			);
			responsiveContainer.style.setProperty(
				'--oudgoud-mobile-menu-toggle-top',
				`${ toggleBounds.top }px`
			);
			responsiveContainer.style.setProperty(
				'--oudgoud-mobile-menu-toggle-right',
				`${ Math.max( viewportWidth - toggleBounds.right, 0 ) }px`
			);
			responsiveContainer.style.setProperty(
				'--oudgoud-mobile-menu-toggle-width',
				`${ toggleBounds.width }px`
			);
			responsiveContainer.style.setProperty(
				'--oudgoud-mobile-menu-toggle-height',
				`${ toggleBounds.height }px`
			);
		};
		const scheduleMobileMenuLayout = () => {
			if ( menuLayoutFrame === null ) {
				menuLayoutFrame = window.requestAnimationFrame(
					updateMobileMenuLayout
				);
			}
		};

		const syncHeaderActionsLocation = () => {
			if ( mobileMenu.matches ) {
				if ( actions.parentElement !== responsiveContent ) {
					responsiveContent.append( actions );
				}

				navigation.classList.add( 'has-header-actions' );
				return;
			}

			if (
				actions.parentElement !== controls ||
				navigation.nextElementSibling !== actions
			) {
				navigation.after( actions );
			}

			navigation.classList.remove( 'has-header-actions' );
		};
		const resetMobileMenu = () => {
			if ( search ) {
				search.open = false;
			}

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
		const syncMobileMenuPresentation = () => {
			const menuIsOpen = isMobileMenuOpen();

			header.classList.toggle( 'is-mobile-menu-open', menuIsOpen );

			if ( menuIsOpen ) {
				scheduleMobileMenuLayout();
			} else {
				clearMobileMenuLayout();
			}
		};
		const syncResponsiveState = () => {
			if ( isResponsiveMenuOpen() ) {
				revealForInteraction();
			} else {
				resetMobileMenu();
				resetScrollTracking();
			}

			syncMobileMenuPresentation();
		};
		const handleMediaChange = () => {
			syncHeaderActionsLocation();
			resetMobileMenuWhenHidden();
			syncMobileMenuPresentation();
		};
		const handleViewportResize = () => {
			resetScrollTracking();
			scheduleMobileMenuLayout();
		};

		const menuStateObserver = new MutationObserver( syncResponsiveState );
		menuStateObserver.observe( responsiveContainer, {
			attributeFilter: [ 'class' ],
			attributes: true,
		} );

		if ( 'ResizeObserver' in window ) {
			const headerResizeObserver = new ResizeObserver(
				scheduleMobileMenuLayout
			);
			headerResizeObserver.observe( header );
		}

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
		mobileMenu.addEventListener( 'change', handleMediaChange );

		if ( window.visualViewport ) {
			window.visualViewport.addEventListener(
				'resize',
				scheduleMobileMenuLayout,
				{ passive: true }
			);
		}

		syncHeaderActionsLocation();
		resetMobileMenuWhenHidden();
		syncMobileMenuPresentation();
	}

	function mountHeaders() {
		document
			.querySelectorAll( 'header.wp-block-template-part' )
			.forEach( mountHeader );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', mountHeaders, {
			once: true,
		} );
	} else {
		mountHeaders();
	}
} )();
