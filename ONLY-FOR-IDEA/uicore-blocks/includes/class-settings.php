<?php

namespace UiCoreBlocks;

defined('ABSPATH') || exit();

/**
 * UiCore Utils Functions
 */
class Settings
{

    private static $instance;
    private static $module_name = 'uicore_blocks_options';
    private static $frontend_module_name = 'uicore_blocks_front_options';
    private static $global_module_name = 'uicore_blocks_global_options';

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor function to initialize hooks
     *
     * @return void
     */
    public function __construct()
    {
        \add_filter('uicore_extra_settings', [$this, 'extra_settings']);
    }

    /**
     * Adds extra settings to the given list of options.
     *
     * @param array $list The list of options to add the extra settings to.
     * @return array The updated list of options with the extra settings.
     */
    function extra_settings($list)
    {
        return \wp_parse_args(
            $list,
            [
                self::$module_name => self::get_default_settings(),
                self::$frontend_module_name => self::get_default_frontend_settings(),
            ]
        );
    }

    static function get_default_global_settings()
    {
        $list = [
            'gen_full_w'    => '1200',
            'pColor'        => '#00C49A',
            'sColor'        => '#532DF5',
            'aColor'        => '#D1345B',
            'hColor'        => '#070707',
            'bColor'        => '#6E7A84',
            'dColor'        => '#070707',
            'lColor'        => '#F8FCFC',
            'wColor'        => '#FFFFFF',
            'aFont' => [
                'f' => 'Inter',
                'st' => '600',
            ],
            'pFont' => [
                'f' => 'Inter',
                'st' => 'regular',
            ],
            'tFont' => [
                'f' => 'Inter',
                'st' => 'regular',
            ],
            'sFont' => [
                'f' => 'Inter',
                'st' => 'regular',
            ],

            'h1' => [
                'f' => 'Primary',
                's' => [
                    'd' => ['value' => '72', 'unit' => 'px'],
                    't' => ['value' => '60', 'unit' => 'px'],
                    'm' => ['value' => '40', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.2', 'unit' => 'em'],
                    't' => ['value' => '1.2', 'unit' => 'em'],
                    'm' => ['value' => '1.2', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.027', 'unit' => 'em'],
                    't' => ['value' => '-0.027', 'unit' => 'em'],
                    'm' => ['value' => '-0.027', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => '600',
                'c' => 'Headline',
            ],
            'h2' => [
                'f' => 'Secondary',
                's' => [
                    'd' => ['value' => '48', 'unit' => 'px'],
                    't' => ['value' => '34', 'unit' => 'px'],
                    'm' => ['value' => '26', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.175', 'unit' => 'em'],
                    't' => ['value' => '1.175', 'unit' => 'em'],
                    'm' => ['value' => '1.175', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.027', 'unit' => 'em'],
                    't' => ['value' => '-0.027', 'unit' => 'em'],
                    'm' => ['value' => '-0.027', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => '700',
                'c' => 'Headline',
            ],
            'h3' => [
                'f' => 'Primary',
                's' => [
                    'd' => ['value' => '24', 'unit' => 'px'],
                    't' => ['value' => '21', 'unit' => 'px'],
                    'm' => ['value' => '20', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.2', 'unit' => 'em'],
                    't' => ['value' => '1.2', 'unit' => 'em'],
                    'm' => ['value' => '1.2', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.027', 'unit' => 'em'],
                    't' => ['value' => '-0.027', 'unit' => 'em'],
                    'm' => ['value' => '-0.027', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => '600',
                'c' => 'Headline',
            ],
            'h4' => [
                'f' => 'Primary',
                's' => [
                    'd' => ['value' => '21', 'unit' => 'px'],
                    't' => ['value' => '18', 'unit' => 'px'],
                    'm' => ['value' => '16', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.42', 'unit' => 'em'],
                    't' => ['value' => '1.42', 'unit' => 'em'],
                    'm' => ['value' => '1.42', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.027', 'unit' => 'em'],
                    't' => ['value' => '-0.027', 'unit' => 'em'],
                    'm' => ['value' => '-0.027', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => '600',
                'c' => 'Headline',
            ],
            'h5' => [
                'f' => 'Primary',
                's' => [
                    'd' => ['value' => '16', 'unit' => 'px'],
                    't' => ['value' => '15', 'unit' => 'px'],
                    'm' => ['value' => '14', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.187', 'unit' => 'em'],
                    't' => ['value' => '1.187', 'unit' => 'em'],
                    'm' => ['value' => '1.187', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.015', 'unit' => 'em'],
                    't' => ['value' => '-0.015', 'unit' => 'em'],
                    'm' => ['value' => '-0.015', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => '600',
                'c' => 'Accent',
            ],
            'h6' => [
                'f' => 'Primary',
                's' => [
                    'd' => ['value' => '14', 'unit' => 'px'],
                    't' => ['value' => '13', 'unit' => 'px'],
                    'm' => ['value' => '12', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.2', 'unit' => 'em'],
                    't' => ['value' => '1.2', 'unit' => 'em'],
                    'm' => ['value' => '1.2', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '-0.027', 'unit' => 'em'],
                    't' => ['value' => '-0.027', 'unit' => 'em'],
                    'm' => ['value' => '-0.027', 'unit' => 'em'],
                ],
                't' => 'Uppercase',
                'st' => '600',
                'c' => 'Headline',
            ],
            'p' => [
                'f' => 'Text',
                's' => [
                    'd' => ['value' => '16', 'unit' => 'px'],
                    't' => ['value' => '15', 'unit' => 'px'],
                    'm' => ['value' => '14', 'unit' => 'px'],
                ],
                'h' => [
                    'd' => ['value' => '1.875', 'unit' => 'em'],
                    't' => ['value' => '1.875', 'unit' => 'em'],
                    'm' => ['value' => '1.875', 'unit' => 'em'],
                ],
                'ls' => [
                    'd' => ['value' => '0', 'unit' => 'em'],
                    't' => ['value' => '0', 'unit' => 'em'],
                    'm' => ['value' => '0', 'unit' => 'em'],
                ],
                't' => 'None',
                'st' => 'regular',
                'c' => 'Body',
            ],
        ];
        return $list;
    }

    /**
     * Retrieves the default settings for the UICore Blocks plugin.
     *
     * @param string|null $key The specific setting key to retrieve. If null, returns the entire settings list.
     * @return mixed The value of the specified setting key, or the entire settings list if $key is null.
     */
    static function get_default_settings($key = null)
    {
        $list = [
            'uiblocks_quick_actions' => true,
            'uiblocks_dark_mode' => false,
            'uiblocks_small_left_sidebar' => false,
        ];

        if ($key) {
            return isset($list[$key]) ? $list[$key] : '';
        }

        return $list;
    }

    /**
     * Retrieves the default frontend settings for the UICore Blocks plugin.
     *
     * @param string|null $key The specific setting key to retrieve. If null, returns the entire settings list.
     * @return mixed The value of the specified setting key, or the entire settings list if $key is null.
     */
    static function get_default_frontend_settings($key = null)
    {
        $list = [
            'ui_bl_local_fonts' => 'false',
        ];

        if ($key) {
            return isset($list[$key]) ? $list[$key] : '';
        }

        return $list;
    }


    /**
     * Retrieves the value of a specific option from the uicore_blocks_options array.
     *
     * @param string $option_name The name of the option to retrieve.
     * @return mixed The value of the option if it exists, otherwise the default setting for the option.
     */
    static function get_option($option_name)
    {
        $options = \get_option(self::$module_name, []);
        return isset($options[$option_name]) ? $options[$option_name] : self::get_default_settings($option_name);
    }


    /**
     * Retrieves the value of a specific option from the uicore_blocks_options array.
     *
     * @param string $option_name The name of the option to retrieve.
     * @return mixed The value of the option if it exists, otherwise the default setting for the option.
     */
    static function get_frontend_option($option_name)
    {
        $options = \get_option(self::$frontend_module_name, []);
        return isset($options[$option_name]) ? $options[$option_name] : self::get_default_frontend_settings($option_name);
    }

    /**
     * Retrieves the value of a specific option from the uicore_blocks_options array.
     *
     * @param string $option_name The name of the option to retrieve.
     * @return mixed The value of the option if it exists, otherwise the default setting for the option.
     */
    static function get_options()
    {
        $db_options = \get_option(self::$module_name, []);
        $default_options = self::get_default_settings();

        $theme_options = get_option('uicore_theme_options', []);
        if (empty($theme_options['typekit'])) {
            return '';
        }

        $dashboard_options = [
            'googlemaps_api_key' => \get_option('uicore_blocks_googlemaps_api_key'),
            'recaptcha_site_key' => \get_option('uicore_blocks_recaptcha_site_key'),
            'recaptcha_secret_key' => \get_option('uicore_blocks_recaptcha_secret_key'),
            'mailchimp_secret_key' => \get_option('uicore_blocks_mailchimp_secret_key'),
            'typekit_project_id' => \get_option('uicore_theme_options', [])['typekit']['id'] ?? '',
        ];

        $options = array_merge(\wp_parse_args($db_options, $default_options), $dashboard_options);

        return $options;
    }

    /**
     * Update the specified option with the given value.
     *
     * @param string $option_name The name of the option to update.
     * @param mixed $value The new value for the option.
     * @return void
     */
    static function update_option($option_name, $value)
    {
        $options = \get_option(self::$module_name, []);

        //check if the value is different from default
        if ($value == self::get_default_settings($option_name)) {
            unset($options[$option_name]);
        } else {
            $options[$option_name] = $value;
        }
        \update_option(self::$module_name, $options);

        return $options;
    }

    static function get_global_options()
    {
        $db_aptions = \get_option(self::$global_module_name, []);
        $default_options = self::get_default_global_settings();
        return \wp_parse_args($default_options, $db_aptions);
    }

    static function update_global_options($options, $sync = false)
    {
        // parse and save only options that are diffrenet form default
        foreach ($options as $key => $value) {
            if ($value == self::get_default_global_settings($key)) {
                unset($options[$key]);
            }
        }
        //disable autoload if is sync (it means we don't need this options on frontend)
        \update_option(self::$global_module_name, $options, !$sync);



        // if is not sync let's try to sync the framework options
        if (!$sync && \class_exists('\UiCore\Settings')) {
            \UiCore\Settings::update_settings($options, true);
        }

        // GlobalStyles::clear_cache();
    }
}
new Settings();
