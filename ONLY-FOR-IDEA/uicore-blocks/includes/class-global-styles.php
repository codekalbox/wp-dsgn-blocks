<?php

namespace UiCoreBlocks;

defined('ABSPATH') || exit();
/**
 * UiCore Global Styles Functions
 */
class GlobalStyles
{

    function __construct()
    {
        //add global (or critical based on framework settings) styles to framework
        add_filter('uicore_css_critical_global_code_string', [$this, 'add_css_to_framework'], 10, 2);
    }

    static function get_css_styles($is_frontend = true, $custom_settings = false)
    {
        $extra_styles = '';
        // in frontend if we add a specific class it whiill make the css target too powerfull
        // so we need to use the body tag
        $main_prefix = $is_frontend ? '.uicore-bl-styles' : '.editor-styles-wrapper';

        if (\class_exists('\UiCore\Settings')) {

            //get the settings from the framework
            $settings = $custom_settings ? $custom_settings : \UiCore\Settings::current_settings();

            $buttons_selector = \UiCore\Helper::get_buttons_class('default', 'full', true, $main_prefix);
            $buttons_with_padding_selector = \UiCore\Helper::get_buttons_class('default', 'with_padding', true, $main_prefix);
            $buttons_selector_hover = \UiCore\Helper::get_buttons_class('hover', 'full', true, $main_prefix);

            //maybe unify this with framework buttons css + btn interactions
            $shadow = '';
            if ($settings['button_shadow'] && \is_array($settings['button_shadow']) && count($settings['button_shadow']) > 0) {
                $shadow = [];
                foreach ($settings['button_shadow'] as $key => $value) {
                    $shadow[] = ($value['type'] == 'inside' ? 'inset ' : '') .  $value['h_shadow'] . 'px ' . $value['v_shadow'] . 'px ' . $value['blur'] . 'px ' . $value['spread'] . 'px ' . self::get_css_color($value['color']);
                }
                $shadow = 'box-shadow: ' . implode(',', $shadow);
            }

            $extra_styles = '
            ' . $buttons_selector . ',
            ' . $main_prefix . ' .wp-element-button,
            ' . $main_prefix . ' .uicore-button{
                border-style: ' . $settings['button_border_border'] . ';
                border-color: ' . self::get_css_color($settings['button_border_color']['m']) . ';
                border-width: ' . $settings['button_border_width'] . 'px;
                border-radius: ' . $settings['button_border_radius'] . 'px;
                background-color: ' . self::get_css_color($settings['button_background_color']['m']) . ';
                color: ' . self::get_css_color($settings['button_typography_typography']['c']) . ';
                font-family: ' . self::get_font_family($settings['button_typography_typography']['f'], $settings) . ';
                font-weight: ' . self::get_font_weight($settings['button_typography_typography']) . ';
                text-transform: ' . $settings['button_typography_typography']['t'] . ';
                letter-spacing: ' . $settings['button_typography_typography']['ls'] . 'em;

                font-size: var(--uicore-btn-font-size);
                line-height: 1em!important;
                transition: all 0.3s ease,font-size 0s;

                ' . $shadow . ';
            }
            ' . $buttons_with_padding_selector . ',
            ' . $main_prefix . ' .wp-element-button,
            ' . $main_prefix . ' .uicore-button{
                padding: var(--uicore-btn-padding);
            }

            ' . $buttons_selector_hover . ',
            ' . $main_prefix . ' .wp-element-button:hover,
            ' . $main_prefix . ' .uicore-button:hover{
                border-color: ' . self::get_css_color($settings['button_border_color']['h']) . ';
                background-color: ' . self::get_css_color($settings['button_background_color']['h']) . ';
                color: ' . self::get_css_color($settings['button_typography_typography']['ch']) . ';
            }
            ';
        } else {
            //get the settings from blocks global options
            $settings = Settings::get_global_options();
        }

        //add some overwrites for the editor
        if (!$is_frontend) {
            $extra_styles .= '
            :root body {
                --ui-container-size: ' . $settings['gen_full_w'] . 'px;
            }
            .editor-styles-wrapper{
                --wp--custom--layout--content:var(--ui-container-size)
            }    
            ';
        }

        if (class_exists('\UiCore\Settings') && !$is_frontend) {
            $extra_styles .= '
            :root body {
                --uicore-primary-color: ' . $settings['pColor'] . ';
                --uicore-secondary-color: ' . $settings['sColor'] . ';
                --uicore-accent-color: ' . $settings['aColor'] . ';
                --uicore-headline-color: ' . $settings['hColor'] . ';
                --uicore-body-color: ' . $settings['bColor'] . ';
                --uicore-dark-color: ' . $settings['dColor'] . ';
                --uicore-light-color: ' . $settings['lColor'] . ';
                --uicore-white-color: ' . $settings['wColor'] . ';
                --uicore-primary-font-family: "' . $settings['pFont']['f'] . '";
                --uicore-primary-font-w: ' . self::get_font_weight($settings['pFont']) . ';
                --uicore-secondary-font-family: "' . $settings['sFont']['f'] . '";
                --uicore-secondary-font-w: ' . self::get_font_weight($settings['sFont']) . ';
                --uicore-text-font-family: "' . $settings['tFont']['f'] . '";
                --uicore-text-font-w: ' . self::get_font_weight($settings['tFont']) . ';
                --uicore-accent-font-family: "' . $settings['aFont']['f'] . '";
                --uicore-accent-font-w: ' . self::get_font_weight($settings['aFont']) . ';

                ' . self::generate_typography_css('h1', $settings) . '
                ' . self::generate_typography_css('h2', $settings) . '
                ' . self::generate_typography_css('h3', $settings) . '
                ' . self::generate_typography_css('h4', $settings) . '
                ' . self::generate_typography_css('h5', $settings) . '
                ' . self::generate_typography_css('h6', $settings) . '
                ' . self::generate_typography_css('p', $settings) . '
            }
            @media (max-width: 1024px) {
                :root body{
                    ' . self::get_size_and_unit($settings['h1']['s']['t'], '--uicore-typography--h1-s') . '
                    ' . self::get_size_and_unit($settings['h2']['s']['t'], '--uicore-typography--h2-s') . '
                    ' . self::get_size_and_unit($settings['h3']['s']['t'], '--uicore-typography--h3-s') . '
                    ' . self::get_size_and_unit($settings['h4']['s']['t'], '--uicore-typography--h4-s') . '
                    ' . self::get_size_and_unit($settings['h5']['s']['t'], '--uicore-typography--h5-s') . '
                    ' . self::get_size_and_unit($settings['h6']['s']['t'], '--uicore-typography--h6-s') . '
                    ' . self::get_size_and_unit($settings['p']['s']['t'], '--uicore-typography--p-s') . '
                }
            }
            @media (max-width: 740px) {
                :root body{
                    ' . self::get_size_and_unit($settings['h1']['s']['m'], '--uicore-typography--h1-s') . '
                    ' . self::get_size_and_unit($settings['h2']['s']['m'], '--uicore-typography--h2-s') . '
                    ' . self::get_size_and_unit($settings['h3']['s']['m'], '--uicore-typography--h3-s') . '
                    ' . self::get_size_and_unit($settings['h4']['s']['m'], '--uicore-typography--h4-s') . '
                    ' . self::get_size_and_unit($settings['h5']['s']['m'], '--uicore-typography--h5-s') . '
                    ' . self::get_size_and_unit($settings['h6']['s']['m'], '--uicore-typography--h6-s') . '
                    ' . self::get_size_and_unit($settings['p']['s']['m'], '--uicore-typography--p-s') . '
                }
            }

                
            ' . $main_prefix . ' a {
                color: var(--uicore-primary-color);
            }
            ';
        }
        if (class_exists('\UiCore\Settings')) {
            $extra_styles .= '
            :root body {
                --uicore-btn-font-size:' . $settings['button_typography_typography']['s']['d'] . 'px;
                --uicore-btn-padding:' . $settings['button_padding']['d']['top'] . 'px ' . $settings['button_padding']['d']['right'] . 'px ' . $settings['button_padding']['d']['bottom'] . 'px ' . $settings['button_padding']['d']['left'] . 'px;
                }
            @media (max-width: 1024px) {
                :root body{
                    --uicore-btn-font-size:' . $settings['button_typography_typography']['s']['t'] . 'px;
                    --uicore-btn-padding:' . $settings['button_padding']['t']['top'] . 'px ' . $settings['button_padding']['t']['right'] . 'px ' . $settings['button_padding']['t']['bottom'] . 'px ' . $settings['button_padding']['t']['left'] . 'px;
                }
            }
            @media (max-width: 740px) {
                :root body{
                    --uicore-btn-font-size:' . $settings['button_typography_typography']['s']['m'] . 'px;
                    --uicore-btn-padding:' . $settings['button_padding']['m']['top'] . 'px ' . $settings['button_padding']['m']['right'] . 'px ' . $settings['button_padding']['m']['bottom'] . 'px ' . $settings['button_padding']['m']['left'] . 'px;
                }
            }
            ';
        }
        $fonts_list = self::get_fonts_list($settings);
        $fonts = Fonts::get_local_fonts($fonts_list);
        //clear prefix on global typo in frontend  to alow broad targeting on non uicore blocks pages
        $main_prefix = $is_frontend ? '' : '.editor-styles-wrapper';
        return
            $fonts .
            $extra_styles . '


            ' . $main_prefix . ' .uicore-typo-primary{
                font-family: var(--uicore-primary-font-family);
                font-weight: var(--uicore-primary-font-w);
            }
            ' . $main_prefix . ' .uicore-typo-secondary{
                font-family: var(--uicore-secondary-font-family);
                font-weight: var(--uicore-secondary-font-w);
            }
            ' . $main_prefix . ' .uicore-typo-text{
                font-family: var(--uicore-text-font-family);
                font-weight: var(--uicore-text-font-w);
            }
            ' . $main_prefix . ' .uicore-typo-accent{
                font-family: var(--uicore-accent-font-family);
                font-weight: var(--uicore-accent-font-w);
            }

            ' . $main_prefix . ' h1,
            ' . $main_prefix . ' .uicore-typo-h1{
                font-family: var(--uicore-typography--h1-f);
                font-weight: var(--uicore-typography--h1-w);
                font-size: var(--uicore-typography--h1-s);
                line-height: var(--uicore-typography--h1-h);
                letter-spacing: var(--uicore-typography--h1-ls);
                text-transform: var(--uicore-typography--h1-t);
                color: var(--uicore-typography--h1-c);
            }
            ' . $main_prefix . ' h2,
            ' . $main_prefix . ' .uicore-typo-h2{
                font-family: var(--uicore-typography--h2-f);
                font-weight: var(--uicore-typography--h2-w);
                font-size: var(--uicore-typography--h2-s);
                line-height: var(--uicore-typography--h2-h);
                letter-spacing: var(--uicore-typography--h2-ls);
                text-transform: var(--uicore-typography--h2-t);
                color: var(--uicore-typography--h2-c);
            }
            ' . $main_prefix . ' h3,
            ' . $main_prefix . ' .uicore-typo-h3{
                font-family: var(--uicore-typography--h3-f);
                font-weight: var(--uicore-typography--h3-w);
                font-size: var(--uicore-typography--h3-s);
                line-height: var(--uicore-typography--h3-h);
                letter-spacing: var(--uicore-typography--h3-ls);
                text-transform: var(--uicore-typography--h3-t);
                color: var(--uicore-typography--h3-c);
            }
            ' . $main_prefix . ' h4,
            ' . $main_prefix . ' .uicore-typo-h4{
                font-family: var(--uicore-typography--h4-f);
                font-weight: var(--uicore-typography--h4-w);
                font-size: var(--uicore-typography--h4-s);
                line-height: var(--uicore-typography--h4-h);
                letter-spacing: var(--uicore-typography--h4-ls);
                text-transform: var(--uicore-typography--h4-t);
                color: var(--uicore-typography--h4-c);
            }
            ' . $main_prefix . ' h5,
            ' . $main_prefix . ' .uicore-typo-h5{
                font-family: var(--uicore-typography--h5-f);
                font-weight: var(--uicore-typography--h5-w);
                font-size: var(--uicore-typography--h5-s);
                line-height: var(--uicore-typography--h5-h);
                letter-spacing: var(--uicore-typography--h5-ls);
                text-transform: var(--uicore-typography--h5-t);
                color: var(--uicore-typography--h5-c);
            }
            ' . $main_prefix . ' h6,
            ' . $main_prefix . ' .uicore-typo-h6{
                font-family: var(--uicore-typography--h6-f);
                font-weight: var(--uicore-typography--h6-w);
                font-size: var(--uicore-typography--h6-s);
                line-height: var(--uicore-typography--h6-h);
                letter-spacing: var(--uicore-typography--h6-ls);
                text-transform: var(--uicore-typography--h6-t);
                color: var(--uicore-typography--h6-c);
            }
            ' . ($main_prefix === '' ? 'body' : $main_prefix) . ',
            ' . $main_prefix . ' p, 
            ' . $main_prefix . ' .uicore-typo-p{
                font-family: var(--uicore-typography--p-f);
                font-weight: var(--uicore-typography--p-w);
                font-size: var(--uicore-typography--p-s);
                line-height: var(--uicore-typography--p-h);
                letter-spacing: var(--uicore-typography--p-ls);
                text-transform: var(--uicore-typography--p-t);
                color: var(--uicore-typography--p-c);
            }

        ';
    }

