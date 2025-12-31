<?php

if (empty(trim($content))) {
    return;
}

$page_key = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$page     = empty($_GET[$page_key]) ? 1 : (int) $_GET[$page_key];

// Override the custom query with the global query if needed.
$use_global_query = (isset($block->context['query']['inherit']) && $block->context['query']['inherit']);

if ($use_global_query) {
    global $wp_query;
    $query = $wp_query;
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

    $args  = uicore_query_post_filter($args, $block->context, get_the_ID());
    $query = new \WP_Query($args);
}

if ($query->max_num_pages < 2) {
    return;
}

echo $content;
