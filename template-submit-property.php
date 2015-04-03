<?php
/*
Template Name: Submit Property
*/

nocache_headers();
$success = false;	
	// Validate required again - just in case
	$required = array(
		'title' 			=> __('Имя', 'colabsthemes'),
		'property_price'	=> __('Цена', 'colabsthemes'),
		'property_address' 	=> __('Адресс', 'colabsthemes'),
		'property_citystate'=> __('Город', 'colabsthemes'),
		'property_size' 	=> __('Площадь', 'colabsthemes')
	);
	$submit = true;
	if ($_POST['title'] && isset($_POST['title'])){
	foreach ($required as $field=>$name) {
		if (empty($_POST[$field])) {
			$msgrequire = '<div class="error-alert">*Пожалуйста, введите все поля корректно!</div>';
			$submit = false;
		}
	}
	}
	//feature taxonomy
	$options = '';  
	$colabs_properties_obj = get_categories('hide_empty=0&taxonomy=property_features');
	foreach ($colabs_properties_obj as $colabs_tax) $options .= '<label class="input-checkbox-wrap"><input type="checkbox" name="property_features[]" value="'.$colabs_tax->cat_ID.'">'.$colabs_tax->cat_name.'</input></label>';


	//agent Post 	
	$agent = '';
	global $wpdb,$post_id;
	$agentarray = $wpdb->get_results( "
	SELECT id,post_title
	FROM $wpdb->posts AS p
	WHERE p.post_type = 'agent' AND p.post_status = 'publish'
	" );
	$agent .= '<option name="property_features[]" value="self">'.__('Self','colabsthemes').'</option>';
	foreach ($agentarray as $dataagent)
		$agent .= '<option name="property_features[]" value="'.$dataagent->id.'">'.$dataagent->post_title.'</option>';
	 
	if ($_POST['title'] && isset($_POST['title']) && ($submit == true) ) {
		global $user_ID, $wpdb;
		$status='publish';	
		if (get_option('colabs_property_require_moderation')=='true')$status='pending';
		$new_post = array(
					'post_title' => wp_filter_nohtml_kses($_POST['title']),
					'post_status' => $status, 
					'post_content' => $_POST['description'],
					'post_date' => date('Y-m-d H:i:s'),
					'post_author' => $user_ID,
					'post_type' => 'property'
	 
				);
				
		$post_id = wp_insert_post($new_post);	
		
		if ($post_id!=0 || !is_wp_error($post_id)) {
				
				wp_set_post_terms( $post_id, $_POST['property_type'], 'property_type' );

				if($_POST['property_location_new']!=''){
					$get_property_location = wp_insert_term( $_POST['property_location_new'], 'property_location' );
					wp_set_post_terms( $post_id, $get_property_location['term_id'], 'property_location' );	
				}else{
					wp_set_post_terms( $post_id, $_POST['property_location'], 'property_location' );	
				}
				
				wp_set_post_terms( $post_id, $_POST['property_features'], 'property_features' );	
				wp_set_post_terms( $post_id, $_POST['property_status'], 'property_status' ); 	
				
				$colabs_map_input_names = array('colabs_maps_enable','colabs_maps_streetview','colabs_maps_address','colabs_maps_from','colabs_maps_to','colabs_maps_long','colabs_maps_lat','colabs_maps_zoom','colabs_maps_type','colabs_maps_mode','colabs_maps_pov_pitch','colabs_maps_pov_yaw','colabs_maps_walking');
				foreach ($colabs_map_input_names as $name) {
					$var = $name;
					
					if (isset($_POST[$var])) {            
						if( get_post_meta( $post_id, $name ) == "" )
							add_post_meta($post_id, $name, $_POST[$var], true );
						elseif($_POST[$var] != get_post_meta($post_id, $name, true))
							update_post_meta($post_id, $name, $_POST[$var]);
						elseif($_POST[$var] == "") 
							delete_post_meta($post_id, $name, get_post_meta($post_id, $name, true));
								
					}elseif(!isset($_POST[$var]) && $name == 'colabs_maps_enable') 
						update_post_meta($post_id, $name, 'false'); 
					else 
						delete_post_meta($post_id, $name, get_post_meta($post_id, $name, true)); 			
						
				}
				
				update_post_meta($post_id, "property_price", $_POST['property_price']); 
				update_post_meta($post_id, "property_beds", $_POST['property_beds']); 
				update_post_meta($post_id, "property_baths", $_POST['property_baths']); 
				update_post_meta($post_id, "property_address", $_POST['property_address']); 
				update_post_meta($post_id, "property_citystate", $_POST['property_citystate']); 
				update_post_meta($post_id, "property_size", $_POST['property_size']); 
				update_post_meta($post_id, "property_garage", $_POST['property_garage']);
				update_post_meta($post_id, "property_furnished", $_POST['property_furnished']); 
				update_post_meta($post_id, "property_mortgage", $_POST['property_mortgage']);
				update_post_meta($post_id, "property_agent", $_POST['property_agent']); 
				
				$property_last = get_option('colabs_prun_period');				
				if (!isset($property_last)) $property_last = 30; // 30 day default
				$date = strtotime('+'.$property_last.' day', current_time('timestamp'));
				update_post_meta($post_id, "expires", $date);
				
				function insert_attachment($file_handler,$post_id,$setthumb='false') {
				  if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

				  require_once(ABSPATH . "wp-admin/includes/image.php");
				  require_once(ABSPATH . "wp-admin/includes/file.php");
				  require_once(ABSPATH . "wp-admin/includes/media.php");

				  $attach_id = media_handle_upload( $file_handler, $post_id );

				  if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
				  return $attach_id;
				}
				
				if ($_FILES) {
					$allowed = array('png','gif','jpg','jpeg','pdf');
					foreach ($_FILES as $file => $array) {
					
						// Check valid extension
						$extension = strtolower(substr(strrchr($_FILES[$file]['name'], "."), 1));
						if (in_array($extension, $allowed)) $newupload = insert_attachment($file,$post_id);
					}
				};		
				$success = true;
				
				### Create the order in the database so it can be confirmed by user/IPN before going live
				$listing_cost = get_option('colabs_property_listing_cost');
				$cost = $listing_cost;
				
				if ($_POST['featureit']==true) :
					update_post_meta($post_id, "property_as_featured", 'true');
					$featured_cost = get_option('colabs_cost_to_feature');
					$cost += $featured_cost;
					$insfeatured = 1;
				endif;
				
				if ($cost > 0) {
				   
					$colabs_order = new colabs_order( 0, $user_ID, $cost, $post_id, $insfeatured );
					
					$colabs_order->insert_order();
					
					### Redirect to paypal payment page	(if paid listing)
					if($_POST['colabs_payment_method']=='paypal'){
						$name = urlencode(__('Property Listing ', 'colabsthemes').$_POST['title'] );
						
						$link = $colabs_order->generate_paypal_link( $name, $post_id);
						
						header('Location: '.$link);
						exit();
					}else if($_POST['colabs_payment_method']=='banktransfer'){
						colabs_admin_new_property_pending($post_id);
						colabs_bank_owner_new_property_email($post_id);
						
					}
					
				}else{
				
					### FREE LISTING / LISTING PAID  (no additional cost)
				
					if (get_option('colabs_property_require_moderation')=='true') {
						
						colabs_admin_new_property_pending($post_id);
						colabs_owner_new_property_pending($post_id);
							
					} else{
						colabs_admin_new_property($post_id);
					}
				
					redirect_myproperty();
						
				}
		}else{
			wp_die( __('Error: Unable to create entry.', 'colabsthemes') );
		}
	 
	}
	
?>

<?php get_header(); colabsthemes_metabox_maps_header();?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
<?php 
	if($success==true){
		echo '<div class="alert alert-success">'.__('Successfully Added Property','colabsthemes').'</div>';
	}
?>         
<div class="main-content column col9">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php the_title();?></h2>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content" id="property-details">
        <?php 
				echo $msgrequire;
				
				if (current_user_can( 'level_10' ) || current_user_can( 'member' )) : ?>
				<?php if(isset($success) && $success == true){ 
					?> <p> <?php _e('Thank you for your recent submission! Your property listing has been submitted for review and will not appear live on our site until it has been approved. Below you will find a summary of your property listing on the site.', 'colabsthemes'); ?></p> 
	
						<h4><?php _e('Property Details', 'colabsthemes')?></h4>
						<?php $property_info = get_post($post_id);
						$property_title = stripslashes($property_info->post_title);
						$property_author = stripslashes(get_the_author_meta('user_login', $property_info->post_author));
						$message .= '<hr/>';
						$message .= __('Title: ', 'colabsthemes') . $property_title .'<br/>';
						$message .= __('Author: ', 'colabsthemes') . $property_author.'<br/>';
						$message .= __('Total Amount: ', 'colabsthemes') . $cost . " (" . get_option('colabs_currency_symbol') . ")".'<br/>';
						$message .= '<hr/>'.'<br/>'.'<br/>';
						if($_POST['colabs_payment_method']=='paypal'){
						}else{
						$message .= '<h4>'.__('Bank Transfer Instructions', 'colabsthemes').'</h4>';
						$message .= '<hr/>'.'<br/>';
						$message .= strip_tags( stripslashes( get_option('colabs_bank_instructions') ) ).'<br/>';
						$message .= '<hr/>'.'<br/>';
						}
						$message .= __('You may check the status of your property(s) at anytime by logging into the "Dashboard" page.', 'colabsthemes').'<br/>';
						echo $message;
						?>

				 <?php }else{ ?>
				
				<form class="property-submission" action="<?php echo get_permalink($post->ID); ?>" id="propertyform" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'property-form', '_wpnonce'); ?>
				  <div class="column col6 leftside">
					<p>
						<label ><?php _e("*Title","colabsthemes"); ?> : </label>  
						<input type="text" name="title" />
					</p>
					<p>
						<label ><?php _e("*Price","colabsthemes"); ?> : </label>  
						<input type="text" name="property_price" />
					</p>
					<p>
						<label ><?php _e("*Address","colabsthemes"); ?> : </label>  
						<textarea name="property_address" rows="10" cols="30"></textarea>
					</p>
					<p>
						<label ><?php _e("*City and State","colabsthemes"); ?> : </label>  
						<input type="text" name="property_citystate" />
					</p>
					<p>
						<label ><?php _e("*Size","colabsthemes"); ?> : </label>  
						<input type="text" name="property_size" />
					</p>
					<p>
						<label ><?php _e("Descriptions","colabsthemes"); ?> : </label>  
						<textarea name="description" rows="10" cols="30"></textarea>
					</p>
					<p>
						<label ><?php _e("Features","colabsthemes"); ?> : </label>  
						<?php echo $options; ?>
					</p>
		
				  </div>
				  <div class="column col6 rightside">
					<p>
						<label ><?php _e("Bedrooms","colabsthemes"); ?> : </label>  
						<select class="colabs_input_select" id="colabsthemes_property_beds" name="property_beds"><option value=""><?php _e("Select to return to default","colabsthemes"); ?></option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10+">10+</option></select>
					</p>
					<p>
						<label ><?php _e("Bathrooms","colabsthemes"); ?> : </label>  
						<select class="colabs_input_select" id="colabsthemes_property_baths" name="property_baths"><option value=""><?php _e("Select to return to default","colabsthemes"); ?></option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10+">10+</option></select>
					</p>
					<p>
						<label ><?php _e("Type","colabsthemes"); ?> : </label>  
						<?php wp_dropdown_categories(array('taxonomy' => 'property_type', 'hide_empty' => 0, 'name' => 'property_type')); ?>
					</p>
					<p>
						<label ><?php _e("Location","colabsthemes"); ?> : </label>  
						<?php wp_dropdown_categories(array('taxonomy' => 'property_location', 'hide_empty' => 0, 'name' => 'property_location', 'show_option_none'   => __('Add New...','colabsthemes'))); ?>
						<input style="margin-top: 5px;" type="text" name="property_location_new" class="add-new" placeholder="<?php _e('Add new location', 'colabsthemes'); ?>">
					</p>
					<p>
						<label ><?php _e("Property Status","colabsthemes"); ?> : </label>  
						<?php wp_dropdown_categories(array('taxonomy' => 'property_status', 'hide_empty' => 0, 'name' => 'property_status')); ?>
					</p>
					<p>
						<label ><?php _e("Agent","colabsthemes"); ?> : </label> 
						<select class="colabs_input_select" id="colabsthemes_property_agent" name="property_agent">
						<?php echo $agent; ?>
						</select>
					</p>
					<p>
						<label ><?php _e("Garages","colabsthemes"); ?> : </label>  
						<select class="colabs_input_select" id="colabsthemes_property_garage" name="property_garage"><option value=""><?php _e("Select to return to default","colabsthemes"); ?></option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10+">10+</option></select>
					</p>
					<p class="column col6 alpha">
						<label ><?php _e("Fully Furnished","colabsthemes"); ?> : </label>  
						<label class="colabs_input_radio_desc">
							<input type="radio" checked="" value="false" class="colabs_input_radio" name="property_furnished">
							<?php _e("No Available","colabsthemes"); ?>&nbsp;
						</label>
						<label class="colabs_input_radio_desc">
							<input type="radio" value="true" class="colabs_input_radio" name="property_furnished">
							<?php _e("Available","colabsthemes"); ?>&nbsp;
						</label>
					</p>
					<p class="column col6">
						<label ><?php _e("Mortgage","colabsthemes"); ?> : </label>  
						<label class="colabs_input_radio_desc">
							<input type="radio" checked="" value="false" class="colabs_input_radio" name="property_mortgage">
							<?php _e("No Available","colabsthemes"); ?>&nbsp;
						</label>						
						<label class="colabs_input_radio_desc">
							<input type="radio" value="true" class="colabs_input_radio" name="property_mortgage">
							<?php _e("Available","colabsthemes"); ?>&nbsp;
						</label>
					</p>
					<div class="clear"></div>
					<p class="input-file">
						<label ><?php _e("Images 1","colabsthemes"); ?> : </label>  
						<input type="file" name="file1">
					</p>	
					<p class="input-file">
						<label ><?php _e("Images 2","colabsthemes"); ?> : </label>  
						<input type="file" name="file2">
					</p>	
					<p class="input-file">
						<label ><?php _e("Images 3","colabsthemes"); ?> : </label>  
						<input type="file" name="file3">
					</p>						
					<p class="input-file">
						<label ><?php _e("Images 4","colabsthemes"); ?> : </label>  
						<input type="file" name="file4">
					</p>	
					<input type="hidden" name="submitted" id="submitted" value="true" />
				  </div>
				  
				    
					<?php colabsthemes_metabox_maps_create(); ?>	
					<?php
						$featured_cost = get_option('colabs_cost_to_feature');
						if ($featured_cost && is_numeric($featured_cost) && $featured_cost > 0) :
							
							// Featuring is an option
							echo '<div class="featured"><h3>'.__('Feature your listing for ', 'colabsthemes').colabs_get_currency($featured_cost).__('?', 'colabsthemes').'</h3>';
							
							echo '<p>'.__('Featured listings are displayed on the homepage and are also listed in all other listing pages.', 'colabsthemes').'</p>';
							
							echo '<p><input type="checkbox" name="featureit" id="featureit" /> <label for="featureit" style="float:none">'.__('Yes please, feature my listing.', 'colabsthemes').'</label></p></div>';
							
						endif;
					?>	
					<p>
						<?php 
						$pay_cost = 0;
						$pay_cost = get_option('colabs_property_listing_cost');
						$pay_featured_cost = get_option('colabs_cost_to_feature');
						$pay_cost += $pay_featured_cost;
						if($pay_cost>0):
						if ( ( get_option('colabs_enable_paypal') == 'true' )||( get_option('colabs_enable_bank') == 'true' ) ) : ?>
						<h3><?php _e('Payment Method','colabsthemes'); ?>:</h3>
											
						<select name="colabs_payment_method" class="dropdownlist required">
							<?php if ( get_option('colabs_enable_paypal') == 'true' ) { ?><option value="paypal"><?php echo _e('PayPal', 'colabsthemes') ?></option><?php } ?>
							<?php if ( get_option('colabs_enable_bank') == 'true' ) { ?><option value="banktransfer"><?php echo _e('Bank Transfer', 'colabsthemes') ?></option><?php } ?>							
						</select>
						<?php endif; 
						endif;
						?>
					</p>		
					<p>
						<input type="submit" id="submit" class="button button-bold" value="<?php _e('Submit', 'colabsthemes') ?>" />
					</p>
				  	
				</form>

			<?php } 
			else : 
			if (!$action) 
				$action = site_url('wp-login.php');
			$redirect = get_permalink($post->ID);	
			?>	
			<form action="<?php echo $action; ?>" method="post" class="loginform">
				
				<p>
					<label for="login_username"><?php _e('Username', 'colabsthemes'); ?>&nbsp;:</label>
					<input type="text" class="text" name="log" id="login_username" value="<?php if (isset($posted['login_username'])) esc_attr_e($posted['login_username']); ?>" />
				</p>

				<p>
					<label for="login_password"><?php _e('Password', 'colabsthemes'); ?>&nbsp;:</label>
					<input type="password" class="text" name="pwd" id="login_password" value="" />
				</p>
				
				<div class="clr"></div>

				<div id="checksave">
				
					<p class="rememberme">
						<input name="rememberme" class="checkbox" id="rememberme" value="forever" type="checkbox" checked="checked"/>
						<label for="rememberme"><?php _e('Remember me','colabsthemes'); ?></label>
					</p>	

					<p class="submit">
						<input type="submit" class="button button-bold" name="login" id="login" value="<?php _e('Login &raquo;','colabsthemes'); ?>" />					
						<input type="hidden" name="redirect_to" value="<?php echo $redirect; ?>" />
						<input type="hidden" name="testcookie" value="1" />						
					</p>
					
					<p class="lostpass">
						<a class="lostpass" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found', 'colabsthemes'); ?>"><?php _e('Lost your password?', 'colabsthemes'); ?></a>
					</p>
					
					<?php wp_register('<p class="register">','</p>'); ?>					
					
					<?php do_action('login_form'); ?>
					
				</div>

			</form>
			<?php
			endif; ?>
      </div>

    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->
	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->
<?php get_footer(); ?>
