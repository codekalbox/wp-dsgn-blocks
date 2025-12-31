<?php

namespace UiCoreBlocks;

/**
 * Dynamic Content Handler
 */
class BlocksDynamicContent
{

    /**
     * Constructor function to initialize hooks
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('render_block', [$this, 'render_dynamic_blocks'], 10, 1);
    }

    /**
     * Returns an array of dynamic fields.
     * 
     * @return array An array of dynamic fields.
     */
    public static function get_fields_options(): array
    {
        $dynamic_fields_options = [
            'fields' => [
                ['label' => __('Select an option', 'uicore-blocks'), 'value' => ''],
                [
                    'label'   => __('Posts', 'uicore-blocks'),
                    'options' => [
                        ['label' => __('Post ID', 'uicore-blocks'),                'value' => 'postID'],
                        ['label' => __('Post Title', 'uicore-blocks'),             'value' => 'postTitle'],
                        ['label' => __('Post Content', 'uicore-blocks'),           'value' => 'postContent'],
                        ['label' => __('Post Excerpt', 'uicore-blocks'),           'value' => 'postExcerpt'],
                        ['label' => __('Post Date', 'uicore-blocks'),              'value' => 'postDate'],
                        ['label' => __('Post Time', 'uicore-blocks'),              'value' => 'postTime'],
                        ['label' => __('Post Terms', 'uicore-blocks'),             'value' => 'postTerms'],
                        ['label' => __('Post Custom Field', 'uicore-blocks'),      'value' => 'postMeta'],
                        ['label' => __('Post ACF', 'uicore-blocks'),               'value' => 'acf'],
                        ['label' => __('Post Type', 'uicore-blocks'),              'value' => 'postType'],
                        ['label' => __('Post Status', 'uicore-blocks'),            'value' => 'postStatus'],
                        ['label' => __('Post No. Comments', 'uicore-blocks'),      'value' => 'postNoComments'],
                        ['label' => __('Post Description', 'uicore-blocks'),       'value' => 'pageDescription'],
                    ],
                ],
                [
                    'label'   => __('Site', 'uicore-blocks'),
                    'options' => [
                        ['label' => __('Site Title', 'uicore-blocks'),   'value' => 'siteTitle'],
                        ['label' => __('Site Tagline', 'uicore-blocks'), 'value' => 'siteTagline'],
                    ],
                ],
                [
                    'label'   => __('Author', 'uicore-blocks'),
                    'options' => [
                        ['label' => __('Author Name', 'uicore-blocks'),        'value' => 'authorName'],
                        ['label' => __('Author Description', 'uicore-blocks'), 'value' => 'authorDescription'],
                        ['label' => __('Author Meta', 'uicore-blocks'),        'value' => 'authorMeta'],
                        ['label' => __('Author ACF', 'uicore-blocks'),         'value' => 'authorAcf'],
                    ],
                ],
                [
                    'label'   => __('Logged-in User', 'uicore-blocks'),
                    'options' => [
                        ['label' => __('Logged-in User Name', 'uicore-blocks'),        'value' => 'loggedInUserName'],
                        ['label' => __('Logged-in User Description', 'uicore-blocks'), 'value' => 'loggedInUserDescription'],
                        ['label' => __('Logged-in User Email', 'uicore-blocks'),       'value' => 'loggedInUserEmail'],
                        ['label' => __('Logged-in User Meta', 'uicore-blocks'),        'value' => 'loggedInUserMeta'],
                        ['label' => __('Logged-in User Acf', 'uicore-blocks'),         'value' => 'loggedInUserAcf'],
                    ],
                ],
                [
                    'label'   => __('Miscellaneous', 'uicore-blocks'),
                    'options' => [
                        ['label' => __('Archive Title', 'uicore-blocks'),       'value' => 'archiveTitle'],
                        ['label' => __('Archive Description', 'uicore-blocks'), 'value' => 'archiveDescription'],
                        ['label' => __('Current Date', 'uicore-blocks'),        'value' => 'date'],
                        ['label' => __('Current Time', 'uicore-blocks'),        'value' => 'time'],
                        ['label' => __('URL Parameter', 'uicore-blocks'),       'value' => 'queryString'],
                    ],
                ],
            ],
            'urls' => [
                ['label' => __('Select an option', 'uicore-blocks'),        'value' => ''],
                ['label' => __('Post Url', 'uicore-blocks'),                'value' => 'postUrl'],
                ['label' => __('Site Url', 'uicore-blocks'),                'value' => 'siteUrl'],
                ['label' => __('Featured Image Url', 'uicore-blocks'),      'value' => 'featuredImageUrl'],
                ['label' => __('Author Url', 'uicore-blocks'),              'value' => 'authorUrl'],
                ['label' => __('Author Website', 'uicore-blocks'),          'value' => 'authorWebsite'],
                ['label' => __('Post Custom Field', 'uicore-blocks'),       'value' => 'postMeta'],
                ['label' => __('Post ACF', 'uicore-blocks'),                'value' => 'acf'],
            ]
        ];

        if (!function_exists('get_field')) {
            $dynamic_fields_options['fields'] = array_map(
                fn($group) => !empty($group['options']) ? array_merge($group, [
                    'options' => array_values(array_filter(
                        $group['options'],
                        fn($opt) => !in_array($opt['value'], ['acf', 'authorAcf', 'loggedInUserAcf'], true)
                    ))
                ]) : $group,
                $dynamic_fields_options['fields']
            );

            $dynamic_fields_options['urls'] = array_values(array_filter(
                $dynamic_fields_options['urls'],
                fn($u) => $u['value'] !== 'acf'
            ));
        }

        return $dynamic_fields_options;
    }

