<?php
add_to_hook('head_bottom', function() {
    $plugin_slug = basename(dirname(__FILE__));
    $cursor_style = get_plugin_pref($plugin_slug, 'cursor_style', '0');

    if ($cursor_style === '0') return;

    if ($cursor_style === 'custom') {
        $normal_url = get_plugin_pref($plugin_slug, 'custom_normal_url', '');
        $hover_url = get_plugin_pref($plugin_slug, 'custom_hover_url', '');
    } else {
        $base_url = DOMAIN . "content/plugins/" . $plugin_slug;
        $normal_url = "$base_url/cursors/cursor-{$cursor_style}a.png";
        $hover_url = "$base_url/cursors/cursor-{$cursor_style}b.png";
    }

    if (!empty($normal_url)) {
        echo '<style>
            body, * {
                cursor: url("' . $normal_url . '") 0 0, auto !important;
            }
            a, button, .btn, [role="button"], 
            a:hover, button:hover, .btn:hover, [role="button"]:hover,
            input[type="submit"], input[type="button"],
            select, option {
                cursor: url("' . $hover_url . '") 0 0, pointer !important;
            }
        </style>';
    }
});