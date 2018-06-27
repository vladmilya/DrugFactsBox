<?php
get_header(); 
?>
<?php
$user = get_user_by( 'slug', $wp->query_vars['usr'] );
?>
<script>
    <?php /*jQuery(window).load(function(){
        //parent.location = '<?php echo esc_url( home_url( '/' ) )?>all-drugs/';
        parent.location = '<?php echo !empty($_SERVER['HTTP_REFERER']) ? $referrer = str_replace('&tab=enterprise', '',str_replace('?login=failed', '', $_SERVER['HTTP_REFERER'])) :esc_url( home_url( '/' ) )?>';
    });*/?>
    jQuery(window).load(function(){
        <?php if(@$wp->query_vars['level'] == 'unc'){?>
        parent.location = '<?php echo esc_url( home_url( '/all-drugs/' ))?>';
        <?php }else{?>
        parent.location = '<?php echo !empty($_SERVER['HTTP_REFERER']) ? $referrer = str_replace('&tab=enterprise', '',str_replace('?login=failed', '', $_SERVER['HTTP_REFERER'])) :esc_url( home_url( '/' ) )?>';
        <?php }?>
    });
</script>
<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1>Welcome <?php echo $user->data->display_name ? $user->data->display_name : $wp->query_vars['usr']?></h1>	
		</div>
	</div>
</div>
<!-- // end title-h1 -->

<div class="page-content">	
	<hr />
    <div class="container">
<?php the_content()?>

    </div>
</div>
<?php
get_footer();
?>

<?php 
//header('Location: '.esc_url( home_url( '/' ) ))
?>