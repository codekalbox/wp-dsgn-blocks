<?php

namespace Nectar\Utilities;

use Nectar\Utilities\{Log, DemoUtils, WidgetImport};

/**
 * Import Utilities
 * @version 1.3.0
 * @since 0.1.5
 */
class ImportUtils {
  public static function run_full_import($demo_slug, $options) {
    $start = timer_start();
    error_log('Import started: ' . $demo_slug);

    add_action('nectar_demo_import_end', '\Nectar\Utilities\DemoUtils::run_end_function' );

    // Demo Import
    if ( array_key_exists('demoContent', $options) && $options['demoContent'] === true) {
      ImportUtils::import_demo($demo_slug);
    }

    // Theme Import
    if ( array_key_exists('themeOptions', $options) && $options['themeOptions'] === true) {
      ImportUtils::import_customizer_options($demo_slug);
    }

    // Plugin Import
    if ( array_key_exists('globalSettings', $options) && $options['globalSettings'] === true) {
      ImportUtils::import_global_settings($demo_slug);
    }

    // Widget import
    if ( array_key_exists('widgets', $options) && $options['widgets'] === true) {
      ImportUtils::import_widgets($demo_slug);
    }

    $time_elapsed = timer_stop();
    error_log('Import completed: ' . $time_elapsed . ' - ' . $demo_slug);
  }

  public static function import_demo($demo_slug) {
    Log::debug('Demo Import: Starting');
    $importer = new \WP_Import();
    $importer->fetch_attachments = true;
    $file_path = NB_IE_PLUGIN_DIR . '/includes/Demos/' . $demo_slug . '/nectarblocks_content.xml';
    $import_success = false;

    if (! DemoUtils::file_path_exists($file_path) ) {
      Log::error('Demo content file path not found');
    } else {
      $import_success = $importer->import(
          $file_path,
          $demo_slug
      );
    }
    Log::debug('Demo Import: Completed');

    return $import_success;
  }

  public static function import_customizer_options($demo_slug) {
    Log::debug('Customizer Import: Starting');
    if (NECTAR_THEME_DIRECTORY === null) {
      Log::error('Theme not installed while trying to import theme files');
    } else {
      $file_path = NB_IE_PLUGIN_DIR . '/includes/Demos/' . $demo_slug . '/nectarblocks_theme.json';

      if (! DemoUtils::file_path_exists($file_path) ) {
        Log::error('Customizer file path not found');
      } else {

        $theme_json = DemoUtils::read_json_file($file_path);
        require_once NECTAR_THEME_DIRECTORY . '/nectar/theme-import-export.php';
        $instance = \Theme_IE::get_instance();
        $instance->import_options($theme_json);
      }
    }
    Log::debug('Customizer Import: Starting');
  }

  public static function import_global_settings($demo_slug) {
    Log::debug('Global Settings Import: Starting');

      if (NECTAR_BLOCKS_ROOT_DIR_PATH === null) {
        Log::error('Plugin not installed while typing to import plugin files');
      } else {
        $file_path = NB_IE_PLUGIN_DIR . '/includes/Demos/' . $demo_slug . '/nectarblocks_plugin.json';

        if (! DemoUtils::file_path_exists($file_path) ) {
          Log::error('Global Settings file path not found');
        } else {
          $plugin_json = DemoUtils::read_json_file($file_path);

          require_once NECTAR_BLOCKS_ROOT_DIR_PATH . '/includes/Import_Export/Plugin_IE.php';
          $instance = \Nectar\Import_Export\Plugin_IE::get_instance();
          $instance->import_options($plugin_json);
        }
      }
      Log::debug('Global Settings Import: Completed');
  }

  public static function import_widgets($demo_slug) {
    Log::debug('Widget Import: Starting');

    $file_path = NB_IE_PLUGIN_DIR . '/includes/Demos/' . $demo_slug . '/nectarblocks_widgets.json';

    if (! DemoUtils::file_path_exists($file_path) ) {
      Log::error('Widget file path not found');
    } else {
      $import_success = WidgetImport::import_widgets($file_path);
      return $import_success;
    }
    Log::debug('Widget Import: Completed');
  }

  public static function get_post_id_by_title($title, $post_type = 'post') {
    $post = get_page_by_title($title, OBJECT, $post_type);
    return $post ? $post->ID : null;
  }

  public static function get_product_category_id_by_slug($slug) {
    $category = get_term_by('slug', $slug, 'product_cat');
    return $category ? $category->term_id : null;
  }

  // Taxonomy terms will get different ids when imported. This function will allow us to add placeholder
  // strings to the post content and then replace them with the actual term ids after the import.
  public static function nectar_replace_term_placeholders($post_title, $term_placeholders) {

    $post = get_page_by_title($post_title);
    if (!$post) {
        return;
    }

    $content = $post->post_content;

    // Process each placeholder
    foreach ($term_placeholders as $placeholder) {
        // Get the term by slug (extract slug from the full placeholder)
        $term_slug = str_replace('nb_term__', '', $placeholder);
        $term = get_term_by('slug', $term_slug, 'category');

        if ($term) {
            // Replace the full placeholder with the term ID
            $content = str_replace($placeholder, $term->term_id, $content);
        }
    }

    // IMPORTANT.
    // Direct database update instead of wp_update_post() to avoid WordPress filters which could cause block validation issues.
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        ['post_content' => $content],
        ['ID' => $post->ID],
        ['%s'],
        ['%d']
    );

