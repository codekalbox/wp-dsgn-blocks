<?php

namespace UiCoreBlocks;

/**
 * Admin Pages Handler
 */
class Admin
{

    /**
     * Constructor function to initialize hooks
     *
     * @return void
     */
    public function __construct()
    {
        //add all editot scripts and styles
        add_action('enqueue_block_assets', [$this, 'enqueue_block_assets'], 50);
        //add editor dark mode classname
        add_filter('admin_body_class', [$this, 'add_dark_mode_class']);
    }

    /**
     * Enqueues the block editor assets.
     *
     * This function is responsible for enqueueing the styles and scripts required for the block editor.
     * It retrieves the styles and scripts using the `Assets` class and enqueues them using the WordPress
     * `wp_enqueue_style` and `wp_enqueue_script` functions.
     */
    function enqueue_block_assets()
    {

        // https://github.com/WordPress/gutenberg/pull/49655
        if (! is_admin()) {
            return;
        }

        if (is_customize_preview()) {
            return;
        }

        // // We don't want to load EDITOR scripts in the iframe, only enqueue front-end assets for the content.
        // $should_load_assets = apply_filters('should_load_block_editor_scripts_and_styles', true);
        // //editor scripts
        // if ($should_load_assets) {

        //add tools
        \wp_enqueue_script('uicore-bl-vendors');
        \wp_enqueue_script('uicore-bl-tools');
        \wp_enqueue_style('uicore-bl-tools');

        //add woocommerce assets if woocommerce is active
        if (class_exists('WooCommerce')) {
            \wp_enqueue_script('uicore-bl-woocommerce');
            \wp_enqueue_style('uicore-bl-woocommerce');
        }

        //add common
        \wp_enqueue_script('uicore-bl-common');
        \wp_enqueue_style('uicore-bl-common');

        //add icons
        \wp_enqueue_script('uicore-bl-icons');


        //framework portfolio and blog styles
        wp_enqueue_style('uicore-portfolio-st');
        wp_enqueue_style('uicore-blog-st');


        // Enqueue global styles
        $global_styles = GlobalStyles::get_css_styles(false);
        \wp_register_style('uicore-bl-global-styles', false);
        \wp_enqueue_style('uicore-bl-global-styles');
        \wp_add_inline_style('uicore-bl-global-styles', $global_styles);

        // Enqueue global fonts
        Fonts::enqueue_global_fonts();

        // Enqueue metafonts
        if (get_the_ID()) {
            Fonts::enqueue_post_fonts(get_the_ID());
        } else {
            // Fonts::enqueue_widgets_fonts();
        }

        // Get snippets styles and scripts
        $styles = Assets::get_snippets_styles();
        $scripts = Assets::get_snippets_scripts();

        // Enqueue styles
        foreach ($styles as $handle => $style) {
            wp_enqueue_style($handle);
        }

        // Enqueue scripts
        foreach ($scripts as $handle => $script) {
            wp_enqueue_script($handle);
        }

        $dynamic_fields_options =  BlocksDynamicContent::get_fields_options();

        $editor_settings = Settings::get_options();
        $scriptVars = 'var uicore_blocks_editor_settings = ' . \json_encode($editor_settings) . '; ';
        $scriptVars .= 'var uicore_blocks_dynamic_fields_options = ' . \json_encode($dynamic_fields_options) . '; ';


        //Design Cloud
        if (class_exists('\UiCore\Assets')) {
            $critical_inline_css = \get_option('uicore_global_critical_css', false);
            if ($critical_inline_css) {
                $inline_css = $critical_inline_css;
            } else {
                $css_url = \UiCore\Assets::get_global("uicore-global.css");
                $response = wp_remote_get($css_url);
                if (! is_wp_error($response)) {
                    $inline_css = wp_remote_retrieve_body($response);
                }
            }
        } else {
            // get elementor kit css file
            $inline_css = GlobalStyles::get_css_styles(true);
        }



        $local_data = get_option('uicore_connect', [
            'url' => '',
            'token' => '',
        ]);
        $product = \defined('UICORE_NAME') ? \UICORE_NAME : 'uicore-blocks-free';
        $product = \apply_filters('uicore_product_id', $product);
        $dc_settings = [
            'api' => 'https://dc-gtbg.uicore.co',
            'builder' => 'gt',
            'nonce' => wp_create_nonce('wp_rest'),
            'preview' => [
                'class' => 'preview-blocks',
                'assets' => [],
                'inline_css' => $inline_css,
            ],
            'local_url' => get_site_url(),
            'license' => [
                'product' => $product,
                'key' => $local_data['token'],
                'url' => $local_data['url'],
            ]
        ];
        $blocks_data = [
            "version" => \UICORE_BLOCKS_VERSION,
            'admin_url' => admin_url(),
        ];
        $blocks_data = \apply_filters('uicore_blocks_data', $blocks_data);
        $scriptVars .= "window.ui_dc_global = " . json_encode($dc_settings) . ";";
        $scriptVars .= "window.ui_blocks_data = " . json_encode($blocks_data) . ";";

        \wp_add_inline_script('uicore-bl-tools',  $scriptVars, 'before');
    }

    function add_dark_mode_class($classes)
    {
        $screen = get_current_screen();
        if (method_exists($screen, 'is_block_editor') && $screen->is_block_editor()) {
            $dark_mode = Settings::get_option('uiblocks_dark_mode');
            if ($dark_mode) {
                $classes .= ' uicore-bl-editor-dark-mode';
            }

            $small_left_sidebar = Settings::get_option('uiblocks_small_left_sidebar');
            if ($small_left_sidebar) {
                $classes .= ' uicore-bl-editor-small-left-sidebar';
            }
        }

        return $classes;
    }
}
