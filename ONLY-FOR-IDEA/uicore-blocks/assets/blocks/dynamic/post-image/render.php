<?php

namespace UiCoreBlocks;

if (!function_exists(__NAMESPACE__ . '\uicore_inject_into_empty_wrapper')) {

    function uicore_inject_into_empty_wrapper(string $html, string $inner): string
    {
        if ($html === '') {
            return $inner;
        }

        if (preg_match('/^\s*(<([a-z][\w:-]*)\b[^>]*>)(\s*)(<\/\2\s*>)\s*$/is', $html, $m)) {
            return $m[1] . $inner . $m[4];
        }

        return $html;
    }
}

if (!function_exists(__NAMESPACE__ . '\uicore_render_img_into_wrapper')) {

    function uicore_render_img_into_wrapper(string $html, array $img): string
    {
        $tp = new \WP_HTML_Tag_Processor($html);

        if ($tp->next_tag('img')) {
            $tp->set_attribute('src', esc_url($img['src']));

            if (!empty($img['id'])) {
                $cls = trim(preg_replace('/\bwp-image-\d+\b/', '', (string) $tp->get_attribute('class')) ?? '');
                $tp->set_attribute('class', trim($cls . ' wp-image-' . $img['id']));
                $tp->set_attribute('data-bl-image', (string) $img['id']);
            } else {
                $tp->set_attribute('class', trim(preg_replace('/\bwp-image-\d+\b/', '', (string) $tp->get_attribute('class')) ?? ''));
                $tp->remove_attribute('data-bl-image');
            }

            if (!$tp->get_attribute('width') && !empty($img['width'])) {
                $tp->set_attribute('width', (string) $img['width']);
            }

            if (!$tp->get_attribute('height') && !empty($img['height'])) {
                $tp->set_attribute('height', (string) $img['height']);
            }

            if (!empty($img['srcset'])) {
                $tp->set_attribute('srcset', $img['srcset']);
            } else {
                $tp->remove_attribute('srcset');
            }

            if (!empty($img['sizes'])) {
                $tp->set_attribute('sizes', $img['sizes']);
            } else {
                $tp->remove_attribute('sizes');
            }

            $tp->set_attribute('alt', $img['alt'] ?? '');

            if (!$tp->get_attribute('decoding') && !empty($img['decoding'])) {
                $tp->set_attribute('decoding', $img['decoding']);
            }

            if (!$tp->get_attribute('loading') && !empty($img['loading'])) {
                $tp->set_attribute('loading', $img['loading']);
            }

            if (!$tp->get_attribute('fetchpriority') && !empty($img['fetchpriority'])) {
                $tp->set_attribute('fetchpriority', $img['fetchpriority']);
            }

            return $tp->get_updated_html();
        }

        $attrs = [
            'src'      => esc_url($img['src']),
            'decoding' => $img['decoding'],
            'alt'      => $img['alt'] ?? '',
            'as'       => 'image',
        ];

        if (!empty($img['loading'])) {
            $attr['loading'] = $img['loading'];
        }

        if (!empty($img['fetchpriority'])) {
            $attr['fetchpriority'] = $img['fetchpriority'];
        }

        if (!empty($img['id'])) {
            $attrs['class'] = 'wp-image-' . $img['id'];
            $attrs['data-bl-image'] = (string) $img['id'];
        }

        if (!empty($img['width'])) {
            $attrs['width'] = (string) $img['width'];
        }

        if (!empty($img['height'])) {
            $attrs['height'] = (string) $img['height'];
        }

        if (!empty($img['srcset'])) {
            $attrs['srcset'] = $img['srcset'];
        }

        if (!empty($img['sizes'])) {
            $attrs['sizes'] = $img['sizes'];
        }

        $imgTag = '<img ' . implode(' ', array_map(function ($k, $v) {
            return sprintf('%s="%s"', esc_attr($k), esc_attr($v));
        }, array_keys($attrs), $attrs)) . '>';

        return uicore_inject_into_empty_wrapper($html, $imgTag);
    }
}

/** Base output = saved content */
$output = (string) $content;
$dynamicContent = $attributes['dynamicContent'] ?? null;
$loadingImage = $attributes['loadingImage'] ?? '';

//set image url 
$href = '';
if (preg_match('/<([a-z][a-z0-9]*)\b([^>]*)>/i', $content, $m)) {
    if (preg_match('/\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/i', $m[2], $h)) {
        $href = $h[1] !== '' ? $h[1] : ($h[2] !== '' ? $h[2] : ($h[3] ?? ''));
    }
}

if ($href !== '') {
    $urlAttr = array_combine(
        ['urltype', 'urlfield', 'urlfieldcustom'],
        array_pad(explode('|', $href, 3), 3, '')
    );

    $post_id = $block->context['queryId'] ? $block->context['postId'] : BlocksDynamicContent::dynamic_get_assigned_page_id();
    $imgUrl = BlocksDynamicContent::get_dynamic_content_url(
        $urlAttr,
        $post_id
    );

    $href = esc_url($imgUrl ?: '#');

    if (class_exists('\WP_HTML_Tag_Processor')) {
        $p = new \WP_HTML_Tag_Processor($output);
        if ($p->next_tag('a')) { // only the first <a>
            $p->set_attribute('href', $href); // add or replace
        }
        $output = $p->get_updated_html();
    }
}

if (empty($dynamicContent) || (($dynamicContent['type'] ?? '') === 'featuredImage')) {

    $post_id = !empty($block->context['queryId'] ?? null) ? $block->context['postId'] : BlocksDynamicContent::dynamic_get_assigned_page_id();
    $featId  = $post_id ? (int) get_post_thumbnail_id($post_id) : 0;

    if ($featId) {
        $src = '';
        $width = '';
        $height = '';
        $srcset = '';
        $sizes = '';
        $alt = '';

        if ($img = wp_get_attachment_image_src($featId, 'full')) {
            $src = $img[0];
            $width = $img[1] ?? $width;
            $height = $img[2] ?? $height;
        } elseif ($img = wp_get_attachment_image_src($featId, 'thumbnail')) {
            $src = $img[0];
            $width = $img[1] ?? $width;
            $height = $img[2] ?? $height;
        }

        $srcset = wp_get_attachment_image_srcset($featId, 'full') ?: '';
        $sizes  = wp_get_attachment_image_sizes($featId, 'full') ?: '';
        $alt    = get_post_meta($featId, '_wp_attachment_image_alt', true) ?: '';
        $fetchPriority  = $loadingImage === 'lazy' ? '' : 'high';
        $loading        = ['lazy' => 'lazy', 'preload' => 'eager'][$loadingImage] ?? '';

        $output = uicore_render_img_into_wrapper($output, [
            'id'            => $featId,
            'src'           => $src,
            'width'         => $width,
            'height'        => $height,
            'srcset'        => $srcset,
            'sizes'         => $sizes,
            'alt'           => $alt,
            'fetchpriority' => $fetchPriority,
            'loading'       => $loading,
            'decoding'      => 'async',
        ]);
    }
}

echo $output;
