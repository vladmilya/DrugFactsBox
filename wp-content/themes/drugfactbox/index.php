<?php
get_header(); 
?>

<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php the_title()?></h1>	
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