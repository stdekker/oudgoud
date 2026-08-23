/**
 * Oudgoud block-editor enhancements and compatibility fixes.
 */
( function ( blocks, data, domReady, element, hooks, i18n ) {
	'use strict';

	const LANDING_TEMPLATE = 'landing-page';
	const LANDING_INTRODUCTION_CLASS = 'landing-introduction';
	const LANDING_EDITOR_CLASS = 'is-oudgoud-landing-page';
	const NEWS_CARDS_VARIATION = 'oudgoud/news-cards';
	const { createElement, useCallback } = element;
	const { createBlock, registerBlockVariation } = blocks;
	const { __ } = i18n;

	/**
	 * Register a focused Query Loop variation for reusable news cards.
	 */
	function registerNewsCardsVariation() {
		registerBlockVariation( 'core/query', {
			name: NEWS_CARDS_VARIATION,
			title: __( 'Nieuwskaarten', 'oudgoud' ),
			description: __(
				'Toon recente nieuwsberichten als kaarten.',
				'oudgoud'
			),
			keywords: [
				__( 'nieuws', 'oudgoud' ),
				__( 'berichten', 'oudgoud' ),
				__( 'kaarten', 'oudgoud' ),
			],
			icon: 'grid-view',
			attributes: {
				align: 'wide',
				className: 'news-cards',
				namespace: NEWS_CARDS_VARIATION,
				query: {
					author: '',
					exclude: [],
					excludeCurrent: null,
					format: [],
					inherit: false,
					offset: 0,
					order: 'desc',
					orderBy: 'date',
					pages: 0,
					parents: [],
					perPage: 3,
					postType: 'post',
					search: '',
					sticky: '',
					taxQuery: null,
				},
			},
			allowedControls: [ 'postCount', 'taxQuery' ],
			innerBlocks: [
				[
					'core/post-template',
					{
						layout: {
							columnCount: 3,
							type: 'grid',
						},
					},
					[
						[
							'core/group',
							{
								className: 'news-card news-card--compact',
								layout: { type: 'default' },
								tagName: 'article',
							},
							[
								[
									'core/post-featured-image',
									{
										aspectRatio: '3/2',
										className: 'news-card__image',
										isLink: true,
										sizeSlug: 'featured-short',
									},
								],
								[
									'core/group',
									{
										className: 'news-card__body',
										layout: { type: 'default' },
									},
									[
										[
											'core/post-title',
											{
												className: 'news-card__title',
												isLink: true,
												level: 3,
											},
										],
										[
											'core/group',
											{
												className: 'news-card__meta',
												layout: { type: 'default' },
											},
											[
												[
													'core/post-date',
													{
														className: 'news-card__date',
														format: 'j F Y',
													},
												],
												[
													'core/post-terms',
													{
														className: 'news-card__categories',
														term: 'category',
													},
												],
											],
										],
									],
								],
							],
						],
					],
				],
				[
					'core/query-no-results',
					{},
					[
						[
							'core/paragraph',
							{
								content: __(
									'Er zijn momenteel geen nieuwsberichten.',
									'oudgoud'
								),
							},
						],
					],
				],
			],
			isActive: [ 'namespace' ],
			scope: [ 'inserter' ],
		} );
	}

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

		function syncLandingEditorClass() {
			const editor = data.select( 'core/editor' );
			const isLandingPage =
				editor &&
				editor.getCurrentPostType() === 'page' &&
				editor.getEditedPostAttribute( 'template' ) === LANDING_TEMPLATE;

			document.body.classList.toggle(
				LANDING_EDITOR_CLASS,
				isLandingPage
			);

			const canvasBody = editorCanvas?.contentDocument?.body;

			if ( canvasBody ) {
				canvasBody.classList.toggle(
					LANDING_EDITOR_CLASS,
					isLandingPage
				);
			}
		}

		function connectEditorCanvas() {
			editorCanvas = document.querySelector(
				'iframe[name="editor-canvas"]'
			);

			if ( ! editorCanvas ) {
				return false;
			}

			editorCanvas.addEventListener( 'load', syncLandingEditorClass );
			syncLandingEditorClass();
			return true;
		}

		data.subscribe( syncLandingEditorClass );

		if ( ! connectEditorCanvas() ) {
			const observer = new MutationObserver( () => {
				if ( connectEditorCanvas() ) {
					observer.disconnect();
				}
			} );

			observer.observe( document.body, {
				childList: true,
				subtree: true,
			} );
		}
	}

	/**
	 * Preserve an Image block's crop when WordPress resets dimensions for a
	 * wide or full alignment. WordPress core clears all four attributes when
	 * the block mounts; only width and height should be cleared.
	 *
	 * @param {Function} BlockEdit Original block edit component.
	 * @return {Function} Wrapped block edit component.
	 */
	function preserveWideImageCrop( BlockEdit ) {
		return function OudgoudImageEdit( props ) {
			const isWideImage =
				props.name === 'core/image' &&
				[ 'wide', 'full' ].includes( props.attributes.align );

			const setAttributes = useCallback(
				( nextAttributes ) => {
					const resetKeys = [
						'width',
						'height',
						'aspectRatio',
						'scale',
					];
					const nextKeys = Object.keys( nextAttributes );
					const isCoreAlignmentReset =
						isWideImage &&
						nextKeys.length === resetKeys.length &&
						resetKeys.every(
							( key ) =>
								Object.prototype.hasOwnProperty.call(
									nextAttributes,
									key
								) && nextAttributes[ key ] === undefined
						);

					if ( isCoreAlignmentReset ) {
						props.setAttributes( {
							width: undefined,
							height: undefined,
						} );
						return;
					}

					props.setAttributes( nextAttributes );
				},
				[ isWideImage, props.setAttributes ]
			);

			return createElement( BlockEdit, {
				...props,
				setAttributes,
			} );
		};
	}

	hooks.addFilter(
		'editor.BlockEdit',
		'oudgoud/preserve-wide-image-crop',
		preserveWideImageCrop
	);

	domReady( () => {
		registerNewsCardsVariation();
		seedLandingIntroduction();
		initializeLandingEditorClass();
	} );
} )(
	window.wp.blocks,
	window.wp.data,
	window.wp.domReady,
	window.wp.element,
	window.wp.hooks,
	window.wp.i18n
);
