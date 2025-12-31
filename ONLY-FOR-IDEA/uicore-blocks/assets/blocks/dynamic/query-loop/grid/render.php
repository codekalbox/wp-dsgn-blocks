<?php

/**
 * Query Loop Grid — render one grid-item per post by loop index (legacy include style)
 *
 * Variables provided by WP when this file is included:
 *   @var array     $attributes
 *   @var string    $content
 *   @var \WP_Block $block
 */

/**
 * Determines whether a block list contains a block that uses the featured image.
 * (Core-compatible; defined only if missing.)
 *
 * @since 6.0.0
 *
 * @param iterable $inner_blocks Inner block list (WP_Block_List|array).
 * @return bool Whether any inner block uses the featured image.
 */
if (!function_exists('uicore_block_use_featured_image')) {
    function uicore_block_use_featured_image(iterable $inner_blocks): bool
    {
        foreach ($inner_blocks as $b) {
            $name     = is_object($b) ? ($b->name ?? null) : ($b['name'] ?? null);
            $attrs    = is_object($b) ? ($b->attributes ?? []) : ($b['attributes'] ?? []);
            $children = is_object($b) ? ($b->inner_blocks ?? []) : ($b['innerBlocks'] ?? []);

            if ($name === 'uicore/post-image' && !empty($attrs['dynamicContent']) && $attrs['dynamicContent']['type'] === 'featuredImage') {
                return true;
            }

            if ($name === 'core/post-featured-image') {
                return true;
            }

            if ($name === 'core/cover' && !empty($attrs['useFeaturedImage'])) {
                return true;
            }

            if (!empty($children) && uicore_block_use_featured_image($children)) {
                return true;
            }
        }
        return false;
    }
}


/**
 * Apply QueryLoop "queryPost" filters from block context to WP_Query args.
 *
 * Expected shape in context:
 * $block->context['query']['queryPost'] = [
 *   ['entity' => 'post|taxonomy|author', 'include' => 'IN|NOT_IN', 'taxonomy' => 'slug', 'items' => [ids]]
 * ]
 *
 * @param array     $args
 * @param array     $query_post
 * @return array
 */
