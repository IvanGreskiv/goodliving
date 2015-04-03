<?php get_header(); ?>
<?php if('true' == get_option('colabs_announcement')):?>
<div class="alert alert-info">
  <h4 class="alert-title"><?php echo get_option('colabs_announcement_title');?></h4>
  <p><?php echo get_option('colabs_announcement_text');?></p>
  <button class="close">&times;</button>
</div>
<?php endif;?>

<?php
	if($_GET['propertyorder']=='sort-price'){
		$args = array(
									'post_type' => 'property',
									'meta_key'	=> 'property_price',
									'orderby'		=> 'meta_value',
									'order'			=> 'DESC',
									'paged'			=> $paged,
									);
	}elseif($_GET['propertyorder']=='sort-title'){
		$args = array(
									'post_type' => 'property',
									'orderby'		=> 'title',
									'order'			=> 'ASC',
									'paged'			=> $paged,
									);
	}elseif($_GET['propertyorder']=='sort-popular'){
		$args = array(
									'post_type' => 'property',
									'orderby'		=> 'comment_count',
									'paged'			=> $paged,
									);									
	}else{
		$args = array('post_type' => 'property',
									'paged'			=> $paged,
									);
	}

	$latestproperties = new WP_Query($args);
	if ( $latestproperties->have_posts() ) :
?>
<div style="height:50px">
	<h3 class="hed3">Последние добавленные:</h3>
</div>
<div class="post-list post-grid loading">
	<?php while ( $latestproperties->have_posts() ) : $latestproperties->the_post(); ?>
  <?php get_template_part('content','property');?>
	<?php endwhile;wp_reset_postdata();?>        
</div><!-- .post-list -->

<div class="post-loader">
	<a href="#" class="button button-grey"><?php _e('Загрузить больше', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($latestproperties);?>
<?php endif;?>
<?php get_footer(); ?>