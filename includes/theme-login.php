<?php
/**
 *
 * This controls how the login, logout,
 * registration, and forgot your password pages look.
 * It overrides the default WP pages by intercepting the request.
 *
 * @author ColorLabs
 *
 */

global $pagenow;

// check to prevent php "notice: undefined index" msg
if ( isset($_GET['action']) ) 
    $theaction = $_GET['action']; 
else 
    $theaction ='';

// if the user is on the login page, then let the games begin
if ( $pagenow == 'wp-login.php' && $theaction != 'logout' && !isset($_GET['key']) ) :
	add_action('init', 'colabs_login_init', 98);
	add_filter('colabs_title', 'colabs_log_title');
endif;

// main function that routes the request
function colabs_login_init() {

	nocache_headers();
	
    if ( isset($_REQUEST['action']) ) :
        $action = $_REQUEST['action'];
    else :
        $action = 'login';
    endif;
    switch( $action ) :
        case 'lostpassword' :
        case 'retrievepassword' :
            colabs_show_password();
        break;
        case 'register':
            colabs_show_registration();
            break;
        case 'login':
        default:
            colabs_show_login();
        break;
    endswitch;
    exit;
}

// display the meta page title based on the current page
function colabs_log_title($title) {
    global $pagenow;
    if ( $pagenow == 'wp-login.php' ) :
        switch( $_GET['action'] ) :
            case 'lostpassword':
                $title = __('Retrieve your lost password for ','colabsthemes');
            break;
            case 'login':
                $title = __('Login ','colabsthemes');
            case 'register':
                $title = __('Register at ','colabsthemes');
            default:
                $title = __('Login/Register at ','colabsthemes');
            break;
        endswitch; 

    elseif ( $pagenow == 'profile.php' ) :
       $title = __('Your Profile at ','colabsthemes');
    endif; 
    return $title;
}

// Show registation form
function colabs_show_registration() {

    //Set a cookie now to see if they are supported by the browser.
    setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
    if ( SITECOOKIEPATH != COOKIEPATH )
        setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);


	global $posted;
	
	if ( isset($_POST['register']) && $_POST['register'] ) {
		
            // redirect to dashboard page once they are registered
            $result = colabs_process_register_form(CL_DASHBOARD_URL);

            $errors = $result['errors'];
            $posted = $result['posted'];
		
	}

	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) ) $errors = new WP_Error();

	if ( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
            $message = __('You are now logged out.','colabsthemes');

	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	
            $errors->add('registerdisabled', __('User registration is currently not allowed.','colabsthemes'));

	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	
            $message = __('Check your email for the confirmation link.','colabsthemes');

	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	
            $message = __('Check your email for your new password.','colabsthemes');

	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
            $message = __('Registration complete. Please check your e-mail.','colabsthemes');

	if ( file_exists(STYLESHEETPATH . '/header.php') )
            include_once(STYLESHEETPATH . '/header.php');
	else
            include_once(TEMPLATEPATH . '/header.php');
	?>
      
	<div class="main-content column col9">
		
		<article class="single-entry-post">
			<header class="entry-header">
				<h2 class="entry-title"><?php _e('Register', 'colabsthemes'); ?></h2>
			</header>

			<div class="property-details">

				<div class="property-details-panel entry-content">
					<?php 
							if ( isset($message) && !empty($message) ) {
								echo '<p class="success">'.$message.'</p>';
							}
						?>
						<?php 
						if ( isset($errors) && sizeof($errors)>0 && $errors->get_error_code() ) :
							echo '<ul class="errors">';
							foreach ($errors->errors as $error) {
								echo '<li>'.$error[0].'</li>';
							}
							echo '</ul>';
						endif; 
						?>

					<p><?php _e('Complete the fields below to create your free account. Your login details will be emailed to you for confirmation so make sure to use a valid email address. Once registration is complete, you will be able to submit your ads.', 'colabsthemes') ?></p>
					
					<?php colabs_register_form(); ?>
				</div>

			</div><!-- .property-details -->

		</article><!-- .single-entry-post -->

	</div><!-- .main-content -->

	<?php get_sidebar();?>
	
<?php 
	
	if ( file_exists(STYLESHEETPATH . '/footer.php') )
		include_once(STYLESHEETPATH . '/footer.php');
	else
		include_once(TEMPLATEPATH . '/footer.php');

}