    /**
     * Retrieve all available URL field keys from the dynamic fields options. 
     * 
     * @return array<string>  A flat list of available URL keys
     */
    public static function get_url_value_keys(): array
    {
        $all = self::get_fields_options();

        if (empty($all['urls']) || !is_array($all['urls'])) {
            return [];
        }

        $values = array_column($all['urls'], 'value');

        $values = array_values(array_filter($values, static function ($v) {
            return $v !== null && $v !== '';
        }));

        return array_values(array_unique($values, SORT_STRING));
    }

    /**
     * Catch every block’s HTML and process our dynamic spans.
     *
     * @param  string $block_content The block’s raw HTML.
     * @param  array  $block         Block metadata.
     * @return string                Modified HTML.
     */
    public function render_dynamic_blocks(string $block_content): string
    {

        if (strpos($block_content, 'uicore-bl-dynamic-content') !== false) {
            $block_content = preg_replace_callback(
                //1. \b to ensure we match a whole “span” tag
                //2. [^>]*class="…uicore-bl-dynamic-content…"
                //3. [^>]* captures all other attrs
                //4. (.*?)<\/span> with s-flag to get inner HTML across lines
                '#<span\b[^>]*class="[^"]*\buicore-bl-dynamic-content\b[^"]*"[^>]*>(.*?)</span>#is',
                function (array $matches): string {
                    preg_match(
                        '#<span\b([^>]*)>#i',
                        $matches[0],
                        $attr_match
                    );

                    return self::replace_dynamic_content([
                        $matches[0],
                        $attr_match[1] ?? '',
                        $matches[1],
                    ]);
                },
                $block_content
            );
        }

        // Resolve exact dynamic hrefs
        $keys = self::get_url_value_keys();
        $regex = '#href\s*=\s*["\'](?:' .
            implode('|', array_map(fn($k) => in_array($k, ['postMeta', 'acf'], true)
                ? preg_quote($k, '#') . '\|[^"\']+'
                : preg_quote($k, '#'), $keys)) .
            ')["\']#i';

        if (preg_match($regex, $block_content)) {
            $block_content = self::replace_exact_dynamic_links($block_content, (int) get_the_ID());
        }

        return $block_content;
    }

