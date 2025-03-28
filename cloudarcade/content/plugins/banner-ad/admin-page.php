<?php
/**
 * This file is responsible for displaying and managing the Banner Ad plugin's admin interface.
 * It allows administrators to define ad initialization scripts and ad slots.
 */

if(!has_admin_access()){
    exit;
}

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__));

// Handle form submission
if(isset($_POST['action'])){
    $action = $_POST['action'];
    
    if($action == 'save_init_script'){
        // Get the initialization script (decode from base64 if it's encoded)
        $init_script = isset($_POST['init_script']) ? $_POST['init_script'] : '';
        
        // Check if the data is base64 encoded from client-side
        if(isset($_POST['is_encoded']) && $_POST['is_encoded'] === '1' && base64_decode($init_script, true) !== false) {
            $init_script = base64_decode($init_script);
        }
        
        // Base64 encode to bypass shared hosting JS restrictions
        $encoded_init_script = base64_encode($init_script);
        
        // Save the encoded script
        set_plugin_pref($plugin_slug, 'init_script', $encoded_init_script);
        
        show_alert('Initialization script saved successfully!', 'success');
    }
    
    if($action == 'save_ad_slot'){
        $slot_id = isset($_POST['slot_id']) ? sanitize_slot_id($_POST['slot_id']) : '';
        $slot_code = isset($_POST['slot_code']) ? $_POST['slot_code'] : '';
        
        // Check if the data is base64 encoded from client-side
        if(isset($_POST['is_encoded']) && $_POST['is_encoded'] === '1' && base64_decode($slot_code, true) !== false) {
            $slot_code = base64_decode($slot_code);
        }
        
        if(empty($slot_id)){
            show_alert('Ad ID is required!', 'error');
        } else {
            // Get existing slots
            $ad_slots = get_plugin_pref($plugin_slug, 'ad_slots', []);
            if(!is_array($ad_slots)){
                $ad_slots = json_decode($ad_slots, true);
                if(!is_array($ad_slots)){
                    $ad_slots = [];
                }
            }
            
            // Create or update slot
            $ad_slots[$slot_id] = [
                'code' => base64_encode($slot_code)
            ];
            
            // Save slots
            set_plugin_pref($plugin_slug, 'ad_slots', json_encode($ad_slots));
            
            show_alert('Banner ad saved successfully!', 'success');
        }
    }
    
    if($action == 'delete_ad_slot' && isset($_POST['slot_id'])){
        $slot_id = $_POST['slot_id'];
        
        // Get existing slots
        $ad_slots = get_plugin_pref($plugin_slug, 'ad_slots', []);
        if(!is_array($ad_slots)){
            $ad_slots = json_decode($ad_slots, true);
            if(!is_array($ad_slots)){
                $ad_slots = [];
            }
        }
        
        // Remove slot if it exists
        if(isset($ad_slots[$slot_id])){
            unset($ad_slots[$slot_id]);
            set_plugin_pref($plugin_slug, 'ad_slots', json_encode($ad_slots));
            show_alert('Banner ad deleted successfully!', 'success');
        }
    }
}

// Helper function to sanitize slot ID (alphanumeric and underscores only)
function sanitize_slot_id($id){
    return preg_replace('/[^a-zA-Z0-9_]/', '', $id);
}

// Get initialization script
$init_script = get_plugin_pref($plugin_slug, 'init_script', '');
if(!empty($init_script)){
    $init_script = base64_decode($init_script);
}

