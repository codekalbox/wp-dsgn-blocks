<?php

namespace Nectar\API;

/**
 * Import Progress API
 * @version 1.2.0
 * @since 1.2.0
 */
class Importer_Progress {
  public static $instance;

  protected $parent;

  public $total_posts;

  /**
   * Class Constructor
   *
   * @since       1.0
   * @access      public
   * @return      void
   */
  public function __construct() {
    self::$instance = $this;

    add_action( 'wp_import_posts', [ $this, 'progress_init' ] );
    add_action( 'import_end', [ $this, 'progress_end'  ]);

    add_action( 'add_attachment', [ $this, 'update_count' ] );
    add_action( 'edit_attachment', [ $this, 'update_count' ] );
    add_action( 'wp_insert_post', [ $this, 'update_count' ] );

    add_filter( 'wp_import_post_data_raw', [ $this, 'check_post' ] );
  }

  /**
   * Sets post count option
   * @param  array $posts Post array
   */
  public function progress_init( $posts ) {

    $progress_array = [
      'total_post' => count( $posts ),
      'imported_count' => 0,
      'remaining' => count( $posts )
    ];

    update_option( 'wbc_import_progress', $progress_array );

    return $posts;
  }

  /**
   * Sets post count option to completed incase we have extra responses/
   */
  public function progress_end() {
    $post_count = get_option( 'wbc_import_progress' );

    if ( is_array( $post_count ) ) {

      $progress_array = [
        'total_post' => $post_count['total_post'],
        'imported_count' => $post_count['total_post'],
        'remaining' => 0
      ];

      update_option( 'wbc_import_progress', $progress_array );
    }
  }

  /**
   * Checks if posts already exists or post types missing
   *
   * @param array   $post post to be imported
   */
  public function check_post( $post ) {

    if ( ! post_type_exists( $post['post_type'] ) ) {
      $this->update_count();
      return $post;
    }

    if ( $post['status'] == 'auto-draft' ) {
      $this->update_count();
      return $post;
    }

    if ( 'nav_menu_item' == $post['post_type'] ) {
      $this->update_count();
      return $post;
    }

    $post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
    if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
      $this->update_count();
      return $post;
    }

    return $post;
  }

  /**
   * Update post count totals
   */
  public function update_count() {
    $post_count = get_option( 'wbc_import_progress' );

    if ( is_array( $post_count ) ) {
      if ( $post_count['remaining'] > 0 ) {
        $post_count['remaining'] = $post_count['remaining'] - 1;
        $post_count['imported_count'] = $post_count['imported_count'] + 1;
        update_option( 'wbc_import_progress', $post_count );
      } else {
        $post_count['remaining'] = 0;
        $post_count['imported_count'] = $post_count['total_post'];
        update_option( 'wbc_import_progress', $post_count );
      }
    }
  }

  // Returns post count array
  public function get_count() {
    return get_option( 'wbc_import_progress' );
  }

  public static function get_instance() {
    if ( ! self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;
  }
}
