<?php
/*-----------------------------------------------------------------------------------*/
/* Get Max Price */
/*-----------------------------------------------------------------------------------*/
function goodliving_setup() {
	global $wpdb;
	$max = ceil( $wpdb->get_var(
				$wpdb->prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key = \'%3$s\'
				', $wpdb->posts, $wpdb->postmeta, 'property_price' )
			) );
	update_option('colabs_property_max',$max);
	$size = ceil( $wpdb->get_var(
				$wpdb->prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key = \'%3$s\'
				', $wpdb->posts, $wpdb->postmeta, 'property_size' )
			) );
	update_option('colabs_property_size',$size);	
}
add_action( 'after_setup_theme', 'goodliving_setup' );

add_action( 'save_post', 'property_max_price_updated' );

function property_max_price_updated( $post_id ) {

	if ( 'property' == $_POST['post_type'] ) {
    if(get_option('colabs_property_max') < get_post_meta($post_id,'property_price')){
		update_option('colabs_property_max', get_post_meta($post_id,'property_price'));
		}	
		if(get_option('colabs_property_size') < get_post_meta($post_id,'property_size')){
		update_option('colabs_property_size', get_post_meta($post_id,'property_size'));
		}
  }

}

/*-----------------------------------------------------------------------------------*/
/* Start Property Functions */
/*-----------------------------------------------------------------------------------*/
add_action( 'after_setup_theme', 'colabs_tables_install' );

// contains the reCaptcha anti-spam system. Called on reg pages
function colabsthemes_recaptcha() {

    // process the reCaptcha request if it's been enabled
    if (get_option('colabs_captcha_enable') == 'true' && get_option('colabs_captcha_theme') && get_option('colabs_captcha_public_key')) :
?>
        <script type="text/javascript">
        // <![CDATA[
         var RecaptchaOptions = {
            custom_translations : {
                instructions_visual : "<?php _e('Type the two words:','colabsthemes') ?>",
                instructions_audio : "<?php _e('Type what you hear:','colabsthemes') ?>",
                play_again : "<?php _e('Play sound again','colabsthemes') ?>",
                cant_hear_this : "<?php _e('Download sound as MP3','colabsthemes') ?>",
                visual_challenge : "<?php _e('Visual challenge','colabsthemes') ?>",
                audio_challenge : "<?php _e('Audio challenge','colabsthemes') ?>",
                refresh_btn : "<?php _e('Get two new words','colabsthemes') ?>",
                help_btn : "<?php _e('Help','colabsthemes') ?>",
                incorrect_try_again : "<?php _e('Incorrect. Try again.','colabsthemes') ?>",
            },
            theme: "<?php echo get_option('colabs_captcha_theme') ?>",
            lang: "en",
            tabindex: 5
         };
        // ]]>
        </script>

        <p>
        <?php
        // let's call in the big boys. It's captcha time.
        require_once (TEMPLATEPATH . '/includes/lib/recaptchalib.php');
        echo recaptcha_get_html(get_option('colabs_captcha_public_key'));
        ?>
        </p>

<?php
    endif;  // end reCaptcha

}

