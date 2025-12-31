<?php

namespace Nectar\API;

use Nectar\API\{Router, Importer_Progress};
use Nectar\Utilities\{Log, DemoUtils, ImportUtils};

/**
 * Import Export API
 * @version 0.1.5
 * @since 0.1.5
 */
class Core_Import_Export_API {
  const API_BASE = '/import-export';

  public function build_routes() {
    if ( ! class_exists('Nectar\API\Router')) {
      return;
    }

    Router::add_route($this::API_BASE . '/core/import', [
      'callback' => [$this, 'core_import'],
      'methods' => 'POST',
      'permission_callback' => function() {
        if ( is_user_logged_in() ) {
          return current_user_can('manage_options');
        }
        return false;
      }
    ]);
    Router::add_route($this::API_BASE . '/core/import/status', [
      'callback' => [$this, 'import_status'],
      'methods' => 'GET',
      'permission_callback' => function() {
        if ( is_user_logged_in() ) {
          return current_user_can('manage_options');
        }
        return false;
      }
    ]);
  }

  public function import_status(\WP_REST_Request $request) {
    $importer_progress = Importer_Progress::get_instance();
    $count = $importer_progress->get_count();

    return new \WP_REST_Response([
      'status' => 'success',
      'counts' => $count
    ], 200);
  }

  public function upload_svg_files( $allowed ) {
    if ( ! current_user_can( 'manage_options' ) )
        return $allowed;
    $allowed['svg'] = 'image/svg+xml';
    return $allowed;
  }

  public function core_import(\WP_REST_Request $request) {
    require_once ABSPATH . 'wp-admin/includes/import.php';
    if ( ! class_exists( 'WP_Importer' ) ) {
      $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
      if ( file_exists( $class_wp_importer ) ) {
        require $class_wp_importer;
      }
    }
    require_once NB_IE_PLUGIN_DIR . '/includes/WP_Import/parsers/class-wxr-parser.php';
    require_once NB_IE_PLUGIN_DIR . '/includes/WP_Import/parsers/class-wxr-parser-simplexml.php';
    require_once NB_IE_PLUGIN_DIR . '/includes/WP_Import/parsers/class-wxr-parser-xml.php';
    require_once NB_IE_PLUGIN_DIR . '/includes/WP_Import/parsers/class-wxr-parser-regex.php';
    require_once NB_IE_PLUGIN_DIR . '/includes/WP_Import/class-wp-import.php';

    if ( ! is_admin() ) {
      require_once( ABSPATH . 'wp-admin/includes/post.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/taxonomy.php');
      require_once( ABSPATH . 'wp-admin/includes/media.php');
      require_once( ABSPATH . 'wp-admin/includes/comment.php');
    } else {
      return new \WP_REST_Response([ 'status' => 'failure'], 200);
    }
    // Make sure these hooks are alive
    Importer_Progress::get_instance();

    $params = $request->get_body_params();
    Log::debug('params', $params);
    $demo_slug = $params['demo'];

    $demos = DemoUtils::get_demos();
    if ( ! $demos || ! in_array($demo_slug, $demos) ) {
      return new \WP_REST_Response([
        'status' => 'failure',
        'message' => 'Unable to find demo'
      ], 200);
    }

    $options = json_decode($params['options'], true);

    add_filter( 'upload_mimes', [$this, 'upload_svg_files']);

    ImportUtils::run_full_import($demo_slug, $options);

    $import_success = true;
    if ($import_success) {
      $response = new \WP_REST_Response([ 'status' => 'success'], 200);
    } else {
      $response = new \WP_REST_Response([ 'status' => 'failure'], 200);
    }

    remove_filter( 'upload_mimes', [$this, 'upload_svg_files']);

    return $response;
  }
}