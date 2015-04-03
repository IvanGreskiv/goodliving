<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 ie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<title>
		<?php if ( function_exists( 'colabs_title' ) ){ 
			colabs_title(); 
		} else { 
			echo get_bloginfo( 'name' ); 
		?>&nbsp;<?php wp_title(); } ?>
	</title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="stylesheet" type="text/css" href="1.css">
	
	<!--[if lt IE 9]>
		<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/js/html5shiv.js"></script>
	<![endif]-->

	<?php
		if ( function_exists( 'colabs_meta' ) ) colabs_meta();
		if ( function_exists( 'colabs_meta_head' ) ) colabs_meta_head(); 
		if ( function_exists( 'colabs_head' ) ) colabs_head();
		$site_title = get_bloginfo( 'name' );
		$site_url = home_url( '/' );
		$site_description = get_bloginfo( 'description' );
		wp_head();
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
</head>

<body <?php body_class(); ?>>

<div style="background-color:#f7f7f7">
    
        
      <?php 
      echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="http://b730188r.bget.ru/"><img src="logo.jpg"></a>';
      ?>

      <h1 id="logooo" style="font-family:TickerTape, 'Comic Sans MS'">Снять недвижимость в Севастополе</h1>
        <!-- .logo-wrapper -->

</div>
<!-- .header-section -->
<div class="navbar container">
  <div class="row">
			<div class="collapse-button">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</div>
		
  	<div class="nav-collapse" style="background-color:#f8f8f8">
  	
	    <nav class="top-menu clearfix" style="background-color:#f8f8f8">
				<?php wp_nav_menu( array( 
							'theme_location' => 'top-menu',
							'container_class' => '',
							'menu_class' => 'menu'
				) ); ?>
	    </nav><!-- .top-menu -->

   	</div><!-- .nav-collapse -->

  </div>
</div>


<header class="header-section container" style="background-image: url(background.jpg); background-repeat:repeat-y;">
      <div class="row">
        <div class="logo-wrapper">
        <div class="search-wrapper clearfix" style="">
        <div class="iv2" id="i1" style="background-color:#f7f7f7; position:absolute; height: 34px; width:15%; margin: 0; top: 35.5%; right:33.4%; @media(min-width:50%){.need-hide{display:none;}}">
    		
    		</div>
    	<div class="iv1" id="i1" style="background-color:#f7f7f7; position:absolute; height: 34px; width:15%; margin: 0; top: 35.5%; right:49.6%">
    		
    		</div>
					<?php $idx_options = get_option(DSIDXPRESS_OPTION_NAME);
					if ( $idx_options['Activated'] && ( get_option('colabs_idx_plugin_search') == 'true' ) ) : ?>
						<ul class="search-tabs clearfix">
							<li><a href="#property-search"><?php echo get_option('colabs_search_header'); ?></a></li>
							<li><a href="#idx-search"><?php echo get_option('colabs_search_mls_header'); ?></a></li>
						</ul>
					<?php endif; ?>
					<?php get_template_part( 'includes/forms/property-search' ); ?>
        </div><!-- .search-wrapper -->
      </div>
</header>
<!-- .header-section -->

<div class="main-menu-wrapper container">
  <div class="row">
    <nav class="main-menu clearfix">
			<div style="width:100px; height:41px;">
			</div>
    </nav><!-- .main-menu -->
    <?php if ( is_home() ):?>      
    <div class="property-ordering">
          <select id="propertyorder" name="propertyorder">
						<option value="" <?php if($_GET['propertyorder']=='')echo 'selected="selected"';?>><?php _e('Сортировать по последним','colabsthemes');?></option>
            <option value="sort-price" <?php if($_GET['propertyorder']=='sort-price')echo 'selected="selected"';?>><?php _e('Сортировать по цене','colabsthemes');?></option>
            <option value="sort-title" <?php if($_GET['propertyorder']=='sort-title')echo 'selected="selected"';?>><?php _e('Сортировать по названию','colabsthemes');?></option>
						<option value="sort-popular" <?php if($_GET['propertyorder']=='sort-popular')echo 'selected="selected"';?>><?php _e('Сортировать по популярности','colabsthemes');?></option>
          </select>
					<script type="text/javascript"><!--
							var dropdown = document.getElementById("propertyorder");
							function onOrderChange() {
							if ( dropdown.options[dropdown.selectedIndex].value != '' ) {
								location.href = "<?php echo get_option('home');
					?>/?propertyorder="+dropdown.options[dropdown.selectedIndex].value;
							}
							}
							dropdown.onchange = onOrderChange;
					--></script>
    </div><!-- .property-ordering -->
		<?php endif;?>
  </div>
</div>
<!-- .main-menu-wrapper -->

<div class="main-content-wrapper container">
	<div class="row">
	<?php if (isset($_GET['property-search-submit'])) : get_template_part('search'); exit; endif;?>