<?php
/**
 * Register blocks and enqueue assets.
 *
 * @package WP_DSGN_Blocks
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
function wp_dsgn_blocks_register_blocks() {
	// Check if function exists (WordPress 5.0+).
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Register Section Block.
	register_block_type(
		WP_DSGN_BLOCKS_PLUGIN_DIR . 'build/blocks/section'
	);

	// Register Columns Block.
	register_block_type(
		WP_DSGN_BLOCKS_PLUGIN_DIR . 'build/blocks/columns'
	);
}
add_action( 'init', 'wp_dsgn_blocks_register_blocks' );

/**
 * Enqueue block assets.
 *
 * @since 1.0.0
 */
function wp_dsgn_blocks_enqueue_block_assets() {
	// Enqueue frontend styles for all blocks.
	wp_enqueue_style(
		'wp-dsgn-blocks-style',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/style.css',
		array(),
		WP_DSGN_BLOCKS_VERSION
	);
}
add_action( 'enqueue_block_assets', 'wp_dsgn_blocks_enqueue_block_assets' );

/**
 * Enqueue editor assets.
 *
 * @since 1.0.0
 */
function wp_dsgn_blocks_enqueue_editor_assets() {
	// Enqueue editor styles.
	wp_enqueue_style(
		'wp-dsgn-blocks-editor',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/editor.css',
		array( 'wp-edit-blocks' ),
		WP_DSGN_BLOCKS_VERSION
	);

	// Enqueue editor scripts.
	wp_enqueue_script(
		'wp-dsgn-blocks-editor-js',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/js/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		WP_DSGN_BLOCKS_VERSION,
		true
	);

	// Localize script with plugin data.
	wp_localize_script(
		'wp-dsgn-blocks-editor-js',
		'wpDsgnBlocks',
		array(
			'pluginUrl'   => WP_DSGN_BLOCKS_PLUGIN_URL,
			'breakpoints' => WP_DSGN_Blocks::get_breakpoints(),
			'units'       => WP_DSGN_Blocks::get_spacing_units(),
			'flexbox'     => WP_DSGN_Blocks::get_flexbox_options(),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'wp_dsgn_blocks_enqueue_editor_assets' );

