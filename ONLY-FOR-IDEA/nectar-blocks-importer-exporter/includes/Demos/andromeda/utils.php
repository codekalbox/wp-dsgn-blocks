<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {
  ImportUtils::assign_menu('andromeda-menu', 'top_nav');
  ImportUtils::assign_menu('andromeda-mobile-menu', 'off_canvas_nav');
  ImportUtils::assign_front_page('Andromeda Landing');

  // get WooCommerce shop page url
  $shop_page = get_permalink(wc_get_page_id('shop'));

  $nav_menu = get_term_by('slug', 'andromeda-menu', 'nav_menu');
  if ( isset($nav_menu->term_id) ) {
    $shop_menu_item_id = wp_update_nav_menu_item($nav_menu->term_id, 0, [
      'menu-item-title' => 'Shop',
      'menu-item-url' => $shop_page,
      'menu-item-status' => 'publish',
      'menu-item-type' => 'custom',
    ]);

    if ( $shop_menu_item_id ) {
      // Set megamenu on shop link by updating nectar_menu_options meta value.
      $nectar_menu_options = 'a:15:{s:16:"enable_mega_menu";s:2:"on";s:24:"mega_menu_global_section";s:4:"1368";s:31:"mega_menu_global_section_mobile";s:1:"-";s:19:"menu_item_icon_type";s:6:"custom";s:21:"menu_item_icon_custom";a:2:{s:3:"url";s:0:"";s:2:"id";s:0:"";}s:23:"menu_item_icon_position";s:7:"default";s:25:"menu_item_link_link_style";s:7:"default";s:30:"menu_item_link_link_text_style";s:7:"default";s:27:"menu_item_link_button_color";s:7:"#000000";s:32:"menu_item_link_button_color_text";s:7:"#ffffff";s:33:"menu_item_link_button_color_hover";s:7:"#222222";s:38:"menu_item_link_button_color_text_hover";s:7:"#ffffff";s:34:"menu_item_link_button_color_border";s:7:"#666666";s:39:"menu_item_link_button_color_border_text";s:7:"#000000";s:40:"menu_item_link_button_color_border_hover";s:7:"#000000";}';
      update_post_meta( $shop_menu_item_id, 'nectar_menu_options', $nectar_menu_options);

    }

  }

  // Custom menu links for Shop New Arrivals
  $permalink_structure = get_option( 'permalink_structure' );
  $permalink_format = empty($permalink_structure) ? '&' : '/?';
  ImportUtils::add_custom_links('andromeda-menu', [
    'New Arrivals' => $shop_page . $permalink_format . 'orderby=date&on_sale=1',
  ]);
  ImportUtils::add_custom_links('andromeda-mobile-menu', [
    'New' => $shop_page . $permalink_format . 'orderby=date&on_sale=1',
  ]);

  // Update links in megamenuget_product_category_id_by_slug
  $megamenu_post_id = ImportUtils::get_post_id_by_title('Shop Megamenu', 'nectar_sections');

  if ( $megamenu_post_id ) {
    ImportUtils::replace_woocommerce_category_link(
        $megamenu_post_id,
        'apparel',
        'https://demos.nectarblocks.com/andromeda/product-category/apparel/'
    );

    ImportUtils::replace_woocommerce_category_link(
        $megamenu_post_id,
        'bling',
        'https://demos.nectarblocks.com/andromeda/product-category/bling/'
    );

    ImportUtils::replace_woocommerce_category_link(
        $megamenu_post_id,
        'footwear',
        'https://demos.nectarblocks.com/andromeda/product-category/footwear/'
    );

    ImportUtils::replace_woocommerce_category_link(
        $megamenu_post_id,
        'gifts',
        'https://demos.nectarblocks.com/andromeda/product-category/gifts/'
    );
  }

  // Update WooCommerce Image settings in Customizer.
  update_option('woocommerce_thumbnail_image_width', 900);
  update_option('woocommerce_single_image_width', 1200);
  update_option('woocommerce_thumbnail_cropping', 'custom');
  update_option('woocommerce_thumbnail_cropping_custom_width', 3);
  update_option('woocommerce_thumbnail_cropping_custom_height', 4);

  ImportUtils::update_woocommerce_lookup_tables();

  // Regenerate thumbnails if needed
  if (function_exists('wc_regenerate_images')) {
    wc_regenerate_images();
  }

  // TODO: attributes won't be imported unless we use CSV.

}
