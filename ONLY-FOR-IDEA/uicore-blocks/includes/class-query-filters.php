<?php

namespace UiCoreBlocks;

use WP_REST_Request;

/**
 * Query Filters
 */
class QueryFilters
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_exact_search']);
        add_filter('posts_where', [$this, 'uicore_search_where'], 10, 2);
    }


    public function register_exact_search(): void
    {
        $post_types = get_post_types(['show_in_rest' => true], 'names');

        if (is_array($post_types) && !empty($post_types)) {
            foreach ($post_types as $type) {
                add_filter("rest_{$type}_collection_params", [$this, 'extend_collection_params']);
                add_filter("rest_{$type}_query", [$this, 'map_exact_search_query'], 10, 2);
            }
        }
    }

    public function extend_collection_params(array $params): array
    {
        $params['uicore_search_include'] = [
            'description' => 'Exact phrase(s) that must appear in title OR content OR excerpt. AND logic across phrases.',
            'type'        => 'array',
            'items'       => ['type' => 'string'],
            'required'    => false,
        ];

        $params['uicore_search_exclude'] = [
            'description' => 'Exact phrase(s) that must NOT appear in title OR content OR excerpt. AND logic across phrases.',
            'type'        => 'array',
            'items'       => ['type' => 'string'],
            'required'    => false,
        ];

        $params['woo_stock_status'] = [
            'description' => 'Filter products by WooCommerce stock status.',
            'type'        => 'array',
            'items'       => [
                'type' => 'string',
                'enum' => ['instock', 'outofstock', 'onbackorder'],
            ],
            'required'    => false,
        ];

        $params['woo_on_sale'] = [
            'description' => 'Filter products by on-sale status.',
            'type'        => 'array',
            'items'       => [
                'type' => 'boolean',
            ],
            'required'    => false,
        ];

        return $params;
    }

    // NOTE the fully-qualified \WP_REST_Request
    public function map_exact_search_query(array $args, \WP_REST_Request $request): array
    {
        $inc = $this->normalize_phrase_array($request->get_param('uicore_search_include'));
        $exc = $this->normalize_phrase_array($request->get_param('uicore_search_exclude'));

        if (!empty($inc)) {
            $args['uicore_search_include'] = $inc;
        }

        if (!empty($exc)) {
            $args['uicore_search_exclude'] = $exc;
        }

        if (!empty($inc) || !empty($exc)) {
            $args['suppress_filters'] = false;
        }

        /**
         * WooCommerce product-only filters:
         * - woo_stock_status[] => _stock_status IN (...)
         * - woo_on_sale[]      => on sale / not on sale
         */
        if ($this->is_product_rest_request($request)) {
            $stock_status = $request->get_param('woo_stock_status');

            if (is_array($stock_status) && !empty($stock_status)) {
                $clean_status = [];
                foreach ($stock_status as $s) {
                    if (is_string($s) && $s !== '') {
                        $clean_status[] = sanitize_text_field($s);
                    }
                }

                if (!empty($clean_status)) {
                    if (empty($args['meta_query']) || !is_array($args['meta_query'])) {
                        $args['meta_query'] = [];
                    }

                    $args['meta_query'][] = [
                        'key'     => '_stock_status',
                        'value'   => array_values(array_unique($clean_status)),
                        'compare' => 'IN',
                    ];
                }
            }

            $on_sale_param = $request->get_param('woo_on_sale');

            if (is_array($on_sale_param) && $on_sale_param !== []) {
                $on_sale = (bool) $on_sale_param[0];

                if (function_exists('wc_get_product_ids_on_sale')) {
                    $sale_ids = wc_get_product_ids_on_sale();

                    if ($on_sale) {
                        if (!empty($sale_ids)) {
                            if (!empty($args['post__in']) && is_array($args['post__in'])) {
                                $args['post__in'] = array_values(
                                    array_intersect($args['post__in'], $sale_ids)
                                );
                            } else {
                                $args['post__in'] = $sale_ids;
                            }
                        } else {
                            $args['post__in'] = [0];
                        }
                    } else {
                        if (!empty($sale_ids)) {
                            if (!empty($args['post__not_in']) && is_array($args['post__not_in'])) {
                                $args['post__not_in'] = array_values(
                                    array_unique(array_merge($args['post__not_in'], $sale_ids))
                                );
                            } else {
                                $args['post__not_in'] = $sale_ids;
                            }
                        }
                    }
                }
            }
        }

        return $args;
    }

    // NOTE the fully-qualified \WP_Query
    public function uicore_search_where(string $where, \WP_Query $wp_query): string
    {
        global $wpdb;

        $inc = $wp_query->get('uicore_search_include');
        $exc = $wp_query->get('uicore_search_exclude');

        if (empty($inc) && empty($exc)) {
            return $where;
        }

        $clauses = [];
        $params  = [];

        $add_like_group = static function (string $phrase, bool $negate) use (&$clauses, &$params, $wpdb): void {
            $like = '%' . $wpdb->esc_like($phrase) . '%';

            if ($negate) {
                $clauses[] = "( {$wpdb->posts}.post_title NOT LIKE %s AND {$wpdb->posts}.post_content NOT LIKE %s AND {$wpdb->posts}.post_excerpt NOT LIKE %s )";
            } else {
                $clauses[] = "( {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s )";
            }

            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        };

        if (is_array($inc)) {
            foreach ($inc as $p) {
                if (is_string($p)) {
                    $p = trim($p);
                    if ($p !== '') {
                        $add_like_group($p, false);
                    }
                }
            }
        }

        if (is_array($exc)) {
            foreach ($exc as $p) {
                if (is_string($p)) {
                    $p = trim($p);
                    if ($p !== '') {
                        $add_like_group($p, true);
                    }
                }
            }
        }

        if (!empty($clauses)) {
            $where .= ' AND ' . $wpdb->prepare(implode(' AND ', $clauses), $params);
        }

        return $where;
    }

    private function normalize_phrase_array($value): array
    {
        $out = [];

        if (is_string($value)) {
            $value = [$value];
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                if (is_string($v)) {
                    $t = trim(wp_unslash($v));
                    if ($t !== '') {
                        $out[] = sanitize_text_field($t);
                    }
                }
            }
        }

        if (!empty($out)) {
            $out = array_values(array_unique($out));
        }

        return $out;
    }

    private function is_product_rest_request(\WP_REST_Request $request): bool
    {
        $route = $request->get_route();
        if (is_string($route) && strpos($route, '/wp/v2/product') === 0) {
            return true;
        }

        $type = $request->get_param('type');
        return $type === 'product';
    }
}
