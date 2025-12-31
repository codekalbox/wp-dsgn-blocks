<?php

namespace Nectar\Utilities;

class DemoUtils {
  public static function get_demos() {
    global $wp_filesystem;
    if ( empty($wp_filesystem) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    WP_Filesystem();

    $files = $wp_filesystem->dirlist(NB_IE_PLUGIN_DIR . '/includes/Demos', false);
    if ( $files === false ) {
      return false;
    }

    $files_array = [];
    foreach ($files as $file) {
      array_push($files_array, $file['name']);
    }

    return $files_array;
  }

  public static function file_path_exists($file_path) {
    global $wp_filesystem;
    if ( empty($wp_filesystem) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    WP_Filesystem();
    return $wp_filesystem->exists($file_path);
  }

  public static function read_json_file($file_path) {
    return wp_json_file_decode($file_path, [ 'associative' => true ]);
  }

  public static function run_end_function($demo_slug) {
    require_once( NB_IE_PLUGIN_DIR . '/includes/Demos/' . $demo_slug . '/utils.php' );
    if (function_exists('nectar_on_demo_success')) {
      nectar_on_demo_success();
    } else {
      error_log('Unable to find on complete');
    }
  }
}
