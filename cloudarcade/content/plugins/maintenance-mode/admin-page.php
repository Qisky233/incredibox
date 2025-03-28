<?php
/**
 * This file is responsible for displaying and managing the maintenance mode plugin's admin interface.
 * It allows administrators to enable/disable maintenance mode and customize the maintenance page.
 */

if(!has_admin_access()){
    exit('p');
}

// Dynamically set the plugin slug
$plugin_slug = basename(dirname(__FILE__));

// Handle form submissions based on 'action'
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'maintenance_status') {
        // Save maintenance mode status
        $enabled = isset($_POST['maintenance_enabled']) ? 'true' : 'false';
        set_plugin_pref($plugin_slug, 'maintenance_enabled', $enabled);
        
        // Save admin visibility setting
        $admin_enabled = isset($_POST['maintenance_enabled_for_admin']) ? 'true' : 'false';
        set_plugin_pref($plugin_slug, 'maintenance_enabled_for_admin', $admin_enabled);
        
        show_alert('Maintenance mode settings saved!', 'success');
    }
    
    if ($_POST['action'] == 'maintenance_html') {
        if (isset($_POST['maintenance_html'])) {
            $custom_html = $_POST['maintenance_html'];
            file_put_contents(dirname(__FILE__) . '/custom.html', $custom_html);
            show_alert('Custom HTML template saved!', 'success');
        }
    }
    
    if ($_POST['action'] == 'reset_html') {
        if (file_exists(dirname(__FILE__) . '/custom.html')) {
            unlink(dirname(__FILE__) . '/custom.html');
            show_alert('HTML template reset to default!', 'success');
        }
    }
}

// Get current settings
$maintenance_enabled = get_plugin_pref_bool($plugin_slug, 'maintenance_enabled', false);
$maintenance_enabled_for_admin = get_plugin_pref_bool($plugin_slug, 'maintenance_enabled_for_admin', false);

// Get HTML content
$custom_html_exists = file_exists(dirname(__FILE__) . '/custom.html');
$html_content = $custom_html_exists 
    ? file_get_contents(dirname(__FILE__) . '/custom.html')
    : file_get_contents(dirname(__FILE__) . '/default.html');
?>

<div class="section">
    <div class="bs-callout bs-callout-info">
        Maintenance Mode Plugin: Configure your maintenance page settings and customize the page content.
    </div>

    <!-- Status Settings Section -->
    <div class="mb-4">
        <h4>Maintenance Mode Status</h4>
        <form method="post">
            <input type="hidden" name="action" value="maintenance_status">
            
            <!-- Enable Maintenance Mode -->
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="maintenance_enabled" class="form-check-input" id="maintenance_enabled" 
                        <?php echo $maintenance_enabled ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="maintenance_enabled">Enable maintenance mode for visitors</label>
                </div>
                <small class="form-text text-muted">
                    Current status: <?php echo $maintenance_enabled ? 'Enabled' : 'Disabled'; ?>
                </small>
            </div>

            <!-- Admin Visibility -->
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="maintenance_enabled_for_admin" class="form-check-input" id="maintenance_enabled_for_admin" 
                        <?php echo $maintenance_enabled_for_admin ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="maintenance_enabled_for_admin">Show maintenance page to administrators</label>
                </div>
                <small class="form-text text-muted">
                    Current status: <?php echo $maintenance_enabled_for_admin ? 'Visible to admins' : 'Hidden from admins'; ?>
                </small>
            </div>

            <button class="btn btn-primary btn-md">Save Status Settings</button>
        </form>
    </div>

    <!-- HTML Editor Section -->
    <div class="mb-3">
        <h4>Maintenance Page HTML</h4>
        <form method="post">
            <input type="hidden" name="action" value="maintenance_html">
            <textarea name="maintenance_html" class="form-control code" rows="20"><?php 
                echo htmlspecialchars($html_content); 
            ?></textarea>
            <small class="form-text text-muted mb-2 d-block">
                Edit the HTML for your maintenance page. PHP code is not allowed and will not be executed.
                <?php if ($custom_html_exists): ?>
                    You are currently using a custom template.
                <?php else: ?>
                    You are currently using the default template.
                <?php endif; ?>
            </small>
            <div class="btn-group">
                <button class="btn btn-primary btn-md">Save HTML</button>
                <?php if ($custom_html_exists): ?>
                    <button type="submit" formaction="?action=reset_html" name="action" value="reset_html" 
                            class="btn btn-outline-secondary btn-md" 
                            onclick="return confirm('Are you sure you want to reset to the default template?');">
                        Reset to Default
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<style>
    textarea.code {
        font-family: monospace;
        white-space: pre;
        tab-size: 4;
    }
    .btn-group {
        gap: 10px;
    }
</style>