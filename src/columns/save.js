/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { generateColumnsStyles, generateColumnStyles } from '../utils/styles';

/**
 * Save component for Columns block.
 *
 * @param {Object} props Block properties.
 * @return {Element} Columns block save output.
 */
export default function save( { attributes } ) {
	const {
		columnCount,
		columns,
		stackOnMobile,
		mobileBreakpoint,
		reverseOnMobile,
	} = attributes;

	const columnsStyles = generateColumnsStyles( attributes );
	const blockProps = useBlockProps.save( {
		style: columnsStyles,
		className: `flexblocks-columns flexblocks-columns-${ columnCount }`,
	} );

	// Generate inline styles for responsive behavior
	let responsiveStyles = '';
	if ( stackOnMobile ) {
		responsiveStyles = `
			@media (max-width: ${ mobileBreakpoint || '768px' }) {
				.flexblocks-columns {
					flex-direction: ${ reverseOnMobile ? 'column-reverse' : 'column' } !important;
				}
				.flexblocks-column {
					flex: 0 0 100% !important;
					max-width: 100% !important;
				}
			}
		`;
	}

	return (
		<>
			{ responsiveStyles && (
				<style>{ responsiveStyles }</style>
			) }
			<div { ...blockProps }>
				{ Array.from( { length: columnCount } ).map( ( _, index ) => {
					const columnData = columns[ index ] || {};
					const columnStyles = generateColumnStyles( columnData, columnCount );

					return (
						<div
							key={ index }
							className="flexblocks-column"
							style={ columnStyles }
						>
							<InnerBlocks.Content />
						</div>
					);
				} ) }
			</div>
		</>
	);
}

