/**
 * Keep the header actions inside WordPress's responsive navigation container.
 */
( function () {
	'use strict';

	let submenuId = 0;

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

			if ( ! responsiveContainer || ! responsiveContent ) {
				return;
			}

			mountMobileSubmenuToggles( navigation );
			responsiveContent.append( actions );
			actions.dataset.navigationMounted = 'true';
			navigation.classList.add( 'has-header-actions' );

			const search = actions.querySelector( '.header-search' );
			const mobileMenu = window.matchMedia( '(max-width: 599px)' );
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

			const closeContentWithMenu = new MutationObserver( () => {
				if ( ! responsiveContainer.classList.contains( 'is-menu-open' ) ) {
					resetMobileMenu();
				}
			} );

			closeContentWithMenu.observe( responsiveContainer, {
				attributeFilter: [ 'class' ],
				attributes: true,
			} );

			mobileMenu.addEventListener( 'change', resetMobileMenuWhenHidden );
			resetMobileMenuWhenHidden();
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', mountHeaderActions, {
			once: true,
		} );
	} else {
		mountHeaderActions();
	}
} )();
