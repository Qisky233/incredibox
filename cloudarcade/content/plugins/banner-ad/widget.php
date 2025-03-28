<?php
/**
 * This file defines the Banner Ad Display widget for use in the CMS.
 */

// Define the Banner Ad Display widget class
class Widget_Banner_Ad_Display extends Widget {
    function __construct() {
        $this->name = 'Banner Ad Display';
        $this->id_base = 'banner_ad_display';
        $this->description = 'Display a banner ad from your Banner Ad collection';
    }
    
    public function widget($instance, $args = array()) {
        // Get plugin slug
        $plugin_slug = basename(dirname(__FILE__));
        
        // Get the selected ad slot ID
        $slot_id = isset($instance['slot_id']) ? $instance['slot_id'] : '';
        
        if(empty($slot_id)){
            // No slot selected
            if(USER_ADMIN){
                echo '<div style="padding: 10px; border: 1px dashed #ccc; text-align: center;">Banner ad not configured</div>';
            }
            return;
        }
        
        // Get all ad slots
        $ad_slots = get_plugin_pref($plugin_slug, 'ad_slots', []);
        if(!is_array($ad_slots)){
            $ad_slots = json_decode($ad_slots, true);
            if(!is_array($ad_slots)){
                $ad_slots = [];
            }
        }
        
        // Check if the selected slot exists
        if(!isset($ad_slots[$slot_id])){
            if(USER_ADMIN){
                echo '<div style="padding: 10px; border: 1px dashed #ccc; text-align: center;">Banner ad "' . htmlspecialchars($slot_id) . '" not found</div>';
            }
            return;
        }
        
        // Get slot data
        $slot = $ad_slots[$slot_id];
        
        // Output ad container with custom class if specified
        $classes = 'banner-ad-container';
        if(!empty($instance['container_class'])){
            $classes .= ' ' . $instance['container_class'];
        }
        
        // Start output
        echo '<div class="' . $classes . '">';
        
        // Decode and output the ad code
        $ad_code = base64_decode($slot['code']);
        echo $ad_code;
        
        // End output
        echo '</div>';
    }
    
    public function form($instance = array()) {
        // Get plugin slug
        $plugin_slug = basename(dirname(__FILE__));
        
        // Default values
        $slot_id = isset($instance['slot_id']) ? $instance['slot_id'] : '';
        $container_class = isset($instance['container_class']) ? $instance['container_class'] : '';
        
        // Get all ad slots
        $ad_slots = get_plugin_pref($plugin_slug, 'ad_slots', []);
        if(!is_array($ad_slots)){
            $ad_slots = json_decode($ad_slots, true);
            if(!is_array($ad_slots)){
                $ad_slots = [];
            }
        }
        ?>
        <div class="mb-3">
            <label class="form-label">Select Banner Ad:</label>
            <select class="form-control" name="slot_id">
                <option value="">-- Select Banner Ad --</option>
                <?php foreach($ad_slots as $id => $slot): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>" <?php echo ($slot_id == $id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($id); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(empty($ad_slots)): ?>
                <small class="text-muted">
                    No banner ads defined yet. Go to the Banner Ad plugin settings to create some.
                </small>
            <?php endif; ?>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Container Class (optional):</label>
            <input type="text" class="form-control" name="container_class" value="<?php echo htmlspecialchars($container_class); ?>">
            <small class="text-muted">
                Add custom CSS classes to the banner container for styling purposes.
            </small>
        </div>
        <?php
    }
}

// Register the Banner Ad Display widget
register_widget('Widget_Banner_Ad_Display');
?>