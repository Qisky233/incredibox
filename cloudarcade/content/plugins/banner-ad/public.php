<?php
/**
 * This file interacts with the frontend of the CMS.
 * It adds hooks to include ad initialization code in the head
 * and includes the widget definition file.
 */

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__));

// Hook into 'head_bottom' to add ad initialization script
add_to_hook('head_bottom', function() use ($plugin_slug) {
    // Get the initialization script
    $init_script = get_plugin_pref($plugin_slug, 'init_script', '');
    
    if(!empty($init_script)){
        // Decode the base64-encoded script
        $decoded_script = base64_decode($init_script);
        
        // Output the script
        echo $decoded_script;
    }
});

// Include widget definition
require_once(dirname(__FILE__) . '/widget.php');
?>