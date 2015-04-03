<?php
/**
 * Search form template
 */
?>

<form role="search" method="get" id="searchform" action="http://localhost/colorlabs/wp35/goodliving/" style="background-color:#f7f7f7">
	<div>
		<label class="screen-reader-text" for="s">Искать:</label>
		<input type="text" value="" name="s" id="s" placeholder="<?php _e('Поиск', 'colabsthemes'); ?>">
		<?php if( is_404() ) : ?>
			<input class="button" type="submit" value="<?php esc_html_e( 'Поиск', 'colabsthemes' ); ?>">
		<?php endif; ?>
	</div>
</form>