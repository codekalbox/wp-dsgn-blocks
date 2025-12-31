<?php

namespace UiCoreBlocks;

defined('ABSPATH') || exit();
/**
 * UiCore Utils Functions
 */
class Helper
{
    /**
     * Convert camelCase (with optional digits) to snake_case,
     * then reorder email_xxx_2 patterns correctly.
     *
     * @param string $input CamelCase or camelCaseWithDigits string.
     * @return string Snake_case string, with email_subject_2 formatting.
     */
    public static function camel_to_snake_with_number(string $input): string
    {
        $parts = preg_split(
            '/(?<=[a-z])(?=[A-Z])|(?<=[A-Za-z])(?=\d)|(?<=[0-9])(?=[A-Z])/',
            $input
        );
        $snake = strtolower(implode('_', $parts));

        // reorder email_2_subject -> email_subject_2
        if (preg_match('#^(email)_([0-9]+)_(.+)$#', $snake, $m)) {
            return "{$m[1]}_{$m[3]}_{$m[2]}";
        }

        return $snake;
    }

    /**
     * Recursively search an array of parsed blocks for a block with matching blockId.
     *
     * @param array  $blocks   Array of blocks from parse_blocks().
     * @param string $block_id The blockId attribute to search for.
     * @return array|null      The block array if found, or null otherwise.
     */
    public static function find_block_by_id(array $blocks, string $block_id)
    {
        foreach ($blocks as $block) {
            if (!empty($block['attrs']['blockId']) && $block['attrs']['blockId'] === $block_id) {
                return $block;
            }
            if (!empty($block['innerBlocks'])) {
                $found = self::find_block_by_id($block['innerBlocks'], $block_id);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Validate form fields for required and email format rules.
     *
     * @param array $fields Associative array of field_name => value.
     * @return array Array of validation errors, field_name => message. Empty if none.
     */
    public static function validate_fields(array $fields): array
    {
        $errors = [];
        foreach ($fields as $name => $value) {
            // Required: empty string
            // TODO: add server side validation
            // if ($value === '') {
            //    $errors[$name] = sprintf('The "%s" field is required.', $name);
            // }
            // Email format: if key contains 'email'
            if (stripos($name, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$name] = sprintf('The "%s" field must be a valid email address.', $name);
            }
        }
        return $errors;
    }

    /**
     * Import images from a template (html/json string).
     *
     * @return array
     */
    public static function process_images_in_template($html)
    {
        $fails = 0;
        /*----------------------------------------------------------
        | 1.  <img …> tags (classic & UiCore)                      |
        ----------------------------------------------------------*/
        $html = preg_replace_callback(
            '/<img\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>/i',
            function ($m) use (&$fails) {
                $tag     = $m[0];
                $oldUrl  = $m[1];

                // detect old ID (data-bl-image or wp-image-XXX class)
                $oldId = null;
                if (preg_match('/data-bl-image\s*=\s*["\']?([a-z0-9]+)/i', $tag, $o)) {
                    $oldId = $o[1];
                } elseif (preg_match('/wp-image-([a-z0-9]+)/i', $tag, $o)) {
                    $oldId = $o[1];
                }

                /* ask MediaManager */
                $mm     = self::upload_media($oldUrl);
                if (!$mm) {
                    $fails++;
                    return $tag; // skip this image
                }
                $newUrl = $mm['url'];
                $newId  = $mm['id'];


                /* rewrite URL */
                $tag = preg_replace(
                    '/\bsrc=["\']' . preg_quote($oldUrl, '/') . '["\']/',
                    'src="' . $newUrl . '"',
                    $tag
                );

                /* rewrite ID wherever it lives */
                if ($oldId !== null) {
                    $tag = preg_replace(
                        '/data-bl-image\s*=\s*["\']?' . preg_quote($oldId, '/') . '["\']?/',
                        'data-bl-image="' . $newId . '"',
                        $tag
                    );
                    $tag = preg_replace(
                        '/wp-image-' . preg_quote($oldId, '/') . '\b/',
                        'wp-image-' . $newId,
                        $tag
                    );
                }
                return $tag;
            },
            $html
        );

        /*----------------------------------------------------------
        | 2.  inline-CSS  url("…")                                 |
        ----------------------------------------------------------*/
        $html = preg_replace_callback(
            '/url\((["\']?)([^"\')]+\.(?:png|jpe?g|gif|svg|webp)(?:\?[^\)"\']*)?)\1\)/i',
            function ($m) use (&$fails) {
                $quote   = $m[1];
                $oldUrl  = $m[2];

                $mm = self::upload_media($oldUrl);
                if (!$mm) {
                    $fails++;
                    return 'url(' . $quote . $oldUrl . $quote . ')'; // skip this image
                }
                $newUrl  = $mm['url'];

                return 'url(' . $quote . $newUrl . $quote . ')';
            },
            $html
        );

        /*----------------------------------------------------------
        | 3.  JSON-like objects  {"url":"…","id":123}              |
        |     (also catches  {"id":123,"url":"…"} and              |
        |      {"customImageMask":"…"} without id)                 |
        ----------------------------------------------------------*/
        /* url → id order */
        $html = preg_replace_callback(
            '/"url"\s*:\s*"([^"]+)"\s*,\s*"id"\s*:\s*(?:"([^"]+)"|([0-9]+))/i',
            function ($m) use (&$fails) {
                [$oldUrl, $oldId] = [$m[1], $m[2]];
                $mm = self::upload_media($oldUrl);
                if ($mm) {
                    return '"url":"' . $mm['url'] . '", "id":' . $mm['id'];
                }
                $fails++;
            },
            $html
        );

        /* id → url order */
        $html = preg_replace_callback(
            '/"id"\s*:\s*(?:"([^"]+)"|([0-9]+))\s*,\s*"url"\s*:\s*"([^"]+)"/i',
            function ($m)  use (&$fails) {
                [$oldId, $oldUrl] = [$m[1], $m[2]];
                $mm = self::upload_media($oldUrl);
                if ($mm) {
                    return '"id":' . $mm['id'] . ', "url":"' . $mm['url'] . '"';
                }
                $fails++;
            },
            $html
        );

        /* imageId and imageSrc pairs */
        $html = preg_replace_callback(
            '/imageId\s*:\s*"([^"]+)"\s*,\s*imageSrc\s*:\s*"([^"]+)"/i',
            function ($m) use (&$fails) {
                $oldId  = $m[1];
                $oldUrl = $m[2];
                $mm = self::upload_media($oldUrl);
                if ($mm) {
                    return '"imageId":"' . $mm['id'] . '", "imageSrc":"' . $mm['url'] . '"';
                }
                $fails++;
                return $m[0]; // Important: return the original match if nothing changed
            },
            $html
        );

        /* imageSrc and imageId pairs */
        $html = preg_replace_callback(
            '/imageSrc\s*:\s*"([^"]+)"\s*,\s*imageId\s*:\s*"([^"]+)"/i',
            function ($m) use (&$fails) {
                $oldUrl = $m[1];
                $oldId  = $m[2];
                $mm = self::upload_media($oldUrl);
                if ($mm) {
                    return '"imageSrc":"' . $mm['url'] . '", "imageId":"' . $mm['id'] . '"';
                }
                $fails++;
                return $m[0]; // Important: return the original match if nothing changed
            },
            $html
        );

        /* properties that hold only a URL (no id) */
        $html = preg_replace_callback(
            '/"\w*(?:image|mask)\w*"\s*:\s*"([^"]+\.(?:png|jpe?g|gif|svg|webp)(?:\?[^\"]*)?)"/i',
            function ($m) use (&$fails) {
                $oldUrl = $m[1];
                $mm = self::upload_media($oldUrl);
                if ($mm) {
                    return str_replace($oldUrl, $mm['url'], $m[0]);
                }
                $fails++;
            },
            $html
        );




        // /*----------------------------------------------------------
        // | 5.  image object: { "url": "...", "id": ... }           |
        // ----------------------------------------------------------*/
        // $fails = 0;
        // $html = preg_replace_callback(
        //     '/"image"\s*:\s*\{\s*"url"\s*:\s*"([^"]+)"\s*,\s*"id"\s*:\s*(\d+)\s*\}/i',
        //     function ($m) use (&$fails) {
        //         [$oldUrl, $oldId] = [$m[1], $m[2]];
        //         $mm = self::upload_media($oldUrl);
        //         if ($mm) {
        //             return '"image":{"url":"' . $mm['url'] . '", "id":' . $mm['id'] . '}';
        //         }
        //         $fails++;
        //     },
        //     $html
        // );


        //fix for gutenberg css var in attributes (maybe use wp_slash on al post data but did not had time to test this with elementor)
        $html = str_replace('(\u002d\u002d', '(--', $html);

        return [
            'template' => $html,
            'fails' => $fails,
        ];
    }

    public static function upload_media($media_url)
    {
        // example url: https:\/\/uicore.pro\/templates\/globalchart\/wp-content\/uploads\/sites\/113\/2025\/07\/Financial-Consultant-Hero-Image-1.webp
        $media_url = str_replace('\/', '/', $media_url);
        // Extract filename from URL
        $filename = basename(parse_url($media_url, PHP_URL_PATH));

        // Check if the file already exists in the Media Library by filename
        $existing = get_posts([
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => [
                [
                    'key'     => '_wp_attached_file',
                    'value'   => $filename,
                    'compare' => 'LIKE',
                ],
            ],
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);

        if (!empty($existing)) {
            // File already exists – return the attachment ID
            return [
                'id' => $existing[0],
                'url' => str_replace('\/', '/', wp_get_attachment_url($existing[0])),
            ];
        }

        // Make sure WordPress media functions are loaded
        if (!function_exists('media_handle_sideload')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }

        // Download the file to a temp location
        $tmp = download_url($media_url);

        if (is_wp_error($tmp)) {
            \error_log('Error downloading file: ' . $media_url);
            \error_log('Error:  ' . $tmp->get_error_message());
            return false;
        }

        $file_array = [
            'name'     => $filename,
            'tmp_name' => $tmp,
        ];

        // Upload the file
        $attachment_id = media_handle_sideload($file_array, 0);

        // Clean up if there's an error
        if (is_wp_error($attachment_id)) {
            \error_log('Error inserting file: ' . $media_url);
            \error_log('Error:  ' . $attachment_id->get_error_message());
            @unlink($file_array['tmp_name']);
            return false;
        }

        return [
            'id' => $attachment_id,
            'url' => str_replace('\/', '/', wp_get_attachment_url($attachment_id)),
        ];
    }
}
