<?php
/*-----------------------------------------------------------------------------------*/
/* SET GLOBAL CoLabs VARIABLES
/*-----------------------------------------------------------------------------------*/
add_role('member', 'Member', array(
    'read' => true, // True allows that capability
    'edit_posts' => true,
    'delete_posts' => false, // Use false to explicitly deny
));

add_theme_support( 'automatic-feed-links' );

/**
 * ===================================================================
 * Custom Excerpt
 * ===================================================================
 */

// Add excerpt on pages
// --------------------
if(function_exists('add_post_type_support'))
add_post_type_support('page', 'excerpt');

// Set excerpt length
// ------------------
function colabs_excerpt_length( $length ) {
	if( get_option('colabs_excerpt_length') != '' ){
		return get_option('colabs_excerpt_length');
	}else{
		return 45;
	}
}
add_filter('excerpt_length', 'colabs_excerpt_length');

// Remove [..] in excerpt
// ----------------------
function colabs_trim_excerpt($text) {
	return rtrim($text,'[...]');
}
add_filter('get_the_excerpt', 'colabs_trim_excerpt');

// Add excerpt more
// ----------------
function colabs_excerpt_more($more) {
	global $post;
	return '<span class="more"><a href="'. get_permalink($post->ID) . '">'. __( 'Read more', 'colabsthemes' ) . '&hellip;</a></span>';
}
add_filter('excerpt_more', 'colabs_excerpt_more');

/**
 * ===================================================================
 * Register Custom Menus
 * ===================================================================
 */
if ( function_exists('register_nav_menus') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array(
	'top-menu' => __( 'Top Menu','colabsthemes' ),
	'main-menu' => __( 'Main Menu','colabsthemes' )
	) );
}

/**
 * ===================================================================
 * WP 3.0 post thumbnails compatibility
 * ===================================================================
 */
if(function_exists( 'add_theme_support')){
	if( get_option('colabs_post_image_support') ){
		add_theme_support( 'post-thumbnails' );		
		// set height, width and crop if dynamic resize functionality isn't enabled
		if ( get_option( 'colabs_pis_resize') <> "true" ) {
			$hard_crop = get_option( 'colabs_pis_hard_crop' );
			if( 'true' == $hard_crop ) {$hard_crop = true; } else { $hard_crop = false;} 
			add_image_size( 'headline-thumb', 978, 99999, $hard_crop);
		}
	}
}
/*-----------------------------------------------------------------------------------*/
/* CoLabs - User Meta */
/*-----------------------------------------------------------------------------------*/ 
function new_user_meta( $contactmethods ) {
$contactmethods['address'] = 'Address';
$contactmethods['phone'] = 'Phone';
return $contactmethods;
}
add_filter('user_contactmethods','new_user_meta',10,1);
/*-----------------------------------------------------------------------------------*/
/*  Open Graph Meta Function    */
/*-----------------------------------------------------------------------------------*/
function colabs_meta_head(){
    do_action( 'colabs_meta' );
}
add_action( 'colabs_meta', 'og_meta' );

/*-----------------------------------------------------------------------------------*/
/* CoLabs - Footer Credit */
/*-----------------------------------------------------------------------------------*/
function colabs_credit(){
global $themename,$colabs_options;
if( $colabs_options['colabs_footer_credit'] != 'true' ){ ?>
            Copyright &copy; 2013 <a href="http://colorlabsproject.com/themes/<?php echo get_option('colabs_themename'); ?>/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo get_option('colabs_themename'); ?></a> by <a href="http://colorlabsproject.com/" title="Colorlabs">ColorLabs & Company</a>. All rights reserved.
<?php }else{ echo stripslashes( $colabs_options['colabs_footer_credit_txt'] ); } 
}