    /**
     * Build the replacement string for one <span class="uicore-bl-dynamic-content">.
     *
     * @param  array  $matches [0]=full span tag, [1]=attr string, [2]=inner HTML]
     * @return string          Final HTML/text (with <a> if urltype)
     */
    protected static function replace_dynamic_content(array $matches): string
    {

        $attr_string = $matches[1];

        // 1. Extract data-* into $attr[<key>] where key matches mapping:
        //    type, field, fieldcustom, format, formatcustom,
        //    prefix, sufix, urltype, urlfield, urlfieldcustom, urltarget
        $attr = [];
        preg_match_all('/data-([\w\-]+)="([^"]*)"/', $attr_string, $m, PREG_SET_ORDER);
        $attr = array_reduce($m, function ($carry, $m) {
            $carry[str_replace('-', '', $m[1])] = $m[2];
            return $carry;
        }, []);

        //normalise field, fieldcustom, format, formatcustom if exists
        $attr = (function ($d, $keys) {
            foreach ($keys as $k) {
                if (!empty($d[$k]) && $d[$k] === 'custom' && !empty($d["{$k}custom"])) {
                    $d[$k] = $d["{$k}custom"];
                }
            }
            return $d;
        })($attr, ['field', 'format']);

        // 2. Compute the raw value
        $defaultValue = self::get_default_value($matches[2] ?? '');


        return self::get_dynamic_content_value($attr, $defaultValue);
    }

