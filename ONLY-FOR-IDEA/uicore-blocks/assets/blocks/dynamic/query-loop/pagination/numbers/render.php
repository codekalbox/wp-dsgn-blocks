<?php
// UiCore Query Pagination Numbers — echo markup.
// Uses $attributes, $content, $block.

global $wp_query;

$no_type             = isset($attributes['noType']) ? (string)$attributes['noType'] : '';
$mid_size            = isset($attributes['midSize']) ? (int)$attributes['midSize'] : null;
$page_key            = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$enhanced_pagination = isset($block->context['enhancedPagination']) && $block->context['enhancedPagination'];
$max_page_ctx        = isset($block->context['query']['pages']) ? (int)$block->context['query']['pages'] : 0;
$inherit             = isset($block->context['query']['inherit']) && $block->context['query']['inherit'];
$page                = empty($_GET[$page_key]) ? 1 : (int)$_GET[$page_key];

/** Get wrapper attributes */
$src = preg_replace('/^(?:\s*<!--.*?-->\s*)+/s', '', (string) $content);
$wrapper_attributes = '';

if (preg_match('/^\s*<([a-z][\w:-]*)\b([^>]*)>/i', $src, $m)) {
    $wrapper_attributes = trim($m[2]); // raw attributes string: class="..." data-... id="..."
}

/** Build current/total and paginate pieces (array) */
$current = 1;
$total = 0;
$items = array();

if ($inherit) {
    $current = get_query_var('paged') ? (int)get_query_var('paged') : 1;
    $total   = !$max_page_ctx || $max_page_ctx > $wp_query->max_num_pages ? (int)$wp_query->max_num_pages : $max_page_ctx;

    $args = array('prev_next' => false, 'total' => $total, 'type' => 'array');

    if ($mid_size !== null) {
        $args['mid_size'] = $mid_size;
    }

    $items = paginate_links($args);
} else {

    if (function_exists('build_query_vars_from_query_block')) {
        $args = build_query_vars_from_query_block($block, $page);
    } else {
        $args = [
            'post_type'      => $block->context['query']['postType'] ?? 'post',
            'posts_per_page' => (int) ($block->context['query']['perPage'] ?? get_option('posts_per_page')),
            'paged'          => max(1, $page),
        ];
    }

    $args = uicore_query_post_filter($args, $block->context, get_the_ID());
    $block_query = new WP_Query($args);

    $prev_wpq    = $wp_query;
    $wp_query = $block_query;

    $current = max(1, $page);
    $calc_total = (int)$wp_query->max_num_pages;
    $total   = !$max_page_ctx || $max_page_ctx > $calc_total ? $calc_total : $max_page_ctx;

    $args = array(
        'base'      => '%_%',
        'format'    => "?$page_key=%#%",
        'current'   => $current,
        'total'     => $total,
        'prev_next' => false,
        'type'      => 'array',
    );

    if ($mid_size !== null) {
        $args['mid_size'] = $mid_size;
    }

    if (1 !== $page) {
        $args['add_args'] = array('cst' => '');
    }

    $paged = empty($_GET['paged']) ? null : (int)$_GET['paged'];

    if ($paged) {
        $args['add_args'] = array('paged' => $paged);
    }

    $items = paginate_links($args);

    wp_reset_postdata();
    $wp_query = $prev_wpq;
}

/** Progress mode: "X/Y" */
if ($no_type === 'progress') {
    if ($total < 1) {
        return;
    }
    echo '<div ' . $wrapper_attributes . '>' .
        '<span class="uicore-nav-item">' . esc_html($current) . '</span>' .
        '<span class="uicore-nav-item uicore-nav-separator">/</span>' .
        '<span class="uicore-nav-item">' . esc_html($total) . '</span>' .
        '</div>';
    return;
}

/** List mode: <ul>…</ul> */
if (empty($items)) {
    return;
}

echo '<ul ' . $wrapper_attributes . '>';

$idx = 0;

foreach ($items as $item_html) {
    $is_dots    = strpos($item_html, 'page-numbers dots') !== false;
    $is_current = strpos($item_html, 'page-numbers current') !== false;
    $text       = trim(wp_strip_all_tags($item_html));

    echo '<li>';

    if ($is_dots) {
        echo '<span class="uicore-nav-item uicore-nav-separator">' . esc_html($text !== '' ? $text : '…') . '</span>';
    } elseif ($is_current) {
        echo '<span class="uicore-nav-item uicore-is-active" aria-current="page">' . esc_html($text) . '</span>';
    } else {
        $href = '';

        if (preg_match('/href=["\']([^"\']+)["\']/', $item_html, $mHref)) {
            $href = $mHref[1];
        }

        $attrs = 'class="uicore-nav-item" href="' . esc_url($href) . '"';

        if ($enhanced_pagination) {
            $attrs .= ' data-wp-key="index-' . ((int)$idx) . '" data-wp-on--click="core/query::actions.navigate"';
        }

        echo '<a ' . $attrs . '>' . esc_html($text) . '</a>';
    }

    echo '</li>';

    $idx++;
}

echo '</ul>';