// Get ad slots
$ad_slots = get_plugin_pref($plugin_slug, 'ad_slots', []);
if(!is_array($ad_slots)){
    $ad_slots = json_decode($ad_slots, true);
    if(!is_array($ad_slots)){
        $ad_slots = [];
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Banner Ad</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <p>This plugin allows you to add JavaScript-based banner advertisements to your site, even on shared hosting that restricts JavaScript in POST requests. It works by safely storing your ad code in an encoded format and providing widgets to display them anywhere on your site.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Ad Initialization Script</h5>
                    <div class="bs-callout bs-callout-info mb-3">
                        <p>This script will be loaded in the &lt;head&gt; section of your site. Use it to initialize your ad provider's code.</p>
                    </div>
                    
                    <form method="post" id="init-script-form">
                        <input type="hidden" name="action" value="save_init_script">
                        <input type="hidden" name="is_encoded" value="1" id="init_is_encoded">
                        <div class="mb-3">
                            <label for="init_script" class="form-label">Script:</label>
                            <textarea id="init_script" name="init_script" class="form-control" rows="8"><?php echo htmlspecialchars($init_script); ?></textarea>
                            <small class="text-muted">
                                Add your ad provider's initialization code here. This typically includes script tags that load the ad network's JavaScript.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Initialization Script</button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add/Edit Banner Ad</h5>
                    <div class="bs-callout bs-callout-info mb-3">
                        <p>Create banner ads that you can place in your site using widgets.</p>
                    </div>
                    
                    <form method="post" id="banner-ad-form">
                        <input type="hidden" name="action" value="save_ad_slot">
                        <input type="hidden" name="is_encoded" value="1" id="ad_is_encoded">
                        <div class="mb-3">
                            <label for="slot_id" class="form-label">Ad ID:</label>
                            <input type="text" id="slot_id" name="slot_id" class="form-control" required
                                   placeholder="E.g., sidebar_top">
                            <small class="text-muted">Use only letters, numbers, and underscores (no spaces).</small>
                            <div id="slot_id_feedback" class="invalid-feedback">
                                Ad ID must contain only letters, numbers, and underscores.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slot_code" class="form-label">Banner Ad HTML/Code:</label>
                            <textarea id="slot_code" name="slot_code" class="form-control" rows="8" required></textarea>
                            <small class="text-muted">
                                Enter the HTML/JavaScript code for this banner ad. This will be displayed in the position you place the widget.
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Banner Ad</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Existing Banner Ads</h5>
                    <?php if(empty($ad_slots)): ?>
                        <p>No banner ads defined yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($ad_slots as $id => $slot): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($id); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-slot"
                                                        data-id="<?php echo htmlspecialchars($id); ?>"
                                                        data-code="<?php echo htmlspecialchars(base64_decode($slot['code'])); ?>">
                                                    Edit
                                                </button>
                                                <form method="post" style="display:inline;" class="delete-form">
                                                    <input type="hidden" name="action" value="delete_ad_slot">
                                                    <input type="hidden" name="slot_id" value="<?php echo htmlspecialchars($id); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this banner ad?');">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Help & Usage</h5>
                    <div class="mb-3">
                        <h6>How to Use Banner Ad</h6>
                        <ol>
                            <li><strong>Initialize Your Ad Provider</strong> - Add your ad provider's JavaScript initialization code to the initialization script section.</li>
                            <li><strong>Create Banner Ads</strong> - Define banner ads with a unique ID and HTML code.</li>
                            <li><strong>Display Ads</strong> - Use the "Banner Ad Display" widget in your theme's widget areas to show your ads. Select which banner ad to display in each widget.</li>
                        </ol>
                        
                        <h6>Example Usage for Google AdSense</h6>
                        <div class="bg-light p-3 mb-3">
                            <p><strong>Initialization Script:</strong></p>
                            <pre><code>&lt;script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"&gt;&lt;/script&gt;
&lt;script&gt;
     (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-XXXXXXXXXX",
          enable_page_level_ads: true
     });
&lt;/script&gt;</code></pre>
                            
                            <p><strong>Banner Ad Code Example:</strong></p>
                            <pre><code>&lt;ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-XXXXXXXXXX"
     data-ad-slot="XXXXXXXXXX"
     data-ad-format="auto"
     data-full-width-responsive="true"&gt;&lt;/ins&gt;
&lt;script&gt;
     (adsbygoogle = window.adsbygoogle || []).push({});
&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit slot button
    const editButtons = document.querySelectorAll('.edit-slot');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');
            
            // Populate form
            document.getElementById('slot_id').value = id;
            document.getElementById('slot_code').value = code;
            
            // Scroll to form
            document.querySelector('.card-body h5.card-title').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Client-side validation for slot_id
    const bannerAdForm = document.getElementById('banner-ad-form');
    const slotIdInput = document.getElementById('slot_id');
    const slotIdFeedback = document.getElementById('slot_id_feedback');
    const slotCodeInput = document.getElementById('slot_code');
    
    bannerAdForm.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();
        
        // Sanitize the slot_id field
        const slotId = slotIdInput.value;
        const sanitizedSlotId = slotId.replace(/[^a-zA-Z0-9_]/g, '');
        
        if (sanitizedSlotId !== slotId) {
            // Show validation feedback
            slotIdInput.classList.add('is-invalid');
            slotIdFeedback.style.display = 'block';
            return false;
        } else {
            // Valid input
            slotIdInput.classList.remove('is-invalid');
            slotIdFeedback.style.display = 'none';
            
            // Set the sanitized value
            slotIdInput.value = sanitizedSlotId;
            
            // Base64 encode the script content to bypass security filters
            const encodedValue = btoa(slotCodeInput.value);
            slotCodeInput.value = encodedValue;
            
            // Submit the form
            this.submit();
        }
    });
    
    // Real-time validation as user types
    slotIdInput.addEventListener('input', function() {
        const slotId = this.value;
        const sanitizedSlotId = slotId.replace(/[^a-zA-Z0-9_]/g, '');
        
        if (sanitizedSlotId !== slotId) {
            // Invalid character detected
            slotIdInput.classList.add('is-invalid');
            slotIdFeedback.style.display = 'block';
        } else {
            // Valid input
            slotIdInput.classList.remove('is-invalid');
            slotIdFeedback.style.display = 'none';
        }
    });
    
    // Handle initialization script form
    const initScriptForm = document.getElementById('init-script-form');
    const initScriptInput = document.getElementById('init_script');
    
    initScriptForm.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();
        
        // Base64 encode the script content to bypass security filters
        const encodedValue = btoa(initScriptInput.value);
        initScriptInput.value = encodedValue;
        
        // Submit the form
        this.submit();
    });
});
</script>