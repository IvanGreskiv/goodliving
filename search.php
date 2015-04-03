<?php ob_start(); ?>
<?php get_header(); ?>

<?php 
global $wp_query;
	//get variables
if (isset($_GET['price_min'])) { $min_price = $_GET['price_min']; } else { $min_price = '';  }
if (isset($_GET['price_max'])) { $max_price = $_GET['price_max']; } else { $max_price = '';  }
if (isset($_GET['size_min'])) { $min_size = $_GET['size_min']; } else { $min_size = '';  }
if (isset($_GET['size_max'])) { $max_size = $_GET['size_max']; } else { $max_size = '';  }
if (isset($_GET['no_beds'])) { $no_beds = $_GET['no_beds']; } else { $no_beds = '';  }
if (isset($_GET['no_baths'])) { $no_baths = $_GET['no_baths']; } else { $no_baths = '';  }
if (isset($_GET['location_names'])) { $location_id = $_GET['location_names']; } else { $location_id = '';  }
if (isset($_GET['property_types'])) { $propertytypes_id = $_GET['property_types']; } else { $propertytypes_id = '';  }
if (isset($_GET['property_status_id'])) { $propertystatus_id = $_GET['property_status_id']; } else { $propertystatus_id = '';  }

if ( $no_garages == '' ) {
	$no_garages = 'all';
}
if ( $no_beds == '' ) {
	$no_beds = 'all';
}
if ( $no_baths == '' ) {
	$no_baths = 'all';
}
if ( $location_id == '' ) {
	$location_id = 0;
}
if ( $propertytypes_id == ''  ) {
	$propertytypes_id = 0;
}
if ( $propertystatus_id == ''  ) {
	$propertystatus_id = 0;
}
if ( $min_price == '' ) {
	$min_price = 0;
}
if ( $max_price == ''  ) {
	$max_price = 0;
}
if ( ($no_garages != '') || ($no_beds != '') || ($no_baths != '') ) {
	$array_advanced_search = array(	'garages'	=>	$no_garages,
									'beds'		=>	$no_beds,
									'baths'		=>	$no_baths
									);
}

$array_advanced_search['price_min'] = $min_price;
$array_advanced_search['price_max'] = $max_price;

if ( $min_size == '' ) {
	$min_size = 0;
}
if ( $max_size == ''  ) {
	$max_size = 0;
}

$array_advanced_search['size_min'] = $min_size;
$array_advanced_search['size_max'] = $max_size;

if ( $location_id > 0 ) { $location_slug = get_term($location_id,'property_location'); } else { $location_slug = ''; }
if ( $propertytypes_id > 0 ) { $propertytype_slug = get_term($propertytypes_id,'property_type'); } else { $propertytype_slug = ''; }
if ( $propertystatus_id > 0 ) { $propertystatus_slug = get_term($propertystatus_id,'property_status'); } else { $propertystatus_slug = ''; }
//setup query string
$query_args = array();
$query_args['post_type'] = 'property';
    			
if ($location_id > 0) { $query_args['location'] = $location_slug->slug; }
if ($propertytypes_id > 0) { $query_args['propertytype'] = $propertytype_slug->slug; }
if ($propertystatus_id > 0) { $query_args['propertystatus'] = $propertytype_slug->slug; }

$has_results = false;
$keyword_to_search_raw = get_search_query();
 
if ( ($keyword_to_search_raw == get_option('colabs_search_keyword_text')) || ($keyword_to_search_raw == 'Your Keywords') ) { $keyword_to_search = ''; } else { $keyword_to_search = $keyword_to_search_raw; }

$posts_array = colabs_property_search_result_set($query_args, $keyword_to_search, $location_id, $propertytypes_id, $propertystatus_id, $array_advanced_search);

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$query_args['paged'] = $paged;
$array_counter = count($posts_array);
if ( $array_counter > 0 ) {
	$query_args['post__in'] = $posts_array;

}
ob_flush();
?>

<?php
if('Search'==$_GET['property-search-submit']){
	$searchproperties = new WP_Query($query_args);
	if ( $searchproperties->have_posts() ) :
?>
	<div class="post-list post-grid loading">
		<?php while ( $searchproperties->have_posts() ) : $searchproperties->the_post(); ?>
		<?php get_template_part('content','property');?>
		<?php endwhile;wp_reset_postdata();?>        
	</div><!-- .post-list -->
	<div class="post-loader">
		<a href="#" class="button button-grey"><?php _e('Загрузить больше', 'colabsthemes'); ?></a>
	</div>

	<?php colabs_content_nav($latestproperties);?>
	<?php else:?>
		<?php get_template_part('content','nopost');?>
	<?php endif;?>
<?php }else{
	if ( have_posts() ) :
?>
	<div class="post-list post-grid loading post-blog">
		<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part('content','post');?>
		<?php endwhile;?>        
	</div><!-- .post-list -->
	<div class="post-loader">
		<a href="#" class="button button-grey"><?php _e('Загрузить больше', 'colabsthemes'); ?></a>
	</div>

	<?php colabs_content_nav();?>
	<?php else:?>
		<?php get_template_part('content','nopost');?>
	<?php endif;?>
<?php }?>
<?php get_footer(); ?>