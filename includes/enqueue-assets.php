<?php
/**
 * Enqueue assets.
 *
 * @package WP_DSGN_Blocks
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend assets.
 *
 * @since 1.0.0
 */
function wp_dsgn_blocks_enqueue_frontend_assets() {
	// Enqueue frontend styles.
	wp_enqueue_style(
		'wp-dsgn-blocks-frontend',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/style.css',
		array(),
		WP_DSGN_BLOCKS_VERSION
	);

	// Enqueue frontend scripts.
	wp_enqueue_script(
		'wp-dsgn-blocks-frontend',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/js/frontend.js',
		array(),
		WP_DSGN_BLOCKS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wp_dsgn_blocks_enqueue_frontend_assets' );

/**
 * Enqueue admin assets.
 *
 * @since 1.0.0
 */
function wp_dsgn_blocks_enqueue_admin_assets( $hook ) {
	// Only load on relevant admin pages.
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'site-editor.php' ), true ) ) {
		return;
	}

	// Enqueue admin styles.
	wp_enqueue_style(
		'wp-dsgn-blocks-admin',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/admin.css',
		array(),
		WP_DSGN_BLOCKS_VERSION
	);

	// Enqueue admin scripts.
	wp_enqueue_script(
		'wp-dsgn-blocks-admin',
		WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/js/admin.js',
		array( 'jquery' ),
		WP_DSGN_BLOCKS_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'wp_dsgn_blocks_enqueue_admin_assets' );

