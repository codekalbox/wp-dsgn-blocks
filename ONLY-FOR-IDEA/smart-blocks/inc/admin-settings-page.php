<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$sb_all_blocks = smart_blocks_get_all_blocks_list();

?>

<div class="sb-wrap">

    <div class="sb-admin-header-section">
        <h1 class="sb-admin-header-text">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 226.72 213.12">
                <g>
                    <g>
                        <path d="M221.08 54.39c5.71-5.85 6.55-7.25 4.88-8.36-1.12-.7-11.44-5.44-23-10.33s-34.73-15.06-51.47-22.45C134.61 6 120.24 0 119.4 0 118 0 34.74 80.2 33.91 82.15c-.28.42 8 4.46 18.13 8.78s36.68 15.9 58.86 25.67 42 18.13 43.93 19c3.35 1.26 2.51 2.24-23.57 27.48l-27.2 26.22-28.59-12.45c-15.76-7-36-15.62-44.77-19.39l-15.9-7-7.67 7.67c-4.18 4.32-7.39 8-7.11 8.23.55.64 102.09 44.64 107.11 46.75 1.12.42 11.71-9.07 25.8-22.87 13.25-13 32.36-31.66 42.54-41.57s18-18.54 17.43-19a108 108 0 0 0-11.57-5.3c-5.72-2.37-24.27-10.32-41.15-17.72S100.57 89.4 89.56 84.8l-20-8.65L93.74 52c13.25-13.25 25.52-25.1 27.2-26.22 2.65-1.81 6.13-.56 46.16 16.74 23.71 10.32 44.07 18.83 45.19 18.83s5.02-3.19 8.79-6.96"></path>
                        <path d="M57.48 134.31c-7.11 7.25-8.51 9.34-6 9.34.84 0 12.83 5 26.78 11.16s26.36 11 27.2 10.88c1.81-.42 30.26-27.75 29.56-28.31-.28-.28-4.88-2.23-10.32-4.6l-9.9-4.19-6.8 6.84-6.84 6.83-15.48-6.83c-22.06-9.63-19.83-9.63-28.2-1.12M149 58.58c-14-6.14-26-11.16-26.81-11.16-1.39 0-30.54 28-29.84 28.59 1.67 1.26 17.15 7.67 18.55 7.67.83 0 4.74-2.79 8.5-6.13 6.42-5.86 7-6.14 11-4.47 2.23 1 10.32 4.47 17.85 7.67l13.67 5.86 7.53-7.25c7.53-7.11 9.07-9.63 6.28-9.63-.82 0-12.81-5.02-26.73-11.15"></path>
                    </g>
                </g>
            </svg>
            <?php echo esc_html(apply_filters('sb_settings_title', 'Smart Blocks Setttings - V' . SMART_BLOCKS_VERSION)); ?>
        </h1>
        <div class="sb-dcoumentation-link">
            <a href="https://hashthemes.com/documentation/smart-blocks-documentation/" target="_blank">
                <span class="mdi-text-box-multiple-outline"></span>
                <?php echo esc_html__('Documentation', 'smart-blocks'); ?>
            </a>
        </div>
    </div>

    <nav class="sb-nav-tab-wrapper">
        <?php
        $sb_block_settings_tabs = array(
            'blocks' => array(
                'name' => __('Blocks', 'smart-blocks'),
                'icon' => 'mdi-widgets-outline'
            ),
            'settings' => array(
                'name' => __('Settings', 'smart-blocks'),
                'icon' => 'mdi-cog'
            ),
            'about' => array(
                'name' => __('About', 'smart-blocks'),
                'icon' => 'mdi-file-document-multiple-outline'
            )
        );
        foreach ($sb_block_settings_tabs as $key => $val) {
            ?>
            <a href="javascript:void(0)" class="<?php echo $key == 'blocks' ? 'nav-tab-active ' : ''; ?>sb-tab" data-tab="sb-<?php echo esc_attr($key); ?>-section-content" data-tohide="tab-content">
                <i class="<?php echo esc_attr($val['icon']); ?>"></i>
                <?php echo esc_html($val['name']); ?>
            </a>
            <?php
        }
        ?>
    </nav>

    <div class="sb-tab-contents">
        <div id="sb-blocks-section-content" class="tab-content">
            <?php do_action('sb_before_admin_blocks'); ?>

            <form id="sb-block-selection-form">
                <div class="sb-block-action-buttons">
                    <button class="sb-block-action-btn sb-block-enable-all">
                        <i class="mdi-check-circle-outline"></i><?php esc_html_e('Enable All', 'smart-blocks') ?>
                    </button>
                    <button class="sb-block-action-btn sb-block-disable-all">
                        <i class="mdi-close-circle-outline"></i><?php esc_html_e('Disable All', 'smart-blocks') ?>
                    </button>
                </div>

                <div class="sb-block-section-inner-wrap">
                    <?php
                    foreach ($sb_all_blocks as $key => $val) {
                        $this->get_block_field($val['name'], $key, $val['icon'], isset($val['premium']) && $val['premium'], isset($val['category']) ? $val['category'] : '');
                    }
                    ?>
                </div>

                <div class="sb-save-button-wrap">
                    <button name="sb-block-enable" id="sb-block-selection-btn" class="sb-save-button">
                        <i class="mdi-content-save"></i><?php esc_html_e('Save', 'smart-blocks'); ?>
                        <span class="sb-loader"></span>
                    </button>
                </div>
            </form>
        </div>

        <div id="sb-about-section-content" class="tab-content" style="display: none;">
            <h3><?php esc_html_e('Description', 'smart-blocks'); ?></h3>
            <p><?php esc_html_e('Smart Blocks is an all in one block pack extension for Gutenberg page builder. It provides creative blocks to provide an outstanding look to your Gutenberg based WordPress website. The blocks are multi concept and contain amazing features to make your website more effective by placing the spectacular blocks and enhance the engagement rate.', 'smart-blocks'); ?></p>

            <h3><?php esc_html_e('Blocks Available in the Extension:', 'smart-blocks'); ?></h3>

            <?php
            $description = sb_get_all_blocks_desc();
            $count = 0;
            foreach ($sb_all_blocks as $key => $val) {
                $count++;
                ?>
                <p><?php echo esc_html($count); ?>) <a href="https://demo.hashthemes.com/smart-blocks/<?php echo esc_attr($key); ?>/" target="_blank"><?php echo esc_html($val['name']); ?></a> - <?php echo isset($description[$key]) ? esc_html($description[$key]) : ''; ?></p>
                <?php
            }
            ?>

            <p><?php esc_html_e('More Coming', 'smart-blocks'); ?></p>

            <h3><?php esc_html_e('Support:', 'smart-blocks'); ?></h3>
            <p><?php esc_html_e('If you have any issues while using our plugin, feel free to contact us for support. Our support team will be more than happy to help you resolve your issue. You can chat with us or email us at our website', 'smart-blocks'); ?> <a href="https://hashthemes.com/" target="_blank"><?php esc_html_e('here', 'smart-blocks'); ?></a>.</p>

            <p style="height:40px;"></p>
        </div>

        <div id="sb-settings-section-content" class="tab-content" style="display: none;">
            <form id="sb-general-settings-form">
                <?php
                $sb_general_settings = get_option('sb_general_settings');
                $load_fonts_locally = isset($sb_general_settings['load_fonts_locally']) && $sb_general_settings['load_fonts_locally'] ? $sb_general_settings['load_fonts_locally'] : '';
                $default_section_width = isset($sb_general_settings['default_section_width']) && $sb_general_settings['default_section_width'] ? $sb_general_settings['default_section_width'] : '';
                $sb_settings_tabs = apply_filters('sb_setting_tabs', array(
                    'block_styles' => __('Block Styles', 'smart-blocks'),
                    'block_settings' => __('Block Settings', 'smart-blocks')
                ));
                ?>
                <div class="sb-htab-container">
                    <div class="sb-htabs">
                        <?php
                        foreach ($sb_settings_tabs as $key => $tab) {
                            ?>
                            <div class="sb-htab<?php echo $key == 'block_styles' ? ' sb-active' : ''; ?>" data-tab="<?php echo esc_attr($key); ?>"><?php echo esc_html($tab); ?></div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="sb-htab-content">
                        <?php
                        foreach ($sb_settings_tabs as $key => $tab) {
                            ?>
                            <div class="sb-htab-panel<?php echo $key == 'block_styles' ? ' sb-active' : ''; ?>" id="<?php echo esc_attr($key); ?>">
                                <?php
                                if ($key == 'block_styles') {
                                    ?>
                                    <div class="sb-settings-field">
                                        <label><?php esc_html_e('Default Columns Width (px)', 'smart-blocks'); ?></label>
                                        <div class="sb-settings-input-field">
                                            <input type="number" name="default_section_width" value="<?php echo esc_attr($default_section_width); ?>">
                                        </div>
                                    </div>
                                    <?php
                                }
                                if ($key == 'block_settings') {
                                    ?>
                                    <div class="sb-settings-field">
                                        <label><?php esc_html_e('Load Google Fonts Locally', 'smart-blocks'); ?></label>
                                        <div class="sb-settings-fields sb-toggle-input-field">
                                            <div class="sb-checkbox">
                                                <input type="checkbox" class="sb-block-checkbox" name="load_fonts_locally" value="on" <?php checked($load_fonts_locally, 'on', true); ?>>
                                                <label></label>
                                            </div>
                                            <p class="sb-desc"><?php echo esc_html__('It is required to load the Google Fonts locally in order to comply with GDPR. However, if your website is not required to comply with GDPR then you can check this field off. Loading the Fonts locally with lots of different Google fonts can decrease the speed of the website slightly.', 'smart-blocks'); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                }
                                do_action('sb_block_settings_' . $key . '_content', $sb_general_settings);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="sb-save-button-wrap">
                    <button class="sb-save-button" id="sb-general-settings-save">
                        <i class="mdi mdi-content-save"></i>
                        <?php esc_html_e('Save', 'smart-blocks'); ?>
                        <span class="sb-loader"></span>
                    </button>
                </div>
            </form>
        </div>

        <div class="sb-admin-notificn" style="display: none;"></div>
    </div>
</div>