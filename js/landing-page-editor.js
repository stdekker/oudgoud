/**
 * Seed landing-page content and synchronize its editor-only canvas state.
 */
( function ( blocks, data, domReady ) {
	'use strict';

	const LANDING_TEMPLATE = 'landing-page';
	const LANDING_INTRODUCTION_CLASS = 'landing-introduction';
	const LANDING_EDITOR_CLASS = 'is-oudgoud-landing-page';
	const { createBlock } = blocks;

	/**
	 * Create the editable introduction used by the landing-page template.
	 *
	 * @return {Object} Cover block with editable inner blocks.
	 */
	function createLandingIntroduction() {
		return createBlock(
			'core/cover',
			{
				align: 'full',
				className: LANDING_INTRODUCTION_CLASS,
				contentPosition: 'center center',
				dimRatio: 50,
				isUserOverlayColor: true,
				overlayColor: 'dark',
			},
			[
				createBlock(
					'core/group',
					{
						className: 'landing-introduction__content',
						layout: {
							contentSize: '1100px',
							type: 'constrained',
						},
					},
					[
						createBlock( 'core/heading', {
							className: 'landing-introduction__title',
							content: 'Pakkende titel',
							level: 1,
							textAlign: 'center',
						} ),
						createBlock( 'core/paragraph', {
							align: 'center',
							className: 'landing-introduction__byline',
							content: 'Korte introductie of byline',
							fontSize: 'large',
						} ),
						createBlock(
							'core/buttons',
							{
								className: 'landing-introduction__actions',
								layout: {
									justifyContent: 'center',
									type: 'flex',
								},
							},
							[
								createBlock( 'core/button', {
									text: 'Meer informatie',
								} ),
							]
						),
					]
				),
			]
		);
	}

	/**
	 * Check whether the landing introduction is already present.
	 *
	 * @param {Object} block Editor block.
	 * @return {boolean} Whether the block is the landing introduction.
	 */
	function isLandingIntroduction( block ) {
		const classNames = block.attributes.className
			? block.attributes.className.split( /\s+/ )
			: [];

		return classNames.includes( LANDING_INTRODUCTION_CLASS );
	}

	/**
	 * Seed a landing introduction when an editor selects the landing template.
	 */
	function seedLandingIntroduction() {
		let insertionHandled = false;

		const unsubscribe = data.subscribe( () => {
			if ( insertionHandled ) {
				return;
			}

			const editor = data.select( 'core/editor' );
			const blockEditor = data.select( 'core/block-editor' );

			if ( ! editor || ! blockEditor ) {
				return;
			}

			const postType = editor.getCurrentPostType();

			if ( ! postType ) {
				return;
			}

			if ( postType !== 'page' ) {
				unsubscribe();
				return;
			}

			const editedTemplate = editor.getEditedPostAttribute( 'template' );
			const savedTemplate = editor.getCurrentPostAttribute( 'template' );
			const savedStatus = editor.getCurrentPostAttribute( 'status' );
			const landingTemplateSelected =
				editedTemplate === LANDING_TEMPLATE &&
				savedTemplate !== LANDING_TEMPLATE;
			const newLandingPage =
				editedTemplate === LANDING_TEMPLATE && savedStatus === 'auto-draft';

			if ( ! landingTemplateSelected && ! newLandingPage ) {
				return;
			}

			insertionHandled = true;
			unsubscribe();

			if ( blockEditor.getBlocks().some( isLandingIntroduction ) ) {
				return;
			}

			data
				.dispatch( 'core/block-editor' )
				.insertBlocks( createLandingIntroduction(), 0 );
		} );
	}

	/**
	 * Mark the editor canvas when the landing-page template is active.
	 */
	function initializeLandingEditorClass() {
		let editorCanvas;
		let lastCanvasBody;
		let lastIsLandingPage;

		function connectEditorCanvas() {
			const nextEditorCanvas = document.querySelector(
				'iframe[name="editor-canvas"]'
			);

			if ( nextEditorCanvas === editorCanvas ) {
				return Boolean( editorCanvas );
			}

			if ( editorCanvas ) {
				editorCanvas.removeEventListener(
					'load',
					handleEditorCanvasLoad
				);
			}

			editorCanvas = nextEditorCanvas;
			lastCanvasBody = undefined;

			if ( ! editorCanvas ) {
				return false;
			}

			editorCanvas.addEventListener( 'load', handleEditorCanvasLoad );
			return true;
		}

		function syncLandingEditorClass( force = false ) {
			if ( ! editorCanvas?.isConnected ) {
				connectEditorCanvas();
			}

			const editor = data.select( 'core/editor' );
			const isLandingPage = Boolean(
				editor &&
				editor.getCurrentPostType() === 'page' &&
				editor.getEditedPostAttribute( 'template' ) === LANDING_TEMPLATE
			);

			if ( force || isLandingPage !== lastIsLandingPage ) {
				document.body.classList.toggle(
					LANDING_EDITOR_CLASS,
					isLandingPage
				);
			}

			const canvasBody = editorCanvas?.contentDocument?.body;

			if (
				canvasBody &&
				( force ||
					isLandingPage !== lastIsLandingPage ||
					canvasBody !== lastCanvasBody )
			) {
				canvasBody.classList.toggle(
					LANDING_EDITOR_CLASS,
					isLandingPage
				);
			}

			lastCanvasBody = canvasBody;
			lastIsLandingPage = isLandingPage;
		}

		function handleEditorCanvasLoad() {
			lastCanvasBody = undefined;
			syncLandingEditorClass( true );
		}

		data.subscribe( syncLandingEditorClass );

		if ( connectEditorCanvas() ) {
			syncLandingEditorClass( true );
		} else {
			const observer = new MutationObserver( () => {
				if ( connectEditorCanvas() ) {
					observer.disconnect();
					syncLandingEditorClass( true );
				}
			} );

			observer.observe( document.body, {
				childList: true,
				subtree: true,
			} );
		}
	}

	domReady( () => {
		seedLandingIntroduction();
		initializeLandingEditorClass();
	} );
} )( window.wp.blocks, window.wp.data, window.wp.domReady );
