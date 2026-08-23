/**
 * Manage the responsive navigation content and scroll-aware header.
 */
( function () {
	'use strict';

	const HEADER_HIDE_DISTANCE = 32;
	const HEADER_REVEAL_DISTANCE = 512;
	let submenuId = 0;

	function mountScrollAwareHeader() {
		const header = document.querySelector( 'header.wp-block-template-part' );

		if ( ! header || header.dataset.scrollAwareMounted ) {
			return;
		}

		header.dataset.scrollAwareMounted = 'true';

		let accumulatedDistance = 0;
		let animationFrame = null;
		let lastScrollY = Math.max( window.scrollY, 0 );
		let scrollDirection = null;
		const hoverCapable = window.matchMedia(
			'(hover: hover) and (pointer: fine)'
		);

		const showHeader = () => {
			header.classList.remove( 'is-scroll-hidden' );
		};
		const resetScrollTracking = () => {
			accumulatedDistance = 0;
			lastScrollY = Math.max( window.scrollY, 0 );
			scrollDirection = null;
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
			animationFrame = null;

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
			if ( animationFrame === null ) {
				animationFrame = window.requestAnimationFrame(
					updateHeaderVisibility
				);
			}
		};
		const revealForInteraction = () => {
			showHeader();
			resetScrollTracking();
		};

		const responsiveContainer = header.querySelector(
			'.wp-block-navigation__responsive-container'
		);

		if ( responsiveContainer ) {
			const revealForOpenMenu = new MutationObserver( () => {
				if ( responsiveContainer.classList.contains( 'is-menu-open' ) ) {
					revealForInteraction();
				} else {
					resetScrollTracking();
				}
			} );

			revealForOpenMenu.observe( responsiveContainer, {
				attributeFilter: [ 'class' ],
				attributes: true,
			} );
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
		window.addEventListener( 'resize', resetScrollTracking, {
			passive: true,
		} );
		window.addEventListener( 'scroll', scheduleHeaderUpdate, {
			passive: true,
		} );
	}

	function mountMobileSubmenuToggles( navigation ) {
		navigation
			.querySelectorAll(
				'.wp-block-navigation-item.has-child > .wp-block-navigation-submenu__toggle'
			)
			.forEach( ( coreToggle ) => {
				const submenu = coreToggle.nextElementSibling;

				if (
					! submenu ||
					! submenu.classList.contains(
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

	function mountHeaderActions() {
		document.querySelectorAll( '.site-header__controls' ).forEach( ( controls ) => {
			const navigation = controls.querySelector( '.site-navigation' );
			const actions = controls.querySelector( '.header-actions' );

			if ( ! navigation || ! actions || actions.dataset.navigationMounted ) {
				return;
			}

			const responsiveContainer = navigation.querySelector(
				'.wp-block-navigation__responsive-container'
			);
			const responsiveContent = navigation.querySelector(
				'.wp-block-navigation__responsive-container-content'
			);
			const header = controls.closest( 'header.wp-block-template-part' );
			const menuOpenButton = navigation.querySelector(
				'.wp-block-navigation__responsive-container-open'
			);

			if ( ! responsiveContainer || ! responsiveContent ) {
				return;
			}

			mountMobileSubmenuToggles( navigation );
			actions.dataset.navigationMounted = 'true';

			const search = actions.querySelector( '.header-search' );
			const mobileMenu = window.matchMedia( '(max-width: 599px)' );
			let menuLayoutFrame = null;
			const isMobileMenuOpen = () =>
				mobileMenu.matches &&
				responsiveContainer.classList.contains( 'is-menu-open' );
			const updateMobileMenuLayout = () => {
				menuLayoutFrame = null;

				if ( ! header || ! menuOpenButton || ! isMobileMenuOpen() ) {
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
			const syncMobileMenuPresentation = () => {
				const menuIsOpen = isMobileMenuOpen();

				if ( header ) {
					header.classList.toggle( 'is-mobile-menu-open', menuIsOpen );
				}

				if ( menuIsOpen ) {
					scheduleMobileMenuLayout();
					return;
				}

				if ( menuLayoutFrame !== null ) {
					window.cancelAnimationFrame( menuLayoutFrame );
					menuLayoutFrame = null;
				}

				responsiveContainer.style.removeProperty(
					'--oudgoud-mobile-menu-top'
				);
				responsiveContainer.style.removeProperty(
					'--oudgoud-mobile-menu-toggle-top'
				);
				responsiveContainer.style.removeProperty(
					'--oudgoud-mobile-menu-toggle-right'
				);
				responsiveContainer.style.removeProperty(
					'--oudgoud-mobile-menu-toggle-width'
				);
				responsiveContainer.style.removeProperty(
					'--oudgoud-mobile-menu-toggle-height'
				);
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
				if (
					mobileMenu.matches &&
					! responsiveContainer.classList.contains( 'is-menu-open' )
				) {
					resetMobileMenu();
				}
			};

			const syncContentWithMenu = new MutationObserver( () => {
				if ( ! responsiveContainer.classList.contains( 'is-menu-open' ) ) {
					resetMobileMenu();
				}

				syncMobileMenuPresentation();
			} );

			syncContentWithMenu.observe( responsiveContainer, {
				attributeFilter: [ 'class' ],
				attributes: true,
			} );

			if ( header && 'ResizeObserver' in window ) {
				const headerResizeObserver = new ResizeObserver(
					scheduleMobileMenuLayout
				);

				headerResizeObserver.observe( header );
			}

			window.addEventListener( 'resize', scheduleMobileMenuLayout, {
				passive: true,
			} );

			if ( window.visualViewport ) {
				window.visualViewport.addEventListener(
					'resize',
					scheduleMobileMenuLayout,
					{ passive: true }
				);
			}

			mobileMenu.addEventListener( 'change', () => {
				syncHeaderActionsLocation();
				resetMobileMenuWhenHidden();
				syncMobileMenuPresentation();
			} );
			syncHeaderActionsLocation();
			resetMobileMenuWhenHidden();
			syncMobileMenuPresentation();
		} );
	}

	function mountHeader() {
		mountHeaderActions();
		mountScrollAwareHeader();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', mountHeader, {
			once: true,
		} );
	} else {
		mountHeader();
	}
} )();
