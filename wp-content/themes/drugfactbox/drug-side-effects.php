<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $sideeffects, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;
$aSE = array();
if(!empty($sideeffects)){ //dump($sideeffects);
    foreach($sideeffects as $se){
        if($se['type'] === 'bbw' and (!empty($se['name']) or !empty($se['description']))){
            $aSE['bbw'][] = $se;
        }
        if($se['type'] === 'call_doctor' and (!empty($se['name']) or !empty($se['description']))){
            $aSE['call_doctor'][] = $se;
        }
        if($se['type'] === 'tell_doctor' and (!empty($se['name']) or !empty($se['description']))){
            $aSE['tell_doctor'][] = $se;
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
			<h2>Side Effects</h2>
		</div>
	</div>
</div>
<!--  -->

<div class="container content-block grey">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Black Box Warning</h3>
			</div>
			<div class="td2">
            <?php if(@$aSE['bbw']){?>
                <?php foreach($aSE['bbw'] as $se){?>
                <div class="title-body"><?php echo $se['name'];?></div>
				<p>					
				    <?php echo $se['description'];?>
				</p>
                <?php }?>
            <?php }else{?>
                <div class="title-body">None</div>
            <?php }?>
			</div>
		</div>
		
	</div>
</div>
<?php if(!empty($aSE['call_doctor'])){?>
<div class="container content-block">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Call Doctor Right Away</h3>
			</div>
			<div class="td2">
                <?php foreach($aSE['call_doctor'] as $se){?>
                <div class="title-body"><?php echo $se['name'];?></div>
				<p>					
				    <?php echo $se['description'];?>
				</p>
                <?php }?>            
			</div>
		</div>
		
	</div>
</div>
<?php }?>
<?php if(!empty($aSE['tell_doctor'])){?>
<div class="container content-block grey">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Tell Your Doctor</h3>
			</div>
			<div class="td2">
			
                <?php foreach($aSE['tell_doctor'] as $se){?>
                <div class="title-body"><?php echo $se['name'];?></div>
				<p>					
				    <?php echo $se['description'];?>
				</p>
                <?php }?>            
			</div>
		</div>
		
	</div>
</div>
<?php }?>

<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo $condition->post_name?>/how-to-use">NEXT: HOW TO USE</a>
	</div>
</div>

<!-- // end  -->
<?php }else{?>
<?php get_template_part("drug-not-allowed"); ?> 
<?php }?>
<?php }else{?>
<br /><br />
<?php }?>