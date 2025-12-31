/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import './style.scss';

/**
 * Block icon
 */
const icon = (
	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" strokeWidth="2" fill="none"/>
		<rect x="6" y="8" width="4" height="8" rx="1" fill="currentColor" opacity="0.3"/>
		<rect x="14" y="8" width="4" height="8" rx="1" fill="currentColor" opacity="0.3"/>
	</svg>
);

/**
 * Register the Section block.
 */
registerBlockType( metadata.name, {
	...metadata,
	icon,
	edit: Edit,
	save,
} );

