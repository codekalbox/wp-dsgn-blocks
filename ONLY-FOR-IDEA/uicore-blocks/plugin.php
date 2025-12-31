<?php
/*
Plugin Name: UiCore Blocks
Plugin URI: https://blocks.uicore.co
Description: Free WordPress Gutenberg Blocks.
Version: 1.0.10
Author: UiCore
Author URI: https://uicore.co
License: GPL3
Text Domain: uicore-blocks
Domain Path: /languages
*/

namespace UiCoreBlocks;

// don't call the file directly
if (!defined('ABSPATH')) exit;

/**
 * Base class
 *
 * @class Base The class that holds the entire plugin
 */
final class Base
{

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.10';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Base class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {

        $this->define_constants();

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'init_plugin'));
    }

    /**
     * Initializes the Base() class
     *
     * Checks for an existing Base() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Base();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('UICORE_BLOCKS_VERSION', $this->version);
        define('UICORE_BLOCKS_FILE', __FILE__);
        define('UICORE_BLOCKS_PATH', dirname(UICORE_BLOCKS_FILE));
        define('UICORE_BLOCKS_INCLUDES', UICORE_BLOCKS_PATH . '/includes');
        define('UICORE_BLOCKS_URL', plugins_url('', UICORE_BLOCKS_FILE));
        define('UICORE_BLOCKS_ASSETS', UICORE_BLOCKS_URL . '/assets');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {

        $installed = get_option('uiblocks_installed');

        if (!$installed) {
            update_option('uiblocks_installed', time());
        }

        update_option('uiblocks_version', UICORE_BLOCKS_VERSION);
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {}

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {

        require_once UICORE_BLOCKS_INCLUDES . '/class-helper.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-fonts.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-settings.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-global-styles.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-assets.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-blocks.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-blocks-save.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-blocks-dynamic-content.php';
        require_once UICORE_BLOCKS_INCLUDES . '/class-query-filters.php';

        if ($this->is_request('admin')) {
            require_once UICORE_BLOCKS_INCLUDES . '/class-admin.php';
            require_once UICORE_BLOCKS_INCLUDES . '/class-dashboard.php';
        }

        if ($this->is_request('frontend')) {
            require_once UICORE_BLOCKS_INCLUDES . '/class-frontend.php';
            require_once UICORE_BLOCKS_INCLUDES . '/class-woo-frontend.php';
        }

        if ($this->is_request('rest')) {
            require_once UICORE_BLOCKS_INCLUDES . '/class-forms-service.php';
            require_once UICORE_BLOCKS_INCLUDES . '/class-rest-api.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {

        add_action('init', array($this, 'init_classes'));

        // Localize our plugin
        add_action('init', array($this, 'localization_setup'));
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {

        if ($this->is_request('admin')) {
            $this->container['admin'] = new Admin();
            $this->container['dashboard'] = new Dashboard();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new Frontend();
            $this->container['woo-frontend'] = new WooFrontend();
            $this->container['dynamic-content'] = new BlocksDynamicContent();
        }

        if ($this->is_request('rest')) {
            $this->container['rest'] = new Api();
        }

        $this->container['assets'] = new Assets();
        $this->container['settings'] = new Settings();
        $this->container['blocks-save'] = new BlocksSave();

        //extra
        $this->container['blocks'] = new Blocks();
        $this->container['query-filters'] = new QueryFilters();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        // load_plugin_textdomain('uicore-blocks', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');

            case 'rest':
                return self::is_rest();
        }
    }

    /**
     * Check if is Rest API request
     *
     * @return bool
     */
    static function is_rest()
    {
        if (
            defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
            && strpos($_GET['rest_route'], '/', 0) === 0
        )
            return true;

        // (#3)
        global $wp_rewrite;
        if ($wp_rewrite === null) $wp_rewrite = new \WP_Rewrite();

        // (#4)
        $rest_url = wp_parse_url(trailingslashit(rest_url()));
        $current_url = wp_parse_url(add_query_arg(array()));
        return strpos($current_url['path'] ?? '/', $rest_url['path'], 0) === 0;
    }
} // Base

$uicore_animate = Base::init();
