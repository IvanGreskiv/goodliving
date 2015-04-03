	</div>
</div>
    <!-- .main-content-wrapper -->

<footer class="footer-section container">
  <div class="row">
  <div>
  <h3 class="hed3">Последние новости:</h3>
  

<div class="post-list post-grid post-blog loading">
  <?php query_posts('cat=14');?>
  <?php while ( have_posts() ) : the_post(); ?>
  <?php get_template_part('content','post');?>
  <?php endwhile;?>    
</div><!-- .post-list -->

<div class="post-loader">
  <a href="#" class="button button-grey"><?php _e('Загрузить больше', 'colabsthemes'); ?></a>
</div>



  </div>
		<?php if('true'==get_option('colabs_subscribe_form')):?>
    <div class="newsletter-subscribe">
          <h4><?php echo get_option('colabs_subscribe_title');?></h4>
          <form action="<?php echo get_option('colabs_subscribe_action');?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" novalidate>
            <input type="email" name="EMAIL" id="mce-EMAIL" placeholder="Sign up newsletter" required>
            <input class="button button-bold" name="subscribe" type="submit" value="<?php echo get_option('colabs_subscribe_button');?>">
          </form>
          <p><?php echo get_option('colabs_subscribe_desc');?></p>
    </div><!-- .newsletter-subscribe -->
    <?php endif;?>    
    <?php get_sidebar( 'footer' );?><!-- .footer-widgets -->
  </div>
</footer>

<div class="copyrights container">
  <div class="row">
    <?php colabs_credit();?>
    <img src="vk.png" style="width:50px; height:50px;" align="right">
  </div>
</div>
<?php wp_footer(); ?>
</body>

</html>