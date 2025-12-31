<?php

namespace Nectar\Render\Blocks\PostGrid;
use Nectar\Dynamic_Data\Dynamic_Helpers;
use Nectar\Dynamic_Data\Frontend_Render;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * PostGrid Rendering
 * @version 1.2.1
 * @since 0.0.8
 */
class PostGrid {
  private $block_attributes;

  private $full_post_link;

  public $offset_compat_mode = false;

  function __construct($block_attributes, $content) {
    $this->block_attributes = $block_attributes;
    $this->full_post_link = isset($this->block_attributes['linkType']) ? $this->block_attributes['linkType'] === 'wrap-all' : false;
    $this->pre_render();
  }

  public function pre_render() {
    // Compatibility mode for offset in pagination.
    // certain orderby options, such as menu_order, don't work with offset,
    // so we need to set posts_per_page to -1 and handle the offset manually.
    if ( $this->block_attributes['orderBy'] === 'menu_order' &&
      $this->block_attributes['pagination']['enabled'] === true ) {
      $this->offset_compat_mode = true;
    }
  }

  public function nectar_excerpt( int $limit ) {
    $data = '';
    if ( has_excerpt() ) {
      $data = get_the_excerpt();
    } else {
      $data = get_the_content();
    }
    // Strip short codes, but keep short code content
    $data_replaced = preg_replace( '/\[[^\]]+\]/', '', $data );
    return wp_trim_words( $data_replaced, $limit );
  }

  public function typography_class_name($typography, $space = false) {

    $leading_space = $space ? ' ' : '';

    if ( ! $typography) {
      return '';
    }

    if (strpos($typography, 'nectar-gt') !== false) {
        return $leading_space . esc_attr($typography);
    }
    return $typography !== '' ? $leading_space . 'nectar-font-' . esc_attr($typography) : '';
  }

  public function build_args() {
    $args = [];

    if ($this->block_attributes['postType'] !== '') {
      $args['post_type'] = $this->block_attributes['postType'];
    }

    if (
      $this->block_attributes['postType'] !== '' &&
      $this->block_attributes['taxonomies']
    ) {

      $taxonomies_types = get_taxonomies( [ 'public' => true ] );
      $terms = get_terms( array_keys( $taxonomies_types ), [
        'hide_empty' => true,
        'include' => $this->block_attributes['taxonomies'],
      ] );
      $tax_queries = [];
      foreach ( $terms as $term ) {
        $tax_queries[] = [
          'taxonomy' => $term->taxonomy,
          'field' => 'id',
          'terms' => [ $term->term_id ],
          'relation' => 'IN',
        ];
      }
      $tax_queries['relation'] = 'OR';
      $args['tax_query'] = $tax_queries;
    }

    // excludeCurrentPost
    if ( $this->block_attributes['excludeCurrentPost'] === true ) {
      $args['post__not_in'] = [ get_the_ID() ];
    }

    $args['posts_per_page'] = $this->block_attributes['postsPerPage'];
    $args['offset'] = $this->block_attributes['postOffset'];
    $args['order'] = $this->block_attributes['postOrder'];
    $args['orderby'] = $this->block_attributes['orderBy'];

    return apply_filters('nectar_blocks_post_grid_query_args', $args);
  }

  public function get_author($post, $settings, $display_meta) {

    // Get typography of excerpt
    $excerpt_typo = '';
    foreach( $display_meta as $meta ) {
      if ( $meta['type'] === 'excerpt' ) {
        $excerpt_typo = $this->typography_class_name($meta['typography'], true);
      }
    }

    $output = '';

    // Output based on style
    $author_avatar = get_avatar( get_the_author_meta( 'email' ), 40, null, get_the_author() );
    $author_text = get_the_author();
    $author_with_by_text = '<span class="inherit-typography-size' . esc_attr($excerpt_typo) . '">'
      . esc_html__('By', 'nectar-blocks') . '</span> <span>' . get_the_author() . '</span>';

    $author_text_render = $author_text;
    if ( $settings['style'] === 'with-by-text' ) {
      $author_text_render = $author_with_by_text;
    }

    $author_avatar_render = $author_avatar . '<span>' . $author_text . '</span>';
    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' ' . $this->typography_class_name($settings['typography']) . '';
    }

