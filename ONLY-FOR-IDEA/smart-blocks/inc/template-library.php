<?php

class Smart_blocks_Template_Library {

	function __construct() {
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	public function register_routes() {
		register_rest_route('smart-blocks/v1', '/fetch_templates', array(
			array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => array($this, 'fetch_templates'),
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				},
			),
		));

		register_rest_route('smart-blocks/v1', '/import_template', array(
			array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => array($this, 'import_template'),
				'args' => array(
					'url' => array(
						'type' => 'string',
						'required' => true,
						'description' => __('URL of the JSON file.', 'smart-blocks'),
					),
					'preview' => array(
						'type' => 'boolean',
						'default' => false,
						'description' => __('Load for Block Preview.', 'smart-blocks'),
					),
				),
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				},
			),
		));
	}

	public function fetch_templates(\WP_REST_Request $request) {
		if (!current_user_can('edit_posts')) {
			return false;
		}

		$templates_list = array();
		$response = wp_remote_get('https://smartblocks.hashcreation.com/wp-json/v2/templates');

		if (!is_wp_error($response)) {
		    $response_dat = json_decode(wp_remote_retrieve_body($response), true);
		    if (isset($response_dat['data'])) {
		    	$templates_list = $response_dat['data'];
		    }
		}

		$templates = apply_filters('smart_block_templates', $templates_list);
		return rest_ensure_response($templates);
	}


	public function import_template($request) {
		global $wp_filesystem;
		if (!current_user_can('edit_posts')) {
			return false;
		}

		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();

		$url = $request->get_param('url');
		$preview = $request->get_param('preview');
		$site_url = get_site_url();

		if (strpos($url, $site_url) !== false) {
			$url = str_replace($site_url, ABSPATH, $url);
			if ($wp_filesystem->exists($url)) {
				$json = $wp_filesystem->get_contents($url);
			} else {
				return new \WP_Error('filesystem_error', __('File doesn\'t exist', 'smart-blocks'));
			}
		} else {
			$request = wp_remote_get($url);
			$json = wp_remote_retrieve_body($request);
		}
		$obj = json_decode($json);
		if (!isset($obj->__file) || 'wp_export' !== $obj->__file || !isset($obj->content) || $preview) {
			return rest_ensure_response($obj);
		}
		$regex = '/https?:\/\/\S+(?:png|jpg|jpeg|gif|webp)/';
		preg_match_all($regex, $obj->content, $images, PREG_SET_ORDER, 0);

		if (count($images) >= 1) {
			foreach ($images as $image) {
				$image = $image[0];
				$value = $this->import_image($image);
				if ($value) {
					$obj->content = str_replace($image, $value, $obj->content);
				}
			}
		}
		return rest_ensure_response($obj);
	}

	public function hasDangerousExtension($filename) {
	    // Extensions that should NEVER be allowed
	    $dangerous = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'pl', 'cgi', 'js', 'html', 'htm'];
	    $parts = explode('.', strtolower($filename));

	    // If there are multiple extensions, check them all
	    foreach ($parts as $ext) {
	        if (in_array($ext, $dangerous)) {
	            return true;
	        }
	    }
	    return false;
	}

	public function get_saved_image($url) {
		global $wpdb;
		$post_id = $wpdb->get_var($wpdb->prepare(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = \'_smart_blocks_image_hash\' AND `meta_value` = %s LIMIT 1;',
			sha1($url)
		));
		if ($post_id) {
			return $post_id;
		}
		return false;
	}

	public function import_image($url) {
		if ($this->hasDangerousExtension($url)) {
			return false;
		}

		$saved_image = $this->get_saved_image($url);
		if ($saved_image) {
			return wp_get_attachment_url($saved_image);
		}

		if (!function_exists('media_handle_sideload')) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$tmp = download_url($url);
		if (is_wp_error($tmp)) {
			wp_delete_file($file_array['tmp_name']);
			return $tmp;
		}
		$file_array = array(
			'name' => basename($url),
			'tmp_name' => $tmp,
		);

		// Use Fileinfo (recommended)
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $tmp);
		finfo_close($finfo);

		// Only allow specific safe image types
		$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

		if (in_array($mime, $allowed)) {
			$id = media_handle_sideload($file_array);
			if (is_wp_error($id)) {
				wp_delete_file($file_array['tmp_name']);
				return $id;
			}
			update_post_meta($id, '_smart_blocks_image_hash', sha1($url));
			$value = wp_get_attachment_url($id);
			return $value;
		}
		return false;
	}
}

new Smart_blocks_Template_Library();