<?php
/**
 * Plugin Name: Infy import.
 * Description: Infy json import.
 * Version: 1.0.0
 */


// Insert the infy_import_add_option_page in the 'admin_menu'
add_action( 'admin_menu', 'infy_import_add_option_page' );

// Displays options
function infy_import_add_option_page() {
    if ( function_exists( 'add_options_page' ) ) {
        add_options_page( 'Informulary', 'Infy Import', 'manage_options', __FILE__, 'infy_import_options_page' );
    }
}


function infy_import_options_page() {
  require_once(dirname(__FILE__).'/admin-page.php');
}