    static function generate_typography_css($element, $settings)
    {
        if (!isset($settings[$element])) {
            return '';
        }
        $value = $settings[$element];
        $css  = '--uicore-typography--' . $element . '-f: ' . self::get_font_family($value['f'], $settings) . ';' . "\n";
        $css .= '--uicore-typography--' . $element . '-w: ' . self::get_font_weight($value) . ';' . "\n";
        $css .= self::get_size_and_unit($value['h']['d'], '--uicore-typography--' . $element . '-h') . "\n";
        $css .= self::get_size_and_unit($value['ls']['d'], '--uicore-typography--' . $element . '-ls') . "\n";
        $css .= '--uicore-typography--' . $element . '-t: ' . $value['t'] . ';' . "\n";
        $css .= '--uicore-typography--' . $element . '-st: ' . self::get_font_style($value) . ';' . "\n";
        $css .= '--uicore-typography--' . $element . '-c: ' . self::get_css_color($value['c']) . ';' . "\n";
        $css .= self::get_size_and_unit($value['s']['d'], '--uicore-typography--' . $element . '-s') . "\n";

        return $css;
    }

    static function get_size_and_unit($size, $prop)
    {
        if ($size['value'] === '') {
            //if the value is empty, return an empty string
            return null;
        }
        if (isset($size['unit']) && $size['unit'] == 'ct') {
            //custom unit, return only the value
            return  $prop . ':' . $size['value'] . ';';
        }
        return $prop . ':' . $size['value'] . $size['unit'] . ';';
    }