/*-----------------------------------------------------------------------------------*/
/*  colabs_share - Twitter, FB & Google +1    */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'colabs_share' ) ) {
function colabs_share() {
    
$return = '';


$colabs_share_twitter = get_option('colabs_share_twitter');
$colabs_share_fblike = get_option('colabs_share_fblike');
$colabs_share_google_plusone = get_option('colabs_share_google_plusone');
$colabs_share_pinterest = get_option('colabs_share_pinterest');
$colabs_share_linkedin = get_option('colabs_share_linkedin');


    //Share Button Functions 
    global $colabs_options;
    $url = get_permalink();
    $share = '';
    
    //Twitter Share Button
    if(function_exists('colabs_shortcode_twitter') && $colabs_share_twitter == "true"){
        $tweet_args = array(  'url' => $url,
   							'style' => 'horizontal',
   							'source' => ( $colabs_options['colabs_twitter_username'] )? $colabs_options['colabs_twitter_username'] : '',
   							'text' => '',
   							'related' => '',
   							'lang' => '',
   							'float' => 'fl'
                        );

        $share .= colabs_shortcode_twitter($tweet_args);
    }
    
   
        
    //Google +1 Share Button
    if( function_exists('colabs_shortcode_google_plusone') && $colabs_share_google_plusone == "true"){
        $google_args = array(
						'size' => 'medium',
						'language' => '',
						'count' => '',
						'href' => $url,
						'callback' => '',
						'float' => 'left',
						'annotation' => 'bubble'
					);        

        $share .= colabs_shortcode_google_plusone($google_args);       
    }
	
	 //Facebook Like Button
    if(function_exists('colabs_shortcode_fblike') && $colabs_share_fblike == "true"){
    $fblike_args = 
    array(	
        'float' => 'left',
        'url' => '',
        'style' => 'button_count',
        'showfaces' => 'false',
        'width' => '80',
        'height' => '',
        'verb' => 'like',
        'colorscheme' => 'light',
        'font' => 'arial'
        );
        $share .= colabs_shortcode_fblike($fblike_args);    
    }
    
		global $post;
	if (is_attachment()){
	$att_image = wp_get_attachment_image_src( $post->id, "thumbnail");
	$image = $att_image[0];
	}else{
    $image = colabs_image('return=true&link=url&id='.$post->ID);
	}
	//Pinterest Share Button
	if( function_exists('colabs_shortcode_pinterest') && $colabs_share_pinterest == "true"){
        $pinterest_args = array(
						'count' => 'horizontal',
						'float' => 'left',  
						'use_post' => 'true',
						'image_url' => $image,
						'url' => $url
					);        

        $share .= colabs_shortcode_pinterest($pinterest_args);       
    } 
	
	//Linked Share Button
    if( function_exists('colabs_shortcode_linkedin_share') && $colabs_share_linkedin == "true"){
        $linkedin_args = array(
						'url' 	=> $url,
						'style' => 'right', 
						'float' => 'left'
					);        

        $share .= colabs_shortcode_linkedin_share($linkedin_args);       
    }
		
    $return .= '<div class="social_share">'.$share.'</div><div class="clear"></div>';
    
    return $return;
}
}

/*-----------------------------------------------------------------------------------*/
/* CoLabs Advertisement - colabs_ad_gen */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'colabs_ad_gen' ) ) {
	function colabs_ad_gen() { 
	   
    global $colabs_options;
    global $post;
    
    //default
    $colabs_ad_single = isset($colabs_options['colabs_ad_single']) ? $colabs_options['colabs_ad_single'] : '';
    $colabs_ad_single_adsense = isset($colabs_options['colabs_ad_single_adsense']) ? $colabs_options['colabs_ad_single_adsense'] : '';
    $colabs_ad_single_image = isset($colabs_options['colabs_ad_single_image']) ? $colabs_options['colabs_ad_single_image'] : '';
    $colabs_ad_single_url = isset($colabs_options['colabs_ad_single_url']) ? $colabs_options['colabs_ad_single_url'] : '';
    $width = 728;
    $height = 90;
    
    //Single Custom Ad
    $colabs_ad_single_custom = get_post_meta($post->ID, 'colabs_ad_single', true); //none, general_ad, custom_ad
    
    if( 'custom_ad' == $colabs_ad_single_custom ){
        $colabs_ad_single = 'true';
        $colabs_ad_single_adsense = get_post_meta($post->ID, 'colabs_ad_single_adsense', true);
        $colabs_ad_single_image = get_post_meta($post->ID, 'colabs_ad_single_image', true);
        $colabs_ad_single_url = get_post_meta($post->ID, 'colabs_ad_single_url', true);
        }
    
        if ( 'true' == $colabs_ad_single && 'none' != $colabs_ad_single_custom && ( '' != $colabs_ad_single_adsense || '' != $colabs_ad_single_image ) ) { ?>
	    <div id="singlead">
            <?php if ("" <> $colabs_ad_single_adsense) { echo stripslashes($colabs_ad_single_adsense);  } else { ?>
                <a href="<?php echo $colabs_ad_single_url; ?>"><img src="<?php echo $colabs_ad_single_image; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="advert" /></a>
            <?php } ?>		   	
        </div><!-- /#topad -->
        <?php }
	}
}

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
add_filter( 'body_class','colabs_layout_body_class', 10 );					// Add layout to body_class output
if ( ! function_exists( 'colabs_layout_body_class' ) ) {
	function colabs_layout_body_class( $classes ) {
		$layout = '';
		// Set main layout
		if ( is_singular() ) {
			global $post;
			$layout = get_post_meta($post->ID, 'layout', true); }
        //set $colabs_option
        if ( $layout != '' ) {
			global $colabs_options;
            $colabs_options['colabs_layout'] = $layout; } else {
                $layout = get_option( 'colabs_layout' );
				if ( $layout == '' ) $layout = "two-col-left";
            }
		// Add classes to body_class() output 
		$classes[] = $layout;
		return apply_filters('colabs_layout_body_class', $classes);
	}
}

