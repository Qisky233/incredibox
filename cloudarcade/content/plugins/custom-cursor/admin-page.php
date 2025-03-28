<?php
/**
 * Admin interface for Custom Cursor Plugin
 */

if(!has_admin_access()){
    exit;
}

$plugin_slug = basename(dirname(__FILE__));

// Handle form submission
if (isset($_POST['action']) && $_POST['action'] == 'save_cursor') {
    $cursor_style = $_POST['cursor_style'] ?? '0';
    $custom_normal_url = $_POST['custom_normal_url'] ?? '';
    $custom_hover_url = $_POST['custom_hover_url'] ?? '';
    
    set_plugin_pref($plugin_slug, 'cursor_style', $cursor_style);
    set_plugin_pref($plugin_slug, 'custom_normal_url', $custom_normal_url);
    set_plugin_pref($plugin_slug, 'custom_hover_url', $custom_hover_url);
    
    show_alert('Cursor settings saved!', 'success');
}

// Get current settings
$current_style = get_plugin_pref($plugin_slug, 'cursor_style', '0');
$current_normal_url = get_plugin_pref($plugin_slug, 'custom_normal_url', '');
$current_hover_url = get_plugin_pref($plugin_slug, 'custom_hover_url', '');
?>

<div class="section">
    <div class="bs-callout bs-callout-info">
        Custom Cursor Plugin: Select a cursor style or use custom cursor URLs.
    </div>

    <h4>Cursor Style Settings</h4>

    <div class="mb-3">
        <form method="post">
            <input type="hidden" name="action" value="save_cursor">
            
            <!-- Predefined Styles -->
            <div class="form-group mb-3">
                <label for="cursor_style"><b>Select Cursor Style</b></label>
                <select name="cursor_style" id="cursor_style" class="form-control">
                    <option value="0" <?php echo $current_style == '0' ? 'selected' : ''; ?>>Default System Cursor</option>
                    <option value="1" <?php echo $current_style == '1' ? 'selected' : ''; ?>>Cursor Style 1</option>
                    <option value="2" <?php echo $current_style == '2' ? 'selected' : ''; ?>>Cursor Style 2</option>
                    <option value="3" <?php echo $current_style == '3' ? 'selected' : ''; ?>>Cursor Style 3</option>
                    <option value="4" <?php echo $current_style == '4' ? 'selected' : ''; ?>>Cursor Style 4</option>
                    <option value="5" <?php echo $current_style == '5' ? 'selected' : ''; ?>>Cursor Style 5</option>
                    <option value="custom" <?php echo $current_style == 'custom' ? 'selected' : ''; ?>>Custom URL</option>
                </select>
            </div>

            <!-- Custom URL inputs -->
            <div id="custom-urls" class="mb-3" style="display: none;">
                <div class="form-group mb-3">
                    <label for="custom_normal_url"><b>Custom Normal Cursor URL</b></label>
                    <input type="url" class="form-control" name="custom_normal_url" id="custom_normal_url" 
                           value="<?php echo htmlspecialchars($current_normal_url); ?>" 
                           placeholder="https://example.com/cursor-normal.png">
                </div>

                <div class="form-group mb-3">
                    <label for="custom_hover_url"><b>Custom Hover Cursor URL</b></label>
                    <input type="url" class="form-control" name="custom_hover_url" id="custom_hover_url" 
                           value="<?php echo htmlspecialchars($current_hover_url); ?>"
                           placeholder="https://example.com/cursor-hover.png">
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <small>Note: Recommended cursor image size is 32x32 - 64x64 pixels.</small>
            </div>

            <div id="cursor-preview" class="my-3 p-3 border rounded" style="height: 200px; background: #f8f9fa;">
                <div class="normal-area" style="height: 50%; display: flex; align-items: center; justify-content: center; border-bottom: 1px dashed #ccc;">
                    Normal cursor preview
                </div>
                <div class="hover-area" style="height: 50%; display: flex; align-items: center; justify-content: center;">
                    Hover cursor preview
                </div>
            </div>

            <button class="btn btn-primary btn-md">Save Settings</button>
        </form>
    </div>
</div>

<style>
#cursor-preview {
    user-select: none;
    -webkit-user-select: none;
}
.normal-area, .hover-area {
    cursor: default;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const normalArea = document.querySelector('.normal-area');
    const hoverArea = document.querySelector('.hover-area');
    const styleSelect = document.getElementById('cursor_style');
    const customUrls = document.getElementById('custom-urls');
    const normalUrlInput = document.getElementById('custom_normal_url');
    const hoverUrlInput = document.getElementById('custom_hover_url');
    
    function updatePreview(style) {
        // Reset cursors
        normalArea.style.cursor = 'default';
        hoverArea.style.cursor = 'pointer';
        
        if (style === '0') {
            return;
        }

        if (style === 'custom') {
            const normalUrl = normalUrlInput.value;
            const hoverUrl = hoverUrlInput.value;
            
            if (normalUrl) {
                normalArea.style.cursor = `url('${normalUrl}') 0 0, auto`;
            }
            if (hoverUrl) {
                hoverArea.style.cursor = `url('${hoverUrl}') 0 0, pointer`;
            }
            return;
        }

        const baseUrl = '<?php echo DOMAIN . "content/plugins/" . $plugin_slug; ?>';
        normalArea.style.cursor = `url('${baseUrl}/cursors/cursor-${style}a.png') 0 0, auto`;
        hoverArea.style.cursor = `url('${baseUrl}/cursors/cursor-${style}b.png') 0 0, pointer`;
    }

    styleSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customUrls.style.display = 'block';
        } else {
            customUrls.style.display = 'none';
        }
        updatePreview(this.value);
    });

    normalUrlInput.addEventListener('input', function() {
        if (styleSelect.value === 'custom') {
            updatePreview('custom');
        }
    });

    hoverUrlInput.addEventListener('input', function() {
        if (styleSelect.value === 'custom') {
            updatePreview('custom');
        }
    });

    // Set initial state
    if (styleSelect.value === 'custom') {
        customUrls.style.display = 'block';
    }
    updatePreview('<?php echo $current_style; ?>');
});
</script>