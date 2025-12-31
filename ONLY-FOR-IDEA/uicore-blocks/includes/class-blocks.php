<?php

namespace UiCoreBlocks;

/**
 * Blocks Class
 */
class Blocks
{

    // Define version assets for compatibility ( max 10 )
    // eg: '6.8.0' for WP 6.8.0 and below
    private static $version_assets = [
        '6.8.0',
        '6.8.3',
    ];

    function __construct()
    {
        add_filter('block_categories_all', [$this, 'register_uicore_block_category'], 1, 2);

        add_action('init', [$this, 'register_uicore_blocks'], 60);
        add_action('rest_api_init', [$this, 'register_uicore_blocks_meta'], 60);
        add_action('admin_enqueue_scripts', [$this, 'replace_wp_core_data_script'], 100);
    }

    /**
     * Replace the wp-core-data script with a custom one
     * This is necessary to ensure that the custom script is loaded instead of the core one
     *
     * @return void
     */
    function replace_wp_core_data_script()
    {

        if (get_current_screen()->base !== 'widgets') {
            global $wp_scripts;


            // Ensure the original script exists
            if (isset($wp_scripts->registered['wp-core-data'])) {

                // Get original dependencies
                $original_script = $wp_scripts->registered['wp-core-data'];
                $dependencies = $original_script->deps;

                // Dequeue and deregister the original script
                wp_dequeue_script('wp-core-data');
                wp_deregister_script('wp-core-data');

                $wp_version = get_bloginfo('version');
                $script_version = '';
                foreach (self::$version_assets as $version_asset) {
                    if (version_compare($wp_version, $version_asset, '<=')) {
                        $script_version = '-' . $version_asset;
                        break;
                    }
                }


                // Enqueue your custom script with the original dependencies
                wp_enqueue_script(
                    'wp-core-data',
                    UICORE_BLOCKS_URL . '/assets/js/core-data' . $script_version . '.js',
                    $dependencies,
                    UICORE_BLOCKS_VERSION,
                    true
                );
            }
        }
    }

    /**
     * Register
     *
     * @return void
     */
    public function register_uicore_blocks()
    {
        foreach (self::get_blocks() as $block => $data) {
            register_block_type(UICORE_BLOCKS_PATH . '/assets/blocks/' . $block);
        }
    }

    /** 
     * Register meta data
     */
    public function register_uicore_blocks_meta()
    {
        register_post_meta('', '_uicore_block_fonts', [
            'type'   => 'string',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
            'auth_callback' => function ($allowed, $meta_key, $post_id, $user_id, $cap, $caps) {
                return current_user_can('edit_post', $post_id);
            },
        ]);
    }

    /**
     * Register
     *
     * @return array
     */
    public function register_uicore_block_category($categories, $post)
    {
        $uicore_blocks_categories = [
            [
                'slug'  => 'uicore-basic',
                'title' => __('UiCore Blocks', 'uicore-blocks'),
            ],
            [
                'slug'  => 'uicore-composite',
                'title' => __('UiCore Composite Blocks', 'uicore-blocks'),
            ],
            [
                'slug'  => 'uicore-advanced',
                'title' => __('UiCore Advanced Blocks', 'uicore-blocks'),
            ],
            [
                'slug'  => 'uicore-dynamic',
                'title' => __('UiCore Dynamic Blocks', 'uicore-blocks'),
            ],
        ];

        $categories = array_merge($categories, $uicore_blocks_categories);
        return $categories;
    }

    static function get_blocks()
    {
        return [

            //abstract item used by composite blocks or/and advanced (common)
            'ai' => [],
            'common/item' => [],
            'common/wrapper' => [],
            'common/social-icons' => [],
            'common/card' => [],

            'common/card-icon' => [],
            'common/card-text' => [],
            'common/card-title' => [],
            'common/card-info' => [],

            'common/testimonial-avatar' => [],
            'common/testimonial-author' => [],
            'common/testimonial-info' => [],
            'common/testimonial-text' => [],

            'common/accordion-item' => [],
            'common/accordion-item/title' => [],
            'common/accordion-item/content' => [],

            'common/checkbox' => [],
            'common/form-field' => [],
            'common/input' => [],
            'common/label' => [],

            'common/badge' => [],

            /*
            atomic blocks ( global )
            */
            'atomic/container' => [
                'frontend_styles' => true,
            ],
            'atomic/heading' => [],

            'atomic/paragraph' => [],
            'atomic/button' => [
                'frontend_styles' => true,
            ],
            'composite/text-list' => [],

            'atomic/image' => [],
            'composite/gallery' => [],
            'composite/carousel-gallery' => [],


            'atomic/divider' => [],
            'atomic/video' => [],
            'composite/accordion' => [],

            'atomic/icon' => [],
            'atomic/toggle-icon' => [
                'frontend_styles' => true,
            ],
            'atomic/google-maps' => [],
            'composite/icon-list' => [],
            'composite/icon-card' => [],
            'composite/grid-icon-card' => [],
            'composite/carousel-icon-card' => [],

            'composite/testimonial-card' => [],
            'composite/grid-testimonial-card' => [],
            'composite/carousel-testimonial' => [],

            'composite/team-card' => [],
            'composite/grid-team-card' => [],
            'composite/carousel-team-card' => [],

            'composite/social-icon-list' => [],
            'composite/newsletter' => [],
            'atomic/rating-stars' => [
                'frontend_styles' => true,
            ],

            /*
            dynamic blocks
            */
            'dynamic/query-loop' => [],
            'dynamic/query-loop/grid' => [],
            'dynamic/query-loop/grid/item' => [],
            'dynamic/query-loop/pagination' => [],
            'dynamic/query-loop/pagination/button' => [],
            'dynamic/query-loop/pagination/numbers' => [],
            'dynamic/query-loop/no-results' => [],
            'dynamic/post-image' => [],
            'dynamic/post-info' => [],
        ];
    }
}
