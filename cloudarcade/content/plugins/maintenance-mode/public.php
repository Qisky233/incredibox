<?php

$plugin_slug = basename(dirname(__FILE__));

// Only proceed if plugin exists
if (!is_plugin_exist($plugin_slug)) {
    return;
}

// Check if maintenance mode is enabled
if (!get_plugin_pref_bool($plugin_slug, 'maintenance_enabled', false)) {
    return;
}

global $base_taxonomy;

// Allow access to login page
if ($base_taxonomy === 'login') {
    return;
}

// Handle admin visibility
$show_admin_banner = false;
if (has_admin_access() && get_plugin_pref_bool($plugin_slug, 'maintenance_enabled_for_admin', false)) {
    $show_admin_banner = true;
}

if ($show_admin_banner) {
    // Show floating banner for admins
    ?>
    <div style="
        background: #dc3545;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        padding: 10px 20px;
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border-radius: 0 0 4px 0;
    ">
        ⚠️ Maintenance Mode Enabled
    </div>
    <?php
} else {
    // Show maintenance page to visitors
    $template_path = dirname(__FILE__) . '/custom.html';
    if (!file_exists($template_path)) {
        $template_path = dirname(__FILE__) . '/default.html';
    }
    
    if (file_exists($template_path)) {
        echo file_get_contents($template_path);
    } else {
        // Fallback if no template exists
        echo '<h1>Site Under Maintenance</h1><p>Please check back later.</p>';
    }
    exit();
}