if (!function_exists('uicore_query_post_filter')) {
    function uicore_query_post_filter(array $args, array $context, int $exclude_current_id = -1): array
    {
        //default per page to 3
        $args['posts_per_page'] = $context['query']['perPage'] ?? 3;

        $query_post = $context['query']['queryPost'] ?? [];

        if ($exclude_current_id) {
            $args['post__not_in'][] = $exclude_current_id;
        }

        if (!is_array($query_post) || empty($query_post)) {
            return $args;
        }

        $post_in       = isset($args['post__in']) ? (array) $args['post__in'] : [];
        $post_not_in   = isset($args['post__not_in']) ? (array) $args['post__not_in'] : [];
        $author_in     = isset($args['author__in']) ? (array) $args['author__in'] : [];
        $author_not_in = isset($args['author__not_in']) ? (array) $args['author__not_in'] : [];
        $parent_in     = isset($args['post_parent__in']) ? (array) $args['post_parent__in'] : [];
        $parent_not_in = isset($args['post_parent__not_in']) ? (array) $args['post_parent__not_in'] : [];
        $tax_query     = (isset($args['tax_query']) && is_array($args['tax_query'])) ? $args['tax_query'] : [];
        $meta_query    = (isset($args['meta_query']) && is_array($args['meta_query'])) ? $args['meta_query'] : [];

        if (!isset($tax_query['relation'])) {
            $tax_query['relation'] = 'AND';
        }

        if (!isset($meta_query['relation'])) {
            $meta_query['relation'] = 'AND';
        }

        $search_include = [];
        $search_exclude = [];

        if (is_array($query_post)) {

            //move sales_status at the end
            $query_post = array_merge(
                array_filter($query_post, fn($i) => ($i['entity'] ?? '') !== 'sales_status'),
                array_filter($query_post, fn($i) => ($i['entity'] ?? '') === 'sales_status')
            );

            foreach ($query_post as $f) {

                if (!is_array($f)) {
                    continue;
                }

                $entity   = isset($f['entity']) ? (string) $f['entity'] : '';
                $include  = isset($f['include']) ? strtoupper((string) $f['include']) : 'IN';
                $taxonomy = isset($f['taxonomy']) ? (string) $f['taxonomy'] : '';

                if ($entity === '') {
                    continue;
                }

                $is_in = ($include === 'IN' || $include === '');

                /**
                 * For all other entities we keep your existing numeric items logic.
                 */
                $items = [];

                if (!empty($f['items'])) {
                    $items = array_map('intval', (array) $f['items']);
                    $items = array_values(array_filter($items, static function ($v) {
                        return $v > 0;
                    }));
                }

                if ($entity === 'post') {
                    if (!empty($items)) {
                        if ($is_in) {
                            $post_in = array_merge($post_in, $items);
                        } else {
                            $post_not_in = array_merge($post_not_in, $items);
                        }
                    }
                }

                if ($entity === 'author') {
                    if (!empty($items)) {
                        if ($is_in) {
                            $author_in = array_merge($author_in, $items);
                        } else {
                            $author_not_in = array_merge($author_not_in, $items);
                        }
                    }
                }

                if ($entity === 'taxonomy') {
                    if ($taxonomy !== '' && !empty($items)) {
                        $tax_query[] = [
                            'taxonomy' => $taxonomy,
                            'field'    => 'term_id',
                            'terms'    => $items,
                            'operator' => $is_in ? 'IN' : 'NOT IN',
                        ];
                    }
                }

                if ($entity === 'parent') {
                    if (!empty($items)) {
                        if ($is_in) {
                            $parent_in = array_merge($parent_in, $items);
                        } else {
                            $parent_not_in = array_merge($parent_not_in, $items);
                        }
                    }
                }

                if ($entity === 'search') {
                    $phrase = isset($f['search']) && is_string($f['search']) ? trim($f['search']) : '';

                    if ($phrase !== '') {
                        if ($is_in) {
                            $search_include[] = $phrase;
                        } else {
                            $search_exclude[] = $phrase;
                        }
                    }
                }

                if ($entity === 'stock_status') {
                    $items_raw = isset($f['items']) ? (array) $f['items'] : [];
                    $items     = [];

                    foreach ($items_raw as $v) {
                        if (is_string($v) && $v !== '') {
                            $items[] = sanitize_text_field($v);
                        }
                    }

                    $items = array_values(array_unique($items));

                    if (!empty($items)) {
                        $meta_query[] = [
                            'key'     => '_stock_status',
                            'value'   => $items,
                            'compare' => $is_in ? 'IN' : 'NOT IN',
                        ];
                    }

                    continue;
                }

                if ($entity === 'sales_status') {
                    $is_sales = isset($f['items'][0])
                        ? (bool) $f['items'][0]
                        : (isset($f['sales_status']) ? (bool) $f['sales_status'] : null);

                    if ($is_sales !== null && $is_in === false) {
                        $is_sales = !$is_sales;
                    }

                    if ($is_sales !== null && function_exists('wc_get_product_ids_on_sale')) {
                        $sale_ids = wc_get_product_ids_on_sale();

                        if ($is_sales) {
                            $post_in = !empty($post_in)
                                ? array_values(array_intersect($post_in, $sale_ids))
                                : (!empty($sale_ids) ? array_values($sale_ids) : [0]);
                        } else {
                            if (!empty($sale_ids)) {
                                $post_not_in = array_merge($post_not_in, $sale_ids);
                            }
                        }
                    }

                    continue;
                }
            }
        }

        if (!empty($post_in)) {
            $args['post__in'] = array_values(array_unique($post_in));
            if (empty($args['orderby'])) {
                $args['orderby'] = 'post__in';
            }
        }

        if (!empty($post_not_in)) {
            $args['post__not_in'] = array_values(array_unique($post_not_in));
        }

        if (!empty($author_in)) {
            $args['author__in'] = array_values(array_unique($author_in));
        }

        if (!empty($author_not_in)) {
            $args['author__not_in'] = array_values(array_unique($author_not_in));
        }

        if (!empty($parent_in)) {
            $args['post_parent__in'] = array_values(array_unique($parent_in));
        }

        if (!empty($parent_not_in)) {
            $args['post_parent__not_in'] = array_values(array_unique($parent_not_in));
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        if (!empty($meta_query) && count($meta_query) > 1) {
            $args['meta_query'] = $meta_query;
        }

        if (!empty($search_include)) {
            $args['uicore_search_include'] = array_values(array_unique($search_include));
        }

        if (!empty($search_exclude)) {
            $args['uicore_search_exclude'] = array_values(array_unique($search_exclude));
        }

        if (!empty($search_include) || !empty($search_exclude)) {
            $args['suppress_filters'] = false;
        }

        return $args;
    }
}

/**
 * Parse repeatIndex string like "1,3,6" into a set of positive integers.
 *
 * @param mixed $value
 * @return array<int,bool>  e.g., [1 => true, 3 => true, 6 => true]; empty array means "no explicit indices".
 */
if (! function_exists('uicore_parse_repeat_positions')) {
    function uicore_parse_repeat_positions($value): array
    {
        if (!is_string($value) && !is_numeric($value)) {
            return [];
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '') {
            return [];
        }

        $positions = [];

        foreach (preg_split('/\s*,\s*/', $stringValue) as $token) {
            if ($token === '') {
                continue;
            }

            $num = (int) $token;

            if ($num > 0) {
                $positions[$num] = true;
            }
        }

        return $positions;
    }
}

/**
 * Choose which grid-item (by index in $items) to render for a given loop index,
 * mirroring the JS getQueryLoopBlockIndex algorithm.
 *
 * @param array $items      First-level parsed child blocks (uicore/query-loop-grid-item only).
 * @param int   $loopIndex  Zero-based post index in the loop.
 * @return int              Chosen child index (0..n-1). Returns -1 if no children.
 */
if (! function_exists('uicore_pick_grid_item_index')) {
    function uicore_pick_grid_item_index(array $items, int $loopIndex): int
    {
        $blockCount = count($items);

        if ($blockCount === 0) {
            return -1;
        }

        // Precompute metadata for each child block
        $blockMeta = [];

        foreach ($items as $idx => $block) {

            $attrs = is_array($block['attrs'] ?? null) ? $block['attrs'] : [];
            $isRepeatable = $attrs['isRepeatable'] ?? true; // boolean

            $defaultPosition = $isRepeatable ? null : $idx + 1;
            $repeatPositions = uicore_parse_repeat_positions($attrs['repeatIndex'] ?? $defaultPosition);

            $blockMeta[] = [
                'idx'             => $idx,
                'isRepeatable'    => $isRepeatable,
                'repeatPositions' => $repeatPositions, // empty array means "no explicit indices"
            ];
        }

        $oneBasedIndex = $loopIndex + 1;

        // 1) Explicit matches
        for ($i = 0; $i < $blockCount; $i++) {
            $meta = $blockMeta[$i];

            if (empty($meta['repeatPositions'])) {
                continue;
            }

            if ($meta['isRepeatable']) {
                // repeatable: r, 2r, 3r, ...
                foreach ($meta['repeatPositions'] as $r => $_true) {
                    if ($r > 0 && ($oneBasedIndex % $r) === 0) {
                        return (int) $meta['idx'];
                    }
                }
            } else {
                // non-repeatable: only at r-th
                if (isset($meta['repeatPositions'][$oneBasedIndex])) {
                    return (int) $meta['idx'];
                }
            }
        }

        // 2) Round-robin among repeatables without explicit indices
        $repeatablePool = [];

        foreach ($blockMeta as $meta) {
            if ($meta['isRepeatable'] && empty($meta['repeatPositions'])) {
                $repeatablePool[] = (int) $meta['idx'];
            }
        }

        if (! empty($repeatablePool)) {
            return $repeatablePool[$loopIndex % count($repeatablePool)];
        }

        // 3) Fallback: first block
        return 0;
    }
}


/**
 * Render ONLY ONE first-level `uicore/query-loop-grid-item` per post,
 * chosen via isRepeatable/repeatIndex rules (parity with the editor).
 * Injects postId/postType into block context. Renders the child's inner blocks only.
 *
 * @param \WP_Block $block
 * @param int       $loop_index
 * @return array 
 */
if (! function_exists('uicore_render_grid_item_by_index')) {
    function uicore_render_grid_item_by_index(\WP_Block $block, int $loop_index): array
    {
        $parsed   = is_array($block->parsed_block) ? $block->parsed_block : [];
        $children = $parsed['innerBlocks'] ?? [];
        $items    = [];

        // Collect only first-level grid-item templates in editor order.
        foreach ($children as $child) {
            if (is_array($child) && ($child['blockName'] ?? null) === 'uicore/query-loop-grid-item') {
                $items[] = $child;
            }
        }

        if (empty($items)) {
            return [];
        }

        // Decide WHICH child to render for this loop index (mirror editor logic).
        $item_idx = uicore_pick_grid_item_index($items, $loop_index);
        if ($item_idx < 0 || ! isset($items[$item_idx])) {
            return [];
        }

        $item_block_id = $items[$item_idx]['attrs']['blockId'] ?? null;

        // Render ONLY the chosen child's inner blocks (remove wrappers/supports).
        $template               = $items[$item_idx];
        $template['blockName']  = 'core/null';

        $post_id   = get_the_ID();
        $post_type = get_post_type();
        $query_id   = $block->context['queryId'] ?? 1;

        $ctx_filter = static function (array $ctx) use ($post_id, $post_type, $query_id): array {
            $ctx['postType'] = $post_type;
            $ctx['postId']   = $post_id;
            $ctx['queryId']  = $query_id;
            return $ctx;
        };

        add_filter('render_block_context', $ctx_filter, 1);
        $html = (new \WP_Block($template))->render(['dynamic' => false]);

        $html = '';
        if (! empty($template['innerBlocks'])) {
            foreach ($template['innerBlocks'] as $ib) {
                $html .= render_block($ib);
            }
        }

        remove_filter('render_block_context', $ctx_filter, 1);

        return [$item_block_id, $html];
    }
}


/**
 * Main renderer: builds the query, warms caches, and outputs one grid-item per post. 
 */

$page_key            = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$enhanced_pagination = !empty($block->context['enhancedPagination']);
$page                = empty($_GET[$page_key]) ? 1 : (int) $_GET[$page_key];

// Build the query (inherit or from block context).
$use_global_query = (isset($block->context['query']['inherit']) && $block->context['query']['inherit']);

if ($use_global_query) {

    global $wp_query;
    $query = in_the_loop() ? clone $wp_query : $wp_query;

    if (in_the_loop()) {
        $query->rewind_posts();
    }
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

    if (!isset($args['paged'])) {
        $args['paged'] = max(1, $page);
    }

    $query = new \WP_Query($args);
}

if (!$query->have_posts()) {
    wp_reset_postdata();
    return;
}

// Warm thumbnail cache if any inner block needs the featured image.
if (uicore_block_use_featured_image($block->inner_blocks)) {
    update_post_thumbnail_cache($query);
}

// Get wrapper attributes
$src = preg_replace('/^(?:\s*<!--.*?-->\s*)+/s', '', (string) $content);
$wrapper_attributes = '';

if (preg_match('/^\s*<([a-z][\w:-]*)\b([^>]*)>/i', $src, $m)) {
    $wrapper_attributes = trim($m[2]); // raw attributes string: class="..." data-... id="..."
}

// Build items: exactly one grid-item per post, alternating by index.
$list_items = '';
$loop_index = 0;

while ($query->have_posts()) {
    $query->the_post();

    [$item_block_id, $html] = uicore_render_grid_item_by_index($block, $loop_index);

    // If no grid-item children exist, skip output for this post.
    if ($html === '') {
        $loop_index++;
        continue;
    }

    $post_id     = get_the_ID();
    $post_cls    = implode(' ', get_post_class('wp-block-post wp-block-uicore-none uicore-block-' . $item_block_id));
    $directives  = $enhanced_pagination ? ' data-wp-key="post-template-item-' . $post_id . '"' : '';

    $list_items .= '<div' . $directives . ' class="' . esc_attr($post_cls) . '">' . $html . '</div>';
    $loop_index++;
}

wp_reset_postdata();

if ($list_items !== '') {
    echo sprintf('<div %1$s>%2$s</div>', $wrapper_attributes, $list_items);
}
