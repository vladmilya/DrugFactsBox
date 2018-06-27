<?php
get_header(); 
?>

<?php
$notfound_post = get_page_by_path('not-found',OBJECT,'post');
?>
<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php echo @$notfound_post->post_title?></h1>	
		</div>
	</div>
</div>
<!-- // end title-h1 -->
 
<div class="page-content">	
	<hr />
    <div class="container">
<?php echo @$notfound_post->post_content?>
    </div>
</div>
	
<?php
get_footer();
?>