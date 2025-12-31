<?php
/**
 * Plugin Name:       FlexBlocks Layout Builder
 * Plugin URI:        https://github.com/codekalbox/wp-dsgn-blocks
 * Description:       Advanced Gutenberg blocks for flexible layout building with complete flexbox control. Includes Section and Columns blocks with extensive customization options.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            CodeKalbox
 * Author URI:        https://github.com/codekalbox
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       flexblocks-layout-builder
 * Domain Path:       /languages
 *
 * @package FlexBlocksLayoutBuilder
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'FLEXBLOCKS_VERSION', '1.0.0' );
define( 'FLEXBLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLEXBLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FLEXBLOCKS_PLUGIN_FILE', __FILE__ );

/**
 * Check if the required WordPress version is met.
 *
 * @since 1.0.0
 * @return bool True if requirements are met, false otherwise.
 */
function flexblocks_check_requirements() {
	global $wp_version;

	$required_wp_version = '6.0';
	$required_php_version = '7.4';

	if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
		add_action( 'admin_notices', 'flexblocks_wp_version_notice' );
		return false;
	}

	if ( version_compare( phpversion(), $required_php_version, '<' ) ) {
		add_action( 'admin_notices', 'flexblocks_php_version_notice' );
		return false;
	}

	return true;
}

/**
 * Display admin notice for WordPress version requirement.
 *
 * @since 1.0.0
 */
function flexblocks_wp_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: required WordPress version */
				esc_html__( 'FlexBlocks Layout Builder requires WordPress version %s or higher. Please update WordPress.', 'flexblocks-layout-builder' ),
				'6.0'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Display admin notice for PHP version requirement.
 *
 * @since 1.0.0
 */
function flexblocks_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: required PHP version */
				esc_html__( 'FlexBlocks Layout Builder requires PHP version %s or higher. Please update PHP.', 'flexblocks-layout-builder' ),
				'7.4'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Load plugin textdomain for translations.
 *
 * @since 1.0.0
 */
function flexblocks_load_textdomain() {
	load_plugin_textdomain(
		'flexblocks-layout-builder',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'flexblocks_load_textdomain' );

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
function flexblocks_init() {
	// Check requirements before proceeding.
	if ( ! flexblocks_check_requirements() ) {
		return;
	}

	// Include the block registration file.
	require_once FLEXBLOCKS_PLUGIN_DIR . 'includes/register-blocks.php';
}
add_action( 'plugins_loaded', 'flexblocks_init' );

/**
 * Enqueue block editor assets.
 *
 * @since 1.0.0
 */
function flexblocks_enqueue_block_editor_assets() {
	// Editor-specific styles.
	wp_enqueue_style(
		'flexblocks-editor-styles',
		FLEXBLOCKS_PLUGIN_URL . 'assets/css/editor.css',
		array(),
		FLEXBLOCKS_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'flexblocks_enqueue_block_editor_assets' );

/**
 * Register block category.
 *
 * @since 1.0.0
 * @param array $categories Array of block categories.
 * @return array Modified array of block categories.
 */
function flexblocks_register_block_category( $categories ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'flexblocks',
				'title' => __( 'FlexBlocks', 'flexblocks-layout-builder' ),
				'icon'  => 'layout',
			),
		)
	);
}
add_filter( 'block_categories_all', 'flexblocks_register_block_category' );

/**
 * Plugin activation hook.
 *
 * @since 1.0.0
 */
function flexblocks_activate() {
	// Check requirements on activation.
	if ( ! flexblocks_check_requirements() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html__( 'FlexBlocks Layout Builder could not be activated due to unmet requirements.', 'flexblocks-layout-builder' ),
			esc_html__( 'Plugin Activation Error', 'flexblocks-layout-builder' ),
			array( 'back_link' => true )
		);
	}

	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'flexblocks_activate' );

/**
 * Plugin deactivation hook.
 *
 * @since 1.0.0
 */
function flexblocks_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'flexblocks_deactivate' );

