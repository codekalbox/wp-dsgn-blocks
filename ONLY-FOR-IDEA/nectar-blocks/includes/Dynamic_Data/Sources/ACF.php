<?php

namespace Nectar\Dynamic_Data\Sources;

/**
 * ACF
 * @since 2.0.0
 * @version 2.0.0
 */
class ACF {
  private static $instance;

  public $render_methods;

  public static function get_instance() {
    if (! self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  function __construct() {

    $this->render_methods = [
      // General text-based fields
      'text' => 'render_general_content',
      'textarea' => 'render_general_content',
      'number' => 'render_general_content',
      'email' => 'render_general_content',
      'password' => 'render_general_content',
      'message' => 'render_general_content',
      'color_picker' => 'render_general_content',

      // Rich text editor
      'wysiwyg' => 'render_general_content_wysiwyg',

      // Link-based fields
      'page_link' => 'render_general_link',
      'url' => 'render_general_link',
      'link' => 'render_link',

      // Choice-based fields
      'select' => 'render_choice',
      'radio' => 'render_choice',
      'button_group' => 'render_choice',
      'checkbox' => 'render_checkbox',
      'true_false' => 'render_true_false',

      // Date & Time fields
      'date_picker' => 'render_date',
      'date_time_picker' => 'render_date',
      'time_picker' => 'render_date',

      // Taxonomy-related fields
      'taxonomy' => 'render_taxonomy',

      // Media fields
      'file' => 'render_file',
      'image' => 'render_image',
    ];

    // Store the field data for later rendering
    add_filter( 'nectar_blocks_dynamic_data/currentPost/fields', [ $this, 'init_acf_fields' ], 2, 3 );
    add_filter( 'nectar_blocks_dynamic_data/otherPosts/fields', [ $this, 'init_acf_fields' ], 2, 3 );

    // Render the content based on the field data
    add_filter( 'nectar_blocks_dynamic_data/currentPost/content', [ $this, 'get_content' ], 20, 3 );
    add_filter( 'nectar_blocks_dynamic_data/otherPosts/content', [ $this, 'get_content' ], 20, 3 );
  }

  public function init_acf_fields($output, $id, bool $is_editor) {
    // TODO: Why was this added? It breaks use inside query loop
    // wp_reset_query();

    if ( ! function_exists( 'acf_get_field_groups' ) ) {
      return $output;
    }

    $id_parts = explode('-', $id);
    $id_part_count = count($id_parts);

    // Handle cases with a single ID
    if ($id_part_count === 1) {
      if ($is_editor) {
        $acf_field_groups = acf_get_field_groups(['post_type' => get_post_type($id)]);
      } else {
        $acf_field_groups = acf_get_field_groups(['post_id' => $id]);
      }

      return $this->get_acf_fields_by_groups($acf_field_groups);
    }

    // Handle cases with multiple ID segments
    $post_type_slug = implode('-', array_slice($id_parts, 0, -1));

    return $this->get_acf_fields_by_groups(acf_get_field_groups(['post_type' => $post_type_slug]));
  }

  public function get_acf_fields(): array {
    if (! function_exists('acf_get_field_groups')) {
      return [];
    }

    $field_groups = acf_get_field_groups();
    if (empty($field_groups)) {
      return [];
    }

    $acf_fields = [];

    foreach ($field_groups as $field_group) {
      // Get all fields for the current field group
      $fields = acf_get_fields($field_group['key']);

      foreach ($fields as $field) {
        if ($field['type'] === 'tab') {
          continue;
        }
        // Add each field key and label to the options array
        array_push($acf_fields, [
          'value' => $field['name'],
          'label' => $field['label'],
          'type' => $field['type'],
          'group' => 'acf'
        ]);
      }
    }

    return $acf_fields;
  }

  public function get_acf_fields_by_groups( $acf_field_groups ) {
    $fields = [];

    $field_types_to_omit = [
      'accordion',
      'clone',
      'flexible_content',
      'gallery',
      'google_map',
      'group',
      'message',
      'oembed', // Misspelling is intentional
      'post_object',
      'user',
      'range',
      'relationship',
      'repeater',
      'tab',
    ];

    foreach ($acf_field_groups as $acf_field_group) {
      $acf_fields = acf_get_fields($acf_field_group['key']);

      foreach ($acf_fields as $acf_field) {
        // Skip fields that are in the omit list
        if (in_array($acf_field['type'], $field_types_to_omit, true)) {
          continue;
        }

        $fields[$acf_field['name']] = [
          'title' => $acf_field['label'],
          'group' => $acf_field_group['title'],
          'field_data' => [
            'type' => $acf_field['type'],
            'field_name' => $acf_field['name'],
            'field_type' => 'acf',
          ],
        ];
      }
    }

    return $fields;
  }

  public function get_content( $output, $args, bool $is_editor ) {

    if ( ! $this->is_acf_field( $args ) ) {
      return $output;
    }

    // full img replacement
    if( $args['replacement_type'] === 'image' ) {
      $this->render_methods['image'] = 'render_image_in_content';
    } else {
      $this->render_methods['image'] = 'render_image';
    }

    $field_type = $args['field_data']['type'];

    if ( isset( $this->render_methods[$field_type] ) ) {
      return call_user_func(
          [ $this, $this->render_methods[$field_type] ],
          $args,
          $is_editor
      );
    }

    return __( 'The field type provided is not valid.', 'nectar-blocks' );
  }

  private function is_acf_field( $args ) {
    return isset( $args['field_data']['field_type'] ) && $args['field_data']['field_type'] === 'acf';
  }

  public function standard_render( $args ) {
    if (
      ! isset( $args['field_data'], $args['field_data']['field_name'], $args['post_id'] ) ||
      empty( $args['field_data']['field_name'] ) ||
      empty( $args['post_id'] )
    ) {
      return '';
    }
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    return $field_value ? wp_kses_post( do_shortcode( $field_value ) ) : '';
  }

  public function render_general_content( $args, $is_editor ) {
    return $this->standard_render( $args );
  }

  public function render_general_content_wysiwyg( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );
    if ( $is_editor ) {
      // Editor won't be able to render arbitrary HTML inside of our span so we'll just render a placeholder.
      return __( 'This is a placeholder for the content of this field:', 'nectar-blocks' ) .
        ' <strong>' . esc_html( $args['field'] ) . '</strong>. ' .
        __( 'The full content will be rendered on the frontend.', 'nectar-blocks' );
    }
    return $field_value ? wp_kses_post( $field_value ) : '';
  }

  public function render_choice( $args, $is_editor ) {
    return $this->standard_render( $args );
  }

  public function render_checkbox( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    if ( ! is_array( $field_value ) ) {
      return '';
    }

    $field_value = array_map( 'esc_html', $field_value );

    return implode( ', ', $field_value );
  }

  public function render_date( $args, $is_editor ) {
    return $this->standard_render( $args );
  }

  public function render_image( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    $image_size = 'full';
    $attributes = [];
    if (isset($args['attributes']) && is_array($args['attributes'])) {
      $attributes = $args['attributes'];
    }
    if (isset($attributes['size'])) {
      $image_size = $attributes['size'];
    }

    // Saved as an array
    if ($field_value && is_array($field_value)) {
      $field_value = $field_value['id'];
      $field_value = wp_get_attachment_image_url($field_value, $image_size);
    }

    // Saved as an ID
    if (is_numeric($field_value)) {
      $field_value = wp_get_attachment_image_url($field_value, $image_size);
    }

    return $field_value;
  }

  public function render_image_in_content( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    $image_size = 'full';
    $attributes = [];
    if (isset($args['attributes']) && is_array($args['attributes'])) {
      $attributes = $args['attributes'];
    }

    if (isset($attributes['size'])) {
      $image_size = $attributes['size'];
    }

    // Saved as an array
    if ($field_value && is_array($field_value)) {
      $field_value = $field_value['id'];
      $field_value = wp_get_attachment_image($field_value, $image_size, false);
    }
    else if (is_numeric($field_value)) {
      // Saved as an ID
      $field_value = wp_get_attachment_image($field_value, $image_size, false);
    } else {
      // Saved as a URL
      $field_value = '<img src="' . esc_attr($field_value) . '" alt="' . esc_attr(get_the_title()) . '" />';
    }

    return $field_value;
  }

  public function render_true_false( $args, $is_editor ) {

    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    $true_text = $args['attributes']['isTrueText'] ?? '';
    $false_text = $args['attributes']['isFalseText'] ?? '';

    if ( $field_value ) {
      return wp_kses_post($true_text);
    }
    return wp_kses_post($false_text);
  }

  public function render_general_link( $args, $is_editor ) {
    return $this->standard_render( $args );
  }

  public function render_link( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );
      // Saved as an array
      if ($field_value && is_array($field_value)) {
      $field_value = $field_value['url'];
    }
    return $field_value;
  }

  public function render_file( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );
    if ($field_value && is_array($field_value)) {
      $field_value = $field_value['url'];
    }
    return $field_value;
  }

  public function render_taxonomy( $args, $is_editor ) {
    $field_value = get_field( $args['field_data']['field_name'], $args['post_id'] );

    if (empty($field_value)) {
      return '';
    }

    if (is_array($field_value)) {
      if (isset($field_value[0]) && is_object($field_value[0]) && isset($field_value[0]->name)) {
        // An array of term objects
        $term_names = array_map(fn($term) => $term->name, $field_value);
      } else {
        // Array of term IDs, fetch term objects
        foreach ($field_value as $term_id) {
          $term = get_term($term_id);
          if ($term) {
            $term_names[] = $term->name;
          }
        }
      }

      return implode(', ', $term_names);
    }

    return '';
  }
}
