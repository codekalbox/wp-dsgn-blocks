<?php

namespace UiCoreBlocks;

/**
 * Scripts and Styles Class
 */
class Assets
{

	function __construct()
	{

		if (is_admin()) {
			add_action('admin_enqueue_scripts', [$this, 'register'], 1);
		} else {
			add_action('wp_enqueue_scripts', [$this, 'register'], 1);
			// Add filter to async load snippet styles
			add_filter('style_loader_tag', [__CLASS__, 'filter_snippet_asset_tag'], 10, 2);
			add_filter('script_loader_tag', [__CLASS__, 'filter_snippet_asset_tag'], 10, 2);
		}
	}

	/**
	 * Register our app scripts and styles
	 *
	 * @return void
	 */
	public function register()
	{
		$this::register_scripts(wp_parse_args($this->get_scripts(), $this->get_snippets_scripts()));
		$this::register_styles(wp_parse_args($this->get_styles(), $this->get_snippets_styles()));
	}

	/**
	 * Register scripts
	 *
	 * @param  array $scripts
	 *
	 * @return void
	 */
	public static function register_scripts($scripts)
	{
		foreach ($scripts as $handle => $script) {
			$deps      = isset($script['deps']) ? $script['deps'] : false;
			$in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;
			$version   = isset($script['version']) ? $script['version'] : UICORE_BLOCKS_VERSION . time(); //TODO: REMOVE TIME ON RELEASE
			wp_register_script($handle, $script['src'], $deps, $version, $in_footer);
		}

		$site_key = get_option('uicore_blocks_recaptcha_site_key');
		wp_localize_script('wp-block-uicore-form-field', 'uicoreBlockForms', [
			'baseUrl'  => esc_url_raw(rest_url()),
			'recaptchaKey' => $site_key
		]);
	}

	/**
	 * Register styles
	 *
	 * @param  array $styles
	 *
	 * @return void
	 */
	public static function register_styles($styles)
	{
		foreach ($styles as $handle => $style) {
			$deps = isset($style['deps']) ? $style['deps'] : [];

			wp_register_style($handle, $style['src'], $deps, UICORE_BLOCKS_VERSION . time()); //TODO: REMOVE TIME ON RELEASE
		}
	}