    /**
     * Helper function used inside css.php files
     *
     * @param mixed $fam
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 3.0.0
     */
    static function get_font_family($fam)
    {

        switch ($fam) {
            case "Primary":
                $font = "var(--uicore-primary-font-family)";
                break;
            case "Secondary":
                $font = "var(--uicore-secondary-font-family)";
                break;
            case "Text":
                $font = "var(--uicore-text-font-family)";
                break;
            case "Accent":
                $font = "var(--uicore-accent-font-family)";
                break;
            default:
                $font = '"' . $fam . '"';
        }
        return $font;
    }

    /**
     * Helper function used inside css.php files
     *
     * @param mixed $for
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 3.0.0
     */
    static function get_font_style($for)
    {
        if (strpos($for['st'], 'italic') !== false) {
            return 'italic';
        } else {
            return 'normal';
        }
    }

    /**
     * Helper function used inside css.php files
     *
     * @param mixed $for
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 3.0.0
     */
    static function get_font_weight($for)
    {
        if (preg_match('/400|regular|normal/', $for['st'])) {
            return '400';
        } else {
            if (strlen(str_replace('italic', '', $for['st'])) < 2) {
                return '400';
            } else {
                return str_replace('italic', '', $for['st']);
            }
        }
    }

    /**
     * Helper function used inside css.php files
     *
     * @param mixed $color
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 3.0.0
     */
    static function get_css_color($color)
    {
        //Color + Blur Migrate support
        if (!is_string($color) && (isset($color['type']) || isset($color['blur']))) {
            $color = $color['color'];
        }
        if ($color == 'Primary') {
            $color = 'var(--uicore-primary-color)';
        } else if ($color == 'Secondary') {
            $color = 'var(--uicore-secondary-color)';
        } else if ($color == 'Accent') {
            $color = 'var(--uicore-accent-color)';
        } else if ($color == 'Headline') {
            $color = 'var(--uicore-headline-color)';
        } else if ($color == 'Body') {
            $color = 'var(--uicore-body-color)';
        } else if ($color == 'Dark Neutral') {
            $color = 'var(--uicore-dark-color)';
        } else if ($color == 'Light Neutral') {
            $color = 'var(--uicore-light-color)';
        } else if ($color == 'White') {
            $color = 'var(--uicore-white-color)';
        }
        return $color;
    }


