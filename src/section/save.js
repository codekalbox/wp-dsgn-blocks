/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { generateSectionStyles } from '../utils/styles';

/**
 * Save component for Section block.
 *
 * @param {Object} props Block properties.
 * @return {Element} Section block save output.
 */
export default function save( { attributes } ) {
	const {
		htmlTag: Tag,
		customClassName,
		customId,
	} = attributes;

	const styles = generateSectionStyles( attributes );
	const blockProps = useBlockProps.save( {
		className: customClassName || undefined,
		id: customId || undefined,
		style: styles,
	} );

	return (
		<Tag { ...blockProps }>
			<div className="flexblocks-section-inner">
				<InnerBlocks.Content />
			</div>
		</Tag>
	);
}