	/**
	 * Retrieves an array of snippets styles.
	 *
	 * @return array An array of snippets styles.
	 */
	static function get_snippets_styles()
	{
		$styles = [
			//generic snippets
			'uicore-bl-border-rotate' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/border-rotate.css'
			],
			'uicore-bl-hover-glow' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/hover-glow.css'
			],
			'uicore-bl-highlight' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/highlight.css'
			],
			'uicore-bl-swiper' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/swiper.css'
			],
			'uicore-bl-lightbox' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/glightbox.css'
			],

			//specific styles snippets
			'block-uicore-accordion-item' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/accordion.css'
			],
			'block-uicore-form-field' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/snippets/form.css'
			],

		];

		return $styles;
	}

	/**
	 * Retrieves the snippets scripts.
	 *
	 * @return array The snippets scripts.
	 */
	static function get_snippets_scripts()
	{
		$scripts = [
			'uicore-bl-hover-glow' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/hover-glow.js',
			],
			'uicore-bl-counter' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/counter.js',
			],
			'uicore-bl-highlight-animate' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/highlight.js'
			],
			'uicore-bl-odometer' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/odometer.js',
			],
			'uicore-bl-swiper' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/swiper.js',
			],
			'uicore-bl-swiper-animation' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/swiper-animation.js',
			],
			'uicore-bl-lightbox' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/js/snippets/glightbox.js'
			],
			'uicore-bl-video' => [
				'src' => UICORE_BLOCKS_ASSETS . '/js/snippets/video.js',
			],
			'wp-block-uicore-accordion-item' => [
				'src' => UICORE_BLOCKS_ASSETS . '/js/snippets/accordion.js',
			],
			'wp-block-uicore-form-field' => [
				'src' => UICORE_BLOCKS_ASSETS . '/js/snippets/form.js',
			],
			'uicore-bl-lazy-bg' => [
				'src' => UICORE_BLOCKS_ASSETS . '/js/snippets/lazy-bg.js',
			],
			'uicore-bl-cluster-markers' => [
				'src' => 'https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js',
			],
			'wp-block-uicore-google-maps' => [
				'src' => (function () {
					$key = get_option('uicore_blocks_googlemaps_api_key');
					return 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&loading=async&callback=uicoreInitMap';
				})(),
			]
		];

		return $scripts;
	}

	/**
	 * Get all registered scripts
	 *
	 * @return array
	 */
	static function get_scripts()
	{

		$tools_data = require UICORE_BLOCKS_PATH . '/assets/blocks/tools.asset.php';
		$common_data = require UICORE_BLOCKS_PATH . '/assets/blocks/common.asset.php';
		$icons_data = require UICORE_BLOCKS_PATH . '/assets/blocks/icons.asset.php';
		$vendors_data = require UICORE_BLOCKS_PATH . '/assets/blocks/vendors.asset.php';

		$scripts = [
			'uicore-bl-tools' => [
				'src'       => UICORE_BLOCKS_URL . '/assets/blocks/tools.js',
				'deps'      => $tools_data['dependencies'],
				'version'   => $tools_data['version'],
				'in_footer' => true
			],
			'uicore-bl-common' => [
				'src'       => UICORE_BLOCKS_URL . '/assets/blocks/common.js',
				'deps'      => $common_data['dependencies'],
				'version'   => $common_data['version'],
				'in_footer' => true
			],
			'uicore-bl-icons' => [
				'src'       => UICORE_BLOCKS_URL . '/assets/blocks/icons.js',
				'deps'      => $icons_data['dependencies'],
				'version'   => $icons_data['version'],
				'in_footer' => true
			],
			'uicore-bl-vendors' => [
				'src'       => UICORE_BLOCKS_URL . '/assets/blocks/vendors.js',
				'deps'      => $vendors_data['dependencies'],
				'version'   => $vendors_data['version'],
				'in_footer' => true
			],
		];

		if (class_exists('WooCommerce')) {
			$woocommerce_data = require UICORE_BLOCKS_PATH . '/assets/blocks/woocommerce.asset.php';

			$scripts['uicore-bl-woocommerce'] = [
				'src'       => UICORE_BLOCKS_URL . '/assets/blocks/woocommerce.js',
				'deps'      => $woocommerce_data['dependencies'],
				'version'   => $woocommerce_data['version'],
				'in_footer' => true
			];
		}

		return $scripts;
	}

	/**
	 * Get registered styles
	 *
	 * @return array
	 */
	public function get_styles()
	{
		$styles = [
			'uicore-bl-frontend' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/css/frontend.css'
			],
			'uicore-bl-tools' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/blocks/tools.css'
			],
			'uicore-bl-common' => [
				'src' =>  UICORE_BLOCKS_ASSETS . '/blocks/common.css'
			],
		];

		if (class_exists('WooCommerce')) {
			$styles['uicore-bl-woocommerce'] = [
				'src' =>  UICORE_BLOCKS_ASSETS . '/blocks/woocommerceStyles.css'
			];
		}

		return $styles;
	}

	/**
	 * Get handles for snippet styles
	 *
	 * @return array
	 */
	public static function get_snippet_style_handles()
	{
		return array_keys(self::get_snippets_styles());
	}

	/**
	 * Get handles for snippet scripts
	 *
	 * @return array
	 */
	public static function get_snippet_script_handles()
	{
		return array_keys(self::get_snippets_scripts());
	}

	/**
	 * Filter style and script tag for snippet handles to load async
	 */
	public static function filter_snippet_asset_tag($tag, $handle)
	{
		$snippet_style_handles = self::get_snippet_style_handles();
		$snippet_script_handles = self::get_snippet_script_handles();

		// Async CSS for snippet styles
		if (in_array($handle, $snippet_style_handles)) {
			if (strpos($tag, "rel='stylesheet'") !== false) {
				$tag = str_replace(
					"rel='stylesheet'",
					"rel='stylesheet' media='print' onload=\"this.onload=null;this.media='all'\"",
					$tag
				);
			}
		}

		// Async CSS for dynamically generated styles while preserving existing media attribute
		if (strpos($handle, 'uicore-bl-p-') === 0) {
			if (strpos($tag, "rel='stylesheet'") !== false) {
				$tag = preg_replace(
					"/media='([^']*)'/",
					"media='print' onload=\"this.onload=null;this.media='$1'\"",
					$tag
				);
			}
		}

		// Async JS
		if (in_array($handle, $snippet_script_handles)) {
			// Only add defer if not already present
			if (strpos($tag, 'defer') === false) {
				// Insert defer before closing '>' of <script ...>
				$tag = preg_replace('/<script(.*?)>/i', '<script$1 defer>', $tag);
			}
		}
		return $tag;
	}
}
