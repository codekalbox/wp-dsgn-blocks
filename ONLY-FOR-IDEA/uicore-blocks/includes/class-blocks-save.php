<?php

namespace UiCoreBlocks;

use WP_REST_Request;

/**
 * BlocksSave Class
 */
class BlocksSave
{

    function __construct()
    {
        add_action('save_post', [$this, 'extract_assets_from_post_content']);
        add_action('save_post', [$this, 'add_data_form_page_in_forms']);
        add_action('rest_after_save_widget', [$this, 'extract_assets_from_widgets_content'], 10, 2);
        add_action('rest_delete_widget', [$this, 'extract_assets_from_widgets_content'], 10, 2);
    }

    /**
     * Injects data-form-page="{post_id}" into every <form> tag in given content.
     *
     * @param string $content The post content to scan.
     * @param int    $post_id The ID of the current post.
     * @return void
     */
    public function add_data_form_page_in_forms(int $post_id): void
    {
        $post = get_post($post_id);

        if (!$post) {
            return;
        }

        if (!empty($post->post_content) && stripos($post->post_content, 'data-form-page') !== false) {
            remove_action('save_post', [$this, 'add_data_form_page_in_forms']);

            $content = $post->post_content;
            $formPage = $post_id;

            $p = addslashes((string)$formPage);

            $content = preg_replace(
                '/(?:\s*data-form-page\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))/i',
                '',
                $content
            );

            $content = preg_replace(
                '/<form\b([^>]*)>/i',
                '<form$1 data-form-page="' . $p . '">',
                $content
            );

            $post->post_content = $content;
            wp_update_post($post);

            // Re-hook the save_post action
            add_action('save_post', [$this, 'add_data_form_page_in_forms'], 10, 1);
        }
    }

    /**
     * Extracts CSS and assets from post content and saves it to post meta.
     *
     * @param int $post_id The ID of the post being saved.
     * @return void
     */
    public static function extract_assets_from_post_content(int $post_id): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        //prevent duble runs
        static $ran = false;
        if ($ran) {
            return;
        }
        $ran = true;

        $post = get_post($post_id);

        if (!$post) {
            return;
        }


        $required_assets = self::get_blocks_content_required_assets($post->post_content);

        $initial_required_assets = get_post_meta($post_id, '_uicore_block_assets', true);

        if (!is_array($initial_required_assets)) {
            $initial_required_assets = [];
        }

        $required_assets = array_merge($initial_required_assets, $required_assets);

