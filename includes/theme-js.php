<?php
/**
 * Load stylesheet and javascripts on frontend
 */
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'colabsthemes_script_and_style' );
}

if( ! function_exists( 'colabsthemes_script_and_style' ) ) {
	function colabsthemes_script_and_style() {

		// Styles
		wp_enqueue_style( 'framework', get_template_directory_uri() . '/includes/css/framework.css' );
		wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', array( 'framework' ) );

		// Scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-slider' );

		// Only load fancybox on single property
		if( 'property' == get_post_type() ) {
			wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/includes/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), '', true );
			wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/includes/js/fancybox/jquery.fancybox-1.3.4.css' );
		}

		wp_enqueue_script( 'plugins', trailingslashit( get_template_directory_uri() ) . 'includes/js/plugins.js', array('jquery'), '', true );
		wp_enqueue_script( 'scripts', trailingslashit( get_template_directory_uri() ) . 'includes/js/scripts.js', array('jquery'), '', true );

		/* Script for threaded comments. */
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
}