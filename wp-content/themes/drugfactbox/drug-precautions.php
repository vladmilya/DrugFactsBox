<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;

$precautions = isset($meta['precautions'][0]) ? @unserialize($meta['precautions'][0]) : '';
$aPrecautions = array();
if(!empty($precautions)){
    foreach($precautions as $pc){
        if($pc['type'] === 'testing'){
            $aPrecautions['testing'][] = $pc;
        }
        if($pc['type'] === 'to_avoid'){
            $aPrecautions['to_avoid'][] = $pc;
        }
    }
}
?>

<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php the_title()?></h1>	
		</div>
		
		<div class="clearfix"></div>
        <?php if(!empty($condition)){?>
		<ul>
			<li><?php the_title()?> for <?=$condition->post_title?></li>
            <?if(!empty($aConditions) and count($aConditions) > 1){?>
			<li><a href="#" title="" data-toggle="modal" data-target="#conditionModal">Change »</a></li>
            <?php }?>
		</ul>
		<?php }?>	
	</div>
</div>
<!-- // end title-h1 -->

<!-- in1 -->
<?php get_template_part("drug-menu"); ?> 
<!-- // end in1 -->

<?php if ( is_user_logged_in()) {?>
<?php if($allowedDrug){?>
<div class="container text-center">
	<div class="row">	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Lifestyle Changes</h2>
		</div>
	</div>
</div>
<!--  -->

<div class="container content-block grey">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Testing your doctor may request</h3>
			</div>
			<div class="td2">
			<?php if($aPrecautions['testing']){?>
                <?php foreach($aPrecautions['testing'] as $pc){?>
                <div class="title-body"><?php echo $pc['name'];?></div>
				<p>					
				    <?php echo $pc['description'];?>
				</p>
                <?php }?>
            <?php }else{?>
                <div class="title-body">None</div>
            <?php }?>
			</div>
		</div>
		
	</div>
</div>

<div class="container content-block">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>What To Avoid</h3>
			</div>
			<div class="td2">
			<?php if(@$aPrecautions['to_avoid']){?>
                <?php foreach($aPrecautions['to_avoid'] as $pc){?>
                <div class="title-body"><?php echo $pc['name'];?></div>
				<p>					
				    <?php echo $pc['description'];?>
				</p>
                <?php }?>
            <?php }else{?>
                <div class="title-body">None</div>
            <?php }?>
			</div>
		</div>
		
	</div>
</div>

<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo $condition->post_name?>/interactions">NEXT: INTERACTIONS</a>
	</div>
</div>

<!-- // end  -->
<?php }else{?>
<?php get_template_part("drug-not-allowed"); ?> 
<?php }?>
<?php }else{?>
<br /><br />
<?php }?>