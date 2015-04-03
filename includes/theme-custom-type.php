<?php

add_action('init', 'property_listing');
add_action('init', 'agent');


function property_listing() {
	$args = array(
		'description' => 'Property Post Type',
		'show_ui' => true,
		'menu_position' => 4,
		'exclude_from_search' => true,
		'labels' => array(
			'name'=> 'Property Listings',
			'singular_name' => 'Property Listings',
			'add_new' => 'Add New Property', 
			'add_new_item' => 'Add New Property',
			'edit' => 'Edit Properties',
			'edit_item' => 'Edit Property',
			'new-item' => 'New Property',
			'view' => 'View Property',
			'view_item' => 'View Property',
			'search_items' => 'Search Properties',
			'not_found' => 'No Properties Found',
			'not_found_in_trash' => 'No Properties Found in Trash',
			'parent' => 'Parent Property'
		),
		'public' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => true,
		'query_var' => true,
		'supports' => array('title', 'editor', 'thumbnail', 'author', 'comments')
	);
	register_post_type( 'property' , $args );
	flush_rewrite_rules();
}


function agent() {
	$args = array(
		'description' => 'Agent Post Type',
		'show_ui' => true,
		'menu_position' => 4,
		'exclude_from_search' => true,
		'labels' => array(
			'name'=> 'Agent',
			'singular_name' => 'Agents',
			'add_new' => 'Add New Agent',
			'add_new_item' => 'Add New Agent',
			'edit' => 'Edit Agents',
			'edit_item' => 'Edit Agent',
			'new-item' => 'New Agent',
			'view' => 'View Agent',
			'view_item' => 'View Agent',
			'search_items' => 'Search Agents',
			'not_found' => 'No Agents Found',
			'not_found_in_trash' => 'No Agents Found in Trash',
			'parent' => 'Parent Agent'
		),
		'public' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => true,
		'supports' => array('title', 'editor','thumbnail')
		
	);

	register_post_type( 'agent' , $args );
	flush_rewrite_rules();
}


add_filter("manage_edit-agent_columns", "agent_edit_columns");   
  
function agent_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />", 
            "photo" => __("","colabsthemes"),
            "title" => __("Name","colabsthemes"), 
            "email" => __("Email","colabsthemes"), 
            "date" => __("Date","colabsthemes"),
              
        );  
  
        return $columns;  
}  

add_action("manage_agent_posts_custom_column",  "agent_custom_columns"); 
  
function agent_custom_columns($column){  
        global $post;  
        switch ($column){    
            case "email":  
                echo get_post_meta($post->ID,'colabs_email_agent',true);  
                break; 	  
            case "photo":
				if(has_post_thumbnail()) the_post_thumbnail(array(50,50));
				break;	
        }  
}


// custom taxonomies

add_action('init', 'property_listing_taxonomies');
function property_listing_taxonomies() {
	register_taxonomy('property_type',
			'property',
			array (
			'labels' => array (
					'name' => 'Property Type',
					'singular_name' => 'Property Type',
					'search_items' => 'Search Property Type',
					'popular_items' => 'Popular Property Types',
					'all_items' => 'All Property Types',
					'parent_item' => 'Parent Property Type',
					'parent_item_colon' => 'Parent Property Type:',
					'edit_item' => 'Edit Property Type',
					'update_item' => 'Update Property Type',
					'add_new_item' => 'Add New Property Type',
					'new_item_name' => 'New Property Type',
			),
					'hierarchical' =>true,
					'show_ui' => true,
					'show_tagcloud' => true,
					'rewrite' => false,
					'public'=>true)
			);
}



add_action('init', 'property_location_taxonomies');
function property_location_taxonomies() {
	register_taxonomy('property_location',
			'property',
			array (
			'labels' => array (
					'name' => 'Property Location',
					'singular_name' => 'Property Location',
					'search_items' => 'Search Location',
					'popular_items' => 'Popular Locations',
					'all_items' => 'All Locations',
					'parent_item' => 'Parent Locations',
					'parent_item_colon' => 'Parent Locations:',
					'edit_item' => 'Edit Locations',
					'update_item' => 'Update Locations',
					'add_new_item' => 'Add New Location',
					'new_item_name' => 'New Location',
			),
					'hierarchical' =>true,
					'show_ui' => true,
					'show_tagcloud' => true,
					'rewrite' => false,
					'public'=>true)
			);
}

