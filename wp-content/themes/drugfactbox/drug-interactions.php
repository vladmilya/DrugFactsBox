<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;

$interactions = isset($meta['drug_interactions'][0]) ? @unserialize($meta['drug_interactions'][0]) : '';
$aInteractions = array();
if(!empty($interactions)){
    foreach($interactions as $ia){
        if($ia['type'] === 'interaction'){
            $aInteractions['interaction'][] = $ia;
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
			<h2>Interactions</h2>
		</div>
	</div>
</div>
<!--  -->

<div class="container content-block grey">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Drug Interactions</h3>
			</div>
			<div class="td2">
			<?php if($aInteractions['interaction']){?>
                <?php foreach($aInteractions['interaction'] as $ia){?>
                <div class="title-body"><?php echo $ia['name'];?></div>
				<p>					
				    <?php echo $ia['description'];?>
				</p>                
                <?php }?>
            <?php }else{?>
                <div class="title-body">Talk to your doctor about any possible interactions</div>
            <?php }?>
			</div>
		</div>
		
	</div>
</div>

<!-- // end  -->
<?php }else{?>
<?php get_template_part("drug-not-allowed"); ?> 
<?php }?>
<?php }else{?>
<br /><br />
<?php }?>