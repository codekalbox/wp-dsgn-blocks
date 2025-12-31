<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {
  ImportUtils::assign_menu('firstlight-navigation', 'top_nav');
  ImportUtils::assign_menu('firstlight-right-navigation', 'top_nav_pull_right');
  ImportUtils::assign_front_page('Firstlight Overview');

  $cta_menu_item_id =  ImportUtils::get_menu_item_id_by_title('firstlight-right-navigation', 'Start a Project');

  // button menu options
  if ( $cta_menu_item_id ) {
    $nectar_menu_options = 'a:15:{s:24:"mega_menu_global_section";s:1:"-";s:31:"mega_menu_global_section_mobile";s:1:"-";s:19:"menu_item_icon_type";s:6:"custom";s:21:"menu_item_icon_custom";a:2:{s:3:"url";s:0:"";s:2:"id";s:0:"";}s:23:"menu_item_icon_position";s:7:"default";s:31:"menu_item_persist_mobile_header";s:2:"on";s:25:"menu_item_link_link_style";s:6:"border";s:30:"menu_item_link_link_text_style";s:11:"text-reveal";s:27:"menu_item_link_button_color";s:7:"#000000";s:32:"menu_item_link_button_color_text";s:7:"#ffffff";s:33:"menu_item_link_button_color_hover";s:7:"#222222";s:38:"menu_item_link_button_color_text_hover";s:7:"#ffffff";s:34:"menu_item_link_button_color_border";s:7:"#ffffff";s:39:"menu_item_link_button_color_border_text";s:7:"#000000";s:40:"menu_item_link_button_color_border_hover";s:7:"#ffffff";}';
    update_post_meta( $cta_menu_item_id, 'nectar_menu_options', $nectar_menu_options);
  }

}
