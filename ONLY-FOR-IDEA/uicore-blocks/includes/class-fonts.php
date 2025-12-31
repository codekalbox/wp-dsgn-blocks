<?php

namespace UiCoreBlocks;

/**
 *  Fonts Manager
 */
class Fonts
{
    /** System and Google fonts */
    public const FONTS_JSON_PATH = UICORE_BLOCKS_PATH . '/assets/fonts/fonts.json';

    /**
     * Check if local fonts option is enabled.
     * @return bool
     */
    public static function is_local_fonts_enabled()
    {
        return Settings::get_frontend_option('ui_bl_local_fonts') === 'true';
    }

    /**
     * Enqueue global fonts for the editor.
     *
     * This function checks if local fonts are enabled and, if not, enqueues Google Fonts
     * from a CDN based on the global styles configuration.
     *
     * @return void
     */
    public static function enqueue_global_fonts()
    {
        $local_fonts = self::is_local_fonts_enabled();

        if ($local_fonts) {
            return;
        }

        // Enqueue Google Fonts from CDN
        $fonts_list = GlobalStyles::get_fonts_list();

        if (empty($fonts_list)) {
            return;
        }

        $google_fonts_url = self::get_google_fonts_url($fonts_list);

        if ($google_fonts_url) {
            wp_enqueue_style('uicore-bl-google-fonts-global', $google_fonts_url, [], null);
        }
    }

    /**
     * Enqueues post-specific fonts for the editor.
     *
     * This function checks if local fonts are enabled and, if not, retrieves the font list
     * for the specified post. If fonts are found, it enqueues them from the Google Fonts CDN.
     *
     * @param int|null $post_id The ID of the post for which fonts should be enqueued.
     *                          Defaults to null.
     * @return void
     */
    public static function enqueue_post_fonts($post_id = null)
    {
        $local_fonts = self::is_local_fonts_enabled();
        if ($local_fonts) {
            return;
        }

        // Enqueue Google Fonts from CDN
        $key = 'post-' . $post_id;
        $fonts_list = [];
        if ($post_id) {
            $raw = get_post_meta($post_id, '_uicore_block_fonts', true);
            $decoded = $raw ? json_decode($raw, true) : [];
            $fonts_list = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($fonts_list) || empty($fonts_list)) {
            return;
        }

        // Enqueue Google Fonts from CDN
        $google_fonts_url = self::get_google_fonts_url($fonts_list);

        if ($google_fonts_url) {
            wp_enqueue_style('uicore-bl-google-fonts-' . $key, $google_fonts_url, [], null);
        }

        $typekit_font_url = self::get_typekit_font_url($fonts_list);

        if ($typekit_font_url) {
            wp_enqueue_style('uicore-bl-typekit-fonts-global', $typekit_font_url, [], null);
        }
    }