    // No link.
    if ( $settings['link'] === 'none' || $this->full_post_link ) {
      if ( $settings['style'] === 'with-gravatar' ) {
        $author = '<span class="has-gravatar">';
        $author .= $author_avatar_render;
        $author .= '</span>';
        $output = $author;
      } else {
        $output = $author_text_render;
      }
    } else {
      // Link to author page.
      $author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
      if ( $settings['style'] === 'with-gravatar' ) {
        $author = '<a class="has-gravatar" href="' . $author_link . '">';
        $author .= $author_avatar_render;
        $author .= '</a>';
        $output = $author;
      } else {
        $author = '<a href="' . $author_link . '"> ';
        $author .= $author_text_render;
        $author .= '</a>';
        $output = $author;
      }
    }

    return '<div class="nectar-blocks-post-grid__item__author inline-meta' . $typo_class_name . '">' . $output . '</div>';

  }

  public function get_taxonomies($post, $settings) {
    $output = '';
    $assigned_terms = [];

    $tax_style = $settings['style'];
    $parent_only = $settings['display'] === 'parent-only' ? true : false;
    $enable_links = false;
    if (isset($settings['link']) &&
      $settings['link'] === true &&
      ! $this->full_post_link) {
        $enable_links = true;
    }
    $typo = $this->typography_class_name($settings['typography'], true);

    if ( $this->block_attributes['taxonomies'] ) {
      foreach($this->block_attributes['taxonomies'] as $term_id) {
        $term_data = get_term($term_id);
        if ( ! is_wp_error($term_data) && has_term($term_id, $term_data->taxonomy ) ) {

          $assigned_term = get_term($term_id);

          if ( $parent_only && $assigned_term->parent ) {
            $assigned_terms[] = get_term($assigned_term->parent);
          } else {
            $assigned_terms[] = get_term($term_id);
          }

        }
      }
    } else {

      $post_type = get_post_type($post);

      $this->block_attributes['displayMeta'];
      $user_set_taxonomy = false;
      foreach($this->block_attributes['displayMeta'] as $meta) {
        if ($meta['type'] === 'taxonomies') {
          if ( array_key_exists('taxonomy', $meta) && $meta['taxonomy'] !== 'default' ) {
            $user_set_taxonomy = $meta['taxonomy'];
          }
        }
      }

      // User set taxonomy to pull from
      if ( $user_set_taxonomy ) {
        $assigned_terms = get_the_terms($post, $user_set_taxonomy);
      }
      else if ( $post_type === 'post' ) {
        // No explicit taxonomies set, so let's get the post type's default taxonomy for common post types
        $assigned_terms = get_the_category($post);
      } else if ( $post_type === 'nectar_portfolio' ) {
        $assigned_terms = get_the_terms($post, 'portfolio_category');
      } else {
        // Make a guess at the taxonomy name
        $assigned_terms = get_the_terms(
            $post,
            apply_filters('nectar_blocks_post_grid_default_taxonomy', $post_type . '_category', $post_type)
        );
      }
    }

    if ($assigned_terms && ! is_wp_error($assigned_terms)) {
      // Make sure we don't have duplicates
      $assigned_terms = array_unique($assigned_terms, SORT_REGULAR);

      foreach ($assigned_terms as $index => $term) {
          if ($enable_links) {
              $output .= '<a class="style--' . esc_attr($tax_style) . esc_attr($typo) . '" href="' . get_term_link($term) . '">' . esc_html($term->name) . '</a>';
          } else {
              $output .= '<span class="style--' . esc_attr($tax_style) . esc_attr($typo) . '">' . esc_html($term->name) . '</span>';
          }

          // Add delimiter if not the last item and tax_style is not "button"
          if (! in_array($tax_style, ["button", "button-border"]) && $index < count($assigned_terms) - 1) {
              $output .= '<span class="delimiter">, </span>';
          }
      }

    }
    return apply_filters('nectar_blocks_post_grid_taxonomies', $output);
  }

  public function get_date($post, $settings) {
    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' ' . $this->typography_class_name($settings['typography']);
    }
    return '<div class="nectar-blocks-post-grid__item__date inline-meta' . $typo_class_name . '">' . get_the_date() . '</div>';
  }

  public function get_title($post, $settings) {
    $typo_class_name = ' class="nectar-blocks-title__text"';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' class="nectar-blocks-title__text ' . $this->typography_class_name($settings['typography']) . '"';
    }
    $allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'];
    if (array_key_exists('headingLevel', $settings) &&
      in_array($settings['headingLevel'], $allowed_tags) ) {
      $h_level = esc_attr($settings['headingLevel']);

      $markup = '';
      if ( ! $this->full_post_link ) {
        $markup = '<a href="' . get_permalink() . '">';
      }

      if( isset($this->block_attributes['postGridStyle']['hoverEffect']) &&
        ($this->block_attributes['postGridStyle']['hoverEffect'] === 'reveal' ||
        $this->block_attributes['postGridStyle']['hoverEffect'] === 'zoom-reveal') ) {
        $title = get_the_title();
        $words = explode(' ', $title);
        $word_spans = array_map(function($word, $index) {
          return '<span class="nectar-blocks-title__text__inner"><span class="nectar-blocks-title__text__inner__word" style="transition-delay: ' . esc_attr($index * 0.018) . 's;" data-text="' . esc_attr($word) . '">' . esc_html($word) . '</span></span>';
        }, $words, array_keys($words));

        $markup .= '<' . $h_level . $typo_class_name . '>' . implode(' ', $word_spans) . '</' . $h_level . '>';
      } else {
        $markup .= '<' . $h_level . $typo_class_name . '>' . get_the_title() . '</' . $h_level . '>';
      }

      if ( ! $this->full_post_link ) {
        $markup .= '</a>';
      }
      return $markup;
    }
    return false;
  }

  public function get_excerpt($post, $settings) {
    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' ' . $this->typography_class_name($settings['typography']);
    }
    $excerpt_length = intval($settings['length']);
    $excerpt = $this->nectar_excerpt( $excerpt_length );
    if ( ! empty($excerpt) ) {
      return '<p class="nectar-blocks-post-grid__item__excerpt ' . $typo_class_name . '">' . $excerpt . '</p>';
    }
    return '';
  }

  public function get_spacer($post, $settings) {
    return '<div class="nectar-blocks-post-grid__item__spacer"></div>';
  }

  public function get_estimated_read_time($post, $settings) {
    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' ' . $this->typography_class_name($settings['typography']);
    }

    $calculated_read_time = $this->calculate_read_time($post);
    return '<div class="nectar-blocks-post-grid__item__read-time inline-meta' . $typo_class_name . '">' . $calculated_read_time . ' ' . esc_html__('min read', 'nectar-blocks') . '</div>';
  }

  public function calculate_read_time($post) {
    // Get the raw post content
    $content = get_the_content($post);

    // Remove HTML comments using WordPress core pattern
    $content = preg_replace('/<!--(.*?)-->/s', '', $content);
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    $content = preg_replace('/[\r\n\t ]+/', ' ', $content);
    $content = trim($content);

    $word_count = str_word_count($content);

    // Calculate read time (assuming 180 words per minute)
    $read_time = ceil($word_count / 180);

    if ($read_time < 1) {
      $read_time = 1;
    }

    return $read_time;
  }

  public function get_read_more($post, $settings) {

    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' ' . $this->typography_class_name($settings['typography']);
    }

    $arrow = '<svg width="20" height="20" aria-hidden="true" viewBox="0 0 22 22">
      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 12.3H7.8C6 12.3 4.5 10.8 4.5 9V6.5"></path>
      <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
        <path d="m13.5 17.5 5-5M13.5 7.5l5 5"></path>
      </g>
    </svg>';

    if ( ! $this->full_post_link ) {
      $read_more = '<a class="nectar-blocks-post-grid__item__read-more' . $typo_class_name . '" href="' . get_permalink() . '">
        <span' . $typo_class_name . '>' . esc_html__('Read More', 'nectar-blocks') . '</span>'
        . $arrow .
      '</a>';
    } else {
      $read_more = '<div class="nectar-blocks-post-grid__item__read-more' . $typo_class_name . '"><span>' . esc_html__('Read More', 'nectar-blocks') . '</span>' . $arrow . '</div>';
    }

    return $read_more;
  }

  public function get_custom($post, $settings) {
    if ( ! isset($post->ID) || $post->ID === 0 || empty($settings['value'])) {
      return;
    }

    $custom_meta_key = sanitize_text_field($settings['value']);

    // Dynamic data should pas through as is
    if ( strpos( $custom_meta_key, '!!nb_dynamic/' ) !== false ) {
      $dynamic_data_parsed = Dynamic_Helpers::parse_dynamic_field($custom_meta_key);
      $custom_meta = Frontend_Render::get_dynamic_content($dynamic_data_parsed, false);
    } else {
      $custom_meta = get_post_meta($post->ID, $custom_meta_key, true);
    }

    if ( ! $custom_meta ) {
      return;
    }

    $custom_meta = apply_filters('nectar_blocks_post_grid_custom_field_value', $custom_meta, $custom_meta_key, $post->ID);

    $before_text = '';
    if (array_key_exists('beforeText', $settings) && ! empty($settings['beforeText']) ) {
      $before_text = '<span>' . esc_html($settings['beforeText']) . '</span>';
    }

    $after_text = '';
    if ( array_key_exists('afterText', $settings) && ! empty($settings['afterText']) ) {
      $after_text = '<span>' . esc_html($settings['afterText']) . '</span>';
    }

    $typo_class_name = '';
    if ( array_key_exists('typography', $settings) && ! empty($settings['typography']) ) {
      $typo_class_name = ' class="' . $this->typography_class_name($settings['typography']) . '"';
    }

    // Only wrap in span if there is before or after text
    $output = '';
    if (! empty($before_text) || ! empty($after_text)) {
      $output = '<span' . $typo_class_name . '>' .
          $before_text .
          '<span>' . do_shortcode(wp_kses_post($custom_meta)) . '</span>' .
          $after_text .
        '</span>';
    } else {
      $output = '<span' . $typo_class_name . '>' . do_shortcode(wp_kses_post($custom_meta)) . '</span>';
    }

    $custom_class = 'nectar-blocks-post-grid__item__custom';
    if (array_key_exists('display', $settings) && $settings['display'] === 'inline') {
      $custom_class .= ' inline-meta';
    }

    return '<div class="' . esc_attr($custom_class) . '">' . $output . '</div>';
  }

  public function get_portfolio_video($post) {
    $portfolio_video = get_post_meta($post->ID, '_nectar_portfolio_video', true);

    $video_url = '';

    if (isset($portfolio_video['source']['id'])) {
      $video_url = wp_get_attachment_url($portfolio_video['source']['id']);
    } else {
      if (isset($portfolio_video['source']['url'])) {
        $video_url = $portfolio_video['source']['url'];
      }
    }

    return $video_url;
  }

  public function get_featured_image($post) {
    $featured_image = '';
    if (has_post_thumbnail($post)) {
      $image_size = isset($this->block_attributes['imageSize']) ? $this->block_attributes['imageSize'] : 'full';
      $featured_image = get_the_post_thumbnail(
          $post->ID,
          $image_size,
          [
          'class' => 'nectar-blocks-post-grid__item__featured-media__item size-' . $image_size
        ]
      );
    }
    return $featured_image;
  }

  function get_pagination_markup($data) {

    $total_pages = $data['totalPages'];
    $markup = '';
    if ( $total_pages > 1 ) {

      $permalink_structure = get_option( 'permalink_structure' );
      $query_type = ( count($_GET) ) ? '&' : '?';
      $format = empty( $permalink_structure ) ? $query_type . 'paged=%#%' : 'page/%#%/';
      $current = isset($this->block_attributes['current_page']) ? $this->block_attributes['current_page'] : 1;

      $markup .= '<nav role="navigation" aria-label="' . esc_attr__("Pagination Navigation", 'nectar-blocks') . '">';

      $left_arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10.8284 12.0007L15.7782 16.9504L14.364 18.3646L8 12.0007L14.364 5.63672L15.7782 7.05093L10.8284 12.0007Z"></path></svg>';
      $right_arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.1714 12.0007L8.22168 7.05093L9.63589 5.63672L15.9999 12.0007L9.63589 18.3646L8.22168 16.9504L13.1714 12.0007Z"></path></svg>';

      $markup .= paginate_links([
        'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
        'format' => $format,
        'type' => 'list',
        'current' => $current,
        'total' => $total_pages,
        'prev_text' => $left_arrow,
        'next_text' => $right_arrow
      ]);

      $markup .= '</nav>';
    }

    return apply_filters('nectar_blocks_post_grid_pagination', $markup);
  }

  function get_animation_attrs() {
    $animation_attributes = isset($this->block_attributes['animation']) ? $this->block_attributes['animation'] : [];

    $attributes = [];
    if ( isset($animation_attributes['selector']) && ! empty($animation_attributes['selector']) ) {
      $attributes['selector'] = $animation_attributes['selector'];
    }

    if ( isset($animation_attributes['selectorMode']) && ! empty($animation_attributes['selectorMode']) ) {
      $attributes['selectorMode'] = $animation_attributes['selectorMode'];
    }

    // Handle click animations
    if (isset($animation_attributes['click']) && ! empty($animation_attributes['click'])) {
      $attributes['click'] = $animation_attributes['click'];
    }

    // Handle scroll position animations
    if (isset($animation_attributes['scrollPosition']) && ! empty($animation_attributes['scrollPosition'])) {
      $attributes['scrollPosition'] = $animation_attributes['scrollPosition'];

      // Clean up empty responsive values
      if (isset($attributes['scrollPosition']['scrollValues'])) {
        $scrollValues = &$attributes['scrollPosition']['scrollValues'];
        if (empty($scrollValues['tablet'])) {
          unset($scrollValues['tablet']);
        }
        if (empty($scrollValues['mobile'])) {
          unset($scrollValues['mobile']);
        }
      }
    }

    // Handle scroll into view animations
    if (isset($animation_attributes['scrollIntoView'])) {
      $attributes['scrollIntoView'] = $animation_attributes['scrollIntoView'];
    }

    if (empty($attributes)) {
      return '';
    }

    $animation_attrs = [
      'data-nectar-block-animation' => esc_attr(wp_json_encode($attributes))
    ];

    if (isset($animation_attributes['scrollIntoView'])) {
      $attributes['scrollIntoView'] = $animation_attributes['scrollIntoView'];

      // Add data attributes for device types
      $deviceTypes = ['desktop', 'tablet', 'mobile'];
      foreach ($deviceTypes as $deviceType) {
        if (isset($attributes['scrollIntoView']['triggerDevices']) &&
            in_array($deviceType, $attributes['scrollIntoView']['triggerDevices'])) {
          $animation_attrs["data-await-in-view-{$deviceType}"] = '';
        }
      }
    }

    return $animation_attrs;
  }

  function get_bg_animation_attrs() {
    if ( isset($this->block_attributes['postGridStyle']['bgScrollAnimation']['enabled']) &&
      $this->block_attributes['postGridStyle']['bgScrollAnimation']['enabled'] === true
    ) {

      // Helper to ensure the selector is correct for updating users pre 2.0.0
      $this->block_attributes['postGridStyle']['bgScrollAnimation']['selector'] = '.nectar-blocks-post-grid__item__featured-media__inner';

      return esc_attr(wp_json_encode($this->block_attributes['postGridStyle']['bgScrollAnimation']));
    }

    return '';
  }

  function get_bg_animation_active_viewports_attrs() {
    // Don't run this in the editor or admin context.
    $rest_context = defined( 'REST_REQUEST' ) && REST_REQUEST;
    if ( $rest_context || is_admin() ) {
      return '';
    }

    if ( isset($this->block_attributes['postGridStyle']['bgScrollAnimation']['enabled']) &&
      $this->block_attributes['postGridStyle']['bgScrollAnimation']['enabled'] === true
    ) {
      $device_types = ['desktop', 'tablet', 'mobile'];
      $data_attrs = [];
      foreach ($device_types as $device_type) {
        if ( isset($this->block_attributes['postGridStyle']['bgScrollAnimation']['triggerDevices']) &&
          in_array($device_type, $this->block_attributes['postGridStyle']['bgScrollAnimation']['triggerDevices']) ) {
          $data_attrs[] = 'data-await-in-view-' . esc_attr($device_type) . '=""';
        }
      }
      return implode(' ', $data_attrs);
    }

    return '';
  }

  function get_query_data() {

    $data = [
      'posts' => []
    ];

    // Inherit from default query when in archive template.
    if ( isset($this->block_attributes['inheritQuery']) &&
      $this->block_attributes['inheritQuery']['enable'] === true) {

      // editor context - mimic what will be archive template.
      if ( defined( 'REST_REQUEST' ) && REST_REQUEST || is_admin()) {
        $args = [
          'posts_per_page' => get_option( 'posts_per_page' ),
          'post_type' => $this->block_attributes['inheritQuery']['postType'],
        ];
        $posts_query = new \WP_Query( $args );
      } else {
        // frontend - full inherit of global query.
        $posts_query = $GLOBALS['wp_query'];
      }

      $this->block_attributes['postsPerPage'] = get_option( 'posts_per_page' );
      if ( isset($this->block_attributes['className']) ) {
        $this->block_attributes['className'] .= ' nectar-inherit-query';
      } else {
        $this->block_attributes['className'] = 'nectar-inherit-query';
      }

      $this->block_attributes['current_page'] = 1;
      if ($posts_query->get('paged')) {
        $this->block_attributes['current_page'] = $posts_query->get('paged');
      }

    } else {

      // Regular custom query.
      $args = $this->build_args();
      $posts_query = new \WP_Query( $args );
    }

     // Pagination.
     $data['pagination'] = [
      'totalPages' => $posts_query->max_num_pages,
      'totalPosts' => $posts_query->found_posts,
      'postsPerPage' => $posts_query->query_vars['posts_per_page'],
      'currentPage' => $posts_query->query_vars['paged'] ? $posts_query->query_vars['paged'] : 1,
      'nextPage' => $posts_query->query_vars['paged'] ? $posts_query->query_vars['paged'] + 1 : 2,
      'prevPage' => $posts_query->query_vars['paged'] ? $posts_query->query_vars['paged'] - 1 : 0,
    ];

    // Compatibility mode for offset in pagination.
    // We have to query all posts and then manually
    // slice the array into pagination chunks to ensure
    // the correct order.
    if ( $this->offset_compat_mode ) {

      $args['posts_per_page'] = '-1';
      $args['offset'] = 0;

      $posts_query = new \WP_Query( $args );

      $offset = $this->block_attributes['postOffset'];
      $limit = $this->block_attributes['postsPerPage'];

      // Manually slicing the posts array
      $posts_query->posts = array_slice($posts_query->posts, $offset, $limit);

      // Update post count and found posts to reflect the sliced data
      $posts_query->post_count = count($posts_query->posts);
    }

    // Posts.
    if ($posts_query->have_posts()) {
      while ( $posts_query->have_posts() ) {
        $posts_query->the_post();
        global $post;

        $active_meta = [];
        $display_meta = $this->block_attributes['displayMeta'];
        foreach( $display_meta as $settings ) {
          $type = str_replace("-", "_", $settings['type'], );
          $active_meta[] = [
            'type' => $type,
            'settings' => $settings,
            'output' => is_callable( [$this, 'get_' . $type] ) ? call_user_func([$this, 'get_' . $type], $post, $settings, $display_meta) : ''
          ];
        }

        $data['posts'][] = [
          'id' => get_the_id(),
          'portfolio_video' => $this->get_portfolio_video($post),
          'has_featured_image' => has_post_thumbnail($post) ? true : false,
          'featured_image' => $this->get_featured_image($post),
          'index' => $posts_query->current_post,
          'permalink' => get_permalink(),
          'title' => get_the_title(),
          'meta' => $active_meta
        ];
      }

      wp_reset_postdata();
    }

    return $data;
  }

  function render() {
    $loader = new FilesystemLoader(__DIR__);
    $twig = new Environment($loader);

    $query_data = $this->get_query_data();
    $post_data = $query_data['posts'];
    $pagination_data = $query_data['pagination'];

    $pagination_markup = $this->get_pagination_markup($pagination_data);
    $bg_animation_attrs = $this->get_bg_animation_attrs();
    $bg_animation_active_viewports_attrs = $this->get_bg_animation_active_viewports_attrs();
    $animation_attrs = $this->get_animation_attrs();

    if (count($post_data) > 0) {
      return $twig->render('./templates/template.twig', [
        'blockId' => $this->block_attributes['blockId'],
        'queryData' => $post_data,
        'pagination' => $pagination_markup,
        'paginationData' => $pagination_data,
        'currentPage' => isset($this->block_attributes['current_page']) ? $this->block_attributes['current_page'] : 1,
        'i18n' => [
          'load_more' => __('Load More', 'nectar-blocks'),
          'previous' => __('Previous', 'nectar-blocks'),
          'next' => __('Next', 'nectar-blocks'),
        ],
        'foundPosts' => count($post_data),
        'settings' => $this->block_attributes,
        'animationAttrs' => $animation_attrs,
        'bgAnimation' => $bg_animation_attrs,
        'bgAnimationActiveViewports' => $bg_animation_active_viewports_attrs,
        'isEditor' => defined( 'REST_REQUEST' ) && REST_REQUEST || is_admin(),
        'actionsHooks' => [
          'nectar_post_grid_item_content_before' => apply_filters('nectar_post_grid_item_content_before', ''),
          'nectar_post_grid_item_content_after' => apply_filters('nectar_post_grid_item_content_after', ''),
        ]
      ]);
    } else {
      return $twig->render('./templates/empty_posts.twig', [
        'i18n' => [
          'no_posts' => __('No posts found.', 'nectar-blocks'),
          'no_posts_desc' => __('Try adjusting your query parameters.', 'nectar-blocks')
        ],
      ]);
    }
  }
};
