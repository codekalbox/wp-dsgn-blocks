<?php

namespace UiCoreBlocks;

use WP_HTML_Tag_Processor;

class WooFrontend
{
    /**
     * Attributes to remove from Woo blocks
     */
    const REMOVE_ATTRIBUTES = [
        "fontSizeDsPr",
        "colorDsPr",
        "alignSelf",
        "columnSpan",
        "rowSpan",
        "flexAlignSelf",
        "order",
        "display",
        "gOutline",
        "gFlow",
        "gColumns",
        "gRows",
        "direction",
        "align",
        "justify",
        "wrap",
        "gap",
        "m",
        "p",
        "width",
        "height",
        "minWidth",
        "minHeight",
        "maxWidth",
        "maxHeight",
        "overflow",
        "bg",
        "font",
        "fontSize",
        "letterSpacing",
        "lineHeight",
        "color",
        //"textAlign",
        "textTransform",
        "wordBreak",
        "lineBreak",
        "textWrap",
        "linkColor",
        "borderStyle",
        "border",
        "borderColor",
        "borderColorGlow",
        "borderColorGradient",
        "borderRadius",
        "shadow",
        "position",
        "vertical",
        "vOffset",
        "horizontal",
        "hOffset",
        "zIndex",
        "opacity",
        "filter",
        "bgFilter",
        "transform",
        "transition",
        "blendingMode",
        "cursor",
        "customCursor",
        "animationTrigger",
        "animationName",
        "animationDuration",
        "animationDelay",
        "animationOffset",
        "imageMaskBlock",
        "customImageMaskBlock",
        "customImageMaskBlockAlt",
        "maskSize",
        "repeat",
        "maskLeftPosition",
        "maskTopPosition",
        "maskWidth",
        "maskHeight",
        "customCss",
        "tag",
        //"url", ??
        //"target", ??
        "visibility",
        //"id",
        //"classNames",
        //"tagAttrs",
        "syncParent",
        "desyncKeys",
        "___",
        "blockId",
        "advanced",
        "isActive"
    ];

    public function __construct()
    {
        add_filter('render_block_data', [$this, 'update_uicore_woo_block_attributes'], 10, 1);
        add_filter('render_block', [$this, 'update_uicore_woo_block_data_attributes'], 99, 2);
    }

    /**
     * Remove woo block attributes before rendering the block
     * The scope of this function is to add uicore classes on woo blocks and remove block attributes because 
     * woocommerce add them as data-attributes using using __experimental_woocommerce_blocks_add_data_attributes_to_block filter
     *
     * @param array $block
     * @return array
     */
    public function update_uicore_woo_block_attributes(array $block): array
    {
        if (!is_admin() && isset($block['attrs']) && is_array($block['attrs'])) {

            $attrs        = isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [];
            $wc_namespace = isset($attrs['__woocommerceNamespace']) ? $attrs['__woocommerceNamespace'] : null;

            $is_woo_by_name      = !empty($block['blockName']) && strpos($block['blockName'], 'woocommerce/') === 0;
            $is_woo_by_namespace = !empty($wc_namespace);
            $block_id            =  $attrs['blockId'] ?? null;

            if (($is_woo_by_name || $is_woo_by_namespace) && $block_id) {

                $block['attrs']['className'] = ($block['attrs']['className'] ?? '') . ' uicore-bl-content uicore-bl-woocommerce uicore-block-' . $block_id;

                foreach (self::REMOVE_ATTRIBUTES as $attr) {
                    if (array_key_exists($attr, $block['attrs'])) {
                        unset($block['attrs'][$attr]);
                    }
                }
            }
        }

        return $block;
    }

    /** 
     * Update Woo blocks that haves tagAttrs attribute to add them as real HTML attributes
     * Woocommerce add all gutenberg blocks as data-attributes, and because tagAttrs is an array of objects, 
     * we need to convert them to real attributes
     *
     * @param string $block_content
     * @param array $block
     * @return string
     */
    public function update_uicore_woo_block_data_attributes($block_content, $block)
    {
        if (is_admin() || empty($block_content) || !is_array($block)) {
            return $block_content;
        }

        $attrs        = isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [];
        $wc_namespace = isset($attrs['__woocommerceNamespace']) ? $attrs['__woocommerceNamespace'] : null;

        $is_woo_by_name      = !empty($block['blockName']) && strpos($block['blockName'], 'woocommerce/') === 0;
        $is_woo_by_namespace = !empty($wc_namespace);
        $tagAttrs            = isset($attrs['tagAttrs']) && is_array($attrs['tagAttrs']) ? $attrs['tagAttrs'] : [];
        $hasTagAttrs         = !empty($tagAttrs);

        if (($is_woo_by_name || $is_woo_by_namespace) && $hasTagAttrs) {
            $html = $block_content;

            if (class_exists('\WP_HTML_Tag_Processor')) {
                $p = new \WP_HTML_Tag_Processor($html);

                if ($p->next_tag()) {
                    foreach ($tagAttrs as $item) {
                        if (!is_array($item) || empty($item['name'])) {
                            continue;
                        }

                        $name  = (string) $item['name'];
                        $value = isset($item['value']) ? (string) $item['value'] : '';

                        $p->set_attribute($name, $value);
                    }

                    // Remove the config attribute itself
                    $p->remove_attribute('data-tag-attrs');
                    $html = $p->get_updated_html();
                }
            }

            return $html;
        }

        return $block_content;
    }
}