    /**
     * Retrieves and generates local font-face CSS for Google Fonts.
     *
     * This function downloads missing Google Fonts, caches them locally, and generates
     * the corresponding CSS for use in the application. It ensures that fonts are stored
     * in the specified directory and are fetched only if they are not already cached.
     *
     * @param array $fonts_list An associative array of font families and their configurations.
     * @return string The generated CSS containing @font-face rules for the local fonts.

     */
    static function get_local_fonts($fonts_list)
    {
        if (!self::is_local_fonts_enabled()) {
            return '';
        }

        /* ---------------- basic setup / cache ---------------- */
        $cache_key  = 'uicore_bl_local_fonts_cache';
        $font_cache = get_transient($cache_key) ?: [];

        $font_dir   = WP_CONTENT_DIR . '/uploads/uicore-blocks/fonts/';
        $font_url   = content_url('/uploads/uicore-blocks/fonts/');
        wp_mkdir_p($font_dir);

        /* ---------------- build list of Google families ------- */
        $to_fetch = [];
        foreach ($fonts_list as $family => $cfg) {
            if (($cfg['category'] ?? '') === 'google') {
                $to_fetch[$family] = [
                    'category' => 'google',
                    'weights'  => $cfg['weights'] ?? [400],
                ];
            }
        }
        if (empty($to_fetch)) {
            return '';
        }

        /* ---------------- download Google CSS ----------------- */
        if (! function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        $css_url    = self::get_google_fonts_url($to_fetch);
        $remote_css = wp_remote_retrieve_body(
            wp_remote_get($css_url, ['headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36']])
        );

        /* ---------------- parse every @font-face --------------- */
        $local_css_blocks = [];

        preg_match_all(
            '/(\/\*[^*]*\*\/\s*)?@font-face\s*{[^}]+?}/i',
            $remote_css,
            $blocks,
            PREG_SET_ORDER
        );

        foreach ($blocks as $block) {

            $css_block = $block[0];

            // pull remote URL + font metadata
            if (! preg_match('/url\(([^)]+\.woff2)\)/i', $css_block, $url_m)) {
                continue;
            }
            $remote_url = trim($url_m[1], '\'"');
            $remote_file = basename(parse_url($remote_url, PHP_URL_PATH));   // ← original filename

            preg_match('/font-family:\s*\'([^\']+)\'/i', $css_block, $fam_m);
            preg_match('/font-style:\s*([^;]+);/i',      $css_block, $sty_m);
            preg_match('/font-weight:\s*(\d+)/i',        $css_block, $wgt_m);

            $family = $fam_m[1] ?? 'unknown';
            $style  = $sty_m[1] ?? 'normal';
            $weight = $wgt_m[1] ?? '400';

            /* ----- download if we don’t already have this file ----- */
            if (empty($font_cache[$family][$weight][$style][$remote_file])) {

                $tmp = download_url($remote_url);
                if (! is_wp_error($tmp)) {
                    copy($tmp, $font_dir . $remote_file);
                    unlink($tmp);
                }

                // cache entry: original filename only
                $font_cache[$family][$weight][$style][$remote_file] = true;
            }

            /* ----- swap URL to local path -------------------------- */
            $local_block = preg_replace(
                '/url\([\'"]?([^\'")]+)[\'"]?\)/',
                "url('{$font_url}{$remote_file}')",
                $css_block
            );

            $local_css_blocks[] = $local_block;
        }

        /* ---------------- persist cache & return CSS ------------- */
        set_transient($cache_key, $font_cache, MONTH_IN_SECONDS);

        return trim(implode("\n", $local_css_blocks));
    }

    /**
     * Deletes the local fonts cache and removes all font files from the specified directory.
     *
     * This function performs the following actions:
     * - Deletes the transient cache associated with local fonts.
     * - Removes all files from the directory where local fonts are stored.
     *
     * @return void
     */
    static function delete_local_fonts_cache()
    {
        $cache_key = 'uicore_bl_local_fonts_cache';
        delete_transient($cache_key);
        $font_dir = WP_CONTENT_DIR . '/uploads/uicore-blocks/fonts/';
        if (is_dir($font_dir)) {
            $files = glob($font_dir . '*'); // get all file names
            foreach ($files as $file) {
                unlink($file); // delete each file
            }
        }
    }

    /**
     * Generates a Google Fonts URL based on the provided font configurations.
     *
     * @param array $fonts_list An associative array of font configurations where the key is the font family name
     * @return string The generated Google Fonts URL including font families, weights, and subsets based on locale.
     */

    public static function get_google_fonts_url($fonts_list)
    {
        $google_families = [];

        foreach ($fonts_list as $family => $conf) {
            if (($conf['category'] ?? '') !== 'google') {
                continue;
            }

            $slug = str_replace(' ', '+', $family);
            $suffix = isset($conf['weights']) ? ':wght@' . implode(';', $conf['weights']) : '';
            $google_families[] = $slug . $suffix;
        }

        if (empty($google_families)) {
            return '';
        }

        $fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $google_families) . '&display=swap';
        $subsets = [
            'ru_RU' => 'cyrillic',
            'bg_BG' => 'cyrillic',
            'he_IL' => 'hebrew',
            'el' => 'greek',
            'vi' => 'vietnamese',
            'uk' => 'cyrillic',
            'cs_CZ' => 'latin-ext',
            'ro_RO' => 'latin-ext',
            'pl_PL' => 'latin-ext',
            'hr_HR' => 'latin-ext',
            'hu_HU' => 'latin-ext',
            'sk_SK' => 'latin-ext',
            'tr_TR' => 'latin-ext',
            'lt_LT' => 'latin-ext',
        ];
        $subsets = apply_filters('uicore_blocks_google_font_subsets', $subsets);

        $locale = get_locale();
        if (isset($subsets[$locale])) {
            $fonts_url .= '&subset=' . $subsets[$locale];
        }

