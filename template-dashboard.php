<?php
/*
Template Name: Dashboard
*/

auth_redirect_login(); // if not logged in, redirect to login page
nocache_headers();

$current_user = wp_get_current_user(); // grabs the user info and puts into vars

$display_user_name = $current_user->display_name;

// check to see if we want to pause or restart the ad
if(!empty($_GET['action'])) :
    $action = trim($_GET['action']);
    $id = trim($_GET['id']);

    // make sure author matches ad. Prevents people from trying to hack other peoples ads
    $sql = $wpdb->prepare("SELECT wposts.post_author "
       . "FROM $wpdb->posts wposts "
       . "WHERE ID = %s "
       . "AND post_author = %s",
       $id,
       $current_user->ID);

    $checkauthor = $wpdb->get_row($sql);

    if($checkauthor != null) { // author check is ok. now update ad status

      if ($action == 'pause') {
          $my_ad = array();
          $my_ad['ID'] = $id;
          $my_ad['post_status'] = 'draft';
          wp_update_post($my_ad);

      } elseif ($action == 'restart') {
          $my_ad = array();
          $my_ad['ID'] = $id;
          $my_ad['post_status'] = 'publish';
          wp_update_post($my_ad);
			} elseif ($action == 'freerenew') { colabs_renew_property_listing($id);
			} elseif ($action == 'delete') { colabs_delete_property($id);
			} elseif ($action == 'setSold') { update_post_meta($id, 'colabs_property_sold', 'true'); 
			} elseif ($action == 'unsetSold') { update_post_meta($id, 'colabs_property_sold', 'false'); 
      }

    }

