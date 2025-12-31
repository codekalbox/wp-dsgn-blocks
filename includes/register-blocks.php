<?php
/**
 * Register blocks and enqueue assets
 *
 * @package FlexBlocksLayoutBuilder
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all blocks.
 *
 * @since 1.0.0
 */
function flexblocks_register_blocks() {
	// Register Section Block.
	register_block_type(
		FLEXBLOCKS_PLUGIN_DIR . 'build/section'
	);

	// Register Columns Block.
	register_block_type(
		FLEXBLOCKS_PLUGIN_DIR . 'build/columns'
	);
}
add_action( 'init', 'flexblocks_register_blocks' );

/**
 * Enqueue frontend and editor styles.
 *
 * @since 1.0.0
 */
function flexblocks_enqueue_assets() {
	// Frontend styles for all blocks.
	wp_enqueue_style(
		'flexblocks-style',
		FLEXBLOCKS_PLUGIN_URL . 'assets/css/style.css',
		array(),
		FLEXBLOCKS_VERSION
	);
}
add_action( 'enqueue_block_assets', 'flexblocks_enqueue_assets' );