    /**
     * Build a dynamic value server-side.
     *
     * @param array       $attr           Block attributes (same shape as in JS).
     * @param string      $defaultValue   Fallback value when resolved value is empty.
     * @param string|null $wrapperFormat  Optional sprintf wrapper, e.g. '<span %s>%s</span>' or '<span class="x">%s</span>'.
     * @param int|null    $withPostId     Optional post id to resolve from (defaults to current global post).
     * @return bool       $isInsideLoop   Determine if the field is inside a loop in our query loop
     */
    public static function get_dynamic_content_value($attr = [], $defaultValue = '', $withPostId = null, $isInsideLoop = false): string
    {
        $value     = '';
        $field     = $attr['field']     ?? '';
        $format    = $attr['format']    ?? '';
        $separator = $attr['separator'] ?? ', ';


        $post_id   = $withPostId ? $withPostId : get_the_ID();

        if (!$isInsideLoop) {
            $queried_post_id = self::dynamic_get_assigned_page_id();

            if ($post_id !== $queried_post_id) {
                $post_id = $queried_post_id;
            }
        }

        switch ($attr['type'] ?? '') {
            case 'postID':
                $value = (string) $post_id;
                break;

            case 'postTitle':
                $value = !$isInsideLoop ? self::dynamic_page_title() : get_the_title($post_id); //self::uicore_page_title();
                break;

            case 'postContent':
                // same as get_the_content() but bound to $post_id
                $value = get_the_content(null, false, $post_id);
                break;

            case 'postExcerpt':
                $value = get_the_excerpt($post_id);
                $wordLimit = (int)($attr['wordLimit'] ?? $attr['wordlimit'] ?? 10);

                if ($wordLimit > 0) {
                    $clean = wp_strip_all_tags($value);
                    $words = preg_split('/\s+/', trim($clean), -1, PREG_SPLIT_NO_EMPTY);

                    if (count($words) > $wordLimit) {
                        $value = implode(' ', array_slice($words, 0, $wordLimit)) . '...';
                    }
                }
                break;

            case 'postDate': {
                    $use_modified = ($field === 'modified');
                    $fmt          = $format ?: get_option('date_format');
                    $value        = $use_modified ? get_the_modified_date($fmt, $post_id) : get_the_date($fmt, $post_id);
                    break;
                }

            case 'postTime': {
                    $use_modified = ($field === 'modified');
                    $fmt          = $format ?: get_option('time_format');
                    $value        = $use_modified ? get_the_modified_time($fmt, $post_id) : get_the_time($fmt, $post_id);
                    break;
                }

            case 'postTerms':
                if (! empty($field)) {
                    $terms = get_the_terms($post_id, $field);

                    if (! is_wp_error($terms) && ! empty($terms)) {

                        if (!empty($attr['urltype'])) {
                            $links = [];

                            foreach ($terms as $term) {
                                $url = get_term_link($term);
                                $target = !empty($attr['urltarget']) ? ' target="' . esc_attr($attr['urltarget']) . '"' : '';
                                $rel = !empty($attr['urltarget']) ? ' rel="noopener"' : '';

                                if (! is_wp_error($url)) {
                                    $links[] = sprintf(
                                        '<a href="%s"%s%s>%s</a>',
                                        esc_url($url),
                                        $target,
                                        $rel,
                                        esc_html($term->name)
                                    );
                                }
                            }

                            $value = implode($separator, $links);
                        } else {

                            $value = implode($separator, wp_list_pluck($terms, 'name'));
                        }
                    }
                }
                break;

            case 'postMeta':
                $value = $field ? get_post_meta($post_id, $field, true) : '';
                break;

            case 'pageDescription':
                $value = get_post_meta($post_id, 'page_description', true);
                break;

            case 'acf':
                if (function_exists('get_field')) {
                    $value = $field ? (get_field($field, $post_id) ?: '') : '';
                }
                break;

            case 'postType':
                $value = get_post_type($post_id) ?: '';
                break;

            case 'postStatus':
                $status = get_post_status($post_id) ?: '';
                if ($status) {
                    $obj   = get_post_status_object($status);
                    $value = $obj ? $obj->label : $status;
                }
                break;

            case 'postNoComments':
                $value = (string) (get_comments_number($post_id) ?: '0');
                break;

            case 'siteTitle':
                $value = get_bloginfo('name');
                break;

            case 'siteTagline':
                $value = get_bloginfo('description');
                break;

            case 'authorName': {
                    $authorId = (int) get_post_field('post_author', $post_id);
                    if ($authorId) {
                        switch ($field) {
                            case 'username':
                                $value = get_the_author_meta('user_login', $authorId);
                                break;
                            case 'firstname':
                                $value = get_the_author_meta('first_name', $authorId);
                                break;
                            case 'lastname':
                                $value = get_the_author_meta('last_name', $authorId);
                                break;
                            case 'firstnamelastname':
                                $value = trim(get_the_author_meta('first_name', $authorId) . ' ' . get_the_author_meta('last_name', $authorId));
                                break;
                            case 'lastnamefirstname':
                                $value = trim(get_the_author_meta('last_name', $authorId) . ' ' . get_the_author_meta('first_name', $authorId));
                                break;
                            case 'displayname':
                            default:
                                $value = get_the_author_meta('display_name', $authorId);
                        }
                    }
                    break;
                }

            case 'authorDescription': {
                    $authorId = (int) get_post_field('post_author', $post_id);
                    $value    = $authorId ? get_the_author_meta('description', $authorId) : '';
                    break;
                }

            case 'authorMeta': {
                    $authorId = (int) get_post_field('post_author', $post_id);
                    $value    = ($authorId && $field) ? get_user_meta($authorId, $field, true) : '';
                    break;
                }

            case 'authorAcf':
                if (function_exists('get_field')) {
                    $authorId = (int) get_post_field('post_author', $post_id);
                    $value    = ($authorId && $field) ? (get_field($field, 'user_' . $authorId) ?: '') : '';
                }
                break;

            case 'loggedInUserName': {
                    $user = wp_get_current_user();
                    if ($user && $user->ID) {
                        switch ($field) {
                            case 'username':
                                $value = $user->user_login;
                                break;
                            case 'firstname':
                                $value = $user->user_firstname;
                                break;
                            case 'lastname':
                                $value = $user->user_lastname;
                                break;
                            case 'firstnamelastname':
                                $value = trim($user->user_firstname . ' ' . $user->user_lastname);
                                break;
                            case 'lastnamefirstname':
                                $value = trim($user->user_lastname . ' ' . $user->user_firstname);
                                break;
                            case 'displayname':
                            default:
                                $value = $user->display_name;
                        }
                    }
                    break;
                }

            case 'loggedInUserDescription': {
                    $user  = wp_get_current_user();
                    $value = ($user && $user->ID) ? get_user_meta($user->ID, 'description', true) : '';
                    break;
                }

            case 'loggedInUserEmail': {
                    $user  = wp_get_current_user();
                    $value = ($user && $user->ID) ? $user->user_email : '';
                    break;
                }

            case 'loggedInUserMeta': {
                    $user  = wp_get_current_user();
                    $value = ($user && $user->ID && $field) ? get_user_meta($user->ID, $field, true) : '';
                    break;
                }

            case 'loggedInUserAcf':
                if (function_exists('get_field')) {
                    $user  = wp_get_current_user();
                    $value = ($user && $user->ID && $field) ? (get_field($field, 'user_' . $user->ID) ?: '') : '';
                }
                break;

            case 'archiveTitle':
                $value = is_archive() ? get_the_archive_title() : '';
                break;

            case 'archiveDescription':
                $value = is_archive() ? get_the_archive_description() : '';
                break;

            case 'date': {
                    $fmt   = $format ?: get_option('date_format');
                    $value = date_i18n($fmt);
                    break;
                }

            case 'time': {
                    $fmt   = $format ?: get_option('time_format');
                    $value = date_i18n($fmt);
                    break;
                }

            case 'queryString':
                $value = ($field && isset($_GET[$field])) ? sanitize_text_field(wp_unslash($_GET[$field])) : '';
                break;

            default:
                $value = $defaultValue;
                break;
        }

        // early out
        if ($value === '' && $defaultValue === '') {
            return '';
        }

        // default fallback
        $value = ($value === '') ? $defaultValue : $value;

        // Optional <a> wrapper from urltype
        if (!empty($attr['urltype']) && $attr['urltype'] !== 'postTerms') {
            $url = self::get_dynamic_content_url($attr, $post_id);

            if ($url) {
                $target = !empty($attr['urltarget']) ? ' target="' . esc_attr($attr['urltarget']) . '"' : '';
                $rel = !empty($attr['urltarget']) ? ' rel="noopener"' : '';

                $value = sprintf(
                    '<a href="%s"%s%s>%s</a>',
                    esc_url($url),
                    $target,
                    $rel,
                    wp_kses_post($value)
                );
            }
        }

        // prefix / suffix
        if (!empty($attr['prefix'])) {
            $value = $attr['prefix'] . $value;
        }

        if (!empty($attr['sufix'])) {
            $value .= $attr['sufix'];
        }

        return $value;
    }