// checks if a user is logged in, if not redirect them to the login page
function auth_redirect_login() {
    $user = wp_get_current_user();
    if ( $user->ID == 0 ) {
        nocache_headers();
        wp_redirect(get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}
if (!function_exists('redirect_myproperty')) {
function redirect_myproperty( $query_string = '' ) {
	$url = get_permalink(get_option('colabs_dashboard_url'));
	if (is_array($query_string)) $url = add_query_arg( $query_string, $url );
    wp_redirect($url);
    exit();
}
}
function colabs_delete_property ($postid) {
  global $wpdb;

	$attachments_query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type='attachment'", $postid);
	$attachments = $wpdb->get_results($attachments_query);

  // delete all associated attachments
  if($attachments)
    foreach($attachments as $attachment)
      wp_delete_attachment( $attachment->ID, true );
  
  // delete post and it's revisions, comments, meta
  if( wp_delete_post( $postid, true ) )
    return true;
  else
    return false;
}

// Create the Orders db tables
function colabs_tables_install() {
  global $wpdb;

  $collate = '';
  if($wpdb->supports_collation()) {
          if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
          if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
  } 
  
  // create the orders table - used to track orders and completed payments
  $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "colabs_orders" ." (
      `id` mediumint(9) NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) NULL,
      `status` varchar(255) NOT NULL DEFAULT 'pending_payment',
      `cost` varchar(255) NULL DEFAULT '',
      `property_id` bigint(20) NULL,
      `featured` int(1) NOT NULL DEFAULT '0',
      `order_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `payment_date` TIMESTAMP NULL,        
      `payer_first_name` longtext NULL,
      `payer_last_name` longtext NULL,
      `payer_email` longtext NULL,
      `payment_type` longtext NULL,
      `approval_method` varchar(255) NULL,
      `payer_address` longtext NULL,
      `transaction_id` longtext NULL,        
      `order_key` varchar(255) NULL,        
      PRIMARY KEY id (`id`)) $collate;";

  $wpdb->query($sql);
	
	// create bookmark table - used to bookmark property
  $sql_bookmark = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "colabs_bookmarks" ." (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`),
			KEY `property` (`post_id`)
			) ENGINE=InnoDB  $collate AUTO_INCREMENT=1 ;";

  $wpdb->query($sql_bookmark);

}
// Get currency function
if (!function_exists('colabs_get_currency')) {
function colabs_get_currency( $amount = '' ) {
    $currency = get_option('colabs_property_paypal_currency');
    $currency_symbol = '';
    
    switch ($currency) :
        case 'GBP':
           $currency_symbol = '&pound;';
        break;
        case 'JPY':
            $currency_symbol = '&yen;';
        break;
        case 'EUR':
            $currency_symbol = '&euro;';
        break;
        case 'PLN' :
        	$currency_symbol = 'zl';
        break;
        default:
            $currency_symbol = '$';
        break;
    endswitch;
	    
    if ($amount) :
    
    	$amount_string = '';
    	
    	
    	$amount_string = '{currency}'.$amount;
    	
    	
    	return str_replace('{currency}', $currency_symbol, $amount_string);
    
    else :
    	return $currency_symbol;
    endif;

    return;
}
}
if ( !function_exists('colabs_renew_property_listing') ) :
function colabs_renew_property_listing ( $property_id ) {
	$listfee = (float)get_post_meta($property_id, 'colabs_property_relisting_cost', true);

	// protect against false URL attempts to hack ads into free renewal
	if ( $listfee == 0 )	{
		$property_length = get_option('colabs_prun_period');
		
		// set the ad listing expiration date
		$property_expire_date = date('m/d/Y H:i:s', strtotime('+' . $property_length . ' days')); // don't localize the word 'days'

		//now update the expiration date on the ad
		update_post_meta($property_id, 'expires', $property_expire_date);
		wp_update_post( array('ID' => $property_id, 'post_date' => date('Y-m-d H:i:s'), 'edit_date' => true) );
		return true;
	}

	//attempt to relist a paid ad
	else {	return false;	}
}
endif;

// pass strings in to clean
function colabsthemes_clean($string) {
    $string = stripslashes($string);
    $string = trim($string);
    return $string;
}

