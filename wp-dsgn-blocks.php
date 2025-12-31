<?php
/**
 * Plugin Name:       WP DSGN Blocks
 * Plugin URI:        https://github.com/codekalbox/wp-dsgn-blocks
 * Description:       Professional Gutenberg blocks with advanced flexbox controls for modern layout building.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            CodeKalbox
 * Author URI:        https://github.com/codekalbox
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-dsgn-blocks
 * Domain Path:       /languages
 * Network:           false
 *
 * @package WP_DSGN_Blocks
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'WP_DSGN_BLOCKS_VERSION', '1.0.0' );
define( 'WP_DSGN_BLOCKS_PLUGIN_FILE', __FILE__ );
define( 'WP_DSGN_BLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_DSGN_BLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_DSGN_BLOCKS_BUILD_DIR', WP_DSGN_BLOCKS_PLUGIN_DIR . 'build/' );
define( 'WP_DSGN_BLOCKS_BUILD_URL', WP_DSGN_BLOCKS_PLUGIN_URL . 'build/' );

/**
 * Check WordPress and PHP version requirements.
 */
function wp_dsgn_blocks_check_requirements() {
	global $wp_version;

	$php_version = phpversion();
	$wp_version_required = '6.0';
	$php_version_required = '7.4';

	$errors = array();

	// Check WordPress version.
	if ( version_compare( $wp_version, $wp_version_required, '<' ) ) {
		$errors[] = sprintf(
			/* translators: 1: Required WordPress version, 2: Current WordPress version */
			__( 'WP DSGN Blocks requires WordPress %1$s or higher. You are running WordPress %2$s.', 'wp-dsgn-blocks' ),
			$wp_version_required,
			$wp_version
		);
	}

	// Check PHP version.
	if ( version_compare( $php_version, $php_version_required, '<' ) ) {
		$errors[] = sprintf(
			/* translators: 1: Required PHP version, 2: Current PHP version */
			__( 'WP DSGN Blocks requires PHP %1$s or higher. You are running PHP %2$s.', 'wp-dsgn-blocks' ),
			$php_version_required,
			$php_version
		);
	}

	if ( ! empty( $errors ) ) {
		add_action( 'admin_notices', function() use ( $errors ) {
			echo '<div class="notice notice-error"><p>';
			echo '<strong>' . esc_html__( 'WP DSGN Blocks Error:', 'wp-dsgn-blocks' ) . '</strong><br>';
			foreach ( $errors as $error ) {
				echo esc_html( $error ) . '<br>';
			}
			echo '</p></div>';
		} );
		return false;
	}

	return true;
}

/**
 * Initialize the plugin.
 */
function wp_dsgn_blocks_init() {
	// Check requirements.
	if ( ! wp_dsgn_blocks_check_requirements() ) {
		return;
	}

	// Load the main plugin class.
	require_once WP_DSGN_BLOCKS_PLUGIN_DIR . 'includes/class-wp-dsgn-blocks.php';

	// Initialize the plugin.
	WP_DSGN_Blocks::get_instance();
}

/**
 * Plugin activation hook.
 */
function wp_dsgn_blocks_activate() {
	// Check requirements on activation.
	if ( ! wp_dsgn_blocks_check_requirements() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html__( 'WP DSGN Blocks could not be activated due to unmet requirements.', 'wp-dsgn-blocks' ),
			esc_html__( 'Plugin Activation Error', 'wp-dsgn-blocks' ),
			array( 'back_link' => true )
		);
	}

	// Set activation flag for any setup needed.
	update_option( 'wp_dsgn_blocks_activated', true );
}

/**
 * Plugin deactivation hook.
 */
function wp_dsgn_blocks_deactivate() {
	// Clean up any temporary data if needed.
	delete_option( 'wp_dsgn_blocks_activated' );
}

/**
 * Plugin uninstall hook.
 */
function wp_dsgn_blocks_uninstall() {
	// Clean up plugin data on uninstall.
	delete_option( 'wp_dsgn_blocks_activated' );
	
	// Note: We don't delete user content (blocks) as that would be destructive.
	// Only clean up plugin-specific options and transients.
}

// Register hooks.
register_activation_hook( __FILE__, 'wp_dsgn_blocks_activate' );
register_deactivation_hook( __FILE__, 'wp_dsgn_blocks_deactivate' );
register_uninstall_hook( __FILE__, 'wp_dsgn_blocks_uninstall' );

// Initialize the plugin.
add_action( 'plugins_loaded', 'wp_dsgn_blocks_init' );

/**
 * Add custom block category.
 */
function wp_dsgn_blocks_add_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'wpdsgn-blocks',
				'title' => __( 'WP DSGN Blocks', 'wp-dsgn-blocks' ),
				'icon'  => 'layout',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'wp_dsgn_blocks_add_block_category' );

/**
 * Add global CSS variables for consistent theming.
 */
function wp_dsgn_blocks_add_global_styles() {
	$css = '
	:root {
		--wpdsgn-breakpoint-tablet: 1024px;
		--wpdsgn-breakpoint-mobile: 768px;
		--wpdsgn-container-max-width: 1200px;
		--wpdsgn-section-padding: 20px;
		--wpdsgn-column-gap: 20px;
		--wpdsgn-border-radius: 4px;
		--wpdsgn-transition: all 0.3s ease;
	}
	';
	
	wp_add_inline_style( 'wp-block-library', $css );
}
add_action( 'wp_enqueue_scripts', 'wp_dsgn_blocks_add_global_styles' );
add_action( 'enqueue_block_editor_assets', 'wp_dsgn_blocks_add_global_styles' );