    /**
     * Extracts the text content from the last <span> tag in a given HTML string.
     * Works even if the <span> tag is not closed. If the value is a label of 
     * the fields options, returns an empty string.
     *
     * @param string $html The HTML string to search in.
     * @return string The inner text of the last <span> tag, or an empty string if none found or is a label text.
     */
    private static function get_default_value(string $html): string
    {
        preg_match_all('/<span[^>]*>([^<]*)/', $html, $matches);
        $spans = $matches[1] ?? [];
        $text = trim($spans ? end($spans) : '');

        // If no span match, try matching <a>text</a>
        if (!$text && preg_match('/<a[^>]*>(.*?)<\/a>/s', $html, $aMatch)) {
            $text = trim(strip_tags($aMatch[1]));
        }

        // If no <a> either, fallback to plain text
        if (!$text) {
            $text = trim(strip_tags($html));
        }

        if (!$text) {
            return '';
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        foreach (self::get_fields_options()['fields'] as $group) {
            foreach ($group['options'] ?? [] as $item) {
                $label = trim(html_entity_decode($item['label'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($label === $text) {
                    return '';
                }
            }
        }

        return $text;
    }

    /**
     * Resolve a dynamic URL based on block attributes and the current post.
     * @param array $attr {
     *   @type string      $urltype          One of the supported types listed above.
     *   @type string|null $urlfield         Meta/field key, or 'custom' to use urlfieldcustom.
     *   @type string|null $urlfieldcustom   Custom key used when urlfield === 'custom'.
     * }
     * @param int $post_id The post ID context.  
     * @return string The resolved URL or an empty string.
     */
    public static function get_dynamic_content_url($attr, $post_id) // no types for max compatibility
    {
        $type = isset($attr['urltype']) ? (string) $attr['urltype'] : '';
        $url  = '';

        switch ($type) {
            case 'postUrl':
                $url = get_permalink((int) $post_id) ?: '';
                break;

            case 'siteUrl':
                $url = home_url();
                break;

            case 'featuredImageUrl':
                $url = get_the_post_thumbnail_url((int) $post_id) ?: '';
                break;

            case 'authorUrl': {
                    $authorId = (int) get_post_field('post_author', (int) $post_id);
                    $url      = $authorId ? (get_author_posts_url($authorId) ?: '') : '';
                    break;
                }

            case 'authorWebsite': {
                    $authorId = (int) get_post_field('post_author', (int) $post_id);
                    $url      = $authorId ? ((string) get_the_author_meta('user_url', $authorId) ?: '') : '';
                    break;
                }

            case 'postMeta': {
                    $key = isset($attr['urlfield']) ? (string) $attr['urlfield'] : '';
                    if ($key === 'custom' && !empty($attr['urlfieldcustom'])) {
                        $key = (string) $attr['urlfieldcustom'];
                    }
                    $url = $key !== '' ? (string) get_post_meta((int) $post_id, $key, true) : '';
                    break;
                }

            case 'acf':
                if (function_exists('get_field')) {
                    $key = isset($attr['urlfield']) ? (string) $attr['urlfield'] : '';
                    if ($key === 'custom' && !empty($attr['urlfieldcustom'])) {
                        $key = (string) $attr['urlfieldcustom'];
                    }
                    $url = $key !== '' ? ((string) (get_field($key, (int) $post_id) ?: '')) : '';
                }
                break;

            default:
                $url = $type !== '' ? (string) $type : '';
                break;
        }

        return $url;
    }

    /**
     * Resolve <a href="..."> when the entire href is a dynamic token.
     *
     * Supported exact forms ONLY:
     *  - href="postUrl"                         -> urltype=postUrl
     *  - href="postMeta|custom|_my_field"       -> urltype=postMeta, urlfield=custom, urlfieldcustom=_my_field
     *  - href="acf|my_field"                    -> urltype=acf,     urlfield=my_field
     *
     * Anything else is left untouched.
     *
     * @param string $html     Raw HTML to scan.
     * @param int    $post_id  Post context for resolution.
     * @return string          HTML with rewritten hrefs.
     */
    public static function replace_exact_dynamic_links(string $html, int $post_id): string
    {
        // Allowed urltype keys (already filtered by ACF availability inside get_fields_options()).
        $allowed = self::get_url_value_keys(true); // include empty; we validate later

        // <a ... href="..."> or '...'
        $pattern = '#<a\b([^>]*?)\bhref\s*=\s*(["\'])(.*?)\2([^>]*)>#is';

        return preg_replace_callback($pattern, static function (array $m) use ($allowed, $post_id) {
            $before  = $m[1];
            $quote   = $m[2];
            $hrefRaw = trim($m[3]);
            $after   = $m[4];

            if ($hrefRaw === '') {
                return $m[0];
            }

            // Parse exact token:
            // - 1 part:    urltype
            // - 2 parts:   urltype|urlfield
            // - 3+ parts:  urltype|urlfield|urlfieldcustom (extra parts joined back into last)
            $parts = explode('|', $hrefRaw);
            $urltype = $parts[0] ?? '';
            if ($urltype === '' || !in_array($urltype, $allowed, true)) {
                return $m[0]; // not a recognized dynamic key; leave as-is
            }

            $urlfield       = null;
            $urlfieldcustom = null;

            if (isset($parts[1])) {
                $urlfield = $parts[1];
            }
            if (isset($parts[2])) {
                // join any extra pipes into the custom value (rare but safe)
                $urlfieldcustom = implode('|', array_slice($parts, 2));
            }

            $attr = [
                'urltype'        => $urltype,
                'urlfield'       => $urlfield,
                'urlfieldcustom' => $urlfieldcustom,
            ];

            $resolved = (string) self::get_dynamic_content_url($attr, $post_id);
            $resolved = $resolved !== '' ? $resolved : '#';

            // Rebuild open tag, preserving all other attributes verbatim
            $old = 'href=' . $quote . $m[3] . $quote;
            $new = 'href=' . $quote . esc_url($resolved) . $quote;

            return str_replace($old, $new, $m[0]);
        }, $html);
    }

    /**
     * Resolve the page title for the current view (singular, archive, taxonomy, search, shop, etc.).
     *
     * @return string
     */
    public static function dynamic_page_title()
    {
        $page_id = static::dynamic_get_assigned_page_id();

        if ($page_id) {
            return get_the_title($page_id);
        }

        // Your original fallbacks
        if (is_search()) {
            return sprintf(esc_html__('Search Results for: %s', 'uicore-pro'), esc_html(get_search_query()));
        } elseif (is_home() && !is_front_page()) {
            return single_post_title('', false);
        } elseif (is_archive()) {
            return get_the_archive_title();
        } elseif (is_singular()) {
            return get_the_title();
        }

        if (class_exists('WooCommerce')) {
            if (is_shop()) {
                return esc_html__('Shop', 'uicore-pro');
            } elseif (is_product_taxonomy()) {
                return single_term_title('', false);
            } elseif (is_product()) {
                return get_the_title();
            }
        }

        return '';
    }

    /**
     * Resolve a Page ID associated with the current view (singular, archive, taxonomy, search, shop, etc.).
     * Returns 0 if none found.
     */
    public static function dynamic_get_assigned_page_id()
    {
        // Singular page
        if (is_singular('page')) {
            return (int) get_the_ID();
        }

        // Blog home
        if (is_home() && !is_front_page()) {
            $p = (int) get_option('page_for_posts');
            if ($p) {
                return $p;
            }
        }

        if (class_exists('WooCommerce')) {
            if (is_shop()) {
                $p = (int) wc_get_page_id('shop');
                if ($p > 0) {
                    return $p;
                }
            }
            if (is_product()) {
                return (int) get_the_ID();
            }
            if (is_product_taxonomy()) {
                $p = (int) wc_get_page_id('shop');
                if ($p > 0) {
                    return $p;
                }
            }
        }

        // Portfolio archive or taxonomy
        if (is_post_type_archive('portfolio') || is_tax(['portfolio_cat', 'portfolio_tag'])) {
            $opts = get_option('uicore_theme_options', []);
            if (!empty($opts['portfolio_page']['id'])) {
                return (int) $opts['portfolio_page']['id'];
            }
        }

        // Taxonomy term assigned page via ACF
        if ((is_tax() || is_category() || is_tag()) && function_exists('get_field')) {
            $term = get_queried_object();
            if ($term && !is_wp_error($term)) {
                $page = get_field('assigned_page', "{$term->taxonomy}_{$term->term_id}");
                if ($page instanceof WP_Post) {
                    return (int) $page->ID;
                }
                if (is_numeric($page)) {
                    return (int) $page;
                }
            }
        }

        // Search results (ACF option field)
        if (is_search() && function_exists('get_field')) {
            $page = get_field('page_for_search', 'option');
            if ($page instanceof WP_Post) {
                return (int) $page->ID;
            }
            if (is_numeric($page)) {
                return (int) $page;
            }
        }

        // Try matching a page by URL path or slug
        $req = $_SERVER['REQUEST_URI'] ?? '';
        $path = trim(parse_url($req, PHP_URL_PATH) ?: '', '/');
        if ($path !== '') {
            $maybe = get_page_by_path($path, OBJECT, 'page');
            if ($maybe) {
                return (int) $maybe->ID;
            }
            $parts = explode('/', $path);
            $first = sanitize_title($parts[0]);
            $maybe = get_page_by_path($first, OBJECT, 'page');
            if ($maybe) {
                return (int) $maybe->ID;
            }
        }

        return 0;
    }
}
