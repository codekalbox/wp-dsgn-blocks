<?php

namespace UiCoreBlocks;

/**
 * Frontend Pages Handler
 */
class Frontend
{

    /**
     * Constructor function to initialize hooks
     *
     * @return void
     */

    public function __construct()
    {

        // check how to add the global assets
        if (!\class_exists('\UiCore\Helper')) {
            add_action('wp_enqueue_scripts', [$this, 'add_global_styles_to_header'], 11);
        } else {
            //add the resources to global files in UiCore Framework
            add_filter('uicore_css_global_critical_files', [$this, 'add_css_to_framework'], 10);
        }

        // add them to the header/footer
        add_action('wp_enqueue_scripts', ['\UiCoreBlocks\Fonts', 'enqueue_global_fonts'], 11);

        //add non global assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_post_assets'], 51); // 51 to load after the global styles

        //This will only disable css assets if the block is not used in the post 
        add_filter('should_load_separate_core_block_assets', '__return_true', 11);

        // Remove block library styles
        add_action('wp_print_styles', [$this, 'remove_block_library_styles'], 100);

        add_action('enqueue_block_assets', function () {
            if (is_singular() && has_block('uicore/container')) {
                wp_enqueue_style('uicore-container-style');
            }

            if (class_exists('WooCommerce')) {
                wp_enqueue_style('uicore-bl-woocommerce');
            }
            // Repeat for other blocks.
        });
        add_action('dynamic_sidebar_before', [$this, 'enqueue_sidebar_assets'], 10, 2);

        add_filter('wp_preload_resources', [$this, 'filter_preload_resources'], 11, 1);
    }

    /**
     * Runs once before any widgets in a sidebar are output.
     *
     * @param int|string $sidebar_id       Sidebar ID or numeric index (e.g. 'blog-sidebar' or 1).
     * @param bool       $has_widgets True if the sidebar has at least one active widget.
     * @return void
     */

    public function enqueue_sidebar_assets($sidebar_id, $has_widgets)
    {

        if (!$has_widgets) {
            return;
        }

        $styles = get_option("_uicore_widget_block_styles_{$sidebar_id}", '');
        if ($styles) {
            echo "<style>\n{$styles}\n</style>\n";
        }

        $assets = get_option("_uicore_widget_block_assets_{$sidebar_id}", []);

        //Enqueue styles
        if (!empty($assets['styles'])) {
            foreach ($assets['styles'] as $style) {
                \wp_enqueue_style($style);
            }
        }
        //Enqueue scripts
        if (!empty($assets['scripts'])) {
            foreach ($assets['scripts'] as $script) {
                \wp_enqueue_script($script);
            }
        }

        Fonts::enqueue_sidebar_fonts($sidebar_id);
    }

    function remove_block_library_styles()
    {
        wp_dequeue_style('wp-block-library');
    }


    public static function enqueue_post_assets($post_id = null)
    {
        $post_id = $post_id ? $post_id : get_the_ID();
        $is_built_with_blocks = Frontend::is_built_with_blocks($post_id);

        if ($is_built_with_blocks) {
            //add uicore-block-ID as a class to the body
            \add_filter('body_class', function ($classes) use ($post_id) {
                $classes[] = 'uicore-bl-styles';
                $classes[] = 'uicore-bl-' . $post_id;
                return $classes;
            }, 1);

            // enqueue block assets
            $assets = get_post_meta($post_id, '_uicore_block_assets', true);

            //enque post fonts
            Fonts::enqueue_post_fonts($post_id);

            // Add block editor critical styles
            // if (isset($assets['critical_styles']) && $assets['critical_styles']) {
            $critical_styles = get_post_meta($post_id, '_uicore_block_critical_styles', true);
            if (!empty($critical_styles)) {
                $inline_style_handle = 'uicore-bl-critical-styles-' . $post_id;
                wp_register_style($inline_style_handle, false); // Register a dummy style handle
                wp_enqueue_style($inline_style_handle); // Enqueue the style
                wp_add_inline_style($inline_style_handle, $critical_styles); // Add inline styles dadasdad
            }
            // }


            //add block editor styles ()
            $should_add_inline_styles = apply_filters('uicore_bl_should_add_inline_styles', true, $post_id);
            if ($should_add_inline_styles) {
                $inline_styles = get_post_meta($post_id, '_uicore_block_styles', true);
                if (!empty($inline_styles) && isset($inline_styles['all'])) {
                    \wp_register_style('uicore-bl-inline-styles-' . $post_id, false);
                    \wp_enqueue_style('uicore-bl-inline-styles-' . $post_id);
                    \wp_add_inline_style('uicore-bl-inline-styles-' . $post_id, $inline_styles['all']);
                }
            }

            //used to trigger other post specific enqueue actions
            do_action('uicore_bl_enqueue_styles', $post_id);

            //Enqueue styles
            if (!empty($assets['styles'])) {
                foreach ($assets['styles'] as $style) {
                    \wp_enqueue_style($style);
                }
            }
            //Enqueue scripts
            if (!empty($assets['scripts'])) {
                foreach ($assets['scripts'] as $script) {
                    \wp_enqueue_script($script);
                }
            }
        }
    }

    /**
     * Injects saved images_preload meta into WP’s preload pipeline.
     *
     * @param array $preloads Existing preload definitions.
     * @return array Modified preload definitions.
     */
    public function filter_preload_resources(array $preloads): array
    {

        if (is_singular()) {
            $post_id = get_the_ID();
            $assets = get_post_meta($post_id, '_uicore_block_assets', true);

            if (!empty($assets['preload_images']) && is_array($assets['preload_images'])) {
                foreach ($assets['preload_images'] as $img) {

                    $preloads[] = $img;
                }
            }
        }

        return $preloads;
    }

    function add_global_styles_to_header()
    {
        \wp_enqueue_style('uicore-bl-frontend');
    }

    public function add_css_to_framework($files)
    {
        $files[] =  UICORE_BLOCKS_PATH . '/assets/css/frontend.css';
        return $files;
    }

    public static function get_post_styles($post_id = null)
    {
        $post_id = $post_id ? $post_id : get_the_ID();
        return get_post_meta($post_id, '_uicore_block_styles', true);
    }

    public static function get_post_style_version($post_id = null)
    {
        $post_id = $post_id ? $post_id : get_the_ID();
        return get_post_meta($post_id, '_uicore_block_styles_version', true);
    }


    public static function is_built_with_blocks($post_id = null)
    {
        $post_id = $post_id ? $post_id : get_the_ID();

        return (bool) get_post_meta($post_id, '_uicore_block_styles_version', true) || (bool) get_post_meta($post_id, '_uicore_block_assets', true);
    }
}