// payment processing for ad dashboard so ad owners can pay for unpaid ads
function colabs_dashboard_paypal_button($the_id) {
	nocache_headers();
    global $wpdb;
	

    // figure out the number of days this ad was listed for
  
	  $prun_period = get_option('colabs_prun_period');
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM ".$wpdb->prefix."colabs_orders WHERE property_id=$the_id;" ) );
		$item_name = sprintf( __('Property listing on %s for %s days', 'colabsthemes'), get_bloginfo('name'), $prun_period);
		$item_number = $wpdb->get_var( $wpdb->prepare( "SELECT order_key FROM ".$wpdb->prefix."colabs_orders WHERE property_id=$the_id;" ) );
		$amount =  $wpdb->get_var( $wpdb->prepare( "SELECT cost FROM ".$wpdb->prefix."colabs_orders WHERE property_id=$the_id;" ) );
		$notify_url = get_bloginfo('url').'/index.php?invoice='.$item_number.'&amp;pid='.$the_id;
		$return = CL_DASHBOARD_URL.'?oid='.$order_id.'&amp;pid='.$the_id;
		$cbt = __('Click here to publish your property on','colabsthemes').' '.get_bloginfo('name');
	

?>

  <form name="paymentform" action="<?php if (get_option('colabs_use_paypal_sandbox') == 'true') echo 'https://www.sandbox.paypal.com/cgi-bin/webscr'; else echo 'https://www.paypal.com/cgi-bin/webscr'; ?>" method="post">

    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="business" value="<?php echo get_option('colabs_property_paypal_email'); ?>" />
    <input type="hidden" name="item_name" value="<?php echo esc_attr( $item_name ); ?>" />    
    <input type="hidden" name="amount" value="<?php echo esc_attr( $amount ); ?>" />
    <input type="hidden" name="no_shipping" value="1" />
    <input type="hidden" name="no_note" value="1" />
	  <input type="hidden" name="item_number" value="<?php echo esc_attr( $item_number ); ?>" />
	  <input type="hidden" name="currency_code" value="<?php echo esc_attr( get_option('colabs_property_paypal_currency') ); ?>" />
    <input type="hidden" name="custom" value="<?php echo esc_attr( $the_id ); ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo home_url(); ?>" />
    <input type="hidden" name="return" value="<?php echo esc_attr( $return ); ?>" />
    <input type="hidden" name="rm" value="2" />
    <input type="hidden" name="cbt" value="<?php echo esc_attr( $cbt ); ?>" />
    <input type="hidden" name="charset" value="UTF-8" />

    <?php if ( get_option('colabs_enable_paypal_ipn') == 'yes' ) { ?>
      <input type="hidden" name="notify_url" value="<?php echo esc_attr( $notify_url ); ?>" />
	  <?php } ?>

    <input type="image" src="<?php bloginfo('template_directory'); ?>/images/paypal.png" name="submit" />

  </form>

<?php
}

/*-----------------------------------------------------------------------------------*/
/* Custom Array Functions */
/*-----------------------------------------------------------------------------------*/

function colabs_multidimensional_array_unique($array)
{
	$result = array_map("unserialize", array_unique(array_map("serialize", $array)));

	foreach ($result as $key => $value)
	{
		if ( is_array($value) )
		{
			$result[$key] = super_unique($value);
		}
	}

	return $result;
}

function colabs_get_custom_post_meta_entries($meta) {
	//db class	
	global $wpdb;
	//tables
	$table_1 = $wpdb->prefix . "postmeta";
	//initialize where clause
	$where_clause = '';
	if (sizeof($meta) > 0) {
		foreach ($meta as $key => $meta_item) {
			if ($key == 0) {
				$where_clause = "WHERE ".$table_1.".meta_key = '".$meta_item."'";
			} else {
				$where_clause .= " OR ".$table_1.".meta_key = '".$meta_item."'";
			}
		}
		$colabs_result = $wpdb->get_results("SELECT ".$table_1.".meta_value FROM ".$table_1." ".$where_clause);
	} else {
		$colabs_result = '';
	}
	return $colabs_result;					
}

/*-----------------------------------------------------------------------------------*/
/* CoLabs Google Mapping */
/*-----------------------------------------------------------------------------------*/

