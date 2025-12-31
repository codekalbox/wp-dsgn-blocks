<?php

namespace UiCoreBlocks;


$postId = $block->context['postId'] ?? null;
$isInsideLoop = !empty($block->context['queryId'] ?? null) ? true : false;

$fieldAttr = (isset($attributes['field']) && is_array($attributes['field']))
    ? array_change_key_case($attributes['field'], CASE_LOWER)
    : [];

if (isset($attributes['url']) && $attributes['url'] !== '') {
    $urlAttr = array_combine(
        ['urltype', 'urlfield', 'urlfieldcustom'],
        array_pad(explode('|', $attributes['url'], 3), 3, '')
    );
    $fieldAttr = array_merge($fieldAttr, $urlAttr);

    if (isset($attributes['target']) && $attributes['target'] !== '') {
        $fieldAttr['urltarget'] = $attributes['target'];
    }
}

$default = '';

/** Get the dynamic value */
$value = BlocksDynamicContent::get_dynamic_content_value(
    $fieldAttr,
    $default,
    $postId,
    $isInsideLoop
);

/** Inject inside the first empty tag in $content */
if (preg_match(
    '/^\s*(<([a-z][\w:-]*)\b[^>]*>)(?:\s|&nbsp;|&#160;|<!--.*?-->)*(<\/\2\s*>)\s*$/is',
    trim((string) $content),
    $m
)) {
    echo $m[1] . $value . $m[3];
} else {
    echo $value;
}
