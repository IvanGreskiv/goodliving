<?php
/**
 * WordPress Registration Form
 * Function outputs the registration form
 *
 *
 * @author ColorLabs
 *
 */

function colabs_register_form( $action = '' ) {	
    global $posted;

    if ( get_option('users_can_register') ) :

        if (!$action) $action = site_url('wp-login.php?action=register');?>
				
          <form action="<?php echo $action; ?>" method="post" class="loginform" name="registerform" id="registerform">

            <p>
                <label><?php _e('Username','colabsthemes') ?>&nbsp;:</label>
                <input tabindex="1" type="text" class="text" name="your_username" id="your_username" value="<?php if (isset($posted['your_username'])) esc_attr_e($posted['your_username']); ?>" />
            </p>

            <p>
                <label><?php _e('Email','colabsthemes') ?>&nbsp;:</label>
                <input tabindex="2" type="text" class="text" name="your_email" id="your_email" value="<?php if (isset($posted['your_email'])) esc_attr_e($posted['your_email']); ?>" />
            </p>

						<?php if (get_option('colabs_allow_registration_password') == 'true') : ?>
							<p>
								<label><?php _e('Password','colabsthemes') ?>&nbsp;:</label>
								<input tabindex="3" type="password" class="text" name="your_password" id="your_password" value="" />
							</p>

							<p>
								<label><?php _e('Password Again','colabsthemes') ?>&nbsp;:</label>
								<input tabindex="4" type="password" class="text" name="your_password_2" id="your_password_2" value="" />
							</p>
						<?php endif; ?>
				
            <?php 
						// include the spam checker if enabled
						colabsthemes_recaptcha();
						?>

            <div id="checksave">

              <p class="submit">
                <input tabindex="6" class="button button-bold" type="submit" name="register" id="wp-submit" value="<?php _e('Create Account','colabsthemes'); ?>" />
              </p>

            </div>

          </form>
	
        <script type="text/javascript">document.getElementById('your_username').focus();</script> 	

		<?php else : ?>

			<p><?php _e('** User registration is currently disabled. Please contact the site administrator. **', 'colabsthemes') ?></p>

		<?php endif; ?>

<?php } ?>