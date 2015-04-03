<?php
/**
 * Payment gateways
 */
global $action_gateway_values, $options_gateways;

$shortname =  'colabs';

$options_gateways = array(); 

$options_gateways[] = array( "name" => __( 'PayPal Options', 'colabsthemes' ),
                        "icon" => "general",
                        "type" => "heading");    

$options_gateways[] = array( "name" => __( 'Enable PayPal', 'colabsthemes' ),
                        "desc" => sprintf( __("You must have a <a target='_new' href='%s'>PayPal</a> account setup before using this feature.",'colabsthemes'), 'http://www.paypal.com/' ),
                        "id" => $shortname."_enable_paypal",
						"type" => "checkbox",
						"std" => "true"); 
						
$options_gateways[] = array( "name" => __( 'PayPal Email', 'colabsthemes' ),
                        "desc" => __( 'Enter your PayPal account email address. This is where your money gets sent.', 'colabsthemes' ),
                        "id" => $shortname."_property_paypal_email",
                        "type" => "text"); 

$options_gateways[] = array( "name" => __('Collect Payments in', 'colabsthemes'),
					"desc" => sprintf( __('See the list of supported <a target="_new" href="%s">PayPal currencies</a>. %s', 'colabsthemes'), 'https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside', __('This is the currency you want to collect payments in. It applies mainly to PayPal payments since other payment gateways accept more currencies. If your currency is not listed then PayPal currently does not support it.','colabsthemes') ),
					"id" => $shortname."_property_paypal_currency",
					"std" => "",
					"type" => "select2",
					"options" => array( 
            			'USD' => __('US Dollars (&#36;)', 'colabsthemes'),
            			'EUR' => __('Euros (&euro;)', 'colabsthemes'),
            			'GBP' => __('Pounds Sterling (&pound;)', 'colabsthemes'),
            			'AUD' => __('Australian Dollars (&#36;)', 'colabsthemes'),
            			'BRL' => __('Brazilian Real (&#36;)', 'colabsthemes'),
            			'CAD' => __('Canadian Dollars (&#36;)', 'colabsthemes'),
            			'CZK' => __('Czech Koruna', 'colabsthemes'),
            			'DKK' => __('Danish Krone', 'colabsthemes'),
            			'HKD' => __('Hong Kong Dollar (&#36;)', 'colabsthemes'),
            			'HUF' => __('Hungarian Forint', 'colabsthemes'),
            			'ILS' => __('Israeli Shekel', 'colabsthemes'),
            			'JPY' => __('Japanese Yen (&yen;)', 'colabsthemes'),
            			'MYR' => __('Malaysian Ringgits', 'colabsthemes'),
            			'MXN' => __('Mexican Peso (&#36;)', 'colabsthemes'),
            			'NZD' => __('New Zealand Dollar (&#36;)', 'colabsthemes'),
            			'NOK' => __('Norwegian Krone', 'colabsthemes'),
            			'PHP' => __('Philippine Pesos', 'colabsthemes'),
            			'PLN' => __('Polish Zloty (z&#321;)', 'colabsthemes'),
            			'SGD' => __('Singapore Dollar (&#36;)', 'colabsthemes'),
            			'SEK' => __('Swedish Krona', 'colabsthemes'),
            			'CHF' => __('Swiss Franc', 'colabsthemes'),
            			'TWD' => __('Taiwan New Dollars', 'colabsthemes'),
            			'THB' => __('Thai Baht', 'colabsthemes')
            		),
                    );
					
$options_gateways[] = array( "name" => __( 'Enable PayPal IPN', 'colabsthemes' ),
                        "desc" => __("Your web host must support fsockopen otherwise this feature will not work. You must also enable IPN within your PayPal account.",'colabsthemes'),
                        "id" => $shortname."_enable_paypal_ipn",
						"type" => "checkbox",
						"std" => "false"); 

$options_gateways[] = array( "name" => __( 'Enable IPN Debug', 'colabsthemes' ),
                        "desc" => sprintf( __("Debug PayPal IPN emails will be sent to %s.",'colabsthemes'), get_option('admin_email') ),
                        "id" => $shortname."_paypal_ipn_debug",
						"type" => "checkbox"); 
											 
$options_gateways[] = array( "name" => __( 'Sandbox Mode', 'colabsthemes' ),
                        "desc" => sprintf( __("You must have a <a target='_new' href='%s'>PayPal Sandbox</a> account setup before using this feature.",'colabsthemes'), 'http://developer.paypal.com/' ),
                        "id" => $shortname."_use_paypal_sandbox",
						"type" => "checkbox");						

$options_gateways[] = array( "name" => __( 'Bank Transfer Options', 'colabsthemes' ),
                        "icon" => "general",
                        "type" => "heading");	

$options_gateways[] = array( "name" => __( 'Enable Bank Transfer', 'colabsthemes' ),
                        "desc" => __('Set this to yes if you want to offer cash payments via bank transfer as a payment option on your site. Note: the &quot;Charge for Listing Ads&quot; option on the pricing page must be set to yes for this option to work.','colabsthemes'),
                        "id" => $shortname."_enable_bank",
						"type" => "checkbox",
						"std" => "true"); 

$options_gateways[] = array( "name" => __( 'Wire Instructions', 'colabsthemes' ),
                        "desc" => __('Enter your specific bank wire instructions here. HTML can be used.','colabsthemes'),
                        "id" => $shortname."_bank_instructions",
						"type" => "textarea");

// hook for admin values
colabs_action_gateway_values();
function colabs_action_gateway_values() {
    do_action( 'colabs_action_gateway_values' ); 
}