add_action('init', 'property_status_taxonomies');
function property_status_taxonomies() {
	register_taxonomy('property_status',
			'property',
			array (
			'labels' => array (
					'name' => 'Property Status',
					'singular_name' => 'Property Status',
					'search_items' => 'Search Status',
					'popular_items' => 'Popular Status',
					'all_items' => 'All Status',
					'parent_item' => 'Parent Status',
					'parent_item_colon' => 'Parent Status:',
					'edit_item' => 'Edit Status',
					'update_item' => 'Update Status',
					'add_new_item' => 'Add New Status',
					'new_item_name' => 'New Status',
			),
					'hierarchical' =>true,
					'show_ui' => true,
					'show_tagcloud' => true,
					'rewrite' => false,
					'public'=>true)
			);
}

add_action('init', 'property_features_taxonomies');
function property_features_taxonomies() {
	register_taxonomy('property_features',
			'property',
			array (
			'labels' => array (
					'name' => 'Features',
					'singular_name' => 'Features Property',
					'search_items' => 'Search Features',
					'popular_items' => 'Popular Features',
					'all_items' => 'All Features',
					'parent_item' => 'Parent Features',
					'parent_item_colon' => 'Parent Features:',
					'edit_item' => 'Edit Features',
					'update_item' => 'Update Features',
					'add_new_item' => 'Add New Features',
					'new_item_name' => 'New Features',
			),
					'hierarchical' =>true,
					'show_ui' => true,
					'show_tagcloud' => true,
					'rewrite' => false,
					'public'=>true)
			);
}


add_filter("manage_edit-property_columns", "property_edit_columns");   
  
function property_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",
						"photo" => __("","colabsthemes"),
            "title" => __("Property","colabsthemes"), 
            "property_type" => __("Type","colabsthemes"), 
            "property_location" => __("Location","colabsthemes"),
            "property_features" => __("Features","colabsthemes"),
            "property_status" => __("Status","colabsthemes"), 
            "date" => __("Date","colabsthemes"),          
        );  
  
        return $columns;  
}  

add_action("manage_property_posts_custom_column",  "property_custom_columns"); 
  
function property_custom_columns($column){  
        global $post;  
        switch ($column){    
            case "property_type":  
                echo get_the_term_list($post->ID, 'property_type', '', ', ','');  
                break; 	  
            case "property_location":  
                echo get_the_term_list($post->ID, 'property_location', '', ', ','');  
                break; 	  
            case "property_features":  
                echo get_the_term_list($post->ID, 'property_features', '', ', ','');  
                break; 	   	
            case "photo":
								if(has_post_thumbnail()) the_post_thumbnail(array(50,50));
								break;
            case "property_status":  
                echo get_the_term_list($post->ID, 'property_status', '', ', ','');  
                break;	
        }  
}


//style for property table
add_action('admin_head', 'colabs_admin_styling');
function colabs_admin_styling() {
echo '<style type="text/css">
		th#photo.column-photo{width:60px;}
		.attachment-50x50.wp-post-image{-webkit-border-radius: 60px;-moz-border-radius: 60px;-ms-border-radius: 60px;border-radius: 60px;}
		.attachment-50x50.wp-post-image:hover{width:55px;height:55px;}
	  </style>';  
}
// action for feature
function property_feature() {

	if ( ! is_admin() ) die;

	if ( ! current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'colabsthemes') );

	if ( ! check_admin_referer('property-feature')) wp_die( __('You have taken too long. Please go back and retry.', 'colabsthemes') );

	$post_id = isset( $_GET['id'] ) && (int) $_GET['id'] ? (int) $_GET['id'] : '';

	if (!$post_id) die;

	$post = get_post($post_id);

	if ( ! $post || $post->post_type !== 'property' ) die;

	$featured = get_post_meta( $post->ID, 'property_as_featured', true );

	if ( $featured == 'true' )
		update_post_meta($post->ID, 'property_as_featured', 'false');
	else
		update_post_meta($post->ID, 'property_as_featured', 'true');

	wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
}

add_action('wp_ajax_property-feature', 'property_feature');



// CREATE SORT WITH CUSTOM TAXONOMIES
add_filter( 'manage_edit-property_sortable_columns', 'property_sortable_columns' );

function property_sortable_columns( $columns ) {

	//$columns['property_location'] = 'property_location';

	return $columns;
}
// CREATE FILTERS WITH CUSTOM TAXONOMIES

if ( isset($_GET['post_type']) ) {
	$post_type = $_GET['post_type'];
}
else {
	$post_type = '';
}

