<?php

namespace Nectar\Utilities;

use Nectar\Utilities\{DemoUtils};

class WidgetImport {
  public static function import_widgets($file_path) {
    global $wp_registered_sidebars;

    $widget_json = DemoUtils::read_json_file($file_path);
    $data = $widget_json['widgets'];

    // Update widget CSS
    $nectar_blocks_widgets_css = $widget_json['nectar_blocks_widgets_css'];
    update_option('nectar_blocks_widgets_css', $nectar_blocks_widgets_css);

    $available_widgets = self::get_widgets();
    $widget_instances = [];
    foreach ($available_widgets as $widget_data) {
      $widget_instances[$widget_data['id_base']] = get_option('widget_' . $widget_data['id_base']);
    }

    $results = [];

    foreach ($data as $sidebar_id => $widgets) {
      // Skip inactive widgets (should not be in export file).
      if ('wp_inactive_widgets' === $sidebar_id) {
        continue;
      }

      // Check if sidebar is available on this site.
      // Otherwise add widgets to inactive, and say so.
      if (isset($wp_registered_sidebars[$sidebar_id])) {
        $sidebar_available = true;
        $use_sidebar_id = $sidebar_id;
        $sidebar_message_type = 'success';
        $sidebar_message = '';
      } else {
        $sidebar_available = false;
        $use_sidebar_id = 'wp_inactive_widgets'; // Add to inactive if sidebar does not exist in theme.
        $sidebar_message_type = 'error';
        $sidebar_message = esc_html__('Widget area does not exist in theme (using Inactive)', 'widget-importer-exporter');
      }

      // Result for sidebar
      // Sidebar name if theme supports it; otherwise ID.
      $results[$sidebar_id]['name'] = ! empty($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id;
      $results[$sidebar_id]['message_type'] = $sidebar_message_type;
      $results[$sidebar_id]['message'] = $sidebar_message;
      $results[$sidebar_id]['widgets'] = [];

      // Loop widgets.
      foreach ($widgets as $widget_instance_id => $widget) {
        $fail = false;

        // Get id_base (remove -# from end) and instance ID number.
        $id_base = preg_replace('/-[0-9]+$/', '', $widget_instance_id);
        $instance_id_number = str_replace($id_base . '-', '', $widget_instance_id);

        // Does site support this widget?
        if (! $fail && ! isset($available_widgets[$id_base])) {
          $fail = true;
          error_log('Site does not support widget: ' . $results[$sidebar_id]['name']);
        }

        // Filter to modify settings array
        // This is preferred over the older wie_widget_settings filter above
        // Do before identical check because changes may make it identical to end result (such as URL replacements).
        $widget = apply_filters('wie_widget_settings_array', $widget);

        // Does widget with identical settings already exist in same sidebar?
        if (! $fail && isset($widget_instances[$id_base])) {
          // Get existing widgets in this sidebar.
          $sidebars_widgets = get_option('sidebars_widgets');
          $sidebar_widgets = isset($sidebars_widgets[$use_sidebar_id]) ? $sidebars_widgets[$use_sidebar_id] : []; // Check Inactive if that's where will go.

          // Loop widgets with ID base.
          $single_widget_instances = ! empty($widget_instances[$id_base]) ? $widget_instances[$id_base] : [];
          foreach ($single_widget_instances as $check_id => $check_widget) {
            // Is widget in same sidebar and has identical settings?
            if (in_array("$id_base-$check_id", $sidebar_widgets, true) && (array) $widget === $check_widget) {
              $fail = true;
              $widget_message_type = 'warning';

              // Explain why widget not imported.
              $widget_message = esc_html__('Widget already exists', 'widget-importer-exporter');
              break;
            }
          }
        }

        // No failure.
        if (! $fail) {
          // Add widget instance
          $single_widget_instances = get_option('widget_' . $id_base); // All instances for that widget ID base, get fresh every time.
          $single_widget_instances = ! empty($single_widget_instances) ? $single_widget_instances : [
            '_multiwidget' => 1,
          ];
          $single_widget_instances[] = $widget;

          // Get the key it was given.
          end($single_widget_instances);
          $new_instance_id_number = key($single_widget_instances);

          // If key is 0, make it 1
          // When 0, an issue can occur where adding a widget causes data from other widget to load,
          // and the widget doesn't stick (reload wipes it).
          if ('0' === strval($new_instance_id_number)) {
            $new_instance_id_number = 1;
            $single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
            unset($single_widget_instances[0]);
          }

          // Move _multiwidget to end of array for uniformity.
          if (isset($single_widget_instances['_multiwidget'])) {
            $multiwidget = $single_widget_instances['_multiwidget'];
            unset($single_widget_instances['_multiwidget']);
            $single_widget_instances['_multiwidget'] = $multiwidget;
          }

          // Update option with new widget.
          update_option('widget_' . $id_base, $single_widget_instances);

          $sidebars_widgets = get_option('sidebars_widgets');

          // Avoid rarely fatal error when the option is an empty string
          if (! $sidebars_widgets) {
            $sidebars_widgets = [];
          }

          // Use ID number from new widget instance.
          $new_instance_id = $id_base . '-' . $new_instance_id_number;

          // Add new instance to sidebar.
          $sidebars_widgets[$use_sidebar_id][] = $new_instance_id;

          // Save the amended data.
          update_option('sidebars_widgets', $sidebars_widgets);

          // After widget import action.
          $after_widget_import = [
            'sidebar' => $use_sidebar_id,
            'sidebar_old' => $sidebar_id,
            'widget' => $widget,
            'widget_type' => $id_base,
            'widget_id' => $new_instance_id,
            'widget_id_old' => $widget_instance_id,
            'widget_id_num' => $new_instance_id_number,
            'widget_id_num_old' => $instance_id_number,
          ];
        }
      }
    }

    return true;
  }

  public static function get_widgets() {
    global $wp_registered_widget_controls;

    $widget_controls = $wp_registered_widget_controls;

    $available_widgets = [];

    foreach ($widget_controls as $widget) {
      // No duplicates.
      if (! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] )) {
        $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
        $available_widgets[$widget['id_base']]['name'] = $widget['name'];
      }
    }

    return $available_widgets;
  }
}