    // Clear post cache
    clean_post_cache($post->ID);
  }


  public static function replace_woocommerce_category_link($post_id, $category_slug, $replace_string) {
    $cat_id = ImportUtils::get_product_category_id_by_slug($category_slug);
    if ( $cat_id ) {
      $category_link = get_term_link( $cat_id, 'product_cat' );
      if ( $category_link ) {
        ImportUtils::replace_content_link(
            $post_id,
            $replace_string,
            $category_link
        );
      }
    }
  }

  public static function get_menu_item_id_by_title($menu_name, $menu_item_title) {
    $menu = wp_get_nav_menu_object($menu_name);

    if (!$menu) {
        return null;
    }

    $menu_items = wp_get_nav_menu_items($menu->term_id);

    if (!$menu_items) {
        return null;
    }

    foreach ($menu_items as $item) {
        if ($item->title === $menu_item_title) {
            return $item->ID;
        }
    }

    return null;
  }


  public static function replace_content_link(int $post_id, string $old_url, string $new_url) {
    // Get the post object by ID
    $post = get_post($post_id);

    // Check if post exists and is not empty
    if (! $post) {
        return 'Post not found';
    }

    // Get current post content
    $content = $post->post_content;

    // Replace old URL with new URL in content
    $updated_content = str_replace($old_url, $new_url, $content);

    // Update the post with the new content if changes were made
    if ($updated_content !== $content) {
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $updated_content,
        ]);
    }
  }

  public static function add_hash_links( string $slug, array $hash_links_arr, array $link_ordering = null ) {
    // Get menu id
    $nav_menu = get_term_by('slug', $slug, 'nav_menu');

    if ($link_ordering !== null) {
      // if (count($hash_links_arr) !== count($link_ordering)) {
      //   return
      // }
      $keys_diff = array_diff_key($hash_links_arr, $link_ordering);
      if (count($keys_diff) !== 0) {
        error_log('Error generating add_hash_links, keys of hash_links_arr and link_ordering do not match: ' . json_encode($keys_diff));
        return;
      }
    }

    if( isset($nav_menu->term_id) ) {

      // Loop and add hash links
      foreach($hash_links_arr as $hash_name => $hash_link) {

        $generated_menu_url = home_url( '/' ) . '#' . $hash_link;

        $menu_item_data = [
          'menu-item-title' => esc_html($hash_name),
          'menu-item-url' => esc_url($generated_menu_url),
          'menu-item-status' => 'publish',
          'menu-item-type' => 'custom',
        ];

        if ($link_ordering !== null && array_key_exists($hash_name, $link_ordering)) {
          $menu_item_data['menu-item-position'] = $link_ordering[$hash_name];
        }

        wp_update_nav_menu_item($nav_menu->term_id, 0, $menu_item_data);

      }

    }
  }

  public static function add_custom_links( string $slug, array $hash_links_arr ) {
    // Get menu id
    $nav_menu = get_term_by('slug', $slug, 'nav_menu');

    if( isset($nav_menu->term_id) ) {

      // Loop and add hash links
      foreach($hash_links_arr as $menu_item_name => $custom_link) {

        wp_update_nav_menu_item($nav_menu->term_id, 0, [
          'menu-item-position' => 1,
          'menu-item-title' => esc_html($menu_item_name),
          'menu-item-url' => esc_url($custom_link),
          'menu-item-status' => 'publish',
          'menu-item-type' => 'custom',
        ]);

      }

    }
  }

  public static function assign_front_page( $page_name ) {
    $args = [
      'title' => $page_name,
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => 1
    ];
    $query = new \WP_Query($args);
    if ( $query->have_posts() ) {
      // The Loop
      while ( $query->have_posts() ) {
        $query->the_post();
        // Get the ID of the page
        $page_id = get_the_ID();

        // Use the ID to set the page as the front page
        update_option('page_on_front', $page_id);
        update_option('show_on_front', 'page');
      }
      // Reset Post Data
      wp_reset_postdata();
    }
  }

  public static function assign_menu( string $slug, $location ) {

    // Get Menu locations.
    $menu_locations = get_nav_menu_locations();

    // Get ID of menu by name.
    $nav_menu = get_term_by('slug', $slug, 'nav_menu');

    if( isset($nav_menu->term_id) ) {

      $nav_menu_id = $nav_menu->term_id;

      // Set menu.
      $menu_locations[$location] = $nav_menu_id;
      set_theme_mod( 'nav_menu_locations', $menu_locations );
    }
  }

  // Needed to index the product meta data in order for WooCommerce filters to work
  // after doing an import.
  public static function update_woocommerce_lookup_tables() {

    if ( ! function_exists( 'wc_update_product_lookup_tables_is_running' ) ) {
      return;
    }

    if ( ! function_exists( 'wc_update_product_lookup_tables' ) ) {
      return;
    }

    if ( wc_update_product_lookup_tables_is_running() ) {
      return;
    }

    wc_update_product_lookup_tables();
  }
}