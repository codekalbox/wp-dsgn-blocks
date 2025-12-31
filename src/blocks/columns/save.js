/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { generateColumnsCSS } from './utils/generateCSS';

/**
 * Save component for Columns block.
 */
export default function save( { attributes } ) {
	const {
		uniqueId,
		customClass,
		customId
	} = attributes;

	// Generate CSS for frontend
	const columnsCSS = generateColumnsCSS( attributes, uniqueId );

	// Block props for save
	const blockProps = useBlockProps.save( {
		className: `wpdsgn-columns wpdsgn-columns-${uniqueId} ${customClass}`.trim(),
		id: customId || undefined
	} );

	return (
		<>
			<div { ...blockProps }>
				<InnerBlocks.Content />
			</div>
			
			{/* Inject CSS for frontend */}
			<style>{ columnsCSS }</style>
		</>
	);
}