function colabs_maps_single_output($args){

	$key = get_option('colabs_maps_apikey');
	
	// No More API Key needed
	
	if ( !is_array($args) ) 
		parse_str( $args, $args );
		
	extract($args);	
		
	$map_height = get_option('colabs_maps_single_height');
	   
	$lang = get_option('colabs_maps_directions_locale');
	$locale = '';
	if(!empty($lang)){
		$locale = ',locale :"'.$lang.'"';
	}
	$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
	
	if(empty($map_height)) { $map_height = 250;}
	
	?>
 
    <div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>

    <script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
    <script type="text/javascript">
		jQuery(document).ready(function(){
			function initialize() {
				
				
			<?php if($streetview == 'on'){ ?>

				var location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
				
				<?php 
				// Set defaults if no value
				if ($yaw == '') { $yaw = 20; }
				if ($pitch == '') { $pitch = -20; }
				?>
				
				var panoramaOptions = {
  					position: location,
  					pov: {
    					heading: <?php echo $yaw; ?>,
    					pitch: <?php echo $pitch; ?>,
    					zoom: 1
  					}
				};
				
				var map = new google.maps.StreetViewPanorama(document.getElementById("single_map_canvas"), panoramaOptions);
				window.prop_map = map;
				
		  		google.maps.event.addListener(map, 'error', handleNoFlash);
				
				<?php if(get_option('colabs_maps_scroll') == 'true'){ ?>
			  	map.scrollwheel = false;
			  	<?php } ?>
				
			<?php } else { ?>
				
			  	<?php switch ($type) {
			  			case 'G_NORMAL_MAP':
			  				$type = 'ROADMAP';
			  				break;
			  			case 'G_SATELLITE_MAP':
			  				$type = 'SATELLITE';
			  				break;
			  			case 'G_HYBRID_MAP':
			  				$type = 'HYBRID';
			  				break;
			  			case 'G_PHYSICAL_MAP':
			  				$type = 'TERRAIN';
			  				break;
			  			default:
			  				$type = 'ROADMAP';
			  				break;
			  	} ?>
			  	
			  	var myLatlng = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
				var myOptions = {
				  zoom: <?php echo $zoom; ?>,
				  center: myLatlng,
				  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
				};
			  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
			  	window.prop_map = map;
				<?php if(get_option('colabs_maps_scroll') == 'true'){ ?>
			  	map.scrollwheel = false;
			  	<?php } ?>
			  	
				<?php if($mode == 'directions'){ ?>
			  	directionsPanel = document.getElementById("featured-route");
 				directions = new GDirections(map, directionsPanel);
  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
			  	<?php
			 	} else { ?>
			 
			  		var point = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
	  				var root = "<?php bloginfo('template_url'); ?>";
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title(get_the_id())); ?>
	  				<?php $title = str_replace('&#8211;','-',$title); ?>
	  				<?php $title = str_replace('&#8217;',"`",$title); ?>
	  				<?php $title = str_replace('&#038;','&',$title); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>'; 
	  				
	  			<?php		 	
			 	if(is_page()){ 
			 		$custom = get_option('colabs_cat_custom_marker_pages');
					if(!empty($custom)){
						$color = $custom;
					}
					else {
						$color = get_option('colabs_cat_colors_pages');
						if (empty($color)) {
							$color = 'red';
						}
					}			 	
			 	?>
			 		var color = '<?php echo $color; ?>';
			 		createMarker(map,point,root,the_link,the_title,color);
			 	<?php } else { ?>
			 		var color = '<?php echo get_option('colabs_cat_colors_pages'); ?>';
	  				createMarker(map,point,root,the_link,the_title,color);
				<?php 
				}
					if(isset($_POST['colabs_maps_directions_search'])){ ?>
					
					directionsPanel = document.getElementById("featured-route");
 					directions = new GDirections(map, directionsPanel);
  					directions.load("from: <?php echo htmlspecialchars($_POST['colabs_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
  					
  					
  					
					directionsDisplay = new google.maps.DirectionsRenderer();
					directionsDisplay.setMap(map);
    				directionsDisplay.setPanel(document.getElementById("featured-route"));
					
					<?php if($walking == 'on'){ ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
					<?php } else { ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
					<?php } ?>
					var start = '<?php echo htmlspecialchars($_POST['colabs_maps_directions_search']); ?>';
					var end = '<?php echo $address; ?>';
					var request = {
       					origin:start, 
        				destination:end,
        				travelMode: travelmodesetting
    				};
    				directionsService.route(request, function(response, status) {
      					if (status == google.maps.DirectionsStatus.OK) {
        					directionsDisplay.setDirections(response);
      					}
      				});	
      				
  					<?php } ?>			
				<?php } ?>
			<?php } ?>
			

			  }
			  function handleNoFlash(errorCode) {
				  if (errorCode == FLASH_UNAVAILABLE) {
					alert("Error: Flash doesn't appear to be supported by your browser");
					return;
				  }
				 }

			
		
			// initialize();
			window.map_init = initialize; 
			
			jQuery('#property-maps').on('tabsshow', function(){
				google.maps.event.trigger(prop_map, 'resize');
			});

		});

		function loadScript() {
			var script = document.createElement('script');
	        script.type = 'text/javascript';
	        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
	            'callback=map_init';
	        document.body.appendChild(script);
		}
		jQuery(window).load(function(){
			if( typeof google == 'undefined' ) {
				loadScript();
			} else {
				initialize();
			}
		});
	
	</script>

<?php
}

