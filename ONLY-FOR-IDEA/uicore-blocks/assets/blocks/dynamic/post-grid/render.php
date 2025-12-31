<?php

/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

use UiCore\Portfolio;
use UiCore\Blog;

global $wp_query;
$default_query = $wp_query;
$_query = $wp_query;
$post_type = 'post';
$is_editor = strpos($content, '[uicore-post-grid-render]') === false;
$query_post_type = isset($attributes['queryPost']) && !empty($attributes['queryPost']) ? $attributes['queryPost'] : 'post';
$is_product_grid = (isset($attributes['isWoo']) && filter_var($attributes['isWoo'], FILTER_VALIDATE_BOOLEAN)) || $query_post_type === 'product';
$is_woo_grid = $is_product_grid && class_exists('WooCommerce');
// print_r($attributes);
// return;
if ($query_post_type  != 'current_query') {
    $query_args = [
        'post_type' => $is_product_grid ? 'product' : $query_post_type,
        'post_status' => 'publish',
        //     'ignore_sticky_posts' => true,
        //     'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        //     'posts_per_page' => 10,
    ];
    if (isset($attributes['queryPostsPerPage'])) {
        $query_args['posts_per_page'] = $attributes['queryPostsPerPage'];
    }
    if (isset($attributes['queryOffSet'])) {
        $query_args['offset'] = $attributes['queryOffSet'];
    }

    if ($query_post_type == 'portfolio') {
        $post_type = 'portfolio';
    }
    //fetch blog posts
    $_query = new WP_Query($query_args);
} else if ($is_editor && $query_post_type === 'current_query') {
    //if the block is used in the editor, we need to set the query to the current one
    echo '<p>Current Query is only available in frontend because it is based on the current post context.</p>';
    echo '<p> <small>You can use a different query in the editor to experiment.</small></p>';
    return;
}


$col_no = isset($attributes['colNumber']) && !empty($attributes['colNumber']) ? $attributes['colNumber'] : null;
ob_start();
//no post found
if (!$_query->have_posts()) {
    echo '<p>' . __('No posts found.', 'uicore-blocks') . '</p>';
} else {
    // render the posts
    if ($is_woo_grid) {
?>
        <ul class="products ui-bl-product-grid columns-<?php echo esc_attr($col_no ?? 3); ?>">
            <?php
            while ($_query->have_posts()) {
                $_query->the_post();
                \wc_get_template_part('content', 'product');
            }

            ?>
        </ul>
<?php

    } elseif ($post_type == 'portfolio') {
        if (!class_exists('\UiCore\Portfolio\Frontend')) {
            require_once UICORE_INCLUDES . '/portfolio/class-template.php';
            require_once UICORE_INCLUDES . '/portfolio/class-frontend.php';
        }
        Portfolio\Frontend::frontend_css(true);
        $portfolio = new Portfolio\Template('display');
        $portfolio->portfolio_layout($_query, null, $col_no);
    } else {
        if (!class_exists('\UiCore\Blog\Frontend')) {
            require_once UICORE_INCLUDES . '/blog/class-template.php';
            require_once UICORE_INCLUDES . '/blog/class-frontend.php';
        }

        $grid_type = isset($attributes['gridType']) && !empty($attributes['gridType']) ? $attributes['gridType'] : null;
        $ratio = isset($attributes['ratio']) && !empty($attributes['ratio']) ? $attributes['ratio'] : null;
        $item_style = isset($attributes['itemStyle']) && !empty($attributes['itemStyle']) ? $attributes['itemStyle'] : null;
        $hover_effect = null;

        $extra = [
            'author' => isset($attributes['showAuthor']) ? filter_var($attributes['showAuthor'], FILTER_VALIDATE_BOOLEAN) : null,
            'date' => isset($attributes['showDate']) ? filter_var($attributes['showDate'], FILTER_VALIDATE_BOOLEAN) : null,
            'category' => isset($attributes['showCategory']) ? filter_var($attributes['showCategory'], FILTER_VALIDATE_BOOLEAN) : null,
            'excerpt' => isset($attributes['showExcerpt']) ? filter_var($attributes['showExcerpt'], FILTER_VALIDATE_BOOLEAN) : null,
        ];

        Blog\Frontend::frontend_css(true, $item_style);
        $blog = new Blog\Template('display');
        $blog->blog_layout($_query, $grid_type, $col_no, $hover_effect, $ratio, $extra, $item_style);
    }
}
$grid = ob_get_clean();

//used for saved content in the editor
if ($is_editor) {
    if ($item_style && $post_type != 'portfolio' && !$is_woo_grid) {
        echo '<style>';
        echo file_get_contents(UICORE_ASSETS . '/css/blog/item-style-' . $item_style . '.css');
        echo '</style>';
    }
    if ($is_woo_grid) {
        //load woocommerce css for product grid in editor
        //TODO: this shoud be improved (both default and framework styles should be loaded in a better way)
        $path = 'assets/css/woocommerce.css';
        $file = apply_filters('woocommerce_get_asset_url', plugins_url($path, WC_PLUGIN_FILE), $path);
        echo '<link rel="stylesheet" href="' . esc_url($file) . '">';

        $framework_global_styles = \UiCore\Assets::get_global("uicore-global.css");
        echo '<link rel="stylesheet" href="' . esc_url($framework_global_styles) . '">';
    }
    echo $grid;
    return;
}
$content = str_replace('[uicore-post-grid-render]', $grid, $content);
echo $content;

// Reset the query to the default
wp_reset_query();
$wp_query = $default_query;
