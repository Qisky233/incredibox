<?php
if (!has_admin_access()) {
    exit('Access denied');
}

// Get user ID from URL
$user_id = isset($_GET['user-id']) ? (int)$_GET['user-id'] : 0;

// Get user object
$user = User::getById($user_id);
if (!$user || $user->role !== 'crew') {
    exit('Invalid user or not a crew member');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // First revoke all existing permissions
        foreach (AVAILABLE_PERMISSIONS as $page => $data) {
            $user->revokeAccess($page);

            // Handle child permissions if they exist
            if (isset($data['children'])) {
                foreach ($data['children'] as $slug => $childData) {
                    $user->revokeAccess($page, $slug);
                }
            }
        }

        // Then grant new permissions
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $permission) {
                // Check if it's a child permission
                if (strpos($permission, ':') !== false) {
                    list($page, $slug) = explode(':', $permission);
                    $user->grantAccess($page, $slug);
                } else {
                    // Only grant parent permission if it has no children
                    // or if at least one child permission is selected
                    $hasChildren = isset(AVAILABLE_PERMISSIONS[$permission]['children']);
                    if (!$hasChildren) {
                        $user->grantAccess($permission);
                    }
                }
            }
        }

        show_alert('Permissions updated successfully', 'success');
    } catch (Exception $e) {
        show_alert('Error updating permissions', 'error');
    }
}

// Get current permissions
$current_permissions = $user->getUserPermissions();
$user_permissions = array();

// Format permissions for easier checking
foreach ($current_permissions as $perm) {
    if ($perm['slug']) {
        $user_permissions[] = $perm['page'] . ':' . $perm['slug'];
    } else {
        $user_permissions[] = $perm['page'];
    }
}

$active_plugin_list = get_active_plugin_list();

?>

<div class="section">
    <div class="row">
        <div class="col-12">
            <div class="-----card">
                <div class="-----card-header">
                    <h5>Manage Permissions for "<?php echo esc_string($user->username) ?>"</h5>
                </div>
                <div class="-----card-body">
                    <form method="post">
                        <?php
                        // First render the existing permissions
                        foreach (AVAILABLE_PERMISSIONS as $page => $data): ?>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-parent"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="<?php echo $page ?>"
                                        id="perm-<?php echo $page ?>"
                                        <?php
                                        if (!isset($data['children'])) {
                                            echo in_array($page, $user_permissions) ? 'checked' : '';
                                        } else {
                                            $anyChildChecked = false;
                                            foreach ($data['children'] as $slug => $childData) {
                                                if (in_array($page . ':' . $slug, $user_permissions)) {
                                                    $anyChildChecked = true;
                                                    break;
                                                }
                                            }
                                            echo $anyChildChecked ? 'checked' : '';
                                        }
                                        ?>>
                                    <label class="form-check-label" for="perm-<?php echo $page ?>">
                                        <?php echo $data['title'] ?>
                                    </label>
                                </div>

                                <?php if (isset($data['children'])): ?>
                                    <div class="ms-4">
                                        <?php foreach ($data['children'] as $slug => $childData): ?>
                                            <div class="form-check">
                                                <input class="form-check-input permission-child"
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="<?php echo $page . ':' . $slug ?>"
                                                    id="perm-<?php echo $page . '-' . $slug ?>"
                                                    data-parent="<?php echo $page ?>"
                                                    <?php echo in_array($page . ':' . $slug, $user_permissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="perm-<?php echo $page . '-' . $slug ?>">
                                                    <?php echo $childData['title'] ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Plugin Permissions Section -->
                        <?php if (!empty($active_plugin_list)): ?>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-parent"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="plugin"
                                        id="perm-plugin"
                                        <?php
                                        $anyPluginChecked = false;
                                        foreach ($active_plugin_list as $plugin) {
                                            if (in_array('plugin:' . $plugin['dir_name'], $user_permissions)) {
                                                $anyPluginChecked = true;
                                                break;
                                            }
                                        }
                                        echo $anyPluginChecked ? 'checked' : '';
                                        ?>>
                                    <label class="form-check-label" for="perm-plugin">
                                        Plugins
                                    </label>
                                </div>

                                <div class="ms-4">
                                    <?php foreach ($active_plugin_list as $plugin): ?>
                                        <div class="form-check">
                                            <input class="form-check-input permission-child"
                                                type="checkbox"
                                                name="permissions[]"
                                                value="plugin:<?php echo esc_string($plugin['dir_name']) ?>"
                                                id="perm-plugin-<?php echo esc_string($plugin['dir_name']) ?>"
                                                data-parent="plugin"
                                                <?php echo in_array('plugin:' . $plugin['dir_name'], $user_permissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="perm-plugin-<?php echo esc_string($plugin['dir_name']) ?>">
                                                <?php echo esc_string($plugin['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Warning Alert -->
                        <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading mb-2">⚠️ Important Security Notice</h4>
                            <p class="mb-0">Before granting permissions, please be aware:</p>
                            <ul class="mb-0 mt-2">
                                <li>Ensure you fully trust the user with these permissions</li>
                                <li>Some plugins may not be compatible with crew permissions as they're designed for admin access only</li>
                                <li>Plugins that only check for admin access will deny crew member access</li>
                                <li>If a plugin's code is not properly restricted, crew members might gain admin-level access for that plugin</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                            <a href="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=users" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update parent checkbox based on children
        function updateParentCheckbox(parentId) {
            const parentCheckbox = document.querySelector(`#perm-${parentId}`);
            const childCheckboxes = document.querySelectorAll(`input[data-parent="${parentId}"]`);

            if (childCheckboxes.length > 0) {
                const anyChildChecked = Array.from(childCheckboxes).some(box => box.checked);
                parentCheckbox.checked = anyChildChecked;
            }
        }

        // When parent permission is checked/unchecked
        document.querySelectorAll('.permission-parent').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const parentDiv = this.closest('.mb-3');
                const childCheckboxes = parentDiv.querySelectorAll('.permission-child');
                childCheckboxes.forEach(function(childBox) {
                    childBox.checked = checkbox.checked;
                });
            });
        });

        // When child permission is checked/unchecked
        document.querySelectorAll('.permission-child').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const parentId = this.dataset.parent;
                updateParentCheckbox(parentId);
            });
        });

        // Initial check of all parent checkboxes
        document.querySelectorAll('.permission-parent').forEach(function(parent) {
            const pageId = parent.value;
            updateParentCheckbox(pageId);
        });
    });
</script>