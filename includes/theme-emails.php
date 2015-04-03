<?php
 
if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

function colabs_new_order( $order ) {
	
    $ordersurl = admin_url("admin.php?page=orders");
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('A new order has just been submitted on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    
    $message .= __('Order Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Order Cost: ', 'colabsthemes') . colabs_get_currency($order->cost) . PHP_EOL;
    $message .= __('User ID: ', 'colabsthemes') . $order->user_id . PHP_EOL;
    $message .= __('Property ID: ', 'colabsthemes') . $order->property_id . PHP_EOL;

    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('View orders: ', 'colabsthemes') . $ordersurl . PHP_EOL . PHP_EOL;
    
	if ($order->property_id) :
        $property_info = get_post($order->property_id);
		
        $property_title = stripslashes($property_info->post_title);
	    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
	    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
	    $property_status = stripslashes($property_info->post_status);
	    $property_slug = stripslashes($property_info->guid);
	    $adminurl = admin_url("post.php?action=edit&post=".$order->property_id."");
	    
	    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	    $message .= __('-----------------') . PHP_EOL;
	    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
	    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
	    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
	    $message .= __('Preview Property: ', 'colabsthemes') . $property_slug . PHP_EOL;
	    $message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;
	endif;
    
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;
	
	$mailto = get_option('admin_email');
	$headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
	$subject = __('New Order', 'colabsthemes').' ['.$blogname.']';
	
    wp_mail($mailto, $subject, $message, $headers);
    
}

function colabs_order_complete( $order ) {

    $ordersurl = admin_url("admin.php?page=orders&show=completed");
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Order number %s has just been completed on your %s website.', 'colabsthemes'), $order->id, $blogname) . PHP_EOL . PHP_EOL;
    
    $message .= __('Order Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Order Date: ', 'colabsthemes') . $order->order_date . PHP_EOL;
    $message .= __('Order Cost: ', 'colabsthemes') . colabs_get_currency($order->cost) . PHP_EOL;
    $message .= __('User ID: ', 'colabsthemes') . $order->user_id . PHP_EOL;
    $message .= __('Property ID: ', 'colabsthemes') . $order->property_id . PHP_EOL;
    
    if ($order->payment_date)  $message .= __('Payment Date: ', 'colabsthemes') . $order->payment_date . PHP_EOL;
    if ($order->payment_type)  $message .= __('Payment Type: ', 'colabsthemes') . $order->payment_type . PHP_EOL;
    if ($order->approval_method)  $message .= __('Approval Method: ', 'colabsthemes') . $order->approval_method . PHP_EOL;
    if ($order->payer_first_name)  $message .= __('First name: ', 'colabsthemes') . $order->payer_first_name . PHP_EOL;
    if ($order->payer_last_name)  $message .= __('Last name: ', 'colabsthemes') . $order->payer_last_name . PHP_EOL;
    if ($order->payer_email)  $message .= __('Email: ', 'colabsthemes') . $order->payer_email . PHP_EOL;
    if ($order->payer_address)  $message .= __('Address: ', 'colabsthemes') . $order->payer_address . PHP_EOL;
    if ($order->transaction_id)  $message .= __('Txn ID: ', 'colabsthemes') . $order->transaction_id . PHP_EOL;

    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('View completed orders: ', 'colabsthemes') . $ordersurl . PHP_EOL . PHP_EOL;
    
	if ($order->property_id) :
		$property_info = get_post($order->property_id);

		$property_title = stripslashes($property_info->post_title);
	    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
	    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
	    $property_status = stripslashes($property_info->post_status);
	    $property_slug = stripslashes($property_info->guid);
	    $adminurl = admin_url("post.php?action=edit&post=".$order->property_id."");
	    
	    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	    $message .= __('-----------------') . PHP_EOL;
	    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
	    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
	    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
	    $message .= __('Preview Property: ', 'colabsthemes') . $property_slug . PHP_EOL;
	    $message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;
	endif;
    
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;
	
    $mailto = get_option('admin_email');
    $headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
    $subject = __('Order Complete', 'colabsthemes').' ['.$blogname.']';
	
    wp_mail($mailto, $subject, $message, $headers);
     
}

function colabs_order_cancelled( $order ) {

    $ordersurl = admin_url("admin.php?page=orders&show=cancelled");
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Order number %s has just been cancelled on your %s website.', 'colabsthemes'), $order->id, $blogname) . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('View cancelled orders: ', 'colabsthemes') . $ordersurl . PHP_EOL . PHP_EOL;
    
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;
	
    $mailto = get_option('admin_email');
    $headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
    $subject = __('Order Cancelled', 'colabsthemes').' ['.$blogname.']';
	
    wp_mail($mailto, $subject, $message, $headers);
    
}
 
// Property that require moderation (non-paid)
function colabs_admin_new_property_pending( $post_id ) {

    $property_info = get_post($post_id);

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    $adminurl = admin_url("post.php?action=edit&post=$post_id");
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = get_option('admin_email');
    $headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
    $subject = __('New Property Pending Approval', 'colabsthemes').' ['.$blogname.']';

    // Message

    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('The following property listing has just been submitted on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('Preview Property: ', 'colabsthemes') . $property_slug . PHP_EOL;
    $message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}

// Edited Property that require moderation
function colabs_edited_property_pending( $post_id ) {

    $property_info = get_post($post_id);

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    $adminurl = admin_url("post.php?action=edit&post=$post_id");
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = get_option('admin_email');
    $headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
    $subject = __('Edited Property Pending Approval', 'colabsthemes').' ['.$blogname.']';

    // Message

    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('The following property listing has just been edited on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('Preview Property: ', 'colabsthemes') . $property_slug . PHP_EOL;
    $message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}


// Property that don't require moderation (non-paid)
function colabs_admin_new_property( $post_id ) {	

    $property_info = get_post($post_id);

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    $adminurl = admin_url("post.php?action=edit&post=$post_id");
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = get_option('admin_email');
    $headers = 'From: '. __('RoyaleRoom Admin', 'colabsthemes') .' <'. get_option('admin_email') .'>' . PHP_EOL;
    $subject = __('New Property Submitted', 'colabsthemes').' ['.$blogname.']';

    // Message

    $message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('The following property listing has just been submitted on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('View Property: ', 'colabsthemes') . $property_slug . PHP_EOL;
    $message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= __('RoyaleRoom', 'colabsthemes') . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}


// New Property Posted (owner) - pending
function colabs_owner_new_property_pending( $post_id ) {

    $property_info = get_post($post_id);

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    
    $siteurl = trailingslashit(get_option('home'));
    $dashurl = trailingslashit(get_permalink(get_option('colabs_dashboard_url')));
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $property_author_email;
    $subject = sprintf(__('Your Property Submission on %s','colabsthemes'), $blogname);
    $headers = 'From: '. sprintf(__('%s Admin', 'colabsthemes'), $blogname) .' <'. get_option('admin_email') .'>' . PHP_EOL;
	
    // Message
    $message  = sprintf(__('Hi %s,', 'colabsthemes'), $property_author) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Thank you for your recent submission! Your property listing has been submitted for review and will not appear live on our site until it has been approved. Below you will find a summary of your property listing on the %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('You may check the status of your property(s) at anytime by logging into the "My Property" page.', 'colabsthemes') . PHP_EOL;
    $message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Your %s Team', 'colabsthemes'), $blogname) . PHP_EOL;
    $message .= $siteurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}


// Property will expire soon
function colabs_owner_property_expiring_soon( $post_id, $days_remaining ) {

    $property_info = get_post($post_id);

    $days_text = '';

    if ($days_remaining==1) $days_text = '1'.__(' day', 'colabsthemes');
        else $days_text = $days_remaining.__(' days', 'colabsthemes');

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    
    $siteurl = trailingslashit(get_option('home'));
    $dashurl = trailingslashit(get_permalink(get_option('colabs_dashboard_page_id')));
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $property_author_email;
    $subject = sprintf(__('Your Property Submission on %s expires in %s','colabsthemes'), $blogname, $days_text);
    $headers = 'From: '. sprintf(__('%s Admin', 'colabsthemes'), $blogname) .' <'. get_option('admin_email') .'>' . PHP_EOL;
	
    // Message
    $message  = sprintf(__('Hi %s,', 'colabsthemes'), $property_author) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('You property listing is set to expire in %s', 'colabsthemes'), $days_text) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
    $message .= __('You may check the status of your property(s) at anytime by logging into the "My Property" page.', 'colabsthemes') . PHP_EOL;
    $message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Your %s Team', 'colabsthemes'), $blogname) . PHP_EOL;
    $message .= $siteurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}

// when a property's status changes, send the property owner an email
function colabs_notify_property_owner_email($new_status, $old_status, $post) {   
    global $wpdb;

    $property_info = get_post($post->ID);
    
    if ($property_info->post_type=='property_listing') :

	    $property_title = stripslashes($property_info->post_title);
	    $property_author_id = $property_info->post_author;
	    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
	    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
	    $property_status = stripslashes($property_info->post_status);
	    $property_slug = stripslashes($property_info->guid);
	    
	    $mailto = $property_author_email;
	    
	    $siteurl = trailingslashit(get_option('home'));
	    $dashurl = trailingslashit(get_permalink(get_option('colabs_dashboard_page_id')));
		
	    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
	    // we want to reverse this for the plain text arena of emails.
	    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	    // make sure the admin wants to send emails
	    $send_approved_email = get_option('colabs_new_property_email_owner');
	    $send_expired_email = get_option('colabs_expired_property_email_owner');
	
	    // if the property has been approved send email to ad owner only if owner is not equal to approver
	    // admin approving own propertys or property owner pausing and reactivating ad on his dashboard don't need to send email
	    if ($old_status == 'pending' && $new_status == 'publish' && get_current_user_id() != $property_author_id && $send_approved_email == 'yes') {
	
	        $subject = __('Your Property Has Been Approved','colabsthemes');
	        $headers = 'From: '. sprintf(__('%s Admin', 'colabsthemes'), $blogname) .' <'. get_option('admin_email') .'>' . PHP_EOL;
	
	        $message  = sprintf(__('Hi %s,', 'colabsthemes'), $property_author) . PHP_EOL . PHP_EOL;
	        $message .= sprintf(__('Your property listing, "%s" has been approved and is now live on our site.', 'colabsthemes'), $property_title) . PHP_EOL . PHP_EOL;
	
	        $message .= __('You can view your property by clicking on the following link:', 'colabsthemes') . PHP_EOL;
	        $message .= get_permalink($post->ID) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	        $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
	        $message .= sprintf(__('Your %s Team', 'colabsthemes'), $blogname) . PHP_EOL;
	        $message .= $siteurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	
	        // ok let's send the email
	        wp_mail($mailto, $subject, $message, $headers);
	        
	
	
	    // if the property has expired, send an email to the property owner only if owner is not equal to approver. This will only trigger if the 30 day option is hide
	    } elseif ($old_status == 'publish' && $new_status == 'private' && $send_expired_email == 'yes') {
	
	        $subject = __('Your property Has Expired','colabsthemes');
	        $headers = 'From: '. sprintf(__('%s Admin', 'colabsthemes'), $blogname) .' <'. get_option('admin_email') .'>' . PHP_EOL;
	
	        $message  = sprintf(__('Hi %s,', 'colabsthemes'), $property_author) . PHP_EOL . PHP_EOL;
	        $message .= sprintf(__('Your property listing, "%s" has expired.', 'colabsthemes'), $property_title) . PHP_EOL . PHP_EOL;
	
	        if (get_option('colabs_allow_relist') == 'yes') {
	            $message .= __('If you would like to relist your property, please visit the "My Property" page and click the "relist" link.', 'colabsthemes') . PHP_EOL;
	            $message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	        }
	
	        $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
	        $message .= sprintf(__('Your %s Team', 'colabsthemes'), $blogname) . PHP_EOL;
	        $message .= $siteurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	
	        // ok let's send the email
	        wp_mail($mailto, $subject, $message, $headers);
	        
	
	    }
	endif;
}

add_action('transition_post_status', 'colabs_notify_property_owner_email', 10, 3);



// email that gets sent out to new users once they register
function colabs_new_user_notification($user_id, $plaintext_pass = '') {
    $colabs_abbr='colabs';

    $user = new WP_User($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);
    //$user_email = 'tester@127.0.0.1'; // USED FOR TESTING

    // variables that can be used by admin to dynamically fill in email content
    $find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
    $replace = array($user_login, $plaintext_pass, get_option('blogname'), get_option('siteurl'), get_option('siteurl').'/wp-login.php', $user_email);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // send the site admin an email everytime a new user registers
    if (get_option($colabs_abbr.'_nu_admin_email') == 'yes') {
        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . PHP_EOL . PHP_EOL;
        $message .= sprintf(__('Username: %s'), $user_login) . PHP_EOL . PHP_EOL;
        $message .= sprintf(__('E-mail: %s'), $user_email) . PHP_EOL;

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
    }

    if ( empty($plaintext_pass) )
        return;

    // check and see if the custom email option has been enabled
    // if so, send out the custom email instead of the default WP one
    if (get_option($colabs_abbr.'_nu_custom_email') == 'yes') {

        // email sent to new user starts here
        $from_name = strip_tags(get_option($colabs_abbr.'_nu_from_name'));
        $from_email = strip_tags(get_option($colabs_abbr.'_nu_from_email'));

        // search and replace any user added variable fields in the subject line
        $subject = stripslashes(get_option($colabs_abbr.'_nu_email_subject'));
        $subject = preg_replace($find, $replace, $subject);
        $subject = preg_replace("/%.*%/", "", $subject);

        // search and replace any user added variable fields in the body
        $message = stripslashes(get_option($colabs_abbr.'_nu_email_body'));
        $message = preg_replace($find, $replace, $message);
        $message = preg_replace("/%.*%/", "", $message);

        // assemble the header
        $headers = "From: $from_name <$from_email>" . PHP_EOL;
        $headers .= "Reply-To: $from_name <$from_email>" . PHP_EOL;
        //$headers .= "MIME-Version: 1.0" . PHP_EOL;
        $headers .= "Content-Type: ". get_option($colabs_abbr.'_nu_email_type') . PHP_EOL;

        // ok let's send the new user an email
        wp_mail($user_email, $subject, $message, $headers);

    // send the default email to debug
    } else {

        $message  = sprintf(__('Username: %s', 'colabsthemes'), $user_login) . PHP_EOL;
        $message .= sprintf(__('Password: %s', 'colabsthemes'), $plaintext_pass) . PHP_EOL;
        $message .= wp_login_url() . PHP_EOL;

        wp_mail($user_email, sprintf(__('[%s] Your username and password', 'colabsthemes'), $blogname), $message);

    }

}
function colabs_bank_owner_new_property_email( $post_id ) {

    $property_info = get_post($post_id);

    $property_title = stripslashes($property_info->post_title);
    $property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
    $property_author_email = stripslashes(get_the_author_meta('user_email', $property_info->post_author));
    $property_status = stripslashes($property_info->post_status);
    $property_slug = stripslashes($property_info->guid);
    
    $siteurl = trailingslashit(get_option('home'));
    $dashurl = trailingslashit(get_permalink(get_option('colabs_dashboard_url')));
	
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $property_author_email;
    $subject = sprintf(__('Your Property Submission on %s','colabsthemes'), $blogname);
    $headers = 'From: '. sprintf(__('%s Admin', 'colabsthemes'), $blogname) .' <'. get_option('admin_email') .'>' . PHP_EOL;
	
    // Message
    $message  = sprintf(__('Hi %s,', 'colabsthemes'), $property_author) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Thank you for your recent submission! Your property listing has been submitted for review and will not appear live on our site until it has been approved. Below you will find a summary of your property listing on the %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= __('Property Details', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= __('Title: ', 'colabsthemes') . $property_title . PHP_EOL;
    $message .= __('Author: ', 'colabsthemes') . $property_author . PHP_EOL;
	$message .= __('Total Amount: ', 'colabsthemes') . $cost . " (" . get_option('colabs_currency_symbol') . ")\r\n";
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
	
	$message .= __('Bank Transfer Instructions', 'colabsthemes') . PHP_EOL;
    $message .= __('-----------------') . PHP_EOL;
    $message .= strip_tags( stripslashes( get_option('colabs_bank_instructions') ) ) . "\r\n";
    $message .= __('-----------------') . PHP_EOL . PHP_EOL;
	
    $message .= __('You may check the status of your property(s) at anytime by logging into the "My Property" page.', 'colabsthemes') . PHP_EOL;
	$message .= __('For questions or problems, please contact us directly at', 'colabsthemes') . " " . get_option('admin_email') . PHP_EOL. PHP_EOL. PHP_EOL;
    $message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
    $message .= __('Regards,', 'colabsthemes') . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Your %s Team', 'colabsthemes'), $blogname) . PHP_EOL;
    $message .= $siteurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);
    
}
?>