// Show registation form
function colabs_show_login() {

	global $posted;
	
	if ( isset($_POST['login']) && $_POST['login'] ) {
		
		$errors = colabs_process_login_form();
		
	}

	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) ) $errors = new WP_Error();

	if ( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
			$message = __('You are now logged out.','colabsthemes');

	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	
			$errors->add('registerdisabled', __('User registration is currently not allowed.','colabsthemes'));

	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	
			$message = __('Check your email for the confirmation link.','colabsthemes');

	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	
			$message = __('Check your email for your new password.','colabsthemes');

	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
			$message = __('Registration complete. Please check your e-mail.','colabsthemes');

	if ( file_exists(STYLESHEETPATH . '/header.php') )
		include_once(STYLESHEETPATH . '/header.php');
	else
		include_once(TEMPLATEPATH . '/header.php');	
	?>
      
	<div class="main-content column col9">
		
		<article class="single-entry-post">
			<header class="entry-header">
				<h2 class="entry-title"><?php _e('Login', 'colabsthemes'); ?></h2>
			</header>

			<div class="property-details">

				<div class="property-details-panel entry-content">
					<?php 
					if ( isset($message) && !empty($message) ) {
								echo '<p class="success">'.$message.'</p>';
					}?>
					<?php 
					if ( isset($errors) && sizeof($errors)>0 && $errors->get_error_code() ) :
						echo '<ul class="errors">';
						foreach ( $errors->errors as $error ) {
							echo '<li>'.$error[0].'</li>';
						}
						echo '</ul>';
					endif; 
					?>

					<p class="alert alert-info"><?php _e('Please complete the fields below to login to your account.', 'colabsthemes') ?></p>
					<?php colabs_login_form(); ?>
				</div>

			</div><!-- .property-details -->

		</article><!-- .single-entry-post -->

	</div><!-- .main-content -->

	<?php get_sidebar();?>
	
<?php 
	
	if ( file_exists(STYLESHEETPATH . '/footer.php') )
		include_once(STYLESHEETPATH . '/footer.php');
	else
		include_once(TEMPLATEPATH . '/footer.php');

}



// show the forgot your password page
function colabs_show_password() {
    $errors = new WP_Error();

    if ( isset($_POST['user_login']) && $_POST['user_login'] ) {
        $errors = retrieve_password();

        if ( !is_wp_error($errors) ) {
            wp_redirect('wp-login.php?checkemail=confirm');
            exit();
        }

    }

    if ( isset($_GET['error']) && 'invalidkey' == $_GET['error'] ) $errors->add('invalidkey', __('Sorry, that key does not colabsear to be valid.','colabsthemes'));

    do_action('lost_password');
    do_action('lostpassword_post');

    if ( file_exists(STYLESHEETPATH . '/header.php') )
		include_once(STYLESHEETPATH . '/header.php');
	else
		include_once(TEMPLATEPATH . '/header.php');
	?>
      
	<div class="main-content column col9">
		
		<article class="single-entry-post">
			<header class="entry-header">
				<h2 class="entry-title"><?php _e('Register', 'colabsthemes'); ?></h2>
			</header>

			<div class="property-details">

				<div class="property-details-panel entry-content">
					<?php 
							if (isset($message) && !empty($message)) {
								echo '<p class="success">'.$message.'</p>';
							}
						?>
						<?php 
						if ($errors && sizeof($errors)>0 && $errors->get_error_code()) :
							echo '<ul class="errors">';
							foreach ($errors->errors as $error) {
								echo '<li>'.$error[0].'</li>';
							}
							echo '</ul>';
						endif; 
						?>
						
						<p><?php _e('Please enter your username or email address. A new password will be emailed to you.', 'colabsthemes') ?></p>
					<?php colabs_forgot_password_form(); ?>
				</div>

			</div><!-- .property-details -->

		</article><!-- .single-entry-post -->

	</div><!-- .main-content -->

	<?php get_sidebar();?>
            
<?php	
	if ( file_exists(STYLESHEETPATH . '/footer.php') )
		include_once(STYLESHEETPATH . '/footer.php');
	else
		include_once(TEMPLATEPATH . '/footer.php');
}

?>