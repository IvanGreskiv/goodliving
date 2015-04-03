<?php get_header(); ?>

<div class="single-prop-top clearfix">
	<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
	<?php echo colabs_share(); ?>
</div>

<div class="main-content column col9">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	
	<?php
	$id = get_the_ID();
	$address = get_post_meta($id, 'property_address',true);
	$citystate = get_post_meta($id, 'property_citystate',true);	
	$beds = get_post_meta($id, 'property_beds',true);
	$baths = get_post_meta($id, 'property_baths',true);
	$size = get_post_meta($id, 'property_size',true);
	$garage = get_post_meta($id, 'property_garage',true);
	$furnished= get_post_meta($id, 'property_furnished',true);
	$mortgage= get_post_meta($id, 'property_mortgage',true);
	
	$latlong = get_post_meta(get_the_ID(), "property_google_location", true); 
	if ($latlong=='')$mapaddress=$address.', '.$citystate;
	$maps_active = get_post_meta(get_the_ID(),'colabs_maps_enable',true);

	$src = get_post_meta(get_the_ID(),'image',true);
	?>
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php the_title();?></h2>
      <span class="property-location"><?php echo $address.' '.$citystate;?></span>
    </header>

		<?php
			$attachments = get_children( array(
				'post_parent' => get_the_ID(),
				'numberposts' => 100,
				'post_type' => 'attachment',
				'post_mime_type' => 'image' )
			);
		?>

    <div class="property-info">
      <ul class="property-info-tabs clearfix">
      	<?php if ( !empty($attachments) ) : ?>	
        	<li><a href="#property-gallery"><i class="icon-camera"></i> <?php _e('Галерея','colabsthemes');?></a></li>
				<?php endif; ?>
				
				<?php if($maps_active == 'on') { ?>
        	<li><a href="#property-maps"><i class="icon-map-marker"></i> <?php _e('Google Карты','colabsthemes');?></a></li>
				<?php }?>

        <li><a href="#property-facilities"><i class="icon-tasks"></i> <?php _e('Удобства','colabsthemes');?></a></li>
      </ul>
      
			<?php
			if ( !empty($attachments) ) :?>	
			<div class="property-info-panel" id="property-gallery">
        <div class="property-gallery-large"></div>
        <div class="property-gallery-thumb-wrapper">
          <div class="property-gallery-thumb">
					<?php
							foreach ( $attachments as $att_id => $attachment ) {
								$url = wp_get_attachment_image_src($att_id, 'full', true);
								echo '<a href="'.$url[0].'" >';
								colabs_image('link=img&width=74&height=74&src='.$url[0]);
								echo '</a>';
							} 
					?>
          </div>
        </div>  
			</div>
			<?php endif; ?>
			
			<?php if($maps_active == 'on') { ?>
      <div class="property-info-panel" id="property-maps">
				<?php 
				// echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
				if($maps_active == 'on'){
					$mode = get_post_meta(get_the_ID(),'colabs_maps_mode',true);
					$streetview = get_post_meta(get_the_ID(),'colabs_maps_streetview',true);
					$address = get_post_meta(get_the_ID(),'colabs_maps_address',true);
					$long = get_post_meta(get_the_ID(),'colabs_maps_long',true);
					$lat = get_post_meta(get_the_ID(),'colabs_maps_lat',true);
					$pov = get_post_meta(get_the_ID(),'colabs_maps_pov',true);
					$from = get_post_meta(get_the_ID(),'colabs_maps_from',true);
					$to = get_post_meta(get_the_ID(),'colabs_maps_to',true);
					$zoom = get_post_meta(get_the_ID(),'colabs_maps_zoom',true);
					$type = get_post_meta(get_the_ID(),'colabs_maps_type',true);
					$yaw = get_post_meta(get_the_ID(),'colabs_maps_pov_yaw',true);
					$pitch = get_post_meta(get_the_ID(),'colabs_maps_pov_pitch',true);
												
					
					if(!empty($lat) OR !empty($from)){
						colabs_maps_single_output("mode=$mode&streetview=$streetview&address=$address&long=$long&lat=$lat&pov=$pov&from=$from&to=$to&zoom=$zoom&type=$type&yaw=$yaw&pitch=$pitch"); 
					}
				}
				?>
			</div>
			<?php }?>
      <div class="property-info-panel" id="property-facilities">
				<?php 
           echo '<ul class="property-facility">'; 
            if($size!='') echo '<li class="prop-size"><span>'.$size.' '.__("М.кв.","colabsthemes").'</span></li>';
            if($beds!='') echo '<li class="prop-beds"><span>'.$beds.' '.__("Комнат","colabsthemes").'</span></li>';
            if($baths!='') echo '<li class="prop-baths"><span>'.$baths.' '.__("Этаж","colabsthemes").'</span></li>';
            if($furnished=='true') echo '<li class="prop-furnished"><span>'.__("Мебель","colabsthemes").'</span></li>';
           echo '</ul>';
				?>
				<div class="property-facilities-info">
					<?php echo get_the_term_list($post->ID, 'property_features', '<div class="entry-features">'.__("Удобства","colabsthemes").' : ', ', ','</div>');   ?>
	        <?php echo get_the_term_list($post->ID, 'property_type', '<div class="entry-type">'.__("Тип","colabsthemes").' : ', ', ','</div>');   ?>
	        <?php echo get_the_term_list($post->ID, 'property_location', '<div class="entry-location">'.__("Расположение","colabsthemes").' : ', ', ','</div>');   ?>
					<?php echo get_the_term_list($post->ID, 'property_status', '<div class="entry-status">'.__("Статус","colabsthemes").' : ', ', ','</div>');   ?>
				</div>
			</div>
    </div><!-- .peroperty-info -->

    <div class="property-details">
      <ul class="property-details-tabs clearfix">
        <li><a href="#property-details"><?php _e('Описание','colabsthemes');?></a></li>
        <li><a href="#property-reviews"><?php _e('Комментарии','colabsthemes');?> <span><?php comments_number( __('(0)','colabsthemes'), __('(1)','colabsthemes'), __('(%)','colabsthemes') ); ?></span></a></li>
      </ul>

      <div class="property-details-panel entry-content" id="property-details">
        <?php the_content();?>
      </div>

      <div class="property-details-panel" id="property-reviews"><?php comments_template(); ?></div>
    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->
	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_sidebar();?><!-- .property-sidebar -->
<?php get_footer(); ?>