        return $fonts_url;
    }

    public static function get_typekit_font_url($fonts_list)
    {
        $has_typekit = false;

        foreach ($fonts_list as $family => $conf) {
            if ($conf['category'] === 'typekit') {
                $has_typekit = true;
            }
        }

        if (!$has_typekit) {
            return '';
        }

        $projectId = get_option('uicore_theme_options', [])['typekit']['id'] ?? '';

        if (empty($projectId)) {
            return '';
        }

        // Note: Replace with actual Typekit URL structure if available
        $fonts_url = 'https://use.typekit.net/' . $projectId . '.css';

        return $fonts_url;
    }

    /**
     * @param array string $sidebar_id
     * @return void
     */
    public static function enqueue_sidebar_fonts(string $sidebar_id = ''): void
    {
        if ('wp_inactive_widgets' === $sidebar_id) {
            return;
        }

        $raw = get_option("_uicore_widget_block_fonts_{$sidebar_id}", '');
        $decoded = $raw ? json_decode($raw, true) : [];
        $fonts_list = is_array($decoded) ? $decoded : [];

        if (!is_array($fonts_list) || empty($fonts_list)) {
            return;
        }

        $use_local_fonts = self::is_local_fonts_enabled();

        if ($use_local_fonts) {
            $local_fonts = self::get_local_fonts($fonts_list);

            if ($local_fonts !== '') {
                echo '<style id="uicore-local-fonts-' . $sidebar_id . '">' . $local_fonts . '</style>';
            }
        } else {
            $google_fonts_url = self::get_fonts_url($fonts_list);

            if ($google_fonts_url) {
                wp_enqueue_style("uicore-bl-google-fonts-{$sidebar_id}", $google_fonts_url, [], null);
            }
        }
    }

    /** 
     * Extracted reusable builder 
     */
    private static function build_section(array $items, string $type, callable $mapVariants): ?array
    {
        $outItems = [];
        $families = [];

        foreach ($items as $font) {
            if (empty($font['family'])) {
                continue;
            }

            $outItems[] = [
                'family'   => (string)$font['family'],
                'variants' => $mapVariants($font['variants'] ?? []),
            ];
            $families[] = (string)$font['family'];
        }

        if (!$outItems) {
            return null;
        }

        return [
            'type'  => $type,
            'items' => $outItems,
            'fam'   => array_values(array_unique($families)),
        ];
    }

    /**
     * Convert typekit font variants into standard weights and styles.
     *
     * @param array $variants
     * @return array
     */
    private static function typekit_variants(array $variants): array
    {
        $result = [];
        foreach ($variants as $variant) {
            if (ctype_digit((string)$variant)) {
                $weight = (string)$variant;
                if ($weight === '400') {
                    $result[] = 'regular';
                    $result[] = 'italic';
                } else {
                    $result[] = $weight;
                    $result[] = $weight . 'italic';
                }
            } else {
                $result[] = $variant;
            }
        }

        return $result;
    }

    /**
     * Return an array of all the base fonts from the JSON file.
     * 
     * @return array
     */
    private static function get_base_fonts(): array
    {
        if (!is_readable(self::FONTS_JSON_PATH)) {
            return [];
        }

        $decoded = json_decode(file_get_contents(self::FONTS_JSON_PATH), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Returns an array of fonts from the theme options.
     * 
     * Fonts are grouped into sections. Each section contains the following:
     * - title: The title of the section.
     * - items: An array of fonts.
     * 
     * @return array
     */
    public static function get_theme_fonts(): array
    {
        $option = get_option('uicore_theme_options');
        if (!is_array($option)) {
            return [];
        }

        $sections = [];

        $typekitId     = $option['typekit']['id'] ?? null;
        $typekitFonts  = $option['typekit']['fonts']['items'] ?? null;

        // Typekit section
        if (!empty($typekitId) && is_array($typekitFonts) && !empty($typekitFonts)) {
            $fontsSection = $option['typekit']['fonts'];
            $sections[] = self::build_section(
                $fontsSection['items'],
                $fontsSection['type'] ?? 'Typekit Fonts',
                fn(array $v) => self::typekit_variants($v)
            );
        }

        // Custom fonts section
        if (!empty($option['customFonts']) && is_array($option['customFonts'])) {
            $sections[] = self::build_section(
                $option['customFonts'],
                'Custom Fonts',
                fn(array $v) => $v
            );
        }

        // Filter out null sections
        return array_values(array_filter($sections));
    }

    /**
     * Returns an array of all fonts available for the editor. 
     * This includes both base fonts and theme fonts.
     * 
     * @return array
     */
    public static function get_editor_fonts(): array
    {
        return array_merge(
            self::get_base_fonts(),
            self::get_theme_fonts()
        );
    }
}
