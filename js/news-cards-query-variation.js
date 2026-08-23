/**
 * Register the reusable news-card Query variation.
 */
( function ( blocks, i18n ) {
	'use strict';

	const NEWS_CARDS_VARIATION = 'oudgoud/news-cards';
	const { registerBlockVariation } = blocks;
	const { __ } = i18n;

	registerBlockVariation( 'core/query', {
		name: NEWS_CARDS_VARIATION,
		title: __( 'Nieuwskaarten', 'oudgoud' ),
		description: __( 'Toon recente nieuwsberichten als kaarten.', 'oudgoud' ),
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
							content: __( 'Er zijn momenteel geen nieuwsberichten.', 'oudgoud' ),
						},
					],
				],
			],
		],
		isActive: [ 'namespace' ],
		scope: [ 'inserter' ],
	} );
} )( window.wp.blocks, window.wp.i18n );