endif;
if( !empty( $_POST['payer_email']) ){
	$colabs_order = new colabs_order( $_GET['oid'] );

  if ($colabs_order->order_key==$_POST['item_number']) :
		
		$colabs_order->complete_order(__('Return URL','colabsthemes'));
          
    $payment_data = array();
    $payment_data['payment_date'] 		= date("Y-m-d H:i:s");
    $payment_data['payer_first_name'] 	= stripslashes(trim($_POST['first_name']));
    $payment_data['payer_last_name'] 	= stripslashes(trim($_POST['last_name']));
    $payment_data['payer_email'] 		= stripslashes(trim($_POST['payer_email']));
    $payment_data['payment_type'] 		= 'PayPal';
    $payment_data['payer_address']		= stripslashes(trim($_POST['residence_country']));
    $payment_data['transaction_id']		= stripslashes(trim($_POST['txn_id']));
    $payment_data['approval_method'] 	= __('Return URL','colabsthemes'); 
    
    $colabs_order->add_payment( $payment_data );
    
  endif;
	
	if ( !empty( $_GET['pid'] ) ) :
		$pid = trim( $_GET['pid'] );
		
		$sql = $wpdb->prepare( "SELECT p.ID, p.post_status
				FROM $wpdb->posts p, $wpdb->postmeta m
				WHERE p.ID = '%s'
				AND p.post_status <> 'publish'
				", $pid );

		$newid = $wpdb->get_row( $sql );
		
		if ( $newid ) {
			//if published already, take the user to there dashboard
			
			$property = array();
			
			$property['ID'] = $newid->ID;
			$property['post_status'] = 'publish';
			
			
			$property_id = wp_update_post( $property );

	
			$property_length = get_option('colabs_prun_period');

			// set the ad listing expiration date
			$property_expire_date = date_i18n( 'm/d/Y H:i:s', strtotime( '+' . $property_length . ' days' ) ); // don't localize the word 'days'

			//now update the expiration date on the ad
			update_post_meta( $property_id, 'expires', $property_expire_date );

			// send the permalink to the page
			$new_property_url = '<a href="' . get_permalink( $property_id ) . '">'. __('View your new property', 'colabsthemes') .'</a>';

		}
	endif;
}
?>			

<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
        
<div class="main-content column col9">
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php printf(__("%s's Property", 'colabsthemes'), $display_user_name); ?></h2>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content" id="property-details">
        <table border="0" cellpadding="4" cellspacing="1" class="table-my-property">
          <thead>
              <tr>
              <th class="text-center" colspan="2"><?php _e('Title','colabsthemes');?></th>		
              <th class="text-center" width="120px"><?php _e('Status','colabsthemes');?></th>
              <th width="90px"><div style="text-align: center;"><?php _e('Options','colabsthemes');?></div></th>
              </tr>
          </thead>
					<tbody>
					<?php 
						// setup the pagination and query
						$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
						$query_property = new WP_Query(array('posts_per_page' => 10, 'post_type' => 'property', 'post_status' => 'publish, pending, draft', 'author' => $current_user->ID, 'paged' => $paged));

						// build the row counter depending on what page we're on
						if($paged == 1) $i = 0; else $i = $paged * 10 - 10;
					?>
					<?php if($query_property->have_posts()) : ?>

					<?php while($query_property->have_posts()) : $query_property->the_post(); $i++; ?>
						<?php 
						$expire_date = '';
            // check to see if property is legacy or not and then format date based on WP options
            if(get_post_meta($post->ID, 'expires', true)!=''){
            $expire_date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post_meta($post->ID, 'expires', true)));
            }                
							
						global $wpdb;		
            $total_cost = $wpdb->get_var( $wpdb->prepare( "SELECT cost FROM ".$wpdb->prefix."colabs_orders WHERE property_id=$post->ID;" ) );
							
            // now let's figure out what the property status and options should be
            // it's a live and published property
            if ($post->post_status == 'publish') {
							if(!empty($expire_date)){
							$poststatus = '<h6 class="property-status">';
              $poststatus .= __('Valid','colabsthemes');
							$poststatus .= ' ' . __('Until','colabsthemes') . '<br/><p class="small">(' . $expire_date . ')</p>';
							$poststatus .= '</h6>';
							}else{
							$poststatus = '<h6 class="property-status">';
              $poststatus .= __('Published','colabsthemes');
							$poststatus .= '</h6>';
							}
              $postimage = 'pause.png';
              $postalt =  __('Pause Property','colabsthemes');
              $postaction = 'pause';

            // it's a pending property which gives us several possibilities
            } elseif ($post->post_status == 'pending') {

              // property is free and waiting to be approved
              if ($total_cost == 0) {
								$poststatus = '<h6 class="property-status">';
                $poststatus .= __('Awaiting approval','colabsthemes');
								$poststatus .= '</h6>';
                $postimage = '';
                $postalt = '';
                $postaction = 'pending';

              // property hasn't been paid for yet
              } else {
								$poststatus = '<h6 class="property-status">';
                $poststatus .= __('Awaiting payment','colabsthemes');
								$poststatus .= '</h6>';
                $postimage = '';
                $postalt = '';
                $postaction = 'pending';
              }
                               
          } elseif ($post->post_status == 'draft') { 

            // current date is past the expires date so mark property ended
            if (strtotime(date('Y-m-d')) > strtotime(get_post_meta($post->ID, 'expires', true))) {
							$poststatus = '<h6 class="property-status">';
              $poststatus .= __('Ended','colabsthemes') . '<br/><p class="small">(' . $expire_date . ')</p>';
							$poststatus .= '</h6>';
              $postimage = '';
              $postalt = '';
              $postaction = 'ended';

             // property has been paused by property owner
            } else {
							$poststatus = '<h6 class="property-status">';
              $poststatus .= __('Offline','colabsthemes');
							$poststatus .= '</h6>';
              $postimage = 'start.png';
              $postalt = __('Restart Property','colabsthemes');
              $postaction = 'restart';
            }

          } else {
            $poststatus = '&mdash;';
          }?>
					<tr class="even">
						<td class="text-center"><?php colabs_image('key=property_image&width=100&height=100'); ?></td>
						<td>
							<h5>
                <?php if ($post->post_status == 'pending' || $post->post_status == 'draft' || $poststatus == 'ended' || $poststatus == 'offline') { ?>
                  <?php the_title(); ?>
                <?php } else { ?>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								<?php } ?>    
              </h5>

              <div class="meta"><span class="folder"><?php echo get_the_term_list(get_the_id(), 'property_type', '', ', ', ''); ?></span><span class="clock"><span><?php the_time(get_option('date_format'))?></span></span></div>

            </td>
            <td class="text-center ads-status"><?php echo ucfirst($poststatus) ?></td>
						<td class="text-center ads-actions">
              <?php if ( $post->post_status == 'pending' && $postaction != 'ended' ) {

                // show the paypal button if the ad has not been paid for yet
                if ( ($total_cost != 0) && (get_option('colabs_enable_paypal') == 'true') ) {
                  echo colabs_dashboard_paypal_button( $post->ID );
                } 
								echo '<a onclick="return confirm_before_delete();" href="' . CL_DASHBOARD_URL . '?id=' . $post->ID . '&amp;action=delete">';
								echo   '<img src="'. get_template_directory_uri() . '/images/close.png" title="' . __('Delete Property', 'colabsthemes') . '" alt="'. __('Delete property', 'colabsthemes') .'" border="0" />';
								echo '</a>';

              } elseif ( $post->post_status == 'draft' && $postaction == 'ended' ) {
                if ( get_option('colabs_allow_relist') == 'true' ) {                                    
									if ( ($total_cost != 0) ) {
                      if ( get_option('colabs_enable_paypal') == 'true' ){
                        echo colabs_dashboard_paypal_button( $post->ID );
											}else{
											    _e('Contact us to relist property', 'colabsthemes');
											}		
                  } else {
                    echo '<a class="btn" href="' . CL_DASHBOARD_URL . '?id=' . $post->ID . '&amp;action=freerenew">' . __('Relist Property', 'colabsthemes') . '</a>';
                  }                                       
                } 
              } else { ?>
								<?php if ( get_option('colabs_allow_editing') == 'true' ) : ?>
									<a class="edit-property" href="<?php echo CL_EDIT_URL; ?>?id=<?php the_id(); ?>" title="<?php _e('Edit Property', 'colabsthemes'); ?>"><i class="icon-edit"></i></a>&nbsp;&nbsp;
							  <?php endif; ?>
                <a class="delete-property" onclick="return confirm_before_delete();" href="<?php echo CL_DASHBOARD_URL; ?>?id=<?php the_id(); ?>&amp;action=delete" title="<?php _e('Delete Property', 'colabsthemes'); ?>"><i class="icon-trash"></i></a>&nbsp;&nbsp;
							  <a href="<?php echo CL_DASHBOARD_URL; ?>?id=<?php the_id(); ?>&amp;action=<?php echo $postaction; ?>" title="<?php echo $postalt;?>"><i class="icon-pause"></i></a>
                <br />
							  
                <?php if ( get_post_meta(get_the_id(), 'colabs_property_sold', true) != 'true' ) : ?>
									<a class="button mark-property" href="<?php echo CL_DASHBOARD_URL; ?>?id=<?php the_id(); ?>&amp;action=setSold"><?php _e('Mark Sold', 'colabsthemes'); ?></a>
                 <?php else : ?>
									<a class="button mark-property" href="<?php echo CL_DASHBOARD_URL; ?>?id=<?php the_id(); ?>&amp;action=unsetSold"><?php _e('Unmark Sold', 'colabsthemes'); ?></a>
							  <?php endif; ?>
              <?php } ?>
            </td>
					</tr>
					<?php endwhile; ?>
							
					<script type="text/javascript">
						/* <![CDATA[ */
						function confirm_before_delete() { return confirm("<?php _e('Are you sure you want to delete this property?', 'colabsthemes'); ?>"); }
						/* ]]> */
					</script>	

          <?php else : ?>
            <tr class="even">
              <td colspan="5">
							<p class="text-center"><?php _e('You currently have no property.','colabsthemes');?></p>
							</td>
            </tr>
          <?php endif; ?>
					</tbody>
				</table>
      </div>

    </div><!-- .property-details -->
		<?php colabs_pagination(array(),$query_property);?>
  </article><!-- .single-entry-post -->

</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->
<?php get_footer(); ?>
