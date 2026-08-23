/**
 * Preserve Image crop settings when wide/full alignment clears dimensions.
 */
( function ( element, hooks ) {
	'use strict';

	const { createElement, useCallback } = element;

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
} )( window.wp.element, window.wp.hooks );