    static function get_fonts_list($custom_settings = false)
    {
        //TEMP: REQUIRE SYNC
        if (\class_exists('\UiCore\Settings')) {
            $settings = $custom_settings ? $custom_settings : \UiCore\Settings::current_settings();
        } else {
            $settings = Settings::get_global_options();
        }
        $fonts = [];
        $font_options = [
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'p',
            'button_typography_typography',
            'pFont',
            'sFont',
            'tFont',
            'aFont',
        ];
        $exclude = ['Primary', 'Secondary', 'Text', 'Accent'];

        foreach ($font_options as $option) {
            // Check if the option is set and if the font is not in the exclude list
            if (isset($settings[$option]['f']) && !in_array($settings[$option]['f'], $exclude)) {
                $font_name = trim(self::get_font_family($settings[$option]['f']) ?? '', '"\''); // Ensure string type
                $font_weight = self::get_font_weight($settings[$option]);
                $font_category = 'google'; // Assuming all fonts are from Google Fonts

                if (!isset($fonts[$font_name])) {
                    $fonts[$font_name] = [
                        'category' => $font_category,
                        'weights' => [],
                    ];
                }

                $fonts[$font_name]['weights'][] = $font_weight === 'normal' ? 400 : intval($font_weight); // Normalize 'normal' to '400'
                $fonts[$font_name]['weights'] = array_values(array_unique($fonts[$font_name]['weights']));
                sort($fonts[$font_name]['weights']);
            }
        }
        return $fonts;
    }


    static function add_css_to_framework($css, $class_css)
    {
        $custom_settings = $class_css->settings;
        // Add the CSS to the framework
        return $css . self::get_css_styles(true, $custom_settings);
    }
}
new GlobalStyles();
