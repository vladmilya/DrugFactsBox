<!-- footer -->
<?php
global $loggedUser;
?>

	<div class="container footer">
		 
        <div class="row">
       
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php if ( is_active_sidebar( 'footer' ) ) : ?>
				<?php dynamic_sidebar( 'footer' ); ?>
            <?php endif ?>
            
            <?php if(!is_user_logged_in() or is_unc()){?>
                <?php wp_nav_menu( array( 'theme_location' => 'Footer', 'menu' => 'Footer Menu') ); ?>
            <?php }?>    
            
			</div>
        
		</div>
        
	</div>
    
	<!-- // end footer -->
<?php wp_footer(); ?>
</body>
</html>