// merge the above options with any passed into via the hook
$options_gateways = array_merge( (array)$options_gateways, (array)$action_gateway_values);

add_action('admin_menu', 'register_gateway_submenu_page');
add_action( 'admin_head', 'colabs_gateway_admin_head' );
function register_gateway_submenu_page() {
	add_submenu_page( 'colabsthemes', __('Payment Gateway Options','colabsthemes'), __('Payment Gateways','colabsthemes'), 'manage_options', 'gateways', 'colabs_gateways' );
}
/* colabs_admin_head()
--------------------------------------------------------------------------------*/
function colabs_gateway_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/functions/admin-style.css" media="screen" />';
	wp_register_script( 'colabs-scripts', get_template_directory_uri() .'/functions/js/colabs-scripts.js', array( 'jquery' ));

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-droppable' );	
    
	wp_enqueue_script( 'colabs-scripts' );
	
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery( '.group').hide();
			jQuery( '.group:first').fadeIn();
		});
	</script>	
	<?php
	
	}

function colabs_gateways() {
    global $options_gateways;
	$themename =  get_option( 'colabs_themename' );
    $manualurl =  get_option( 'colabs_manual' );
	$colabs_framework_version = get_option( 'colabs_framework_version' );
	$theme_data = get_theme_data( get_template_directory() . '/style.css' );
    $local_version = $theme_data['Version'];	
	$pos = strpos($manualurl, 'documentation' );
	$theme_slug = str_replace( "/", "", substr($manualurl, ($pos + 13))); //13 for the word documentation

    //add filter to make the rss read cache clear every 4 hours
    //add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 14400;' ) );
	
	
    colabs_update_options($options_gateways);
    ?>
	<div class="wrap colabs_container">

        <form action="" method="post" id="colabsform">
        <input name="page" type="hidden" value="gateways" />     
        <div class="clear"></div>
		<div id="colabs-popup-save" class="colabs-save-popup"><div class="colabs-save-save">Options Updated</div></div>
		<div id="colabs-popup-reset" class="colabs-save-popup"><div class="colabs-save-reset">Options Reset</div></div>
		<div style="width:100%;padding-top:15px;"></div>

        <div class="clear"></div>
            <?php $return = colabsthemes_machine($options_gateways); ?>
            <div id="main" class="menu-item-settings metabox-holder">
            	<div id="panel-header">
                    <?php colabsthemes_options_page_header(); ?>
                </div><!-- #panel-header -->

                <div id="sidebar-nav">
                    <ul>
                        <?php echo $return[1]; ?>
                    </ul>
                </div>

                <div id="panel-content">
                	<div class="group help-block"> <p>Drag icon on the left and Drop it here to customize</p> </div>
                	<?php echo $return[0]; ?>
									<div class="clear"></div>
                </div>

                <div id="panel-footer">
                    <ul>
                        <li class="docs"><a title="Theme Documentation" href="<?php echo $manualurl; ?>/documentation/<?php echo strtolower( str_replace( " ","",$themename ) ); ?>" target="_blank" >View Documentation</a></li>
                        <li class="forum"><a href="http://colorlabsproject.com/resolve/" target="_blank">Submit a Support Ticket</a></li>
                        <li class="idea"><a href="http://ideas.colorlabsproject.com/" target="_blank">Suggest a Feature</a></li>
                    </ul>
                    
                    <div class="save-bar save_bar_top right">
                        <img style="display:none" src="<?php echo get_template_directory_uri(); ?>/functions/images/ajax-loading.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." />
                        <input type="submit" value="Save Changes" class="button submit-button button-primary" />
						<input name="submitted" type="hidden" value="yes" />
                    	</form>
                        <form action="" method="post" style="display:inline" id="colabsform-reset">
			            
					    	<input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button button-highlighted" onclick="return confirm( 'Click OK to reset all options. All settings will be lost!' );" />
                        	<input type="hidden" name="colabs_save" value="reset" /> 
			        	</form>
                    </div>
                </div><!-- #panel-footer -->

            </div><!-- #main -->
            <div class="clear"></div>            
    <div style="clear:both;"></div>
    </div><!--wrap-->

<?php
}

function colabs_update_options($options) {
    $toolsMessage = '';
	
    if (isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            foreach ( $options as $value ) {
                if ( isset($_POST[$value['id']]) ) {
                    update_option( $value['id'], colabsthemes_clean($_POST[$value['id']]) );
                } else {
                    @delete_option( $value['id'] );
                }
            }

            // do a separate update for price per cats since it's not in the $options array
            if ( isset($_POST['catarray']) ) {
                foreach ( $_POST['catarray'] as $key => $value ) {
                    update_option( $key, colabsthemes_clean($value) );
                }
            }

            if ( isset($_POST['catreqarray']) ) {
                foreach ( $_POST['catreqarray'] as $key => $value ) {
                    $catreqarray[absint($value)] = '';
                }
                update_option('cl_required_categories', $catreqarray);
            } else if (isset($_POST['cl_required_membership_type'])){
                delete_option('cl_required_categories');
            }

			if ( get_option('cl_tools_run_expiredcheck') == 'yes' ) {
					update_option('cl_tools_run_expiredcheck', 'no');
					//cl_check_expired_cron();
					$toolsMessage = '';
					$toolsMessage .= __('Ads Expired Check was executed.');
			}

			// flush out the cache so changes can be visible
			//cl_flush_all_cache();

            echo '<div class="updated"><p>'.__('Your settings have been saved.','colabsthemes'). ' ' . $toolsMessage . '</p></div>';

    } 
}
if (!function_exists('colabs_load_only_gateway')) {
function colabs_load_only_gateway(){
    
}
}	
?>