<?php

if (!function_exists('smart_blocks_custom_excerpt')) {

    function smart_blocks_custom_excerpt($limit) {
        if ($limit) {
            $content = get_the_content();
            $content = wp_strip_all_tags($content);
            $content = strip_shortcodes($content);
            $excerpt = mb_substr($content, 0, $limit);

            if (strlen($content) >= $limit) {
                $excerpt = $excerpt . '...';
            }

            return $excerpt;
        }
    }

}

if (!function_exists('smart_blocks_author_name')) {

    function smart_blocks_author_name($class = '') {
        return '<span class="sb-post-author ' . esc_attr($class) . '"><i class="mdi-account"></i>' . get_the_author() . '</span>';
    }

}

if (!function_exists('smart_blocks_comment_count')) {

    function smart_blocks_comment_count($class = '') {
        return '<span class="sb-post-comment ' . esc_attr($class) . '"><i class="mdi-comment-outline"></i>' . get_comments_number() . '</span>';
    }

}

if (!function_exists('smart_blocks_post_date')) {

    function smart_blocks_post_date($format = '', $class = '') {

        if ($format) {
            return '<span class="sb-post-date ' . esc_attr($class) . '"><i class="mdi-clock-time-four-outline"></i>' . get_the_date($format) . '</span>';
        } else {
            return '<span class="sb-post-date ' . esc_attr($class) . '"><i class="mdi-clock-time-four-outline"></i>' . get_the_date() . '</span>';
        }
    }

}

if (!function_exists('smart_blocks_time_ago')) {

    function smart_blocks_time_ago($class = '') {
        return '<span class="sb-post-date ' . esc_attr($class) . '"><i class="mdi-clock-time-four-outline"></i>' . human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('ago', 'smart-blocks') . '</span>';
    }

}

if (!function_exists('smart_blocks_get_the_primary_category')) {

    function smart_blocks_get_the_primary_category($class = "post-categories", $link_class = '') {
        $post_categories = smart_blocks_get_post_primary_category(get_the_ID());
        $content = '';

        if (!empty($post_categories)) {
            $category_obj = $post_categories['primary_category'];
            $category_link = get_category_link($category_obj->term_id);
            $content .= '<ul class="' . esc_attr($class) . '">';
            $content .= '<li><a class="sb-primary-cat sb-category-' . esc_attr($category_obj->term_id) . ' ' . esc_attr($link_class) . '" href="' . esc_url($category_link) . '">' . esc_html($category_obj->name) . '</a></li>';
            $content .= '</ul>';
        }
        return $content;
    }

}

if (!function_exists('smart_blocks_get_post_primary_category')) {

    function smart_blocks_get_post_primary_category($post_id, $term = 'category', $return_all_categories = false) {
        $return = array();

        if (class_exists('WPSEO_Primary_Term')) {
            // Show Primary category by Yoast if it is enabled & set
            $wpseo_primary_term = new WPSEO_Primary_Term($term, $post_id);
            $primary_term = get_term($wpseo_primary_term->get_primary_term());

            if (!is_wp_error($primary_term)) {
                $return['primary_category'] = $primary_term;
            }
        }

        if (empty($return['primary_category']) || $return_all_categories) {
            $categories_list = get_the_terms($post_id, $term);

            if (empty($return['primary_category']) && !empty($categories_list)) {
                $return['primary_category'] = $categories_list[0];  //get the first category
            }

            if ($return_all_categories) {
                $return['all_categories'] = array();

                if (!empty($categories_list)) {
                    foreach ($categories_list as &$category) {
                        $return['all_categories'][] = $category->term_id;
                    }
                }
            }
        }

        return $return;
    }

}

if (!function_exists('smart_blocks_css_strip_whitespace')) {

    function smart_blocks_css_strip_whitespace($css) {
        $replace = array(
            "#/\*.*?\*/#s" => "", // Strip C style comments.
            "#\s\s+#" => " ", // Strip excess whitespace.
        );
        $search = array_keys($replace);
        $css = preg_replace($search, $replace, $css);

        $replace = array(
            ": " => ":",
            "; " => ";",
            " {" => "{",
            " }" => "}",
            ", " => ",",
            "{ " => "{",
            ";}" => "}", // Strip optional semicolons.
            ",\n" => ",", // Don't wrap multiple selectors.
            "\n}" => "}", // Don't wrap closing braces.
            "} " => "}\n", // Put each rule on it's own line.
        );
        $search = array_keys($replace);
        $css = str_replace($search, $replace, $css);

        return trim($css);
    }

}

if (!function_exists('smart_blocks_is_taxonomy_assigned_to_post_type')) {

    function smart_blocks_is_taxonomy_assigned_to_post_type($post_type, $taxonomy = NULL) {
        if (is_object($post_type))
            $post_type = $post_type->post_type;

        if (empty($post_type))
            return false;

        $taxonomies = get_object_taxonomies($post_type);

        if (empty($taxonomy))
            $taxonomy = get_query_var('taxonomy');

        return in_array($taxonomy, $taxonomies);
    }

}

if (!function_exists('smart_blocks_get_CPTs')) {

    function smart_blocks_get_CPTs() {
        return get_post_types(array('_builtin' => false, 'public' => true));
    }

}

