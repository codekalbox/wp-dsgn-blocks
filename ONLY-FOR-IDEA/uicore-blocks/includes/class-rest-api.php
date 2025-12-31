<?php

namespace UiCoreBlocks;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST_API Handler
 */
class Api
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'add_route']);
    }

    public function add_route()
    {
        register_rest_route('uicore-blocks/v1', 'settings', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'settings_update'],
        ]);

        //global microservice
        register_rest_route('uicore-blocks/v1', 'globals', [
            'methods' => 'GET',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'get_globals'],
        ]);
        register_rest_route('uicore-blocks/v1', 'globals', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'set_globals'],
        ]);

        // Quick Actions
        register_rest_route('uicore-blocks/v1', 'quick-actions', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'set_quick_actions'],
        ]);
        register_rest_route('uicore-blocks/v1', 'quick-actions', [
            'methods' => 'GET',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'get_quick_actions'],
        ]);

        //google fonts microservice
        register_rest_route('uicore-blocks/v1', 'get-editor-fonts', [
            'methods' => 'GET',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'get_editor_fonts'],
        ]);

        //Save styles
        register_rest_route('uicore-blocks/v1', 'save-styles', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'save_styles'],
        ]);

        //Submit form
        register_rest_route('uicore-blocks/v1', 'form-submission', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'show_in_index' => false,
            'callback' => [$this, 'handle_form_submission'],
        ]);

        //import images for templates
        register_rest_route('uicore-blocks/v1', 'import-images', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'import_images'],
        ]);

        //import images for templates
        register_rest_route('uicore-blocks/v1', 'post-no-comments', [
            'methods' => 'POST',
            'permission_callback' => [$this, 'check_for_permission'],
            'show_in_index' => false,
            'callback' => [$this, 'get_post_no_comments'],
        ]);
    }


    public function check_for_permission()
    {
        return current_user_can('manage_options');
    }

    public function settings_update(\WP_REST_Request $request)
    {
        $name = $request->get_param('name');
        $value = $request->get_param('value');

        $new = Settings::update_option($name, $value);

        return new \WP_REST_Response([
            'new' => $new,
            'success' => true,
            'message' => 'Settings updated successfully!',
        ]);
    }

    /**
     * Retrieves the global settings from the database.
     *
     * @param \WP_REST_Request $request The REST request object.
     * @return array The global settings.
     */
    public function get_globals(\WP_REST_Request $request)
    {
        //TEMP: REQUIRE SYNC
        if (\class_exists('\UiCore\Settings')) {
            $settings = \UiCore\Settings::current_settings();
        } else {
            $settings = Settings::get_global_options();
        }

        $globals = [
            'colors' => [
                [
                    'color' => $settings['pColor'],
                    'variable' => 'var(--uicore-primary-color)',
                    'name' => 'Primary',
                ],
                [
                    'color' => $settings['sColor'],
                    'variable' => 'var(--uicore-secondary-color)',
                    'name' => 'Secondary',
                ],
                [
                    'color' => $settings['aColor'],
                    'variable' => 'var(--uicore-accent-color)',
                    'name' => 'Accent',
                ],
                [
                    'color' => $settings['hColor'],
                    'variable' => 'var(--uicore-headline-color)',
                    'name' => 'Heading',
                ],
                [
                    'color' => $settings['bColor'],
                    'variable' => 'var(--uicore-body-color)',
                    'name' => 'Body',
                ],
                [
                    'color' => $settings['dColor'],
                    'variable' => 'var(--uicore-dark-color)',
                    'name' => 'Dark',
                ],
                [
                    'color' => $settings['lColor'],
                    'variable' => 'var(--uicore-light-color)',
                    'name' => 'Light',
                ],
                [
                    'color' => $settings['wColor'],
                    'variable' => 'var(--uicore-white-color)',
                    'name' => 'White',
                ],
            ],
            'fonts' => [
                'globals/primary' => [
                    'name' => 'Primary',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['pFont']['f'],
                            'fontWeight' => self::extract_font_weight($settings['pFont']),
                            'fontStyle' => self::extract_font_style($settings['pFont']),
                        ],
                    ]
                ],
                'globals/secondary' => [
                    'name' => 'Secondary',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['sFont']['f'],
                            'fontWeight' => self::extract_font_weight($settings['sFont']),
                            'fontStyle' => self::extract_font_style($settings['sFont']),
                        ],
                    ]
                ],
                'globals/text' => [
                    'name' => 'Text',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['tFont']['f'],
                            'fontWeight' => self::extract_font_weight($settings['tFont']),
                            'fontStyle' => self::extract_font_style($settings['tFont']),
                        ],
                    ]
                ],
                'globals/accent' => [
                    'name' => 'Accent',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['aFont']['f'],
                            'fontWeight' => self::extract_font_weight($settings['aFont']),
                            'fontStyle' => self::extract_font_style($settings['aFont']),
                        ],
                    ]
                ],
                'globals/typo/h1' => [
                    'name' => 'Global H1',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h1']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h1']),
                            'fontStyle' => self::extract_font_style($settings['h1']),
                        ],
                        'textTransform' => $settings['h1']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h1']['s']['d'],
                            'tablet' => $settings['h1']['s']['t'],
                            'mobile' => $settings['h1']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h1']['h']['d'],
                            'tablet' => $settings['h1']['h']['t'],
                            'mobile' => $settings['h1']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h1']['ls']['d'],
                            'tablet' => $settings['h1']['ls']['t'],
                            'mobile' => $settings['h1']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h1']['c']),
                    ],
                ],
                'globals/typo/h2' => [
                    'name' => 'Global H2',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h2']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h2']),
                            'fontStyle' => self::extract_font_style($settings['h2']),
                        ],
                        'textTransform' => $settings['h2']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h2']['s']['d'],
                            'tablet' => $settings['h2']['s']['t'],
                            'mobile' => $settings['h2']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h2']['h']['d'],
                            'tablet' => $settings['h2']['h']['t'],
                            'mobile' => $settings['h2']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h2']['ls']['d'],
                            'tablet' => $settings['h2']['ls']['t'],
                            'mobile' => $settings['h2']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h2']['c']),
                    ],
                ],
                'globals/typo/h3' => [
                    'name' => 'Global H3',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h3']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h3']),
                            'fontStyle' => self::extract_font_style($settings['h3']),
                        ],
                        'textTransform' => $settings['h3']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h3']['s']['d'],
                            'tablet' => $settings['h3']['s']['t'],
                            'mobile' => $settings['h3']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h3']['h']['d'],
                            'tablet' => $settings['h3']['h']['t'],
                            'mobile' => $settings['h3']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h3']['ls']['d'],
                            'tablet' => $settings['h3']['ls']['t'],
                            'mobile' => $settings['h3']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h3']['c']),
                    ],
                ],
                'globals/typo/h4' => [
                    'name' => 'Global H4',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h4']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h4']),
                            'fontStyle' => self::extract_font_style($settings['h4']),
                        ],
                        'textTransform' => $settings['h4']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h4']['s']['d'],
                            'tablet' => $settings['h4']['s']['t'],
                            'mobile' => $settings['h4']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h4']['h']['d'],
                            'tablet' => $settings['h4']['h']['t'],
                            'mobile' => $settings['h4']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h4']['ls']['d'],
                            'tablet' => $settings['h4']['ls']['t'],
                            'mobile' => $settings['h4']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h4']['c']),
                    ],
                ],
                'globals/typo/h5' => [
                    'name' => 'Global H5',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h5']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h5']),
                            'fontStyle' => self::extract_font_style($settings['h5']),
                        ],
                        'textTransform' => $settings['h5']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h5']['s']['d'],
                            'tablet' => $settings['h5']['s']['t'],
                            'mobile' => $settings['h5']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h5']['h']['d'],
                            'tablet' => $settings['h5']['h']['t'],
                            'mobile' => $settings['h5']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h5']['ls']['d'],
                            'tablet' => $settings['h5']['ls']['t'],
                            'mobile' => $settings['h5']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h5']['c']),
                    ],
                ],
                'globals/typo/h6' => [
                    'name' => 'Global H6',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['h6']['f'],
                            'fontWeight' => self::extract_font_weight($settings['h6']),
                            'fontStyle' => self::extract_font_style($settings['h6']),
                        ],
                        'textTransform' => $settings['h6']['t'],
                        'fontSize' => [
                            'desktop' => $settings['h6']['s']['d'],
                            'tablet' => $settings['h6']['s']['t'],
                            'mobile' => $settings['h6']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['h6']['h']['d'],
                            'tablet' => $settings['h6']['h']['t'],
                            'mobile' => $settings['h6']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['h6']['ls']['d'],
                            'tablet' => $settings['h6']['ls']['t'],
                            'mobile' => $settings['h6']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['h6']['c']),
                    ],
                ],
                'globals/typo/p' => [
                    'name' => 'Global P',
                    'settings' => [
                        'font' => [
                            'fontFamily' => $settings['p']['f'],
                            'fontWeight' => self::extract_font_weight($settings['p']),
                            'fontStyle' => self::extract_font_style($settings['p']),
                        ],
                        'textTransform' => $settings['p']['t'],
                        'fontSize' => [
                            'desktop' => $settings['p']['s']['d'],
                            'tablet' => $settings['p']['s']['t'],
                            'mobile' => $settings['p']['s']['m'],
                        ],
                        'lineHeight' => [
                            'desktop' => $settings['p']['h']['d'],
                            'tablet' => $settings['p']['h']['t'],
                            'mobile' => $settings['p']['h']['m'],
                        ],
                        'letterSpacing' => [
                            'desktop' => $settings['p']['ls']['d'],
                            'tablet' => $settings['p']['ls']['t'],
                            'mobile' => $settings['p']['ls']['m'],
                        ],
                        'color' => self::extract_color($settings['p']['c']),
                    ],
                ],
            ],
            'layout' => [
                'containerWidth' => [
                    'value' => $settings['gen_full_w'],
                    'unit' => 'px'
                ],
            ]
        ];

        return new \WP_REST_Response([
            'success' => true,
            'globals' => $globals,
        ]);
    }

    function set_globals(\WP_REST_Request $request)
    {
        $globals = $request->get_param('globals');

        $new_settings = [];

        // Transform color settings back to original format
        foreach ($globals['layout'] as $key => $value) {
            switch ($key) {
                case 'containerWidth':
                    $new_settings['gen_full_w'] = $value['value'];
                    break;
            }
        }

        foreach ($globals['colors'] as $color) {
            switch ($color['name']) {
                case 'Primary':
                    $new_settings['pColor'] = $color['color'];
                    break;
                case 'Secondary':
                    $new_settings['sColor'] = $color['color'];
                    break;
                case 'Accent':
                    $new_settings['aColor'] = $color['color'];
                    break;
                case 'Heading':
                    $new_settings['hColor'] = $color['color'];
                    break;
                case 'Body':
                    $new_settings['bColor'] = $color['color'];
                    break;
                case 'Dark':
                    $new_settings['dColor'] = $color['color'];
                    break;
                case 'Light':
                    $new_settings['lColor'] = $color['color'];
                    break;
                case 'White':
                    $new_settings['wColor'] = $color['color'];
                    break;
            }
        }

        // Transform font settings back to original format
        //TODO: check if format is correct
        foreach ($globals['fonts'] as $key => $font) {
            $font_settings = $font['settings'];
            $font_key = str_replace('globals/', '', $key);
            $font_key = str_replace('typo/', '', $font_key);
            $font_data = [
                'f' => $font_settings['font']['fontFamily'],
                'st' => $font_settings['font']['fontWeight'] . ($font_settings['font']['fontStyle'] == 'normal' ? '' : 'italic'),
            ];
            /// Check if the font is a typo font
            if (strpos($key, 'typo') !== false) {
                $font_data['t'] = $font_settings['textTransform'];
                $font_data['s'] = [
                    'd' => $font_settings['fontSize']['desktop'],
                    't' => $font_settings['fontSize']['tablet'],
                    'm' => $font_settings['fontSize']['mobile'],
                ];
                $font_data['h'] = [
                    'd' => $font_settings['lineHeight']['desktop'],
                    't' => $font_settings['lineHeight']['tablet'],
                    'm' => $font_settings['lineHeight']['mobile'],
                ];
                $font_data['ls'] = [
                    'd' => $font_settings['letterSpacing']['desktop'],
                    't' => $font_settings['letterSpacing']['tablet'],
                    'm' => $font_settings['letterSpacing']['mobile'],
                ];
                $font_data['c'] = self::extract_color($font_settings['color'], true);
            }
            switch ($font_key) {
                case 'primary':
                    $new_settings['pFont'] = $font_data;
                    break;
                case 'secondary':
                    $new_settings['sFont'] = $font_data;
                    break;
                case 'text':
                    $new_settings['tFont'] = $font_data;
                    break;
                case 'accent':
                    $new_settings['aFont'] = $font_data;
                    break;
                default:
                    $new_settings[$font_key] = $font_data;
                    break;
            }
        }

        // Update the global settings
        if (\class_exists('\UiCore\Settings')) {
            \UiCore\Settings::update_settings($new_settings);
        } else {
            Settings::update_global_options($new_settings);
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Global settings updated successfully!',
            'globals' => $new_settings,
        ]);
    }

    public function set_quick_actions(\WP_REST_Request $request)
    {
        $quick_actions = $request->get_param('quick_actions');
        update_option('uicore_blocks_quick_actions', $quick_actions, false);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Quick actions saved successfully!',
        ]);
    }

    public function get_quick_actions(\WP_REST_Request $request)
    {
        $quick_actions = get_option('uicore_blocks_quick_actions', [
            "uicore/container",
            "uicore/heading",
            "uicore/paragraph",
            "uicore/button",
            "uicore/icon",
            "uicore/image",
        ]);

        return new \WP_REST_Response([
            'success' => true,
            'quick_actions' => $quick_actions,
        ]);
    }

    static function extract_font_weight($font_data)
    {
        if (preg_match('/400|regular|normal/', $font_data['st'])) {
            return 'regular';
        } else {
            if (strlen(str_replace('italic', '', $font_data['st'])) < 2) {
                return 'regular';
            } else {
                return str_replace('italic', '', $font_data['st']);
            }
        }
    }

    static function extract_font_style($font_data)
    {
        if ((strpos($font_data['st'], 'italic') !== false)) {
            return 'italic';
        } else {
            return 'normal';
        }
    }

    function extract_color($color, $reverse = false)
    {
        $color_map = [
            'Primary' => 'var(--uicore-primary-color)',
            'Secondary' => 'var(--uicore-secondary-color)',
            'Accent' => 'var(--uicore-accent-color)',
            'Headline' => 'var(--uicore-headline-color)',
            'Body' => 'var(--uicore-body-color)',
            'Dark Neutral' => 'var(--uicore-dark-color)',
            'Light Neutral' => 'var(--uicore-light-color)',
            'White' => 'var(--uicore-white-color)',
        ];

        if ($reverse) {
            $color_map = array_flip($color_map);
        }

        if (!is_string($color) && (isset($color['type']) || isset($color['blur']))) {
            $color = $color['color'];
        }

        return $color_map[$color] ?? $color;
    }


    public function get_editor_fonts(\WP_REST_Request $request)
    {
        $base_fonts = Fonts::get_editor_fonts();
        return new \WP_REST_Response([
            'success' => true,
            'base_fonts' => $base_fonts,
        ]);
    }

    public function save_styles(\WP_REST_Request $request)
    {
        $styles = $request->get_param('styles');
        $fonts = $request->get_param('fonts');
        $preloadImages = $request->get_param('preloadImages');
        $post_id_or_widget = $request->get_param('post_id');

        try {
            BlocksSave::save_styles_for_post_or_widget($post_id_or_widget, $styles, $fonts, $preloadImages);

            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Styles saved successfully!',
                'styles' => $styles,
                'fonts' => $fonts,
                'preloadImages' => $preloadImages,
            ]);
        } catch (WP_Error $e) {
            $data   = $e->get_error_data();
            $status = isset($data['status']) ? (int) $data['status'] : 400;

            return new WP_REST_Response([
                'success' => false,
                'code'    => $e->get_error_code(),
                'message' => $e->get_error_message(),
            ], $status);
        }
    }

    /**
     * Handle incoming form submission request.
     *
     * Extracts form data and block settings, maps camelCase attributes to
     * snake_case, instantiates the Contact_Form_Service, and returns its result.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response        The response wrapping success, status, and data.
     */
    public function handle_form_submission(WP_REST_Request $request): WP_REST_Response
    {
        $params   = $request->get_json_params();
        $files    = $request->get_file_params();
        $block_id = isset($params['blockId']) ? sanitize_text_field($params['blockId']) : '';
        $post_id  = isset($params['postId'])  ? intval($params['postId']) : 0;

        if (!$block_id || !$post_id) {
            return rest_ensure_response([
                'success' => false,
                'data'    => ['message' => 'Missing blockId or postId.'],
            ]);
        }

        $content = get_post_field('post_content', $post_id);
        $blocks  = parse_blocks($content);
        $block   = Helper::find_block_by_id($blocks, $block_id);

        if (!$block) {
            return rest_ensure_response([
                'success' => false,
                'data'    => ['message' => 'Form block not found.'],
            ]);
        }

        // Convert block attrs from camelCase to snake_case
        $settings = [];
        foreach ($block['attrs'] as $rawKey => $rawVal) {
            $settings[Helper::camel_to_snake_with_number($rawKey)] = $rawVal;
        }

        // Normalize 'actions' to 'submit_actions'
        if (!empty($settings['actions']) && is_array($settings['actions'])) {
            $settings['submit_actions'] = array_map(
                [Helper::class, 'camel_to_snake_with_number'],
                $settings['actions']
            );
            unset($settings['actions']);
        }

        // Sanitize and convert form input field names
        $form_fields = [];
        foreach ($params['formData'] ?? [] as $rawKey => $value) {
            $sKey = sanitize_text_field($rawKey);
            $form_fields[$sKey] = is_array($value)
                ? array_map('sanitize_text_field', $value)
                : sanitize_text_field($value);
        }

        // Server-side validation via Helper
        $validation_errors = Helper::validate_fields($form_fields);
        if (!empty($validation_errors)) {
            return rest_ensure_response([
                'success' => false,
                'status'  => 'error',
                'data'    => ['validation_errors' => $validation_errors],
            ]);
        }

        // Set defaults to settings if not provided
        $settings['form_metadata']        = [];
        $settings['form_metadata_2']      = [];
        $settings['custom_messages']      = 'no';
        $settings['email_content_type']   = $settings['email_content_type'] ?? 'html';
        $settings['email_content_type_2'] = $settings['email_content_type_2'] ?? 'html';
        $settings['email_content']        = $settings['email_content'] ?? '[all-fields]';
        $settings['email_content_2']      = $settings['email_content_2'] ?? '[all-fields]';
        $settings['form_fields']          = array_map(
            fn($key) => ['custom_id' => sanitize_text_field($key)],
            array_keys($params['formData'] ?? [])
        );

        // Set MailChimp values if audience_id is provided                                   
        if (in_array('mailchimp', $settings['submit_actions']) && $settings['audience_id']) {
            $settings['mailchimp_audience_id'] = $settings['audience_id'] ?? '';

            //map MailChimp Fields
            if (!empty($settings['service_mapping'])) {
                foreach ($settings['service_mapping'] as $f) {
                    preg_match('/\[field id="([^"]+)"\]/', $f['fField'], $g);

                    if (isset($g[1]) && isset($form_fields[$g[1]])) {
                        $s = strtolower($f['sField']);
                        $settings["mailchimp_{$s}_id"] = $g[1];
                    }
                }

                unset($settings['service_mapping']);
            }
        }

        // Build form_data array for service
        $form_data = [
            'widget_type'        => $block['blockName'] === 'uicore/newsletter'
                ? 'newsletter'
                : 'contact-form',
            'form_fields'        => $form_fields,
            'grecaptcha_token'   => sanitize_text_field($params['grecaptcha_token'] ?? ''),
            'grecaptcha_version' => sanitize_text_field($block['attrs']['recaptchaVersion'] ?? 'V2'),
            'ui-e-h-p'           => sanitize_text_field($params['ui-e-h-p'] ?? ''),
        ];

        try {
            $service = new Contact_Form_Service($form_data, $settings, $files);
            $result  = $service->handle();
            $success = $result['status'] === 'success';
        } catch (\Exception $e) {
            $success = false;
            $result  = ['status' => 'error', 'data' => ['message' => $e->getMessage()]];
        }

        return rest_ensure_response([
            'success' => $success,
            'status'  => $result['status'],
            'data'    => $result['data'],
        ]);
    }

    function import_images(\WP_REST_Request $request)
    {
        $template = $request->get_param('template');

        //if is not html template, convert to json
        if (\is_array($template)) {
            $template = json_encode($template);
        }

        try {
            $template = Helper::process_images_in_template($template);
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Images imported successfully!',
                'data' => $template,
            ]);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Error importing images: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_post_no_comments(\WP_REST_Request $request): WP_REST_Response
    {
        $post_id = $request->get_param('postId');

        if (!get_post($post_id)) {
            return new \WP_Error('invalid_post', 'Post not found', ['status' => 404]);
        }

        $count = get_comments_number($post_id);

        return new \WP_REST_Response([
            'success' => true,
            'comment_count' => $count,
        ]);
    }
}
