/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { generateSectionCSS } from './utils/generateCSS';

/**
 * Save component for Section block.
 */
export default function save( { attributes } ) {
	const {
		uniqueId,
		htmlTag,
		customClass,
		customId
	} = attributes;

	// Generate CSS for frontend
	const sectionCSS = generateSectionCSS( attributes, uniqueId );

	// Create the HTML tag dynamically
	const TagName = htmlTag || 'section';

	// Block props for save
	const blockProps = useBlockProps.save( {
		className: `wpdsgn-section wpdsgn-section-${uniqueId} ${customClass}`.trim(),
		id: customId || undefined
	} );

	return (
		<>
			<TagName { ...blockProps }>
				<InnerBlocks.Content />
			</TagName>
			
			{/* Inject CSS for frontend */}
			<style>{ sectionCSS }</style>
		</>
	);
}