if (!function_exists('smart_blocks_get_relative_dates')) {

    function smart_blocks_get_relative_dates($post) {
        return array(
            'created' => human_time_diff(get_the_date('U', $post['id'])) . ' ' . __('ago', 'smart-blocks'),
            'modified' => human_time_diff(get_the_modified_date('U', $post['id'])) . ' ' . __('ago', 'smart-blocks')
        );
    }

}

if (!function_exists('smart_blocks_get_font_class')) {

    function smart_blocks_get_font_class($family = '', $weight = '', $textTransform = '', $textDecoration = '') {
        $retrun_classes = array();

        if (strtolower($family) != 'inherit') {
            $retrun_classes[] = 'sb-ff';
        }
        if (strtolower($weight) != 'inherit') {
            $retrun_classes[] = 'sb-fw';
        }
        if (strtolower($textTransform) != 'inherit') {
            $retrun_classes[] = 'sb-tt';
        }
        if (strtolower($textDecoration) != 'inherit') {
            $retrun_classes[] = 'sb-td';
        }
        return implode(' ', $retrun_classes);
    }

}


if (!function_exists('smart_blocks_get_all_blocks_list')) {
    function smart_blocks_get_all_blocks_list() {
        $all_wid = apply_filters('sb_all_blocks_list', array(
            'section' => array(
                'name' => 'Columns',
                'category' => 'creative',
                'icon' => ''
            ),
            'container' => array(
                'name' => 'Container',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-one' => array(
                'name' => 'News Module One',
                'category' => 'essential',
                'icon' => ''
            ),
            'news-module-two' => array(
                'name' => 'News Module Two',
                'category' => 'advanced',
                'icon' => ''
            ),
            'news-moduel-three' => array(
                'name' => 'News Module Three',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-four' => array(
                'name' => 'News Module Four',
                'category' => 'listing',
                'icon' => ''
            ),
            'news-module-five' => array(
                'name' => 'News Module Five',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-six' => array(
                'name' => 'News Module Six',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-seven' => array(
                'name' => 'News Module Seven',
                'category' => 'essential',
                'icon' => ''
            ),
            'news-module-eight' => array(
                'name' => 'News Module Eight',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-nine' => array(
                'name' => 'News Module Nine',
                'category' => 'essential',
                'icon' => ''
            ),
            'news-module-ten' => array(
                'name' => 'News Module Ten',
                'category' => 'creative',
                'icon' => ''
            ),
            'news-module-eleven' => array(
                'name' => 'News Module Eleven',
                'category' => 'essential',
                'icon' => ''
            ),
            'news-module-twelve' => array(
                'name' => 'News Module Twelve',
                'category' => 'advanced',
                'icon' => ''
            ),
            'news-module-thirteen' => array(
                'name' => 'News Module Thirteen',
                'category' => 'listing',
                'icon' => ''
            ),
            'news-module-fourteen' => array(
                'name' => 'News Module Fourteen',
                'category' => 'essential',
                'icon' => ''
            ),
            'news-module-fifteen' => array(
                'name' => 'News Module Fifteen',
                'category' => 'essential',
                'icon' => ''
            ),
            'tile-module-one' => array(
                'name' => 'Tile Module One',
                'category' => 'advanced',
                'icon' => ''
            ),
            'tile-module-two' => array(
                'name' => 'Tile Module Two',
                'category' => 'essential',
                'icon' => ''
            ),
            'tile-module-three' => array(
                'name' => 'Tile Module Three',
                'category' => 'essential',
                'icon' => ''
            ),
            'carousel-module-one' => array(
                'name' => 'Carousel Module',
                'category' => 'essential',
                'icon' => ''
            ),
            'ticker-module' => array(
                'name' => 'Ticker Module',
                'category' => 'advanced',
                'icon' => ''
            ),
            'single-news-one' => array(
                'name' => 'Single News One',
                'category' => 'listing',
                'icon' => ''
            ),
            'single-news-two' => array(
                'name' => 'Single News Two',
                'category' => 'listing',
                'icon' => ''
            ),
            // 'template-import' => array(
            //     'name' => 'Template Import',
            //     'icon' => 'sb-icons-image-accordion',
            //     'category' => 'listing'
            // ),
        ));
        return $all_wid;
    }
}



function sb_get_all_blocks_desc() {
    return array(
        'section' => esc_html__('Place content side by side inside a Columns block', 'smart-blocks'),
        'container' => esc_html__('Group blocks together for layout or styling', 'smart-blocks'),
        'news-module-one' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-two' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-moduel-three' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-four' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-five' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-six' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-seven' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-eight' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-nine' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-ten' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-eleven' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-twelve' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-thirteen' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-fourteen' => esc_html__('Displays News Posts', 'smart-blocks'),
        'news-module-fifteen' => esc_html__('Displays News Posts', 'smart-blocks'),
        'ticker-module' => esc_html__('Displays News Posts', 'smart-blocks'),
        'tile-module-one' => esc_html__('Displays News Posts', 'smart-blocks'),
        'tile-module-two' => esc_html__('Displays News Posts', 'smart-blocks'),
        'tile-module-three' => esc_html__('Displays News Posts', 'smart-blocks'),
        'single-news-one' => esc_html__('Displays News Posts', 'smart-blocks'),
        'single-news-two' => esc_html__('Displays News Posts', 'smart-blocks'),
        'carousel-module-one' => esc_html__('Displays News Posts', 'smart-blocks'),
    );
}