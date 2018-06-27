<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;
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
			<h2>How To Use</h2>
		</div>
	</div>
</div>
<!--  -->
<?php
    $doses = isset($meta['take'][0]) ? @unserialize($meta['take'][0]) : '';
    $aD = array();
    if(!empty($doses)){
        foreach($doses as $d_cond){
            if($d_cond['condition'] === $condition->post_name){
                $aD = $d_cond;
            }
        }
        if(!empty($aD)){?>
<div class="container content-block grey">
	<div class="row">
		<div class="content-table">
			<div class="td1">
				<h3>Dose</h3>
			</div>
			<div class="td2">
            <?php if(!empty($aD['starting-dose'])){?>
                <div class="title-body">Starting dose</div>
				<p>					
					<?php echo $aD['starting-dose'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['maximum-dose'])){?>
                <div class="title-body">Maximum dose</div>
				<p>					
					<?php echo $aD['maximum-dose'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['approved-dose'])){?>
                <div class="title-body">Approved doses</div>
				<p>					
					<?php echo $aD['approved-dose'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['rec-dose'])){?>
                <div class="title-body">Recommended dose</div>
				<p>					
					<?php echo $aD['rec-dose'];?>		
				</p>
            <?php }?>           
            <?php if(!empty($aD['titration-instructions'])){?>
                <div class="title-body">Titration instructions</div>
				<p>					
					<?php echo $aD['titration-instructions'];?>		
				</p>
            <?php }?>
             <?php if(!empty($aD['missed-dose'])){?>
                <div class="title-body">What to do if you miss a dose</div>
				<p>					
					<?php echo $aD['missed-dose'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['stopping-instructions'])){?>
                <div class="title-body">How to safely stop the drug</div>
				<p>					
					<?php echo $aD['stopping-instructions'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['special-populations'])){?>
                <div class="title-body">Special circumstances</div>
				<p>					
					<?php echo $aD['special-populations'];?>		
				</p>
            <?php }?>
            <?php if(!empty($aD['other'])){?>
                <div class="title-body">How to store and other issues</div>
				<p>					
					<?php echo $aD['other'];?>		
				</p>
            <?php }?>
			</div>
		</div>
		
	</div>
</div>
     <?php }?>
   <?php }?>

<?php
    $timeResults = isset($meta['time_to_results'][0]) ? @unserialize($meta['time_to_results'][0]) : ''; 
                        $aTTR = array('name'=>'N/A');
                        if(!empty($timeResults)){
                            foreach($timeResults as $ttr_cond){
                                if($ttr_cond['condition'] === $condition->post_name){
                                    $aTTR = $ttr_cond;
                                }
                            }
                        }
?>

<?php if(!empty($aTTR['name'])){?>
<div class="container content-block">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>When you might see results</h3>
			</div>
			<div class="td2">
            <div class="title-body"><?php echo $aTTR['name']?></div>
				<p>
								
				</p>
			</div>
		</div>
		
	</div>
</div>
<?php }?>

<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo $condition->post_name?>/precautions">NEXT: LIFESTYLE CHANGES</a>
	</div>
</div>

<!-- // end  -->
<?php }else{?>
<?php get_template_part("drug-not-allowed"); ?> 
<?php }?>
<?php }else{?>
<br /><br />
<?php }?>