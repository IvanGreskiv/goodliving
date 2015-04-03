<?php
/*
Template Name: Edit Property
*/
auth_redirect_login(); // if not logged in, redirect to login page
nocache_headers();
global $wpdb;
$current_user = wp_get_current_user(); // grabs the user info and puts into vars
$id = $_GET['id'];

//Start Update Property
$success = false;
if ($_POST['submitted']==true ) {
	$edit_post = array(
					'ID'						=> $id,
					'post_title' 		=> wp_filter_nohtml_kses($_POST['title']), 
					'post_content' 	=> $_POST['description'],
				);
				
	$editpost_id = wp_update_post($edit_post);
	if ($editpost_id!=0 || !is_wp_error($editpost_id)) {
		wp_set_post_terms( $post_id, $_POST['property_type'], 'property_type' );

		if($_POST['property_location_new']!=''){
			$get_property_location = wp_insert_term( $_POST['property_location_new'], 'property_location' );
			wp_set_post_terms( $editpost_id, $get_property_location['term_id'], 'property_location' );	
		}else{
			wp_set_post_terms( $editpost_id, $_POST['property_location'], 'property_location' );	
		}
		
		wp_set_post_terms( $editpost_id, $_POST['property_features'], 'property_features' );	
		wp_set_post_terms( $editpost_id, $_POST['property_status'], 'property_status' );
		
		$colabs_map_input_names = array('colabs_maps_enable','colabs_maps_streetview','colabs_maps_address','colabs_maps_from','colabs_maps_to','colabs_maps_long','colabs_maps_lat','colabs_maps_zoom','colabs_maps_type','colabs_maps_mode','colabs_maps_pov_pitch','colabs_maps_pov_yaw','colabs_maps_walking');
		
		foreach ($colabs_map_input_names as $name) {
			$var = $name;
			
			if (isset($_POST[$var])) {            
				if( get_post_meta( $editpost_id, $name ) == "" )
					add_post_meta($editpost_id, $name, $_POST[$var], true );
				elseif($_POST[$var] != get_post_meta($editpost_id, $name, true))
					update_post_meta($editpost_id, $name, $_POST[$var]);
				elseif($_POST[$var] == "") 
					delete_post_meta($editpost_id, $name, get_post_meta($editpost_id, $name, true));
						
			}elseif(!isset($_POST[$var]) && $name == 'colabs_maps_enable') 
				update_post_meta($editpost_id, $name, 'false'); 
			else 
				delete_post_meta($editpost_id, $name, get_post_meta($editpost_id, $name, true)); 			
				
		}
		
		update_post_meta($editpost_id, "property_price", $_POST['property_price']); 
		update_post_meta($editpost_id, "property_beds", $_POST['property_beds']); 
		update_post_meta($editpost_id, "property_baths", $_POST['property_baths']); 
		update_post_meta($editpost_id, "property_address", $_POST['property_address']); 
		update_post_meta($editpost_id, "property_citystate", $_POST['property_citystate']); 
		update_post_meta($editpost_id, "property_size", $_POST['property_size']); 
		update_post_meta($editpost_id, "property_garage", $_POST['property_garage']);
		update_post_meta($editpost_id, "property_furnished", $_POST['property_furnished']); 
		update_post_meta($editpost_id, "property_mortgage", $_POST['property_mortgage']);
		update_post_meta($editpost_id, "property_agent", $_POST['property_agent']);
		
		if ($_FILES) {
			$allowed = array('png','gif','jpg','jpeg','pdf');
			foreach ($_FILES as $file => $array) {
			
				// Check valid extension
				$extension = strtolower(substr(strrchr($_FILES[$file]['name'], "."), 1));
				if (in_array($extension, $allowed)) $newupload = insert_attachment($file,$editpost_id);
			}
		}
		$success = true;
	}
}
//End Update

// make sure the ad id is legit otherwise set it to zero which will return no results
if (!empty($id)) $id = $id; else $id = '0';	
// select post information and also category with joins.
// filtering based off current user id which prevents people from trying to hack other peoples ads
$sql = $wpdb->prepare("SELECT wposts.* "
    . "FROM ".$wpdb->prefix."posts wposts "
    . "LEFT JOIN ".$wpdb->prefix."term_relationships ON($id = ".$wpdb->prefix."term_relationships.object_id) "
    . "LEFT JOIN ".$wpdb->prefix."term_taxonomy ON(".$wpdb->prefix."term_relationships.term_taxonomy_id = ".$wpdb->prefix."term_taxonomy.term_taxonomy_id) "
    . "LEFT JOIN ".$wpdb->prefix."terms ON(".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id) "
    . "WHERE ID = %s AND ".$wpdb->prefix."term_taxonomy.taxonomy = 'property_type' "
    . "AND post_status <> 'draft' "// turned off to allow "paused" ads to be editable, uncomment to disable editing of paused ads
    . "AND post_author = %s",
    $id,
    $current_user->ID);

