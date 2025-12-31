<?php

namespace Nectar\Global_Sections;

/**
 * Render Global Sections.
 * @since 0.1.4
 * @version 2.0.0
 */
class Render {
  private static $instance;

  public $exclude = false;

  public $post_type;

  public $post_id;

  private function __construct() {
    add_action( 'wp', [$this, 'frontend_display'] );
  }

  public static function get_instance() {
    if (! self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  public function frontend_display() {
    // store post type and id outside of global section query
    // to reflect real post type and id
    if ( ! is_admin() ) {
      $this->post_type = get_post_type();
      $this->post_id = get_the_id();
    } else {
      return;
    }

    $this->render_global_sections();
    $this->render_global_section_filters();
  }

  /**
   * Parse conditional statement and 
   * @param string $conditional
   * @param bool $is_included
   * @return bool
   */
  public function parse_conditional($conditional, $is_included) {
    if ( ! is_string($conditional) ) {
      return true;
    }

    $display = true;

    if( 'is_single' === $conditional ) {
      $display = is_single();
    }
    else if( 'is_archive' === $conditional ) {
      $display = is_archive();
    }
    else if( 'is_search' === $conditional ) {
      $display = is_search();
    }
    else if( 'is_front_page' === $conditional ) {
      $display = is_front_page();
    }
    else if( 'is_user_logged_in' === $conditional ) {
      $display = is_user_logged_in();
    }
    else if( 'is_user_not_logged_in' === $conditional ) {
      $display = ! is_user_logged_in();
    }
    else if( strpos($conditional, 'post_type__') !== false ) {

      $post_type = str_replace('post_type__', '', $conditional);
      if ( $this->post_type === $post_type ) {
        $display = true;
      } else {
        $display = false;
      }
    }
    else if( strpos($conditional, 'single__pt__') !== false ) {

      $post_type = str_replace('single__pt__', '', $conditional);
      if ( $this->post_type === $post_type && is_single() ) {
        $display = true;
      } else {
        $display = false;
      }
    }
    else if( strpos($conditional, 'role__') !== false ) {
      $role = str_replace('role__', '', $conditional);

      if ( current_user_can( $role ) ) {
        $display = true;
      } else {
        $display = false;
      }
    }
    else if( 'everywhere' === $conditional ) {
      $display = true;
    }

    // If excluded, short circuit and prevent display.
    if ( $is_included === false && $display ) {
      $this->exclude = true;
    }

    if ( $is_included === false && ! $this->exclude ) {
      $display = true;
    }

    return $display;
  }

  /**
   * Render Global Section
   */
  public function render_global_sections() {

    // Disabled on cpt single edit.
    if ( Global_Sections::POST_TYPE === get_post_type() ) {
      return;
    }

    $global_sections_query_args = [
      'post_type' => Global_Sections::POST_TYPE,
      'post_status' => 'publish',
      'no_found_rows' => true
    ];

    $global_sections_query = new \WP_Query( $global_sections_query_args );

    if( $global_sections_query->have_posts() ) : while( $global_sections_query->have_posts() ) : $global_sections_query->the_post();

      $global_section_id = get_the_ID();
      $post_meta = get_post_meta($global_section_id, Global_Sections::META_KEY, true);

      // Locations.
      $locations = $post_meta['locations'];
      if( empty( $locations ) || ! is_array($locations) ) {
        continue;
      }

      foreach($locations as $location) {

        $location_options = (array) $location;
        $location_hook = sanitize_text_field($location_options['location']);
        $location_priority = sanitize_text_field($location_options['priority']);

        // Verify display conditions.
        $allow_output = $this->verify_conditional_display($global_section_id);

        // Add section to hook.
        if ( $allow_output ) {
          add_action(
              $location_hook,
              function() use ( $global_section_id, $location_hook ) {
              $this->output_global_section($global_section_id, $location_hook);
            },
              $location_priority
          );

          $this->modify_salient_markup($location_hook);
        }

      } // end foreach locations.

    endwhile; endif;

    wp_reset_query();
  }

  /**
   * Conditional Logic for global section output.
   */
  public function verify_conditional_display($global_section_id) {
    // Gather and format Conditions to be used in final output below.
    $post_meta = get_post_meta($global_section_id, Global_Sections::META_KEY, true);
    $conditions = $post_meta['conditions'];
    $condition_operator = $post_meta['operator'];
    $this->exclude = false;

    // Verify display conditions.
    $conditionals = [];
    foreach($conditions as $condition) {
      $conditionals[] = $this->parse_conditional($condition['condition'], $condition['include']);
    }

    // If no conditions, allow output.
    $allow_output = empty($conditionals);

    if( $this->exclude === true ) {
      return apply_filters( 'salient_global_section_allow_display', $allow_output );
    }

    foreach ($conditionals as $condition) {
      if ($condition === true) {
        $allow_output = true;
      }
    }

    // Operator is 'and' and one of the conditions is false, prevent output.
    if ( $condition_operator === 'and' && in_array(false, $conditionals) ) {
      $allow_output = false;
    }

    return apply_filters( 'salient_global_section_allow_display', $allow_output );
  }

  /**
   * Frontend output.
   */
  public function output_global_section($global_section_id, $location) {

    if ( $this->omit_global_section_render($location) ) {
      return;
    }

    $attrs = apply_filters('nectar_global_section_attrs', [
      'class' => 'nectar-global-section ' . $location
    ], $location);

    $inner_attrs = apply_filters('nectar_global_section_inner_attrs', [
      'class' => 'container normal-container row'
    ], $location);

    $attributes = join(' ', array_map(function($key) use ($attrs) {
      if(is_bool($attrs[$key])) {
        return $attrs[$key] ? $key : '';
      }
      return $key . '="' . $attrs[$key] . '"';
    }, array_keys($attrs)));

    $inner_attributes = join(' ', array_map(function($key) use ($inner_attrs) {
      if(is_bool($inner_attrs[$key])) {
        return $inner_attrs[$key] ? $key : '';
      }
      return $key . '="' . $inner_attrs[$key] . '"';
    }, array_keys($inner_attrs)));

    $global_section_shortcode = ' [nectar_global_section id="' . intval($global_section_id) . '"] ';

    echo do_shortcode('<div ' . $attributes . '><div ' . $inner_attributes . '>' . $global_section_shortcode . '</div></div>');
  }

  public function omit_global_section_render( $hook ) {
    // No Footer Templates.
    $footer_hooks = [
      'nectar_hook_global_section_footer',
      'nectar_hook_global_section_parallax_footer',
      'nectar_hook_global_section_after_footer'
    ];
    if (
      is_page_template( 'template-no-footer.php' ||
      is_page_template( 'template-no-header-footer.php' )) && in_array( $hook, $footer_hooks )
    ) {
      return true;
    }

    // Disabled locations when using contained header.
    if ( function_exists('nectar_is_contained_header') && nectar_is_contained_header() ) {
      $contained_header_non_compat_hooks = [
        'nectar_hook_before_secondary_header',
      ];
      if ( in_array( $hook, $contained_header_non_compat_hooks ) ) {
        return true;
      }
    }

    return false;
  }

  /**
   * Frontend output markup alterations.
   */
  public function render_global_section_filters() {

    add_filter('nectar_global_section_inner_attrs', function($attrs, $location) {

      if( 'nectar_hook_global_section_parallax_footer' === $location ) {
        $attrs['class'] .= ' nectar-el-parallax-scroll';
        $attrs['data-scroll-animation'] = 'true';
        $attrs['data-scroll-animation-intensity'] = '-5';
      }

      return $attrs;
    }, 10, 3);
  }

  /**
   * Changes to Salient markup based on certain global sections being active.
   */
  public function modify_salient_markup($hook) {
    // Calculate nectar_hook_before_secondary_header height asap.
    if ( $hook === 'nectar_hook_before_secondary_header' &&
      function_exists('nectar_is_contained_header') &&
      ! nectar_is_contained_header() ) {
      add_action('nectar_hook_before_secondary_header', function(){
        echo '<script>
          var contentHeight = 0;
          var headerHooks = document.querySelectorAll(".nectar_hook_before_secondary_header");

          if( headerHooks ) {

            Array.from(headerHooks).forEach(function(el){
              contentHeight += el.getBoundingClientRect().height;
            });
          }

          document.documentElement.style.setProperty("--before_secondary_header_height", contentHeight + "px");
        </script>';
      }, 99);
    }

    // Global sections that disabled transparent header.
    $transparent_non_compat_hooks = [
      'nectar_hook_global_section_after_header_navigation',
    ];

    if( in_array( $hook, $transparent_non_compat_hooks ) ) {

      if ( function_exists('nectar_is_contained_header') && ! nectar_is_contained_header() ) {
        add_filter('nectar_activate_transparent_header', [$this,'after_header_navigation_remove_transparency'], 70);
      }
    }
  }

  public function after_header_navigation_remove_transparency() {
    return false;
  }
}