function colabs_content_nav( $query ) {
	global $wp_query;
	
	if(empty($query))$query = $wp_query;

	if ( $query->max_num_pages > 1 ) : ?>
		<nav class="navigation" role="navigation">
			<?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'colabsthemes' ),$query->max_num_pages ); ?>
			<?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'colabsthemes',$query->max_num_pages ) ); ?>
		</nav>
	<?php endif;
}

/*-----------------------------------------------------------------------------------*/
/* WordPress Customizer
/*-----------------------------------------------------------------------------------*/
function colabs_customize_register( $wp_customize ) {
	class Colabs_Customize_Textarea_Control extends WP_Customize_Control {
			public $type = 'textarea';
	 
			public function render_content() {
					?>
					<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
					</label>
					<?php
			}
	}
  $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
  $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	
	$wp_customize->add_setting('colabs_logo', array(
    'default'      => '',
    'capability'   => 'edit_theme_options',
    'type'         => 'option',
	));
	 
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'colabs_logo', array(
			'label'    => __('Upload Logo', 'colabsthemes'),
			'section'  => 'title_tagline',
			'settings' => 'colabs_logo',
			'priority' => 5,
	)));
	
  // Layout Settings
  // -----------------------------
  $wp_customize->add_section( 'layout_settings', array(
    'title'    => __( 'Layout', 'colabsthemes' ),
    'priority' => 50,
  ) );
  
  $wp_customize->add_setting( 'colabs_layout', array(
    'default'    => 'one-col',
    'type'       => 'option',
    'capability' => 'edit_theme_options',
  ) );

  $choices = array(
		'one-col'  			=> __('Fullwidth', 'colabsthemes'),
    'two-col-left'  => __('Content on left', 'colabsthemes'),
    'two-col-right' => __('Content on right', 'colabsthemes')
  );

  $wp_customize->add_control( 'colabs_layout', array(
    'label'    => __( 'Select main content and sidebar alignment. Choose between left or right sidebar layout or fullwidth', 'colabsthemes' ),
    'section'  => 'layout_settings',
    'settings' => 'colabs_layout',
    'type'     => 'radio',
    'choices'  => $choices,
    'priority' => 5,
  ) );  
	 
	
	
	// Footer Settings
  // -----------------------------
  $wp_customize->add_section( 'footer_settings', array(
    'title'    => __( 'Footer', 'colabsthemes' ),
    'priority' => 60,
  ) );
	
	$wp_customize->add_setting('colabs_footer_credit', array(
			'capability' => 'edit_theme_options',
			'type'       => 'option',
	));
	 
	$wp_customize->add_control('colabs_footer_credit', array(
			'settings' => 'colabs_footer_credit',
			'label'    => __('Enable / Disable Custom Credit','colabsthemes'),
			'section'  => 'footer_settings',
			'type'     => 'checkbox',
	));
	
	$wp_customize->add_setting( 'colabs_footer_credit_txt', array(
			'type' 			=> 'option',
			'default'   => '',
	) );
	 
	$wp_customize->add_control( new Colabs_Customize_Textarea_Control( $wp_customize, 'colabs_footer_credit_txt', array(
			'label'   	=> 'Footer Credit',
			'section' 	=> 'footer_settings',
			'settings'  => 'colabs_footer_credit_txt',
	) ) );
}
add_action( 'customize_register', 'colabs_customize_register' );

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 * 
 */
function colabs_customize_preview_js() {
  wp_enqueue_script( 'colabs-customizer', get_template_directory_uri() . '/includes/js/theme-customizer.js', array( 'customize-preview' ), '20120620', true );
}
add_action( 'customize_preview_init', 'colabs_customize_preview_js' );

//Fix for home page navigation error on WP 3.4
function colabs_query_for_homepage( $query ) {
global $paged;
	if ( ! is_preview() && ! is_admin() && ! is_singular() && ! is_404() ) {
		if ( $query->is_feed ) {
		// As always, handle your feed post types here.
		} else {
		$my_post_type = get_query_var( 'post_type' );
		if ( empty( $my_post_type ) ) {
		$args = array(
		'public' => true ,
		'_builtin' => false
		);
		$output = 'names';
		$operator = 'and';

		// Get all custom post types automatically.
		$post_types = get_post_types( $args, $output, $operator );
		// Or uncomment and edit to explicitly state which post types you want. */
		// $post_types = array( 'event', 'location' );

		// Add 'link' and/or 'page' to array() if you want these included.
		// array( 'post' , 'link' , 'page' ), etc.
		$post_types = array_merge( $post_types, array( 'post' ) );
		$query->set( 'post_type', $post_types );
		}
		}
	}
}
add_action( 'pre_get_posts', 'colabs_query_for_homepage' );
?>