// pull ad fields from db
$get_property = $wpdb->get_row($sql);

//feature taxonomy
$av_property = array();
$colabs_avproperties_obj = get_the_terms( $id, 'property_features' );
foreach ($colabs_avproperties_obj as $colabs_avtax) $av_property[]= $colabs_avtax->term_id;
$options = '';
$checked = '';
$colabs_properties_obj = get_categories('hide_empty=0&taxonomy=property_features');
foreach ($colabs_properties_obj as $colabs_tax){ 
if (in_array($colabs_tax->cat_ID, $av_property)){$checked='checked="checked"';}else{$checked = '';}
$options .= '<label class="input-checkbox-wrap"><input type="checkbox" name="property_features[]" '.$checked.' value="'.$colabs_tax->cat_ID.'">'.$colabs_tax->cat_name.'</label>';
}

//agent Post 	
	$agent_id=get_post_meta($get_property->ID,'property_agent',true);
	$agent = '';
	global $wpdb,$post_id;
	$agentarray = $wpdb->get_results( "
	SELECT id,post_title
	FROM $wpdb->posts AS p
	WHERE p.post_type = 'agent' AND p.post_status = 'publish'
	" );
	$agent .= '<option name="property_features[]" value="self">'.__('Self','colabsthemes').'</option>';
	foreach ($agentarray as $dataagent){
		$agent_select='';
		if($agent_id==$dataagent->id)$agent_select='selected="selected"';
		$agent .= '<option '.$agent_select.' name="property_features[]" value="'.$dataagent->id.'">'.$dataagent->post_title.'</option>';
	}	
?>

<?php get_header(); colabsthemes_metabox_maps_header($id);?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
<?php 
	if($success==true){
		echo '<div class="alert alert-success">'.__('Successfully Updated Property','colabsthemes').'</div>';
	}
?>        
<div class="main-content column col9">
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php the_title();?></h2>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content" id="property-details">
        <?php 
				echo $msgrequire;
					
				if ($get_property && (get_option('colabs_allow_editing') == 'true') ):
					
					if (current_user_can( 'level_10' ) || current_user_can( 'member' )) : ?>
						
						<p><?php _e('Edit the fields below and click save to update your property. Your changes will be updated instantly on the site.', 'colabsthemes');?></p>	
						<form class="property-submission" action="<?php echo get_permalink($post->ID).'?id='.$id; ?>" id="propertyform" method="post" enctype="multipart/form-data">
						<?php wp_nonce_field( 'property-form', '_wpnonce'); ?>
							<div class="column col6 leftside">
							<p>
								<label ><?php _e("Title","colabsthemes"); ?> : </label>  
								<input type="text" name="title" value="<?php echo $get_property->post_title?>"/>
							</p>
							<p>
								<label ><?php _e("Price","colabsthemes"); ?> : </label>  
								<input type="text" name="property_price" value="<?php echo get_post_meta($get_property->ID,'property_price',true);?>"/>
							</p>
							<p>
								<label ><?php _e("Address","colabsthemes"); ?> : </label>  
								<textarea name="property_address" rows="10" cols="30"><?php echo get_post_meta($get_property->ID,'property_address',true);?></textarea>
							</p>
							<p>
								<label ><?php _e("City and State","colabsthemes"); ?> : </label>  
								<input type="text" name="property_citystate" value="<?php echo get_post_meta($get_property->ID,'property_citystate',true);?>"/>
							</p>
							<p>
								<label ><?php _e("Size","colabsthemes"); ?> : </label>  
								<input type="text" name="property_size" value="<?php echo get_post_meta($get_property->ID,'property_size',true);?>"/>
							</p>
							<p>
								<label ><?php _e("Descriptions","colabsthemes"); ?> : </label>  
								<textarea name="description" rows="10" cols="30"><?php echo $get_property->post_content;?></textarea>
							</p>
							<p>
								<label ><?php _e("Features","colabsthemes"); ?> : </label>  
								<?php echo $options; ?>
							</p>
							</div>
							<div class="column col6 rightside">
							<p>
								<label ><?php _e("Bedrooms","colabsthemes"); ?> : </label>  
								<select class="colabs_input_select" id="colabsthemes_property_beds" name="property_beds">
								<option value=""><?php _e("Select to return to default","colabsthemes"); ?></option>
								<?php for($i=0;$i<=10;$i++){
								if ($i==10)$plus='+';
								?>
								<option <?php if($i==get_post_meta($get_property->ID,'property_beds',true))echo 'selected="selected"';?> value="<?php echo $i.$plus;?>" ><?php echo $i.$plus;?></option>
								<?php }?>
								</select>
							</p>
							<p>
								<label ><?php _e("Bathrooms","colabsthemes"); ?> : </label>  
								<select class="colabs_input_select" id="colabsthemes_property_baths" name="property_baths">
								<option value=""><?php _e("Select to return to default","colabsthemes"); ?></option>
								<?php for($i=0;$i<=10;$i++){
								$plus='';
								if ($i==10)$plus='+';
								?>
								<option <?php if($i==get_post_meta($get_property->ID,'property_baths',true))echo 'selected="selected"';?> value="<?php echo $i.$plus;?>" ><?php echo $i.$plus;?></option>
								<?php }?>
								</select>
							</p>
							<p>
								<label ><?php _e("Type","colabsthemes"); ?> : </label>  
								<?php 
								$colabs_property_type_obj = get_the_terms( $id, 'property_type' );
								foreach ($colabs_property_type_obj as $colabs_property_type) $property_type= $colabs_property_type->term_id;
								wp_dropdown_categories(array('taxonomy' => 'property_type', 'hide_empty' => 0, 'name' => 'property_type', 'selected' => $property_type)); ?>
							</p>
							<p>
								<label ><?php _e("Location","colabsthemes"); ?> : </label>  
								<?php 
								$colabs_property_location_obj = get_the_terms( $id, 'property_location' );
								foreach ($colabs_property_location_obj as $colabs_property_location) $property_location= $colabs_property_location->term_id;
								wp_dropdown_categories(array('taxonomy' => 'property_location', 'hide_empty' => 0, 'name' => 'property_location', 'selected' => $property_location, 'show_option_none'   => __('Add New...','colabsthemes'))); ?>
								<input style="margin-top: 5px;" type="text" name="property_location_new" class="add-new" placeholder="<?php _e('Add new location', 'colabsthemes'); ?>">
							</p>
							<p>
								<label ><?php _e("Property Status","colabsthemes"); ?> : </label>  
								<?php 
								$property_status = get_the_terms( $id, 'property_status' );
								wp_dropdown_categories(array('taxonomy' => 'property_status', 'hide_empty' => 0, 'name' => 'property_status', 'selected' => $property_status[0]->term_id)); ?>
							</p>
							<p>
								<label ><?php _e("Agent","colabsthemes"); ?> : </label> 
								<select class="colabs_input_select" id="colabsthemes_property_agent" name="property_agent">
								<?php echo $agent; ?>
								</select>
							</p>
							<p>
								<label ><?php _e("Garages","colabsthemes"); ?> : </label>  
								<select class="colabs_input_select" id="colabsthemes_property_garage" name="property_garage">
								<option value=""><?php _e("Select to return to default","colabsthemes"); ?></option>
								<?php for($i=0;$i<=10;$i++){
								$plus='';
								if ($i==10)$plus='+';
								?>
								<option <?php if($i==get_post_meta($get_property->ID,'property_garage',true))echo 'selected="selected"';?> value="<?php echo $i.$plus;?>" ><?php echo $i.$plus;?></option>
								<?php }?>
								</select>
							</p>
							<p>
								<label ><?php _e("Fully Furnished","colabsthemes"); ?> : </label>
								<label class="colabs_input_radio_desc">
									<input type="radio" <?php if(get_post_meta($get_property->ID,'property_furnished',true)=='false')echo 'checked';?> value="false" class="colabs_input_radio" name="property_furnished">
									<?php _e("Not Available","colabsthemes"); ?>
								</label>
								<label class="colabs_input_radio_desc">
									<input type="radio" <?php if(get_post_meta($get_property->ID,'property_furnished',true)=='true')echo 'checked';?> value="true" class="colabs_input_radio" name="property_furnished">
									<?php _e("Availabe","colabsthemes"); ?>
								</label>
							</p>
							<p>
								<label ><?php _e("Mortgage","colabsthemes"); ?> : </label>
								<label class="colabs_input_radio_desc">
									<input type="radio" <?php if(get_post_meta($get_property->ID,'property_mortgage',true)=='false')echo 'checked';?> value="false" class="colabs_input_radio" name="property_mortgage">
									<?php _e("Not Available","colabsthemes"); ?>
								</label>
								<label class="colabs_input_radio_desc">
									<input type="radio" <?php if(get_post_meta($get_property->ID,'property_mortgage',true)=='true')echo 'checked';?> value="true" class="colabs_input_radio" name="property_mortgage">
									<?php _e("Availabe","colabsthemes"); ?>
								</label>
							</p>
							<p class="input-file">				
								<label ><?php _e("Images 1","colabsthemes"); ?> : </label>  
								<?php colabs_image('key=property_image&width=355&height=200&id='.$get_property->ID);?>
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
							
							<?php colabsthemes_metabox_maps_create($get_property->ID); ?>	
																
							<p>
								<input type="submit" class="button button-bold" id="submit" value="<?php _e('Submit', 'colabsthemes') ?>" />
							</p>
							
						</form>

						<?php 
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
					<?php endif; ?>
				
				<?php else : ?>
					<p class="text-center"><?php _e('You have entered an invalid property id or do not have permission to edit that property.', 'colabsthemes');?></p>
				<?php endif;?>
      </div>

    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->

</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->
<?php get_footer(); ?>

