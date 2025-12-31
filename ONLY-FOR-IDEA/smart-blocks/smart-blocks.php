<?php
defined('ABSPATH') || die;
/*
  Plugin Name:       Smart Blocks - WordPress Gutenberg Blocks
  Description:       Collection of advanced blocks to be used with WordPress Gutenberg Pagebuilder
  Version:           2.8
  Author:            HashThemes
  Author URI:        http://hashthemes.com
  License:           GPLv2 or later
  License URI:       https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path:       /languages
  Text Domain:       smart-blocks
 */

define('SMART_BLOCKS_FILE', __FILE__);
define('SMART_BLOCKS_PATH', plugin_dir_path(SMART_BLOCKS_FILE));
define('SMART_BLOCKS_URL', plugins_url('/', SMART_BLOCKS_FILE));
define('SMART_BLOCKS_BASENAME', plugin_basename(SMART_BLOCKS_FILE));
define('SMART_BLOCKS_VERSION', '2.8');

if (!class_exists('Smart_Blocks')) {

    class Smart_Blocks {

        private static $instance = NULL;

        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if (self::$instance == NULL) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct() {
            add_action('init', array($this, 'registerPostMeta'));
            add_action('init', array($this, 'add_page_templates_in_post_types'), 999);

            add_action('enqueue_block_editor_assets', array($this, 'block_localization'));

            // Initialize Blocks
            add_action('init', array($this, 'create_block_init'), 1000);

            // Load necessary files.
            add_action('plugins_loaded', array($this, 'init'));

            // Add new Category for blocks
            add_filter('block_categories_all', array($this, 'register_category'), 10, 2);

            // Add custom fields for post types
            add_action('rest_api_init', array($this, 'register_custom_fields'));

            // Allow more orderBy values for posts
            add_filter('rest_post_collection_params', array($this, 'post_query_vars'));

            // Review Notification
            add_action('wp_loaded', array($this, 'admin_notice'), 20);
            add_action('admin_init', array($this, 'welcome_init'));
            add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));

            add_action('enqueue_block_editor_assets', array($this, 'block_editor_assets'));

            if (is_admin()) {
                add_filter('admin_body_class', array($this, 'add_editor_body_class'));
            }

            add_action('admin_menu', array($this, 'register_admin_menu'));

            add_action('wp_ajax_admin_settings_save', [$this, 'sb_settings_save']);
            add_action('wp_ajax_sb_blocks_save', [$this, 'sb_blocks_save']);

            add_action('wp_head', array($this, 'sb_block_styles'));
            add_action('admin_head', array($this, 'sb_block_styles'));

            add_action('init', array($this, 'maybe_run_one_time_setup'));

            add_filter('plugin_action_links_' . SMART_BLOCKS_BASENAME, array($this, 'add_settings_link'));
        }

        public function maybe_run_one_time_setup() {
            if (!get_option('sb_setup_done')) {
                update_option('sb_blocks', array_keys(smart_blocks_get_all_blocks_list()));

                // Set the flag so it doesn't run again
                update_option('sb_setup_done', true);
            }
        }

        public function sb_block_styles() {
            $sb_general_settings = get_option('sb_general_settings');
            $default_section_width = isset($sb_general_settings['default_section_width']) && $sb_general_settings['default_section_width'] ? $sb_general_settings['default_section_width'] : '';
            ?>
            <style>
                body {
                    <?php
                    echo $default_section_width ? '--sb-columns-width: ' . intval($default_section_width) . 'px;' : '';
                    ?>
                }
            </style>
            <?php
        }

        public function sb_blocks_save() {
            check_ajax_referer('sb_ajax_nonce', 'sb_nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error('You are not allowed to perform this action.');
            }

            $data_ar = isset($_POST['blocks']) && !empty($_POST['blocks']) ? (array) wp_unslash($_POST['blocks']) : array();
            $update_blocks = update_option('sb_blocks', $data_ar);

            if ($update_blocks || !empty($data_ar)) {
                wp_send_json_success('Saved successfully!');
            } else {
                wp_send_json_error('Save failed!');
            }
        }

        public function sb_settings_save() {
            check_ajax_referer('sb_ajax_nonce', 'sb_nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error('You are not allowed to perform this action.');
            }

            $data_ar = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
            $settings_ar = [];

            foreach ($data_ar as $value) {
                $settings_ar[$value['name']] = $value['value'];
            }

            update_option('sb_general_settings', $settings_ar);
            wp_send_json_success('Saved successfully!');

        }

        // Enqueue localization data for our blocks.
        public function block_localization() {
            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations('sb-blocks', 'smart-blocks', SMART_BLOCKS_PATH . 'languages');
            }
        }

        public function init() {
            require SMART_BLOCKS_PATH . 'inc/helper-functions.php';
            require SMART_BLOCKS_PATH . 'inc/blocks/blocks-manager.php';
            require SMART_BLOCKS_PATH . 'inc/blocks/blocks-render.php';
            require SMART_BLOCKS_PATH . 'inc/generate-css.php';
            require SMART_BLOCKS_PATH . 'inc/blocks/attributes.php';
            // require SMART_BLOCKS_PATH . 'inc/template-library.php';
        }

        public function create_block_init() {
            global $wp_roles;

            // automatically load dependencies and version
            $asset_file = include(SMART_BLOCKS_PATH . 'build/index.asset.php');
            wp_register_style('owl-carousel', SMART_BLOCKS_URL . 'inc/assets/css/owl.carousel.css', array(), SMART_BLOCKS_VERSION);
            wp_register_style('materialdesignicons', SMART_BLOCKS_URL . 'inc/assets/css/materialdesignicons.css', array(), SMART_BLOCKS_VERSION);
            wp_register_style('smart-blocks-icons', SMART_BLOCKS_URL . 'inc/assets/css/smart-blocks-icons.css', array(), SMART_BLOCKS_VERSION);
            wp_register_style('sb-style', SMART_BLOCKS_URL . 'inc/assets/css/sb-style.css', array('materialdesignicons', 'owl-carousel'), SMART_BLOCKS_VERSION);
            wp_register_style('sb-block-editor', SMART_BLOCKS_URL . 'inc/assets/css/editor.css', array('smart-blocks-icons'), SMART_BLOCKS_VERSION);

            wp_register_script('owl-carousel', SMART_BLOCKS_URL . 'inc/assets/js/owl.carousel.js', array('jquery'), SMART_BLOCKS_VERSION, true);
            wp_register_script('sb-script', SMART_BLOCKS_URL . 'inc/assets/js/sb-script.js', array('jquery', 'owl-carousel'), SMART_BLOCKS_VERSION, true);

            wp_register_script('sb-blocks', SMART_BLOCKS_URL . 'build/index.js', $asset_file['dependencies'], $asset_file['version'], true);
            wp_localize_script('sb-blocks', 'smartblocks', array(
                'userRoles' => $wp_roles->roles,
                'activeBlocks' => get_option('sb_blocks')
            ));

            $block_render = new Smart_Blocks_Blocks_Render();
            $blocks = array(
                // 'template-import',
                'columns',
                'column',
                'container'
            );

            foreach ($blocks as $block) {
                register_block_type(
                    'smart-blocks/' . $block, array(
                        'editor_script' => 'sb-blocks',
                        'editor_style' => 'sb-block-editor',
                        'style' => 'sb-style',
                        'script' => 'sb-script',
                    )
                );
            }

            $news_blocks = array(
                'news-module-one',
                'news-module-two',
                'news-module-three',
                'news-module-four',
                'news-module-five',
                'news-module-six',
                'news-module-seven',
                'news-module-eight',
                'news-module-nine',
                'news-module-ten',
                'news-module-eleven',
                'news-module-twelve',
                'news-module-thirteen',
                'news-module-fourteen',
                'news-module-fifteen',
                'tile-module-one',
                'tile-module-two',
                'tile-module-three',
                'carousel-module-one',
                'single-news-one',
                'single-news-two',
                'ticker-module'
            );

            foreach ($news_blocks as $block) {
                register_block_type(
                    'smart-blocks/' . $block, array(
                        'api_version' => 2,
                        'editor_script' => 'sb-blocks',
                        'editor_style' => 'sb-block-editor',
                        'style' => 'sb-style',
                        'attributes' => function_exists('smart_blocks_attributes_' . str_replace('-', '_', $block)) ? call_user_func('smart_blocks_attributes_' . str_replace('-', '_', $block)) : [],
                        'script' => 'sb-script',
                        'render_callback' => [$block_render, 'smart_blocks_render_' . str_replace('-', '_', $block)]
                    )
                );
            }
        }


        /**
         * Gutenberg block category.
         *
         * @param array  $categories Block categories.
         * @param object $post Post object.
         */
        public function register_category($categories, $post) {
            return array_merge(
                $categories, array(
                    array(
                        'slug' => 'smart-blocks-block-modules',
                        'title' => __('SB Block Modules', 'smart-blocks')
                    ),
                    array(
                        'slug' => 'smart-blocks-magazine-modules',
                        'title' => __('SB Magazine Modules', 'smart-blocks')
                    ),
                )
            );
        }

        public function register_custom_fields() {
            // POST fields
            register_rest_field(
                'post', 'relative_dates', array(
                    'get_callback' => 'smart_blocks_get_relative_dates',
                    'update_callback' => NULL,
                    'schema' => NULL,
                )
            );

            register_rest_field(
                'page', 'relative_dates', array(
                    'get_callback' => 'smart_blocks_get_relative_dates',
                    'update_callback' => NULL,
                    'schema' => NULL,
                )
            );

            // CPT fields
            foreach (smart_blocks_get_CPTs() as $cpt) {
                register_rest_field(
                    $cpt, 'relative_dates', array(
                        'get_callback' => 'smart_blocks_get_relative_dates',
                        'update_callback' => NULL,
                        'schema' => NULL,
                    )
                );
            }
        }

        public function post_query_vars($query_params) {
            $query_params['orderby']['enum'][] = 'rand';
            $query_params['orderby']['enum'][] = 'comment_count';
            return $query_params;
        }

        public function admin_notice() {
            add_action('admin_notices', array($this, 'admin_notice_content'));
        }

        public function admin_notice_content() {
            if (!$this->is_dismissed('review') && !empty(get_option('smart_blocks_first_activation')) && time() > get_option('smart_blocks_first_activation') + 15 * DAY_IN_SECONDS) {
                $this->review_notice();
            }
        }

        public static function is_dismissed($notice) {
            $dismissed = get_option('smart_blocks_dismissed_notices', array());

            // Handle legacy user meta
            $dismissed_meta = get_user_meta(get_current_user_id(), 'smart_blocks_dismissed_notices', true);
            if (is_array($dismissed_meta)) {
                if (array_diff($dismissed_meta, $dismissed)) {
                    $dismissed = array_merge($dismissed, $dismissed_meta);
                    update_option('smart_blocks_dismissed_notices', $dismissed);
                }
                if (!is_multisite()) {
                    // Don't delete on multisite to avoid the notices to appear in other sites.
                    delete_user_meta(get_current_user_id(), 'smart_blocks_dismissed_notices');
                }
            }

            return in_array($notice, $dismissed);
        }

        public function review_notice() {
            ?>
            <div class="smart-blocks-notice notice notice-info">
                <?php $this->dismiss_button('review'); ?>
                <div class="smart-blocks-notice-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 256 256">
                        <path d="M116 5c-14.5 3.1-28.8 9.8-41.7 19.6C55 39.4 22.4 80 11 103.5c-6.4 13.2-8.3 20.6-8.2 33 0 13.2 2.8 23.9 9.7 38 11 22.5 26.6 38.5 56.2 57.9 12.1 7.9 30.3 16.8 39.3 19.2 8.4 2.2 22.5 2.2 31 0 18.6-4.9 59.2-30.9 79.6-51.1 27.3-27.1 40.2-60.3 35-90.1-6.5-37.5-37.6-78.2-73.3-95.9C158.8 3.9 136.6.6 116 5zm39.6 56.5c12 5.3 28.7 12.5 36.9 16.1 8.3 3.5 15.7 6.9 16.5 7.4 1.2.8.6 1.8-3.5 6-2.7 2.7-5.5 5-6.3 5-.8 0-15.4-6.1-32.4-13.5-28.7-12.4-31.2-13.3-33.1-12-1.2.8-10 9.3-19.5 18.8l-17.3 17.3 14.3 6.2c7.9 3.3 24.2 10.4 36.3 15.7 12.1 5.3 25.4 11 29.5 12.7 4.1 1.7 7.8 3.4 8.3 3.8.4.3-5.2 6.5-12.5 13.6s-21 20.5-30.5 29.8c-10.1 9.9-17.7 16.7-18.5 16.4-3.6-1.5-76.4-33.1-76.8-33.5-.2-.2 2.1-2.8 5.1-5.9l5.5-5.5 11.4 5c6.3 2.7 20.8 8.9 32.1 13.9l20.5 8.9 19.5-18.8c18.7-18.1 19.3-18.8 16.9-19.7-1.4-.6-15.5-6.7-31.5-13.6-15.9-7-35-15.2-42.2-18.4-7.3-3.1-13.2-6-13-6.3.6-1.4 60.3-58.9 61.3-58.9.6 0 10.9 4.3 23 9.5zM153.8 94c10 4.4 18.6 8 19.2 8 2 0 .9 1.8-4.5 6.9l-5.4 5.2-9.8-4.2c-5.4-2.3-11.2-4.8-12.8-5.5-2.9-1.2-3.3-1-7.9 3.2-2.7 2.4-5.5 4.4-6.1 4.4-1 0-12.1-4.6-13.3-5.5-.5-.4 20.4-20.5 21.4-20.5.6 0 9.2 3.6 19.2 8zm-45.4 55.1 11.1 4.9 4.9-4.9 4.9-4.9 7.1 3c3.9 1.7 7.2 3.1 7.4 3.3.5.4-19.9 20-21.2 20.3-.6.1-9.4-3.3-19.5-7.8-10-4.4-18.6-8-19.2-8-1.8 0-.8-1.5 4.3-6.7 6-6.1 4.4-6.1 20.2.8z" />
                    </svg>
                </div>

                <div class="smart-blocks-notice-content">
                    <p>
                        <?php
                        printf(
                            /* translators: %1$s is link start tag, %2$s is link end tag. */
                            esc_html__('Great to see that you have been using Smart Blocks - WordPress Gutenberg Blocks for some time. We hope you love it, and we would really appreciate it if you would %1$sgive us a 5 stars rating%2$s and spread your words to the world.', 'smart-blocks'), '<a target="_blank" href="https://wordpress.org/support/plugin/smart-blocks/reviews/?filter=5">', '</a>'
                        );
                        ?>
                    </p>
                    <a target="_blank" class="button button-primary button-large" href="https://wordpress.org/support/plugin/smart-blocks/reviews/?filter=5"><span class="dashicons dashicons-thumbs-up"></span><?php echo esc_html__('Yes, of course', 'smart-blocks') ?></a>
                    &nbsp;
                    <a class="button button-large" href="<?php echo esc_url(wp_nonce_url(add_query_arg('smart-blocks-hide-notice', 'review'), 'review', 'smart_blocks_notice_nonce')); ?>"><span class="dashicons dashicons-yes"></span><?php echo esc_html__('I have already rated', 'smart-blocks') ?></a>
                </div>
            </div>
            <?php
        }

        public function welcome_init() {
            if (!get_option('smart_blocks_first_activation')) {
                update_option('smart_blocks_first_activation', time());
            }

            if (isset($_GET['smart-blocks-hide-notice'], $_GET['smart_blocks_notice_nonce'])) {
                $notice = sanitize_key($_GET['smart-blocks-hide-notice']);
                check_admin_referer($notice, 'smart_blocks_notice_nonce');
                self::dismiss($notice);
                wp_safe_redirect(remove_query_arg(array('smart-blocks-hide-notice', 'smart_blocks_notice_nonce'), wp_get_referer()));
                exit;
            }
        }

        public function dismiss_button($name) {
            ?>
            <a class="notice-dismiss" href="<?php echo esc_url(wp_nonce_url(add_query_arg('smart-blocks-hide-notice', $name), $name, 'smart_blocks_notice_nonce')); ?>">
                <span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'smart-blocks'); ?></span>
            </a>
            <?php
        }

        public static function dismiss($notice) {
            $dismissed = get_option('smart_blocks_dismissed_notices', array());

            if (!in_array($notice, $dismissed)) {
                $dismissed[] = $notice;
                update_option('smart_blocks_dismissed_notices', array_unique($dismissed));
            }
        }

        public function add_admin_scripts() {
            wp_enqueue_style('smart-blocks-admin-style', SMART_BLOCKS_URL . 'inc/assets/css/admin-style.css', array(), SMART_BLOCKS_VERSION);
            wp_enqueue_script('smart-blocks-admin-script', SMART_BLOCKS_URL . 'inc/assets/js/admin-script.js', array('jquery', 'wp-color-picker'), SMART_BLOCKS_VERSION, true);
            wp_localize_script('smart-blocks-admin-script', 'sb_ajax_obj', [
                'ajaxURL' => admin_url('admin-ajax.php'),
                'adminNonce' => wp_create_nonce('sb_ajax_nonce')
            ]);
            wp_enqueue_script('smart-blocks-jquery-condition', SMART_BLOCKS_URL . 'inc/assets/js/jquery-condition.js', array('jquery'), SMART_BLOCKS_VERSION, true);
        }

        public function block_editor_assets() {
            // wp_enqueue_script('sb-block-editor-assets', SMART_BLOCKS_URL . 'inc/assets/js/editor-assets.js', array(), '1.0', true);
            wp_enqueue_script('sb-editor-sidebar', SMART_BLOCKS_URL . 'inc/assets/js/editor-sidebar.js', array('wp-components', 'wp-data', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-plugins'), SMART_BLOCKS_VERSION, true);
        }

        public function registerPostMeta() {
            register_post_meta('', 'sb_editor_width', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
        }

        public function add_editor_body_class($classes) {
            if ('post' === get_current_screen()->base) {
                global $post;
                $editorWidth = get_post_meta($post->ID, 'sb_editor_width', true);

                // Editor width
                if (isset($editorWidth) && !empty($editorWidth)) {
                    $classes .= ' sb-editor-width-' . esc_attr($editorWidth) . ' ';
                }

                return $classes;
            }
            return $classes;
        }

        public function add_page_templates_in_post_types() {
            $post_types = get_post_types();

            foreach ($post_types as $post_type) {
                add_filter("theme_{$post_type}_templates", [$this, 'add_page_templates'], 10, 4);
            }
            add_filter('page_template', array($this, 'redirect_page_template'), 999);
        }

        public function add_page_templates($templates) {
            $templates['template-sb-full-width.php'] = __('Smart Blocks Full Width', 'smart-blocks');
            return $templates;
        }

        public function redirect_page_template($template) {
            $post = get_post();
            $page_template = '';
            if ($post->ID) {
                $page_template = get_post_meta($post->ID, '_wp_page_template', true);
            }
            $sb_template = false;
            if ('template-sb-full-width.php' == basename($page_template)) {
                $sb_template = true;
                $template = SMART_BLOCKS_PATH . 'inc/page-templates/template-sb-full-width.php';
            }
            if ($sb_template) {
                remove_action('template_include', array('WC_Template_Loader', 'template_loader'));
            }
            return $template;
        }

        public static function is_fse_template() {
            global $_wp_current_template_content;
            if (current_theme_supports('block-templates') && $_wp_current_template_content) {
                return true;
            }
            return false;
        }

        public function register_admin_menu() {

            add_menu_page(esc_html__('Smart Blocks Settings', 'smart-blocks'), esc_html__('Smart Blocks', 'smart-blocks'), 'manage_options', 'sb-block-settings', array($this, 'sb_blocks_settings_page_display'), 'data:image/svg+xml;base64,' . base64_encode('<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 226.72 213.12"><g><g><path d="M221.08 54.39c5.71-5.85 6.55-7.25 4.88-8.36-1.12-.7-11.44-5.44-23-10.33s-34.73-15.06-51.47-22.45C134.61 6 120.24 0 119.4 0 118 0 34.74 80.2 33.91 82.15c-.28.42 8 4.46 18.13 8.78s36.68 15.9 58.86 25.67 42 18.13 43.93 19c3.35 1.26 2.51 2.24-23.57 27.48l-27.2 26.22-28.59-12.45c-15.76-7-36-15.62-44.77-19.39l-15.9-7-7.67 7.67c-4.18 4.32-7.39 8-7.11 8.23.55.64 102.09 44.64 107.11 46.75 1.12.42 11.71-9.07 25.8-22.87 13.25-13 32.36-31.66 42.54-41.57s18-18.54 17.43-19a108 108 0 0 0-11.57-5.3c-5.72-2.37-24.27-10.32-41.15-17.72S100.57 89.4 89.56 84.8l-20-8.65L93.74 52c13.25-13.25 25.52-25.1 27.2-26.22 2.65-1.81 6.13-.56 46.16 16.74 23.71 10.32 44.07 18.83 45.19 18.83s5.02-3.19 8.79-6.96"></path><path d="M57.48 134.31c-7.11 7.25-8.51 9.34-6 9.34.84 0 12.83 5 26.78 11.16s26.36 11 27.2 10.88c1.81-.42 30.26-27.75 29.56-28.31-.28-.28-4.88-2.23-10.32-4.6l-9.9-4.19-6.8 6.84-6.84 6.83-15.48-6.83c-22.06-9.63-19.83-9.63-28.2-1.12M149 58.58c-14-6.14-26-11.16-26.81-11.16-1.39 0-30.54 28-29.84 28.59 1.67 1.26 17.15 7.67 18.55 7.67.83 0 4.74-2.79 8.5-6.13 6.42-5.86 7-6.14 11-4.47 2.23 1 10.32 4.47 17.85 7.67l13.67 5.86 7.53-7.25c7.53-7.11 9.07-9.63 6.28-9.63-.82 0-12.81-5.02-26.73-11.15"></path></g></g></svg>'), 99);
        }

        public function sb_blocks_settings_page_display() {
            include SMART_BLOCKS_PATH . 'inc/admin-settings-page.php';
        }

        public function get_block_field($label, $val, $icon = '', $premium = false, $category = '') {
            $sb_blocks = get_option('sb_blocks') ? get_option('sb_blocks') : array();
            ?>

            <div class="sb-block-wrap <?php echo $premium ? 'sb-premium' : ''; ?>" data-main="<?php echo $premium ? 'pro' : 'free'; ?>" data-sub="<?php echo esc_attr($category); ?>">
                <span>
                    <?php
                    if ($icon) {
                        echo '<i class="' . esc_attr($icon) . '"></i>';
                    }
                    echo esc_html($label);
                    ?>
                </span>

                <div class="sb-checkbox">
                    <input type="checkbox" class="sb-block-checkbox" name="blocks" value="<?php echo esc_attr($val); ?>" <?php checked((isset($sb_blocks) && in_array($val, $sb_blocks)), true); ?>>
                    <label></label>
                </div>
            </div>

            <?php
        }

        public function add_settings_link($links) {
            $settings_link = '<a href="' . get_admin_url(null, 'admin.php?page=sb-block-settings') . '">' . esc_html__('Settings', 'smart-blocks') . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

    }

}


/**
 * Returns instanse of the plugin class.
 *
 * @since  1.0.0
 * @return object
 */
if (!function_exists('smart_blocks')) {

    function smart_blocks() {
        return Smart_Blocks::get_instance();
    }

}

smart_blocks();
