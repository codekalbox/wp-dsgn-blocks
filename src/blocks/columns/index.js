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
		<rect x="2" y="4" width="8" height="16" rx="1" stroke="currentColor" strokeWidth="2" fill="none"/>
		<rect x="14" y="4" width="8" height="16" rx="1" stroke="currentColor" strokeWidth="2" fill="none"/>
		<rect x="6" y="8" width="4" height="2" rx="0.5" fill="currentColor" opacity="0.3"/>
		<rect x="6" y="12" width="4" height="2" rx="0.5" fill="currentColor" opacity="0.3"/>
		<rect x="18" y="8" width="4" height="2" rx="0.5" fill="currentColor" opacity="0.3"/>
		<rect x="18" y="12" width="4" height="2" rx="0.5" fill="currentColor" opacity="0.3"/>
	</svg>
);

/**
 * Register the Columns block.
 */
registerBlockType( metadata.name, {
	...metadata,
	icon,
	edit: Edit,
	save,
} );

