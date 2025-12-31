/**
 * WP DSGN Blocks - Main Entry Point
 * 
 * Registers all blocks and shared components.
 */

/**
 * Import all blocks
 */
import './blocks/section';
import './blocks/columns';

/**
 * Import shared utilities
 */
import './utils/styles';

/**
 * Import shared components (if any global components are needed)
 */
// import './components';

/**
 * Global initialization
 */
document.addEventListener( 'DOMContentLoaded', function() {
	// Any global JavaScript initialization can go here
	console.log( 'WP DSGN Blocks loaded successfully' );
} );

