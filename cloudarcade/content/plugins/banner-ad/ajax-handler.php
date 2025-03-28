<?php
/**
 * This file handles AJAX requests for the Banner Ad plugin.
 * It processes form submissions for ad settings and saves them to the database.
 */

if(!has_admin_access()){
    exit;
}

session_start();

// Required configuration and initialization files for the CMS
require_once('../../../config.php');
require_once('../../../init.php');
require_once('../../../includes/commons.php');

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__));

if (has_admin_access()) {
    // Check if an action is provided in the request
    if (isset($_POST['action'])) {
        // Handle initialization script AJAX save
        if ($_POST['action'] == 'save_init_script_ajax') {
            $init_script = isset($_POST['init_script']) ? $_POST['init_script'] : '';
            
            // Base64 encode to bypass shared hosting JS restrictions
            $encoded_init_script = base64_encode($init_script);
            
            // Save the encoded script
            set_plugin_pref($plugin_slug, 'init_script', $encoded_init_script);
            
            echo "Initialization script saved successfully!";
            exit;
        }
        
        // Handle ad slot AJAX save
        if ($_POST['action'] == 'save_ad_slot_ajax') {
            $slot_id = isset($_POST['slot_id']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['slot_id']) : '';
            $slot_code = isset($_POST['slot_code']) ? $_POST['slot_code'] : '';
            
            if(empty($slot_id)){
                echo "Error: Ad ID is required!";
                exit;
            }
            
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
            
            echo "Ad slot saved successfully!";
            exit;
        }
        
        // Handle ad slot deletion via AJAX
        if ($_POST['action'] == 'delete_ad_slot_ajax' && isset($_POST['slot_id'])) {
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
                echo "Ad slot deleted successfully!";
            } else {
                echo "Ad slot not found!";
            }
            exit;
        }
    }
    
    // No valid action provided
    echo "Invalid action.";
} else {
    // If the user does not have admin access, return a forbidden message
    exit('Forbidden 1101');
}
?>