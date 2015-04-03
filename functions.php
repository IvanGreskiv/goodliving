<?php
/*-----------------------------------------------------------------------------------*/
/* Start ColorLabs Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/
error_reporting(0);

if ( ! isset( $content_width ) ) $content_width = 768;

// Set path to ColorLabs Framework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/includes/';

// ColorLabs Admin
require_once ($functions_path . 'admin-init.php');			// Admin Init

// Theme specific functionality
$includes = array(
				'includes/theme-functions.php', 		// Custom theme functions
        'includes/theme-options.php', 			// Options panel settings and custom settings
				'includes/theme-comments.php', 			// Custom comments/pingback loop
				'includes/theme-js.php', 				// Load JavaScript via wp_enqueue_script
				'includes/theme-sidebar-init.php', 			// Initialize widgetized areas
				'includes/theme-widgets.php',			// Theme widgets
				'includes/theme-gateways.php',	
				'includes/theme-custom-type.php',
				'includes/theme-cron.php',
				'includes/theme-emails.php',
				'includes/theme-bookmark.php',
				'includes/theme-property.php',				
				'includes/orders/admin-orders.php',
				);

// Include the user's custom_functions file, but only if it exists
if (file_exists($custom_path . '/custom_functions.php'))
	$includes[] = 'custom/custom_functions.php';

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'colabs_includes', $includes );
			
foreach ( $includes as $i ) {
	locate_template( $i, true );
}
// Classes
get_template_part('includes/orders/orders.class');

if ( !is_admin() ) :
    include_once($includes_path . 'theme-login.php');
    include_once($includes_path . 'forms/login/login-form.php');
    include_once($includes_path . 'forms/login/login-process.php');
    include_once($includes_path . 'forms/register/register-form.php');
    include_once($includes_path . 'forms/register/register-process.php');
    include_once($includes_path . 'forms/forgot-password/forgot-password-form.php');
endif;

/*-----------------------------------------------------------------------------------*/
/* Define Page */
/*-----------------------------------------------------------------------------------*/
define('CL_DASHBOARD_URL', get_permalink(get_option('colabs_dashboard_url')));
define('CL_PROFILE_URL', get_permalink(get_option('colabs_profile_url')));
define('CL_EDIT_URL', get_permalink(get_option('colabs_edit_url')));
?>