<?php
/**
 * This file handles AJAX requests for saving plugin preferences.
 * It checks for admin access, processes the form data (e.g., 'string_value'),
 * and saves the corresponding preference to the database.
 */

session_start();

// Required configuration and initialization files for the CMS
require_once('../../../config.php');
require_once('../../../init.php');
require_once('../../../includes/commons.php');

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__)); // 'sample-plugin'

if (has_admin_access()) {

    // Check if an action is provided in the request
    if (isset($_POST['action'])) {

        // Handle saving the 'string_value' preference
        if ($_POST['action'] == 'string_value') {
            $string_value = $_POST['string_value'] ?? '';

            // Save the string value preference to the database
            set_plugin_pref($plugin_slug, 'string_value', $string_value);

            // Return success response
            echo "ok";
        }
    } else {
        // No valid action provided
        // echo "Invalid action.";
    }
} else {
    // If the user does not have admin access, return a forbidden message
    exit('Forbidden');
}

?>