function colabsthemes_metabox_maps_header($id){  
  $pID = $id; 
	$key = get_option('colabs_maps_apikey');
	
	// No More API Key needed
	
	?>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
	jQuery(document).ready(function(){
		var map;
		var geocoder;
		var address;
		var pano;
		var location;
		var markersArray = [];
		
		<?php 
		$mode = get_post_meta($pID,'colabs_maps_mode',true);
		if($mode == 'directions'){ ?>
		var mode = 'directions';
		<?php } else { ?>
		var mode = 'plot';
		<?php } ?>
		
		jQuery('#map_mode a').click(function(){
		
			var mode_set = jQuery(this).attr('id');
			if(mode_set == 'colabs_directions_map'){
				mode = 'directions';
				jQuery('.colabs_plot').hide();
				jQuery('.colabs_directions').show();
				jQuery('#colabs_maps_mode').val('directions');

			}
			else {
				mode = 'plot';
				jQuery('.colabs_plot').show();
				jQuery('.colabs_directions').hide();
				jQuery('#colabs_maps_mode').val('plot');
			}
			
			jQuery('#map_mode a').removeClass('active');
			jQuery(this).addClass('active');
		
			return false;
		});
		
		jQuery('#colabs_maps_to').focus(function(){
			jQuery('#colabs_maps_from').removeClass('current_input');
			jQuery(this).addClass('current_input');
		});
		jQuery('#colabs_maps_from').focus(function(){
			jQuery('#colabs_maps_to').removeClass('current_input');
			jQuery(this).addClass('current_input');
		});
	
		function initialize() {
		  
		  <?php 
		  $lat = get_post_meta($pID,'colabs_maps_lat',true);
		  $long = get_post_meta($pID,'colabs_maps_long',true);
		  $yaw = get_post_meta($pID,'colabs_maps_pov_yaw',true);
		  $pitch = get_post_meta($pID,'colabs_maps_pov_pitch',true);
		 
		  if(empty($long) && empty($lat)){
		  	//Defaults...
			$lat = '40.7142691';
			$long = '-74.0059729';
			$zoom = get_option('colabs_maps_default_mapzoom');
		  } else { 
		  	$zoom = get_post_meta($pID,'colabs_maps_zoom',true); 
		  }
		  if(empty($yaw) OR empty($pitch)){
		  	$pov = 'yaw:20,pitch:-20';
		  } else {
		  	$pov = 'yaw:' . $yaw . ',pitch:' . $pitch;
		  }
		  
		  ?>
		  
		  // Manage API V2 existing data
		  <?php switch ($type) {
				case 'G_NORMAL_MAP':
					$type = 'ROADMAP';
					break;
				case 'G_SATELLITE_MAP':
					$type = 'SATELLITE';
					break;
				case 'G_HYBRID_MAP':
					$type = 'HYBRID';
					break;
				case 'G_PHYSICAL_MAP':
					$type = 'TERRAIN';
					break;
				default:
					$type = 'ROADMAP';
		  			break;
		  } ?>
		  
		  // Create Standard Map
		  location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
		  var myOptions = {
		  		zoom: <?php echo $zoom; ?>,
		  		center: location,
		  		mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>,
		  		streetViewControl: false
		  };
		  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		  
      	  <?php
      	  // Set defaults if no value
		  if ($yaw == '') { $yaw = 20; }
		  if ($pitch == '') { $pitch = -20; }
		  ?>
		  
		  // Create StreetView Map		
		  var panoramaOptions = {
  		  	position: location,
  			pov: {
    			heading: <?php echo $yaw; ?>,
    			pitch: <?php echo $pitch; ?>,
    			zoom: 1
  			}
		  };	
		  pano = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
		  
		  // Set initial Zoom Levels
		  var z = map.getZoom();        
          jQuery('#colabs_maps_zoom option').removeAttr('selected');
          jQuery('#colabs_maps_zoom option[value="'+z+'"]').attr('selected','selected');
      	  
      	  // Event Listener - StreetView POV Change
      	  google.maps.event.addListener(pano, 'pov_changed', function(){
      	  	var headingCell = document.getElementById('heading_cell');
      		var pitchCell = document.getElementById('pitch_cell');
      	  	jQuery("#colabs_maps_pov_yaw").val(pano.getPov().heading);
     	  	jQuery("#colabs_maps_pov_pitch").val(pano.getPov().pitch);
     	  	
      	  });
      	  
      	  // Event Listener - Standard Map Zoom Change
      	  google.maps.event.addListener(map, 'zoom_changed', function(){
      	  	var z = map.getZoom();        
        	jQuery('#colabs_maps_zoom option').removeAttr('selected');
        	jQuery('#colabs_maps_zoom option[value="'+z+'"]').attr('selected','selected');
      	  });
      	  
      	  // Event Listener - Standard Map Click Event
      	  geocoder = new google.maps.Geocoder();
      	  google.maps.event.addListener(map, "click", getAddress);
      	
		} // End initialize() function
		
		// Adds the overlays to the map, and in the array
		function addMarker(location) {
  			marker = new google.maps.Marker({
    			position: location,
    			map: map
  			});
  			markersArray.push(marker);
		} // End addMarker() function
		  
		// Removes the overlays from the map, but keeps them in the array
		function clearOverlays() {
  			if (markersArray) {
    			for (i in markersArray) {
      				markersArray[i].setMap(null);
    			}
  			}
		} // End clearOverlays() function
		
		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
		 	if (markersArray) {
		    	for (i in markersArray) {
		      		markersArray[i].setMap(null);
		    	}
		    	markersArray.length = 0;
		  	}
		} // End deleteOverlays() function

		// Shows any overlays currently in the array
		function showOverlays() {
  			if (markersArray) {
    			for (i in markersArray) {
      				markersArray[i].setMap(map);
    			}
  			}
		} // End showOverlays() function
		
		// Sets initial marker on centre point
		function setSavedAddress() {
			point = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
		 	addMarker(point);
  		} // End setSavedAddress() function
		
		// Click event for address
		function getAddress(event) {
		  	
		  	clearOverlays();
		  	point = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
		 	addMarker(point);
		  	if(mode == 'directions'){
				jQuery('#colabs_maps_lat').attr('value',event.latLng.lat());
				jQuery('#colabs_maps_long').attr('value',event.latLng.lng());

			} else {
				jQuery('#colabs_maps_lat').attr('value',event.latLng.lat());
				jQuery('#colabs_maps_long').attr('value',event.latLng.lng());
			}
			
		  	if (event.latLng != null) {
				address = event.latLng;
				geocoder.geocode( { 'location': address}, showAddress);
		  	}
		  	if (event.latLng) {
		  		pano.setPosition(event.latLng);
		  		pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
		  	}
		} // End getAddress() function
		
		// Updates fields with address data
		function showAddress(results, status) {
			
			if (status == google.maps.GeocoderStatus.OK) {
        		deleteOverlays();
        		
        		map.setCenter(results[0].geometry.location);
        			
        		addMarker(results[0].geometry.location);
        				
        		place = results[0].formatted_address;
        		latlngplace = results[0].geometry.location;
        				
				if(mode == 'directions'){
					jQuery('.current_input').attr('value',place);
				} else {
					jQuery('#colabs_maps_address').attr('value',place);
				}
        					
        	} else {
        		alert("Status Code:" + status);
        		
        	}
		} // End showAddress() function
		
		// addAddressToMap() is called when the geocoder returns an
		// answer.  It adds a marker to the map.
		function addAddressToMap(results, status) {
		  
		  deleteOverlays();
		  if (status != google.maps.GeocoderStatus.OK) {
			alert("Sorry, we were unable to geocode that address");
		  } else {
			place = results[0].formatted_address;
			point = results[0].geometry.location;					
			
			addMarker(point);
	
			map.setCenter(point, <?php echo $zoom; ?>);
			pano.setPosition(point);
		  	pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
		  					
			if(mode == 'directions'){
				
				jQuery('.current_input').attr('value',place);
				jQuery('#colabs_maps_lat').attr('value',point.lat());
				jQuery('#colabs_maps_long').attr('value',point.lng());
		
			} else {
				jQuery('#colabs_maps_address').attr('value',place);
				jQuery('#colabs_maps_lat').attr('value',point.lat());
				jQuery('#colabs_maps_long').attr('value',point.lng());
			}
			
		  }
		}
	
		// >> PLOT
		// showLocation() is called when you click on the Search button
		// in the form.  It geocodes the address entered into the form
		// and adds a marker to the map at that location.
		function showLocation() {
		  var address = jQuery('#colabs_maps_search_input').attr('value');
		  geocoder.geocode( { 'address': address}, addAddressToMap);
		}
		initialize();
		setSavedAddress();
		
		// >> PLOT
		//Click on the "Plot" button	
		jQuery('#colabs_maps_search').click(function(){
		
			showLocation();
	
		})
		
	});
	
    </script>
	<style type="text/css">
		#map_canvas { margin:10px 0}
		.colabs_maps_bubble_address { font-size:16px}
		.colabs_maps_style { padding: 10px}
		.colabs_maps_style ul li label { width: 150px; float:left; display: block}
		.colabs_maps_search { border-bottom:1px solid #e1e1e1; padding: 10px}
		
		#colabs_maps_holder .not-active{ display:none }
		
		#map_mode { height: 38px; margin: 10px 0; background: #f1f1f1; padding-top: 10px}
		#map_mode ul li { float:left;  margin-bottom: 0;}
		#map_mode ul li a {padding: 10px 15px; display: block;text-decoration: none;   margin-left: 10px }
		#map_mode a.active { color: black;background: #fff;border: solid #e1e1e1; border-width: 1px 1px 0px 1px; }
		.current_input { background: #E9F2FA!important}
		
	</style>
	
	<?php
}

function colabsthemes_metabox_maps_create($post_id) {
    
	$enable = get_post_meta($post_id,'colabs_maps_enable',true);
	$streetview = get_post_meta($post_id,'colabs_maps_streetview',true);
	$address = get_post_meta($post_id,'colabs_maps_address',true);
	$long = get_post_meta($post_id,'colabs_maps_long',true);
	$lat = get_post_meta($post_id,'colabs_maps_lat',true);
	$zoom = get_post_meta($post_id,'colabs_maps_zoom',true);
	$type = get_post_meta($post_id,'colabs_maps_type',true);
	$walking = get_post_meta($post_id,'colabs_maps_walking',true);
	
	$yaw = get_post_meta($post_id,'colabs_maps_pov_yaw',true);
	$pitch = get_post_meta($post_id,'colabs_maps_pov_pitch',true);
	
	$from = get_post_meta($post_id,'colabs_maps_from',true);
	$to = get_post_meta($post_id,'colabs_maps_to',true);
	
	if(empty($zoom)) $zoom = get_option('colabs_maps_default_mapzoom');
	if(empty($type)) $type = get_option('colabs_maps_default_maptype');
	if(empty($pov)) $pov = 'yaw:0,pitch:0';
	
	$key = get_option('colabs_maps_apikey');
	
	// No More API Key needed	
	?>
 
    <?php
    $mode = get_post_meta($post->ID,'colabs_maps_mode',true); 
    if($mode == 'plot'){ $directions = 'not-active'; $plot = 'active'; }
    elseif($mode == 'directions'){ $directions = 'active'; $plot = 'not-active'; }
    else {$directions = 'not-active'; $plot = 'active';}

    ?>

	<div class="clear"></div>
	<table class="maps-post-options"><tr><td><strong>Enable map on this post: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="colabs_maps_enable" id="colabs_maps_enable" <?php if($enable == 'on'){ echo 'checked=""';} ?> /></td></tr>
    <tr><td><strong>This map will be in Streetview: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="colabs_maps_streetview" id="colabs_maps_streetview" <?php if($streetview == 'on'){ echo 'checked=""';} ?> /></td></tr>
    <tr class="hidden"><td><strong>Outputs directions for walking: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="colabs_maps_walking" id="colabs_maps_walking" <?php if($walking == 'on'){ echo 'checked=""';} ?> /></td></tr>
    
    </table>
    
	<div class="colabs-maps-search-wrapper">
   	<div class="colabs_maps_search">
    <table><tr><td><b>Search for an address:</b></td>
    <td><input class="address_input" type="text" size="40" value="" name="colabs_maps_search_input" id="colabs_maps_search_input"/><span class="button" id="colabs_maps_search">Plot</span>
    </td></tr></table>
    </div>
	<div id="colabs_maps_holder" class="colabs_maps_style" >
    <ul>
    	<li class="colabs_plot <?php echo $plot; ?>">
    		<label>Address Name:</label>
    		<input class="address_input" type="text" size="40" name="colabs_maps_address" id="colabs_maps_address" value="<?php echo $address; ?>" />
    	</li>
    	<li>
    		<label>Latitude: <small class="colabs_directions">Center Point</small></label>
    		<input class="address_input" type="text" size="40" name="colabs_maps_lat" id="colabs_maps_lat" value="<?php echo $lat; ?>"/>
    	</li>
    	<li>
    		<label>Longitude: <small class="colabs_directions">Center Point</small></label>
    		<input class="address_input" type="text" size="40" name="colabs_maps_long" id="colabs_maps_long" value="<?php echo $long; ?>"/>
    	</li>
        <li class="with-button colabs_plot <?php echo $plot; ?>">
    		<label>Point of View: Yaw</label>    	
    		<input class="address_input" type="text" name="colabs_maps_pov_yaw" id="colabs_maps_pov_yaw" size="40" value="<?php echo $yaw;  ?>" />
      		<small class="btn">Streetview</small>	
      	</li>
        <li class="with-button colabs_plot <?php echo $plot; ?>">
    		<label>Point of View: Pitch</label>    		
    		<input class="address_input" type="text" name="colabs_maps_pov_pitch" id="colabs_maps_pov_pitch" size="40" value="<?php echo $pitch;  ?>">
      		<small class="btn">Streetview</small>
      	</li>
    	<li class="colabs_directions <?php echo $directions; ?>">
    		<label>From:</label>
			<input class="address_input current_input" type="text" size="40" name="colabs_maps_from" id="colabs_maps_from" value="<?php echo $from; ?>"/>
    	</li>
    	<li class="colabs_directions <?php echo $directions; ?>">
    		<label>To:</label>
    		<input class="address_input" type="text" size="40" name="colabs_maps_to" id="colabs_maps_to" value="<?php echo $to; ?>"/>
    	</li>
    	 <li>
    		<label>Zoom Level:</label>
    		<select class="address_select" style="width:120px" name="colabs_maps_zoom" id="colabs_maps_zoom">
    			<?php 
				for($i = 0; $i < 20; $i++) {
					if($i == $zoom){ $selected = 'selected="selected"';} else { $selected = '';} ?>
		 			<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
    				<?php } ?>
    		</select>
    	</li>
    	<li>
	  		<label>Map Type:</label>
    		<select class="address_select" style="width:120px" name="colabs_maps_type" id="colabs_maps_type">
   			<?php
			$map_types = array('Normal' => 'G_NORMAL_MAP','Satellite' => 'G_SATELLITE_MAP','Hybrid' => 'G_HYBRID_MAP','Terrain' => 'G_PHYSICAL_MAP',); 
			foreach($map_types as $k => $v) {
				if($type == $v){ $selected = 'selected="selected"';} else { $selected = '';} ?>
				<option value="<?php echo $v; ?>" <?php echo $selected; ?>><?php echo $k; ?></option>
    		<?php } ?>
    		</select>
 		</li>

 	</ul> 
 	<input type="hidden" value="<?php echo $mode; ?>" id="colabs_maps_mode" name="colabs_maps_mode" />
    </div>
	</div><!-- .colabs-maps-search-wrapper -->
    
  <div id="map_canvas" style="width: 100%; height: 250px"></div>
  <div name="pano" id="pano" style="width: 100%; height:250px"></div>

<?php }


?>