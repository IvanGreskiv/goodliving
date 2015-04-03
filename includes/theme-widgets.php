<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/*---------------------------------------------------------------------------------*/
/* Loads all the .php files found in /includes/widgets/ directory */
/*---------------------------------------------------------------------------------*/

include( TEMPLATEPATH . '/includes/widgets/widget-colabs-tabs.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-flickr.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-socialnetwork.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-ad-sidebar.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-embed.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-twitter.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-colabs-fbfriends.php' );

?>