        update_post_meta($post_id, '_uicore_block_assets', $required_assets);
    }

    /**
     * Callback fired after a widget is saved/deleted via the REST API.
     *
     * @param string $widget_id  The widget instance ID (e.g. "block-3").
     * @param string $sidebar_id The sidebar identifier (e.g. "footer-1").
     * @return void
     */

    public function extract_assets_from_widgets_content(string $widget_id, string $sidebar_id): void
    {
        if ($sidebar_id === 'wp_inactive_widgets') {
            return;
        }

        $sidebars = wp_get_sidebars_widgets();

        if (empty($sidebars)) {
            return;
        }
        $widgets_ids = $sidebars[$sidebar_id] ?? [];

        if (empty($widgets_ids)) {
            delete_option("_uicore_widget_block_assets_{$sidebar_id}");
            return;
        }

        $styles_list = [];
        $scripts_list = [];

        foreach ($widgets_ids as $wid) {
            list($base, $num) = explode('-', $wid, 2);
            $instances = get_option("widget_{$base}", []);
            $instance  = $instances[$num] ?? [];

            // get the raw HTML/text field
            $content = $instance['content']
                ?? $instance['text']
                ?? '';

            // parse this widget
            $assets = self::get_blocks_content_required_assets($content);

            $styles_list = array_values(array_unique(array_merge($styles_list, $assets['styles'])));
            $scripts_list = array_values(array_unique(array_merge($scripts_list, $assets['scripts'])));
        }

        if (empty($styles_list) && empty($scripts_list)) {
            delete_option("_uicore_widget_block_assets_{$sidebar_id}");
            return;
        }

        $required_assets = [
            'styles'  => $styles_list,
            'scripts' => $scripts_list,
        ];

        update_option("_uicore_widget_block_assets_{$sidebar_id}", $required_assets);
    }

    /**
     * Examine a chunk of content and return the required asset handles.
     *
     * @param  string $content  Arbitrary HTML/text to scan.
     * @return array{
     *   styles:   string[],
     *   scripts:  string[],
     * }
     */
    public static function get_blocks_content_required_assets(string $content): array
    {
        $required_styles = [];
        $styles_snippets = Assets::get_snippets_styles();
        foreach ($styles_snippets as $handle => $css) {
            if (strpos($content, $handle) !== false) {
                $required_styles[] = $handle;
            }
        }

        $required_js = [];
        $js_snippets  = Assets::get_snippets_scripts();
        foreach ($js_snippets as $handle => $js) {
            if (strpos($content, $handle) !== false) {
                $required_js[] = $handle;
            }
        }

        // also catch any "uicore-bl-block wp-block-uicore-{name}"
        if (preg_match_all(
            '/uicore-bl-block\s+wp-block-uicore-([^\s"]+)/',
            $content,
            $matches
        )) {
            foreach (array_unique($matches[1]) as $block) {
                $required_styles[] = 'uicore-' . $block . '-style';
            }
        }

        return [
            'styles'  => array_values(array_unique($required_styles)),
            'scripts' => array_values(array_unique($required_js)),
        ];
    }

    /**
     * Save styles & fonts for either a post (numeric ID) or a sidebar (string key).
     *
     * @param int|string $targetId  Post ID or sidebar key.
     * @param ?string      $styles    The styles array from the REST request.
     * @param ?array      $fonts     The fonts array from the REST request.
     * @return void
     */
    public static function save_styles_for_post_or_widget($targetId, ?array $styles, ?array $fonts, ?array $preloadImages): void
    {

        $fonts_save = empty($fonts) ? '' : @json_encode($fonts);
        $preloadImagesData = [];

        if (!empty($preloadImages)) {
            foreach ($preloadImages as $id => $image) {
                if (empty($image['src'])) {
                    continue;
                }

                $entry = [
                    'href'           => $image['src'],
                    'as'            => 'image',
                    'fetchpriority' => 'high',
                ];

                if (empty($image['id']) || !$image['isBg']) {
                    if ($srcset = wp_get_attachment_image_srcset($image['id'], 'full')) {
                        $entry['imagesrcset'] = $srcset;
                    }
                    if ($sizes = wp_get_attachment_image_sizes($image['id'], 'full')) {
                        $entry['imagesizes'] = $sizes;
                    }
                }

                $preloadImagesData[] = $entry;
            }
        }

        if (is_numeric($targetId)) {
            $styles_save = str_replace('.editor-styles-wrapper', '.uicore-bl-' . $targetId, $styles);

            if (!empty($styles_save)) {
                $critical_inline_css = $styles_save['criticalCss'] ?? '';
                unset($styles_save['criticalCss']);
                if (!empty($fonts)) {
                    $critical_inline_css .= Fonts::get_local_fonts($fonts);
                }
                // Store the styles in the post meta
                // and create and store new version for assets file (used for cache busting)
                update_post_meta($targetId, '_uicore_block_styles', $styles_save);
                update_post_meta($targetId, '_uicore_block_critical_styles', $critical_inline_css);
                $version = time();
                update_post_meta($targetId, '_uicore_block_styles_version', $version);
                \do_action('uicore_block_styles_updated', $targetId, $styles_save);
            } else {
                // Delete the styles meta if empty
                delete_post_meta($targetId, '_uicore_block_styles');
                delete_post_meta($targetId, '_uicore_block_critical_styles');
                delete_post_meta($targetId, '_uicore_block_styles_version');
                \do_action('uicore_block_styles_deleted', $targetId);
            }

            $required_assets = get_post_meta($targetId, '_uicore_block_assets', true);
            if (empty($required_assets)) {
                $required_assets = [];
            }

            //store to meta
            $new_required_assets = [
                'devices' => [],
                'critical_styles' => false
            ];

            if ($styles_save) {
                if (isset($styles_save['mobile']) && !empty($styles_save['mobile'])) {
                    $new_required_assets['devices'][] = 'mobile';
                }
                if (isset($styles_save['tablet']) && !empty($styles_save['tablet'])) {
                    $new_required_assets['devices'][] = 'tablet';
                }
                if (isset($styles_save['desktop']) && !empty($styles_save['desktop'])) {
                    $new_required_assets['devices'][] = 'desktop';
                }
                if (isset($styles_save['criticalCss']) && !empty($styles_save['criticalCss'])) {
                    $new_required_assets['critical_styles'] = true;
                }
            }

            if (!empty($preloadImagesData)) {
                $new_required_assets['preload_images'] = $preloadImagesData;
            }

            $new_required_assets = array_merge($required_assets, $new_required_assets);
            \error_log('save styles ' . print_r($new_required_assets, true));
            update_post_meta($targetId, '_uicore_block_assets', $new_required_assets);

            if (!empty($fonts_save)) {
                update_post_meta($targetId, '_uicore_block_fonts', $fonts_save);
            } else {
                delete_post_meta($targetId, '_uicore_block_fonts');
            }
        } else {
            //TODO: add preload images also for widgets
            //TODO: there is uicore-footer-widget and uicore-widget, I put for the moment uicore-bl-styles, from body
            $styles_save = str_replace('.editor-styles-wrapper', '.uicore-bl-styles', $styles['all'] ?? '');
            update_option('_uicore_widget_block_styles_' . $targetId, $styles_save);
            update_option('_uicore_widget_block_fonts_' . $targetId,  $fonts_save);
        }
    }
}
