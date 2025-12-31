<?php
/**
 * Main plugin class for WP DSGN Blocks.
 *
 * @package WP_DSGN_Blocks
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main WP DSGN Blocks class.
 */
class WP_DSGN_Blocks {

	/**
	 * Plugin instance.
	 *
	 * @var WP_DSGN_Blocks
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return WP_DSGN_Blocks
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'wp-dsgn-blocks',
			false,
			dirname( plugin_basename( WP_DSGN_BLOCKS_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Register all blocks.
	 */
	public function register_blocks() {
		// Register Section block.
		register_block_type( WP_DSGN_BLOCKS_BUILD_DIR . 'blocks/section' );

		// Register Columns block.
		register_block_type( WP_DSGN_BLOCKS_BUILD_DIR . 'blocks/columns' );
	}

	/**
	 * Enqueue editor assets.
	 */
	public function enqueue_editor_assets() {
		// Enqueue global editor styles.
		wp_enqueue_style(
			'wp-dsgn-blocks-editor',
			WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/editor.css',
			array( 'wp-edit-blocks' ),
			WP_DSGN_BLOCKS_VERSION
		);

		// Localize script with plugin data.
		$localize_data = array(
			'pluginUrl'   => WP_DSGN_BLOCKS_PLUGIN_URL,
			'buildUrl'    => WP_DSGN_BLOCKS_BUILD_URL,
			'version'     => WP_DSGN_BLOCKS_VERSION,
			'breakpoints' => array(
				'desktop' => 1025,
				'tablet'  => 1024,
				'mobile'  => 768,
			),
			'units'       => array(
				'px'  => __( 'Pixels', 'wp-dsgn-blocks' ),
				'em'  => __( 'Em', 'wp-dsgn-blocks' ),
				'rem' => __( 'Rem', 'wp-dsgn-blocks' ),
				'%'   => __( 'Percent', 'wp-dsgn-blocks' ),
				'vh'  => __( 'Viewport Height', 'wp-dsgn-blocks' ),
				'vw'  => __( 'Viewport Width', 'wp-dsgn-blocks' ),
			),
			'flexboxOptions' => array(
				'direction' => array(
					'row'            => __( 'Row', 'wp-dsgn-blocks' ),
					'row-reverse'    => __( 'Row Reverse', 'wp-dsgn-blocks' ),
					'column'         => __( 'Column', 'wp-dsgn-blocks' ),
					'column-reverse' => __( 'Column Reverse', 'wp-dsgn-blocks' ),
				),
				'justify' => array(
					'flex-start'    => __( 'Start', 'wp-dsgn-blocks' ),
					'flex-end'      => __( 'End', 'wp-dsgn-blocks' ),
					'center'        => __( 'Center', 'wp-dsgn-blocks' ),
					'space-between' => __( 'Space Between', 'wp-dsgn-blocks' ),
					'space-around'  => __( 'Space Around', 'wp-dsgn-blocks' ),
					'space-evenly'  => __( 'Space Evenly', 'wp-dsgn-blocks' ),
				),
				'align' => array(
					'flex-start' => __( 'Start', 'wp-dsgn-blocks' ),
					'flex-end'   => __( 'End', 'wp-dsgn-blocks' ),
					'center'     => __( 'Center', 'wp-dsgn-blocks' ),
					'stretch'    => __( 'Stretch', 'wp-dsgn-blocks' ),
					'baseline'   => __( 'Baseline', 'wp-dsgn-blocks' ),
				),
			),
		);

		wp_localize_script(
			'wp-dsgn-blocks-section-editor-script',
			'wpDsgnBlocks',
			$localize_data
		);
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_frontend_assets() {
		// Only enqueue if blocks are present on the page.
		if ( $this->has_blocks_on_page() ) {
			wp_enqueue_style(
				'wp-dsgn-blocks-frontend',
				WP_DSGN_BLOCKS_PLUGIN_URL . 'assets/css/style.css',
				array(),
				WP_DSGN_BLOCKS_VERSION
			);
		}
	}

	/**
	 * Check if any of our blocks are present on the current page.
	 *
	 * @return bool
	 */
	private function has_blocks_on_page() {
		if ( ! function_exists( 'has_block' ) ) {
			return false;
		}

		// Check for our blocks.
		return has_block( 'wpdsgn/section' ) || has_block( 'wpdsgn/columns' );
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return WP_DSGN_BLOCKS_VERSION;
	}

	/**
	 * Get plugin directory path.
	 *
	 * @return string
	 */
	public function get_plugin_dir() {
		return WP_DSGN_BLOCKS_PLUGIN_DIR;
	}

	/**
	 * Get plugin directory URL.
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return WP_DSGN_BLOCKS_PLUGIN_URL;
	}

	/**
	 * Get build directory path.
	 *
	 * @return string
	 */
	public function get_build_dir() {
		return WP_DSGN_BLOCKS_BUILD_DIR;
	}

	/**
	 * Get build directory URL.
	 *
	 * @return string
	 */
	public function get_build_url() {
		return WP_DSGN_BLOCKS_BUILD_URL;
	}
}

