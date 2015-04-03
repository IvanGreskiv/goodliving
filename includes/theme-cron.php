<?php
/**
 * RoyaleRoom Cron Property
 * This file contains the cron property for auto-expiring posts on a daily basis.
 *
 *
 * @version 1.1.0
 * @package RoyaleRoom
 * @copyright 2012 all rights reserved
 *
 */

function colabs_schedule_expire_check(){
	wp_schedule_event(time(), 'hourly', 'colabs_check_property_expired');
	update_option('colabs_check_property_expired', 'true');
}

colabs_schedule_expire_check();

add_action('colabs_check_property_expired', 'colabs_check_expired_cron');

function colabs_check_expired_cron() {
	global $wpdb;
	$action = get_option('colabs_expired_action');
	
	// Get list of expired posts that are published
	$postids = $wpdb->get_col($wpdb->prepare("
		SELECT      postmeta.post_id
		FROM        $wpdb->postmeta postmeta
		LEFT JOIN	$wpdb->posts posts ON postmeta.post_id = posts.ID
		WHERE       postmeta.meta_key = 'expires' 
		            AND postmeta.meta_value < '%s'
		            AND post_status = 'publish'
		            AND post_type = 'property'
	", strtotime('NOW'))); 
	
	if ($action=='hide') :
		if ($postids) foreach ($postids as $id) { 		
			// Captains log supplemental, we have detected a property which is out of date
			// Activate Cloak
			$post = get_post($id);
			if ( empty($post) ) return;
			if ( 'private' == $post->post_status ) return;
			
			$old_status = $post->post_status;
			
			$property_post = array();
			$property_post['ID'] = $id;				
			$property_post['post_status'] = 'private';					
			wp_update_post( $property_post );

			$post->post_status = 'private';
			wp_transition_post_status('private', $old_status, $post);
			
			// Update counts for the post's terms.
			foreach ( (array) get_object_taxonomies('property') as $taxonomy ) {	
				$tt_ids = wp_get_object_terms($id, 'property_type', array('fields' => 'tt_ids'));
				wp_update_term_count($tt_ids, 'property_type');
				$tt_ids = wp_get_object_terms($id, 'property_location', array('fields' => 'tt_ids'));
				wp_update_term_count($tt_ids, 'property_location');
			}
			
			do_action('edit_post', $id, $post);
			do_action('save_post', $id, $post);
			do_action('wp_insert_post', $id, $post);
		}
	endif;
	
	if (get_option('colabs_expired_property_email_owner')=='true') :
	
		$notify_ids = array();
		
		// Get list of expiring posts that are published
		$postids = $wpdb->get_col($wpdb->prepare("
			SELECT      DISTINCT postmeta.post_id
			FROM        $wpdb->postmeta postmeta
			LEFT JOIN	$wpdb->posts posts ON postmeta.post_id = posts.ID
			WHERE       postmeta.meta_key = 'expires' 
			            AND postmeta.meta_value > '%s'
			            AND postmeta.meta_value < '%s'
			            AND post_status = 'publish'
			            AND post_type = 'property'
		", strtotime('NOW'), strtotime('NOW + 5 DAY'))); 
		
		if (sizeof($postids)>0) :
		
			// of those, get ids of posts that have already been notified
			$property_notified = $wpdb->get_col($wpdb->prepare("
				SELECT      postmeta.post_id
				FROM        $wpdb->postmeta postmeta
				WHERE       postmeta.meta_key = 'reminder_email_sent' 
				            AND postmeta.meta_value IN ('5','1')
			")); 

			// Now only send to those who need sending to
			$notify_ids = array_diff($postids, $property_notified);
			if ($notify_ids && sizeof($notify_ids)>0) foreach ($notify_ids as $id) {
				update_post_meta( $id, 'reminder_email_sent', '5' );
				colabs_owner_property_expiring_soon( $id, 5 );
			}
		endif;
		
		// Get list of expiring posts (1 day left) that are published
		$postids = $wpdb->get_col($wpdb->prepare("
			SELECT      postmeta.post_id
			FROM        $wpdb->postmeta postmeta
			LEFT JOIN	$wpdb->posts posts ON postmeta.post_id = posts.ID
			WHERE       postmeta.meta_key = 'expires' 
			            AND postmeta.meta_value > '%s'
			            AND postmeta.meta_value < '%s'
			            AND post_status = 'publish'
			            AND post_type = 'property'
		", strtotime('NOW'), strtotime('NOW + 1 DAY'))); 
		
		if (sizeof($postids)>0) :
		
			// of those, get ids of posts that have already been notified
			$property_notified = $wpdb->get_col($wpdb->prepare("
				SELECT      postmeta.post_id
				FROM        $wpdb->postmeta postmeta
				WHERE       postmeta.meta_key = 'reminder_email_sent' 
				            AND postmeta.meta_value IN ('1')
			", implode(',', $postids) )); 
			
			// Now only send to those who need sending to
			$notify_ids_2 = array_diff($postids, $property_notified, $notify_ids);
			
			if ($notify_ids_2 && sizeof($notify_ids_2)>0) foreach ($notify_ids_2 as $id) {
				update_post_meta( $id, 'reminder_email_sent', '1' );
				colabs_owner_property_expiring_soon( $id, 1 );
			}
			
		endif;
	endif;
}