if ( $post_type == 'property' ) {
	add_action( 'restrict_manage_posts','property_type_filter_list' );
	add_filter('posts_where', 'colabs_property_posts_where');
}



function property_type_filter_list() {
  $screen = get_current_screen();
  global $wp_query;
  if ( $screen->post_type == 'property' ) {
    wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Types',
						'taxonomy' => 'property_type',
						'name' => 'property_type',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_type'] ) ?
						$wp_query->query['property_type'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Locations',
						'taxonomy' => 'property_location',
						'name' => 'property_location',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_location'] ) ?
						$wp_query->query['property_location'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));	
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Features',
						'taxonomy' => 'property_features',
						'name' => 'property_features',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_features'] ) ?
						$wp_query->query['property_features'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Status',
						'taxonomy' => 'property_status',
						'name' => 'property_status',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_status'] ) ?
						$wp_query->query['property_status'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));	
	}
}

// Custom Query to filter edit grid
function colabs_property_posts_where($where) {
    if( is_admin() ) {
        global $wpdb;
        if (isset($_GET['location_names'])) { $location_ID = $_GET['location_names'];  } else { $location_ID = '';  }
        if (isset($_GET['type_names'])) { $type_ID = $_GET['type_names'];  } else { $type_ID = '';  }
		if (isset($_GET['feature_names'])) { $feature_ID = $_GET['feature_names'];  } else { $feature_ID = '';  }
		if ( ($location_ID > 0) || ($type_ID > 0) || ($feature_ID > 0) ) {

			$location_tax_names =  &get_term( $location_ID, 'property_location' );
			$type_tax_names =  &get_term( $type_ID, 'property_type' );
			$feature_tax_names =  &get_term( $feature_ID, 'property_features' );
			$string_post_ids = '';
 			//locations
			if ($location_ID > 0) {
				$location_tax_name = $location_tax_names->slug;
				$location_myposts = get_posts('nopaging=true&post_type=property&property_location='.$location_tax_name);
				foreach($location_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
			}
			//property types
			if ($type_ID > 0) {
				$type_tax_name = $type_tax_names->slug;
				$type_myposts = get_posts('nopaging=true&post_type=property&property_type='.$type_tax_name);
				foreach($type_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
			}
			//additional features
			if ($feature_ID > 0) {
				$feature_tax_name = $feature_tax_names->slug;
				$feature_myposts = get_posts('nopaging=true&post_type=property&property_features='.$feature_tax_name);
				foreach($feature_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
   			}
 			$string_post_ids = chop($string_post_ids,',');
   			$where .= "AND ID IN (" . $string_post_ids . ")";
		}
    }
    return $where;
}

add_filter( 'parse_query','perform_filtering' );

function perform_filtering( $query )
 {
    $qv = &$query->query_vars;
    if (( $qv['property_type'] ) && is_numeric( $qv['property_type'] ) ) {
      $term = get_term_by( 'id', $qv['property_type'], 'property_type' ); 
			$qv['property_type'] = $term->slug;
		}
		if (( $qv['property_location'] ) && is_numeric( $qv['property_location'] ) ) {
      $term = get_term_by( 'id', $qv['property_location'], 'property_location' ); 
			$qv['property_location'] = $term->slug;
		}
		if (( $qv['property_features'] ) && is_numeric( $qv['property_features'] ) ) {
      $term = get_term_by( 'id', $qv['property_features'], 'property_features' ); 
			$qv['property_features'] = $term->slug;
		}
		if (( $qv['property_status'] ) && is_numeric( $qv['property_status'] ) ) {
      $term = get_term_by( 'id', $qv['property_status'], 'property_status' ); 
			$qv['property_status'] = $term->slug;
		}
}

/*-----------------------------------------------------------------------------------*/
/* Taxonomy Search Functions */
/*-----------------------------------------------------------------------------------*/

//search taxonomies for a match against a search term and returns array of success count
function colabs_taxonomy_matches($term_name, $term_id, $post_id = 0, $keyword_to_search = '') {
	$return_array = array();
	$return_array['success'] = false;
	$return_array['keywordcount'] = 0;
	$terms = get_the_terms( $post_id , $term_name );
	$success = false;
	$keyword_count = 0;
	if ($term_id == 0) {
		$success = true;
	}
	$counter = 0;
	// Loop over each item
	if ($terms) {
		foreach( $terms as $term ) {

			if ($term->term_id == $term_id) {
				$success = true;
			}
			if ( $keyword_to_search != '' ) {
				$keyword_count = substr_count( strtolower( $term->name ) , strtolower( $keyword_to_search ) );
				if ( $keyword_count > 0 ) {
					$success = true;
					$counter++;
				}
			} else {
				//If search term is blank
				$location_tax_names =  get_term_by( 'id', $term_id, $term_name );
 				//locations
				if ($location_tax_names) {
					if (isset($location_tax_names->slug)) { $location_tax_name = $location_tax_names->slug; } else { $location_tax_name = ''; }
					if ($location_tax_name != '') {
						$location_myposts = get_posts('nopaging=true&post_type=property&'.$term_name.'='.$location_tax_name);
						foreach($location_myposts as $location_mypost) {
							if ($location_mypost->ID == $post_id) {
								$success = true;
	        					$counter++;
							} 
						}
					}
				}
			}
		}
	}
	$return_array['success'] = $success;
	if ($counter == 0) {
		$return_array['keywordcount'] = $keyword_count;
	} else { 
		$return_array['keywordcount'] = $counter;
	}
	
	return $return_array;
}



/*-----------------------------------------------------------------------------------*/
/* Property Search Function 
/*-----------------------------------------------------------------------------------*/

function colabs_property_search_result_set($query_args,$keyword_to_search, $location_id, $propertytypes_id, $propertystatus_id, $advanced_search = null, $search_type = '') {
	
	$search_results = array();
	$query_args['showposts'] = -1;$query_args['post_type'] = 'property';
	$the_query = new WP_Query($query_args);
	
	//Prepare Garages, Beds, Baths variables
	
	if ($advanced_search['beds'] == '10+') { 
		$advanced_beds = 10;
	} else {
		$advanced_beds = $advanced_search['beds'];
	}
	if ($advanced_search['baths'] == '10+') { 
		$advanced_baths = 10;
	} else {
		$advanced_baths = $advanced_search['baths'];
	}
	if ($advanced_search['garages'] == '10+') { 
		$advanced_garages = 10;
	} else {
		$advanced_garages = $advanced_search['garages'];
	}
	
	//Get matching method
	$matching_method = get_option('colabs_feature_matching_method');
	
	if ($the_query->have_posts()) : $count = 0;

	while ($the_query->have_posts()) : $the_query->the_post();

		global $post;
    $post_type = $post->post_type;
		
		
	  //Check Locations for matches
	  $location_terms = colabs_taxonomy_matches('property_location', $location_id, $post->ID, $keyword_to_search);
	  $success_location = $location_terms['success'];
	  $location_keyword_count = $location_terms['keywordcount'];

	  //Secondary Location Check
	  if ( (!$success_location) || ($location_keyword_count == 0) ) {
	    $location_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_location' );

			if ($location_tax_names) {
				$location_tax_name = $location_tax_names->slug;
				if ($location_tax_name != '') {
					$location_myposts = get_posts('nopaging=true&post_type=property&property_location='.$location_tax_name);
					foreach($location_myposts as $location_mypost) {
						if ($location_mypost->ID == $post->ID) {
							$success_location = true;
							$location_keyword_count++;
						} 
					}
				}
			} 
	  }
	        
	  //Check Property Types for matches
	  $propertytypes_terms = colabs_taxonomy_matches('property_type', $propertytypes_id, $post->ID, $keyword_to_search);
	  $success_propertytype = $propertytypes_terms['success'];
	  $propertytype_keyword_count = $propertytypes_terms['keywordcount'];
	  
	  //Secondary Property Type Check
	  if ( (!$success_propertytype) || ($propertytype_keyword_count == 0) ) {
	  	$propertytype_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_type' );

			if ($propertytype_tax_names) {
				$propertytype_tax_name = $propertytype_tax_names->slug;
				if ($propertytype_tax_name != '') {
					$propertytype_myposts = get_posts('nopaging=true&post_type=property&property_type='.$propertytype_tax_name);
					foreach($propertytype_myposts as $propertytype_mypost) {
						if ($propertytype_mypost->ID == $post->ID) {
							$success_propertytype = true;
	       			$propertytype_keyword_count++;
						} 
					}
				}
			} 
	  }
	  
		//Check Property Status for matches
	  $propertystatus_terms = colabs_taxonomy_matches('property_status', $propertystatus_id, $post->ID, $keyword_to_search);
	  $success_propertystatus = $propertystatus_terms['success'];
	  $propertystatus_keyword_count = $propertystatus_terms['keywordcount'];
	  
	  //Secondary Property Status Check
	  if ( (!$success_propertystatus) || ($propertystatus_keyword_count == 0) ) {
	  	$propertystatus_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_type' );

			if ($propertystatus_tax_names) {
				$propertystatus_tax_name = $propertystatus_tax_names->slug;
				if ($propertystatus_tax_name != '') {
					$propertystatus_myposts = get_posts('nopaging=true&post_type=property&property_type='.$propertystatus_tax_name);
					foreach($propertystatus_myposts as $propertystatus_mypost) {
						if ($propertystatus_mypost->ID == $post->ID) {
							$success_propertystatus = true;
	       			$propertystatus_keyword_count++;
						} 
					}
				}
			} 
	  }	
		
	  //Check Additional Features for matches
	  $propertyfeatures_terms = colabs_taxonomy_matches('property_features', 0, $post->ID, $keyword_to_search);
	  $success_propertyfeatures = $propertyfeatures_terms['success'];
	  $propertyfeatures_keyword_count = $propertyfeatures_terms['keywordcount'];
		//Do custom meta boxes comparisons here
	  $property_address = get_post_meta($post->ID,'property_address',true);
	  $property_garages = get_post_meta($post->ID,'property_garages',true);
	  if ($property_garages == '10+' ) {
	  	$property_garages = 10;
	  }
		$property_garages_success = false;
		if ($advanced_garages == 'all') {
			$property_garages_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_garages >= $advanced_garages) {
						$property_garages_success = true;
					} else {
						$property_garages_success = false;
					}
				} else {
					//Exact Matching
					if ($property_garages == $advanced_garages) {
						$property_garages_success = true;
					} else {
						$property_garages_success = false;
					}
				}
		}
	  $property_beds = get_post_meta($post->ID,'property_beds',true);
	  if ($property_beds == '10+' ) {
	    $property_beds = 10;
	  }
		$property_beds_success = false;
		if ($advanced_beds == 'all') {
				$property_beds_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_beds >= $advanced_beds) {
						$property_beds_success = true;
					} else {
						$property_beds_success = false;
						
					}
				} else {
					//Exact Matching
					if ($property_beds == $advanced_beds) {
						$property_beds_success = true;
					} else {
						$property_beds_success = false;$property_beds_success = get_post_meta($post->ID,'property_beds',true);;
					}
				}
		}
	  $property_baths = get_post_meta($post->ID,'property_bathrooms',true);
	  if ($property_baths == '10+' ) {
	    $property_baths = 10;
	  }
		$property_baths_success = false;
		if ($advanced_baths == 'all') {
			$property_baths_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_baths >= $advanced_baths) {
						$property_baths_success = true;
					} else {
						$property_baths_success = false;
					}
				} else {
					//Exact Matching
					if ($property_baths == $advanced_baths) {
						$property_baths_success = true;
					} else {
						$property_baths_success = false;
					}
				}
		}
			
		// SIZE COMPARISON SCENARIO(S)
	  $property_size = get_post_meta($post->ID,'property_size',true);
		$property_size_success = false;
		//scenario 1 - only size min
		if ( ($advanced_search['size_min'] != '') && ( ($advanced_search['size_max'] == '') || ($advanced_search['size_max'] == 0) ) ) { 
				if ( ($property_size >= $advanced_search['size_min']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
		//scenario 2 - only size max
		elseif ( ( ($advanced_search['size_max'] != '') || ($advanced_search['size_max'] != 0) ) && ($advanced_search['size_min'] == '') ) { 
				if ( ($property_size <= $advanced_search['size_max']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
		//scenario 3 - size min and max are zero
		elseif ( ($advanced_search['size_min'] == '0') && ($advanced_search['size_max'] == 0) ) { 
				$property_size_success = true;
		}
		//scenario 4 - both min and max
		else {
				if ( ($property_size >= $advanced_search['size_min']) && ($property_size <= $advanced_search['size_max']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
			
		// PRICE COMPARISON SCENARIO(S)
	   $property_price = get_post_meta($post->ID,'property_price',true);
		$property_price_success = false;
		//scenario 1 - only price min
		if ( ($advanced_search['price_min'] != '') && ( ($advanced_search['price_max'] == '') || ($advanced_search['price_max'] == 0) ) ) { 
				if ( ($property_price >= $advanced_search['price_min']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//scenario 2 - only price max
		elseif ( ( ($advanced_search['price_max'] != '') || ($advanced_search['price_max'] != 0) ) && ($advanced_search['price_min'] == '') ) { 
				if ( ($property_price <= $advanced_search['price_max']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//scenario 3 - price min and max are zero
		elseif ( ($advanced_search['price_min'] == '0') && ($advanced_search['price_max'] == 0) ) { 
				$property_price_success = true;
		}
		//scenario 4 - both min and max
		else {
				if ( ($property_price >= $advanced_search['price_min']) && ($property_price <= $advanced_search['price_max']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//format price
		$property_price = number_format($property_price , 0 , '.', ',');
			
	  if ( $success_location && $success_propertytype && $success_propertystatus ) {  
	    //Search against post data
	    if ( $keyword_to_search != '' ) {
	    	//Default WordPress Content
	    	$raw_title = get_the_title();
	    	$raw_content = get_the_content();
	    	$raw_excerpt = get_the_excerpt();
	    	//Comparison
	    	$title_keyword_count = substr_count( strtolower( $raw_title ) , strtolower( $keyword_to_search ) );
	    	$content_keyword_count = substr_count( strtolower( $raw_content ) , strtolower( $keyword_to_search ) );
	    	$excerpt_keyword_count = substr_count( strtolower( $raw_excerpt ) , strtolower( $keyword_to_search ) );
	    	$property_address_count = substr_count( strtolower( $property_address ) , strtolower( $keyword_to_search ) );
	    }
	    //Check for matches or blank keyword
	    		
	    if ( $keyword_to_search == '') {
	    			
	    	if ( ( $location_keyword_count > 0 ) || ( $propertytype_keyword_count > 0 ) || ( $propertystatus_keyword_count > 0 ) || ( $propertyfeatures_keyword_count > 0 ) ) { 

						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
							
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
	    	}elseif ( ( $location_keyword_count == 0 ) && ( $propertytype_keyword_count == 0 ) && ( $propertystatus_keyword_count == 0 ) && ( $propertyfeatures_keyword_count == 0 ) ) { 
						
						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID); 
								}
								$search_results = $property_beds_success;
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
				}
	    			
	    } else {
	    		
	    	if ( ( $title_keyword_count > 0 ) || ( $content_keyword_count > 0 ) || ( $excerpt_keyword_count > 0 ) || ( $location_keyword_count > 0 ) || ( $property_address_count > 0 ) || ( $propertytype_keyword_count > 0 ) || ( $propertystatus_keyword_count > 0 ) || ( $propertyfeatures_keyword_count > 0 ) ) {
	    			if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
	    	} 			
	    }   		 		
	  }		
	endwhile; else:
    	//no posts	    	
  endif;
	return $search_results;
}

// custom user page columns
function colabs_manage_users_columns( $columns ) {
  $columns['colabs_property_count'] = __('Property', 'colabsthemes');
	$columns['registered'] = __('Registered', 'colabsthemes');
  return $columns;
}
add_action('manage_users_columns', 'colabs_manage_users_columns');

// display the coumn values for each user
function colabs_manage_users_custom_column( $r, $column_name, $user_id ) {

	// count the total jobs for the user
	if ( 'colabs_property_count' == $column_name ) {
		global $property_counts;

		if ( !isset( $property_counts ) )
			$property_counts = colabs_count_custom_post_types( 'property' );

		if ( !array_key_exists( $user_id, $property_counts ) )
			$property_counts = colabs_count_custom_post_types( 'property' );

		if ( $property_counts[$user_id] > 0 ) {
			$r .= "<a href='edit.php?post_type=property&author=$user_id' title='" . esc_attr__( 'View property by this author', 'colabsthemes' ) . "' class='edit'>";
			$r .= $property_counts[$user_id];
			$r .= '</a>';
		} else {
			$r .= 0;
		}
	}
	
	// get the user registration date	
	if ('registered' == $column_name) {
		$user_info = get_userdata($user_id);
		$r = $user_info->user_registered;
	}

	return $r;
}
//Display the custom column data for each user
add_action( 'manage_users_custom_column', 'colabs_manage_users_custom_column', 10, 3 );

// count the number of property for the user
function colabs_count_custom_post_types( $post_type ) {
	global $wpdb, $wp_list_table;

	$users = array_keys( $wp_list_table->items );
	$userlist = implode( ',', $users );
	$result = $wpdb->get_results( "SELECT post_author, COUNT(*) FROM $wpdb->posts WHERE post_type = '$post_type' AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) )
			$count[ $id ] = 0;
	}

	return $count;
}

?>