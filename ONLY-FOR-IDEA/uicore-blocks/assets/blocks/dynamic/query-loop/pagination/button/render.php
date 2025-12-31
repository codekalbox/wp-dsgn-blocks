<?php
// Uses $attributes, $content, $block. Echoes the final <a>.

if (empty($content) || stripos($content, '<a') === false) {
    return;
}

$btn_type            = isset($attributes['btnType']) ? $attributes['btnType'] : 'previous'; // 'previous'|'next'
$page_key            = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$enhanced_pagination = isset($block->context['enhancedPagination']) && $block->context['enhancedPagination'];
$max_page            = isset($block->context['query']['pages']) ? (int)$block->context['query']['pages'] : 0;
$inherit             = isset($block->context['query']['inherit']) && $block->context['query']['inherit'];

$has_label = strpos($content, 'uicore-bl-text') !== false;
$label_text = isset($attributes['content']) && $attributes['content'] !== ''
    ? (string)$attributes['content']
    : ($btn_type === 'next' ? __('Next Page', 'uicore-blocks') : __('Previous Page', 'uicore-blocks'));

$href = '';
$active = false;

if ($inherit) {
    if (isset($GLOBALS['wp_query']->post_count) && $GLOBALS['wp_query']->post_count == 0) {
        return;
    }

    $current = get_query_var('paged') ? (int)get_query_var('paged') : 1;
    $total   = $max_page ? $max_page : (isset($GLOBALS['wp_query']->max_num_pages) ? (int)$GLOBALS['wp_query']->max_num_pages : 0);

    if ($btn_type === 'previous') {
        $active = $current > 1;

        if ($active) {
            $href = get_pagenum_link($current - 1);
        }
    } else {
        if (!$total) {
            $total = 999999;
        }

        $active = $current < $total;

        if ($active) {
            $href = get_pagenum_link($current + 1);
        }
    }
} else {
    $page = empty($_GET[$page_key]) ? 1 : (int)$_GET[$page_key];

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
    $q = new WP_Query($args);

    if ($q->post_count == 0) {
        return;
    }

    $max_pages = (int)$q->max_num_pages;
    wp_reset_postdata();

    $total = (!$max_page || $max_page > $max_pages) ? $max_pages : $max_page;

    if ($btn_type === 'previous') {
        $active = ($page > 1 && $page <= max(1, $total));

        if ($active) {
            $href = add_query_arg($page_key, $page - 1);
        }
    } else {
        $active = ($total > 0 && $page < $total);

        if ($active) {
            $href = add_query_arg($page_key, $page + 1);
        }
    }
}

if (!$active) {
    return;
}

$p = new WP_HTML_Tag_Processor($content);

if (!$p->next_tag(array('tag_name' => 'a'))) {
    return;
}

$p->set_attribute('href', esc_url($href));
$p->set_attribute('rel', $btn_type === 'next' ? 'next' : 'prev');

if ($enhanced_pagination) {
    $p->set_attribute('data-wp-key', $btn_type === 'next' ? 'uicore-pagination-next' : 'uicore-pagination-previous');
    $p->set_attribute('data-wp-on--click', 'core/query::actions.navigate');
    $p->set_attribute('data-wp-on-async--mouseenter', 'core/query::actions.prefetch');
    $p->set_attribute('data-wp-watch', 'core/query::callbacks.prefetch');
}

if (!$has_label) {
    $p->set_attribute('aria-label', esc_attr($label_text));
}

echo $p->get_updated_html();
