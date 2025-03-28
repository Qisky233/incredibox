<?php

add_admin_hook('admin_dashboard_notifications', function() {
    $plugin_slug = basename(dirname(__FILE__));
    if(get_plugin_pref_bool($plugin_slug, 'maintenance_enabled', false)){
        show_alert('Maintenance Mode is active!', 'warning');
    }
});

?>