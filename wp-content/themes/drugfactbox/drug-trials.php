<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;
?>
<script>
jQuery(document).ready(function(){
    jQuery('.other_switcher').each(function(){
        var switcher = jQuery(this);
        switcher.find('a').click(function(){
                var othersect = switcher.parent().next().next().find('.other_section');
                if(othersect.css('display') == 'none'){
                    othersect.css('display', 'table');
                    jQuery(this).html('Hide&nbsp;<i class="fa fa-angle-up" aria-hidden="true"></i>');
                }else{
                    othersect.css('display', 'none');
                    jQuery(this).html('Show&nbsp;<i class="fa fa-angle-down" aria-hidden="true"></i>');
                }
            return false;
        });
    });
})
    
    
</script>
<style>
.trial-description{text-align:center}
.bnf table td:nth-child(1) {
    background-color: #004f76;
    color: #fff;
    
}
.bnf table  td:nth-child(2) {
    background: rgba(0, 0, 0, 0) url("<?=get_template_directory_uri()?>/images/bg-a6.png") repeat-y scroll 50% 0;
}
.absDiffText{
    margin-top:8px;
    font-size: 12pt;
}
.other_label{float:left;width:156px;}
.other_switcher{
    width:80px;
    height:30px;
    text-align:center;
    border-radius:30px;
    float:right;
    background-color:#fff;
    position:relative;
    left: 40px; 
    
}

.other_switcher a {color:#000;font-family: Tahoma,sans-serif;font-size:16px;text-decoration:none;position:relative; top:-3px;}
@media (max-width: 1200px) {
    .other_label{float:left;width:115px;}
}
@media (max-width: 992px) {
    .other_label{float:none;}
    .other_switcher{float:none;left:0;margin-top:10px;}
    
}

</style>
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

<?php 
$aBoxes = isset($meta['box'][0]) ? @unserialize($meta['box'][0]) : ''; //dump($aBoxes);
$aTestBoxes = array();
$isBoxTitle = 0;
if(!empty($aBoxes)){ ;
    foreach($aBoxes as $box_cond){
        if($box_cond['condition'] === $condition->post_name){
            $aTestBoxes[] = $box_cond;
            $title = trim(strip_tags($box_cond['infy-id']));
            if(empty($title)){
                $title = trim(strip_tags($box_cond['infy-name']));
            }
            if($title){
                $isBoxTitle++;
            }
        }
    }
}?>


<?php if($allowedDrug){?>
<div class="container text-center">
	<div class="row">	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2 class="testbox-header">Benefits and Side Effects Seen in Drug Approval Trials</h2>
            
            <?php if(!empty($aTestBoxes) and $isBoxTitle > 1){?>
             <!-- start menu-select -->
            <div class="menu-select">
                <div class="ms-table">
                    <div class="ms-td">
                        <h3>Choose comparison</h3>
                    </div>
                    <div class="ms-td">
                        <select id="sectionSelector">
                        <?php foreach($aTestBoxes as $k=>$box){
                            $title = trim(strip_tags($box['infy-id']));
                            if(empty($title)){
                                $title = trim(strip_tags($box['infy-name']));
                            }
                                if(!empty($title)){?>
                            <option value="#box<?php echo $k?>"><?php echo $title?></option>
                                <?php } 
                             }?>
                        </select>
                    </div>
                </div>
            </div>
            <!-- stop menu-select -->
            <?php }?>
            
		</div>
	</div>
</div>
		
<!--  -->
<?php /*
<div class="container content-block">
	<div class="row">
				<?php
$bottomLine = isset($meta['bottom_line'][0]) ? @unserialize($meta['bottom_line'][0]) : '';
$aBL = array(0=>array('name'=>'N/A','description'=>''));
if(!empty($bottomLine)){
    $aBL = array();
    foreach($bottomLine as $bl_cond){
        if($bl_cond['condition'] === $condition->post_name){
            if(!empty($bl_cond['name']) or !empty($bl_cond['description'])){
                $aBL[] = $bl_cond;
            }
        }
    }
}
?>
<?php if(!empty($aBL)){?>
		<div class="content-table">
			<div class="td1">
				<h3>Bottom Line</h3>
			</div>
			<div class="td2">
				<?php foreach($aBL as $bl){?>
				<div class="title-body"><?php echo $bl['name']?></div>
				<p><?php echo $bl['description']?></p>
            <?php }?>
			</div>
		</div>
<?php }?>		
	</div>
</div>
*/?>
<!-- // end  -->

<?php
$aTestNumbers = isset($meta['tested'][0]) ? @unserialize($meta['tested'][0]) : '';
if(!empty($aTestBoxes)){
    foreach($aTestBoxes as $k=>$box){        
                        $aTested = isset($meta['tested'][0]) ? @unserialize($meta['tested'][0]) : '';
                        $aCurrentTested = array();
                        if(!empty($aTested)){
                            foreach($aTested as $tested_cond){
                                if($tested_cond['condition'] == $condition->post_name and $tested_cond['box'] == $box['infy-name']){
                                    $aCurrentTested = $tested_cond;
                                }
                            }
                        }
                    ?>
<!--  Drug for Condition -->
<div class="container-fluid greyfon-1">
	<div class="row">
		<div class="container">
			
			<!-- circle-1 -->
			<div class="circle-1">
				<a href="#" title="" class="resultsOn on" id="resultsOn<?php echo $k?>">Hide<br />Results</a>
			</div>
			<!-- // end circle-1 -->
			
			<div class="row">
	
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="box<?php echo $k?>">
					<h2><?php echo $box['infy-name']?></h2>
					<p class="trial-description">
					   <?php echo $aCurrentTested['description']?>
					</p>
				</div>
				<div class="clearfix"></div>
				<div class="separator-grey-1"></div>
				
				<div class="bg-fff">
				
					<!-- block-1ww -->
                    
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 block-1ww">
					
						<h2>Who was in the trial?</h2>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 icon-a4a">
							<img src="<?=get_template_directory_uri()?>/images/a4a.png" alt="" />
						</div>
						
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
						
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
							<div class="lb1-block">
								<h3>Patients</h3>
                                
								<p><?php echo $aCurrentTested['number']?> <br /><?php echo $aCurrentTested['sex']?></p>
							</div>
							<div class="lb1-block">
								<h3>Age</h3>
								<p><?php echo $aCurrentTested['age-range']?> <?php if(!empty($aCurrentTested['age-average'])){?><br />average <?php echo $aCurrentTested['age-average']?><?php }?></p>
							</div>
						</div>
						
                        <?php
                        $aTestConditions = isset($meta['test_cond'][0]) ? @unserialize($meta['test_cond'][0]) : '';
                        if(!empty($aTestConditions)){
                            $aCurrentTestConditions = array();
                            foreach($aTestConditions as $tc_cond){
                                if($tc_cond['condition'] == $condition->post_name and $tc_cond['box'] == $box['infy-name']){
                                     $aCurrentTestConditions[] = $tc_cond;
                                }
                            }
                        }
                        if(!empty($aCurrentTestConditions)){
                        ?>
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
							<div class="lb1-block">
								<h3>Conditions</h3>
                                <?php foreach($aCurrentTestConditions as $ctc){?>
                                    <p><?php echo $ctc['condition-name']?></p>
                                    <div class="text-lb1"><?php echo $ctc['explanation']?></div>
                                    <?php if(!empty($ctc['severity'])){?>
                                    <div class="text-lb1">Severity: <?php echo $ctc['severity']?></div>
                                    <?php }?>
                                <?php }?>
							</div>
						</div>
                        <?php }?>
						
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
						
						<div class="clearfix"></div>
					
					</div>
					<!-- // end block-1ww -->

					<!-- block-1ww -->
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 block-1ww">
                    
                    <?php 
                    $aTestGroups = isset($meta['test_groups'][0]) ? @unserialize($meta['test_groups'][0]) : '';
                    if(!empty($aTestGroups)){
                        $aCurrentTestgroups = array();
                        foreach($aTestGroups as $tg_cond){
                            if($tg_cond['condition'] == $condition->post_name and $tg_cond['box'] == $box['infy-name']){
                                $aCurrentTestgroups[] = $tg_cond;
                            }
                        }
                    }
                    if(!empty( $aCurrentTestgroups)){
                    ?>
					<?php if(count($aCurrentTestgroups) > 1){?>
                    
						<h2>Randomized Trial</h2>
						
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
						
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
						
						
							<div class="block-2ww">					
							
								<div class="icon-a51a">
									<img src="<?=get_template_directory_uri()?>/images/a51a.png" alt="" />
								</div>
								
								<table>
									<tr>
										<td>	
										<div class="block-1ww-bb">
											<h3><?php echo $aCurrentTestgroups[0]['name']?>
                                            <?php 
                                            if(!empty($aCurrentTestgroups[0]['background'])){
                                                echo '<br />+ '.$aCurrentTestgroups[0]['background'];
                                            }?></h3>
										</div>
										</td>
										<td></td>
										<td>	
										<div class="block-1ww-bb">
											<h3><?php echo $aCurrentTestgroups[1]['name']?>
                                            <?php 
                                            if(!empty($aCurrentTestgroups[1]['background'])){
                                                echo '<br />+ '.$aCurrentTestgroups[1]['background'];
                                            }
                                            ?></h3>
										</div>
										</td>
									</tr>
								</table>
									
								<div class="icon-a52a">
									<img src="<?=get_template_directory_uri()?>/images/a52a.png" alt="" />
									<span><?php echo @$aCurrentTested['study-length']?></span>
								</div>
							
							</div>
							
						</div>
						
                    <?php }else{?>
                    
                        <h2>Uncontrolled Trial</h2>
						
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
						
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
						
						
							<div class="block-2ww one-element">					
							
								<div class="icon-a51a">
									<img src="<?=get_template_directory_uri()?>/images/a61a.png" alt="" />
								</div>
								
								<table>
									<tr>
										<td>	
										<div class="block-1ww-bb">
											<h3><?php echo $aCurrentTestgroups[0]['name']?></h3>
										</div>
										</td>
									</tr>
								</table>
									
								<div class="icon-a52a">
									<img src="<?=get_template_directory_uri()?>/images/a62a.png" alt="" />
									<span><?php echo @$aCurrentTested['study-length']?></span>
								</div>
							
							</div>
							
						</div>
                        
                    <?php }?>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                    <div class="clearfix"></div>
                    <div class="whant-happened"><h3>What Happened?</h3></div>
					<?php }?>
					</div>
					<!-- // end block-1ww -->
				
				</div>
				
			</div>
		</div>
	</div>
</div>
<!-- // end Drug for Condition -->

<!-- Benefits -->
<div class="container benefits resultsOn<?=$k?>" style="display:block">
	<div class="row">

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Benefits</h2>
		</div>
		<?php
        $aBenefits = isset($meta['benefits'][0]) ? @unserialize($meta['benefits'][0]) : '';
        if(!empty($aBenefits)){
            $aCurrentBenefits = array();
            foreach($aBenefits as $bnf_cond){
                if($bnf_cond['condition'] == $condition->post_name and $bnf_cond['box'] == $box['infy-name']){ 
                    $aCurrentBenefits[] = $bnf_cond;
                }
            }
        }
        ?>
        <?php if(!empty($aCurrentBenefits)){?>
		<table>
			<tr>
				<th class="w267">&nbsp;</th>
				<th class="w72"><img src="<?=get_template_directory_uri()?>/images/pic-72.png" alt="" /></th>
				<th class="descriptionCell"></th>
				<th>
					<h4><?php echo $aCurrentTestgroups[0]['name']?></h4>
					<p><?php echo $aCurrentTestgroups[0]['dosing']?></p>
				</th>
                <?php if(isset($aCurrentTestgroups[1]['name'])){?>
				<th>
					<h4><?php echo $aCurrentTestgroups[1]['name']?></h4>
					<p><?php echo $aCurrentTestgroups[1]['dosing']?></p>
				</th>
				<th style="width:230px">
					<h4><?php echo $aCurrentTestgroups[0]['name']?> vs. <?php echo $aCurrentTestgroups[1]['name']?></h4>
				</th>
                <?php }?>
			</tr>
            <?php foreach($aCurrentBenefits as $bnf){
                if(!empty($bnf['name'])){
                ?>
			<tr>
				<td class="w267">
					<?php echo $bnf['name']?>
				</td>
                <td colspan="5">
                <?php
                $aBenefitsDetails = isset($meta['benefits_details'][0]) ? @unserialize($meta['benefits_details'][0]) : ''; 
                if(!empty($aBenefitsDetails)){
                    $aCurrentBenefitsDetails = array();
                    foreach($aBenefitsDetails as $bnfdet_cond){
                        if($bnfdet_cond['condition'] === $condition->post_name and $bnfdet_cond['box'] === $box['infy-name'] and $bnfdet_cond['benefit'] === $bnf['name']){ 
                            $aCurrentBenefitsDetails[] = $bnfdet_cond;
                        }
                    }
                }
                if(!empty($aCurrentBenefitsDetails)){
                ?>
                <!-- table-2se -->
					<table class="table-2se">
                    <?php foreach($aCurrentBenefitsDetails as $bnf_det){?>
                    
						<tr>
						    <td class="icon-clock">
                            <?php if(!empty($bnf_det['scale'])){?>
								<img src="<?=get_template_directory_uri()?>/images/icon-<?php echo $bnf_det['scale']?>.png" alt="" />
                            <?php }?>
							</td>
							<td class="descriptionCell">
								<div class="title"><?php echo $bnf_det['description']?></div>
								
							</td>
							<td>
								<?php echo $bnf_det['group0']?>
							</td>
                            <?php if(isset($aCurrentTestgroups[1]['name'])){?>
							<td>
								<?php echo @$bnf_det['group1']?>
							</td>
							<td  style="width:230px">
								<?php echo $bnf_det['comparison']?>
                                <?php if(!empty($bnf_det['abs-difference'])){?>
                                <div class="absDiffText"><?php echo $bnf_det['abs-difference']?></div>
                                <?php }?>
							</td>
                            <?php }?>
						</tr>
					<?php }?>	
						
					</table>
					<!-- // end table-2se -->
                <?php }?>
                </td>
				
			</tr>
            <?php } }?>			
		</table>
        <?php }?>
        <?php
                        $benefitsSource = isset($meta['benefits_source'][0]) ? @unserialize($meta['benefits_source'][0]) : '';
                        $aBS = array('name'=>'N/A');
                        if(!empty($benefitsSource)){
                            foreach($benefitsSource as $bs_cond){
                                if($bs_cond['condition'] === $condition->post_name and $bs_cond['box'] === $box['infy-name']){
                                    $aBS = $bs_cond;
                                }
                            }
                        }
        ?>
        <?php if(!empty($aBS)){?>
		<div class="sourse">
			<p><?php echo $aBS['name']?></p>
		</div>
		<?php }?>
	</div>
</div>
<!-- // end Benefits -->



<!-- Slide Effects -->
<div class="container slide-effects resultsOn<?=$k?>" style="display:block">
	<div class="row">
        <?php
        $aSideEffects = isset($meta['box_side_effects'][0]) ? @unserialize($meta['box_side_effects'][0]) : '';
        if(!empty($aSideEffects)){
            $aCurrentSideEffects = array();
            foreach($aSideEffects as $se_cond){
                if($se_cond['condition'] === $condition->post_name and $se_cond['box'] === $box['infy-name']){
                    if($se_cond['priority'] === 'black box warning'){
                        $aCurrentSideEffects['bbw'][] = $se_cond;
                    }elseif($se_cond['priority'] === 'serious' or $se_cond['priority'] === 'serious_uncommon'){
                        $aCurrentSideEffects['serious'][] = $se_cond;
                    }elseif($se_cond['priority'] === 'fda_symptom'){
                        $aCurrentSideEffects['symptoms'][] = $se_cond;
                    }elseif($se_cond['priority'] === 'lab_abnormalities' or $se_cond['priority'] === 'other_symptom'){
                        $aCurrentSideEffects['other'][] = $se_cond;
                    }
                }
            }
        }
        ?>
        <?php if(!empty($aCurrentSideEffects)){?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Side Effects</h2>
		</div>
		
		<!-- table-1se -->
		<table class="table-1se">
		
			<tr>
				<th></th>
				<th></th>
				<th class="descriptionCell"></th>
				<th>
					<h4><?php echo $aCurrentTestgroups[0]['name']?></h4>
					<p><?php echo $aCurrentTestgroups[0]['dosing']?></p>
				</th>
                 <?php if(isset($aCurrentTestgroups[1]['name'])){?>
				<th>
					<h4><?php echo $aCurrentTestgroups[1]['name']?></h4>
					<p><?php echo $aCurrentTestgroups[1]['dosing']?></p>
				</th>
				<th style="width:230px">
					<h4><?php echo $aCurrentTestgroups[0]['name']?> vs. <?php echo $aCurrentTestgroups[1]['name']?></h4>
				</th>
                 <?php }?>
			</tr>
			<?php if(!empty($aCurrentSideEffects['bbw'])){?>
			<tr class="red-ab2627">
				<td>
					Black Box Warning - FDA’s most serious alert
				</td>
				<td>
					<img src="<?=get_template_directory_uri()?>/images/icon-s1.png" alt="" />
				</td>
				<td colspan="4">
					<!-- table-2se -->
					<table class="table-2se">
                    <?php foreach($aCurrentSideEffects['bbw'] as $se){?>
						<tr>
						
							<td class="descriptionCell">
								<div class="title"><?php echo $se['name']?></div>
								<?/*<p>
									<?php echo $se['medical-name']?>
								</p>*/?>
							</td>
							<td>
								<?php echo $se['group0']?>
							</td>
                            <?php if(isset($aCurrentTestgroups[1]['name'])){?>
							<td>
								<?php echo @$se['group1']?>
							</td>
							<td style="width:230px">
								<?php echo $se['comparison']?>
							</td>
                            <?php }?>
						</tr>
                    <?php }?>						
					</table>
					<!-- // end table-2se -->
				</td>
			</tr>
			<?php }?>
            
            <?php if(!empty($aCurrentSideEffects['serious'])){?>
			<tr class="red-cf4747">
				<td>
					Serious
				</td>
				<td>
					<img src="<?=get_template_directory_uri()?>/images/icon-s2.png" />
				</td>
				<td colspan="4">
					<!-- table-2se -->
					<table class="table-2se">
					<?php foreach($aCurrentSideEffects['serious'] as $se){?>
						<tr>
						
							<td>
								<div class="title"><?php echo $se['name']?></div>
								<?/*<p>
									<?php echo $se['medical-name']?>
								</p>*/?>
							</td>
							<td>
								<?php echo $se['group0']?>
							</td>
                            <?php if(isset($aCurrentTestgroups[1]['name'])){?>
							<td>
								<?php echo @$se['group1']?>
							</td>
							<td style="width:230px">
								<?php echo $se['comparison']?>
							</td>
                            <?php }?>
						</tr>
					<?php }?>						
						
					</table>
					<!-- // end table-2se -->
				</td>
			</tr>
            <?php }?>
			
            <?php if(!empty($aCurrentSideEffects['symptoms'])){?>
			<tr class="yellow-edda36">
				<td>
					Most common symptom side effects highlighted by FDA
				</td>
				<td>
					<img src="<?=get_template_directory_uri()?>/images/icon-s3.png" />
				</td>
				<td colspan="4">
					<!-- table-2se -->
					<table class="table-2se">
					<?php foreach($aCurrentSideEffects['symptoms'] as $se){?>
						<tr>
							<td>
								<div class="title"><?php echo $se['name']?></div>
								<?/*<p>
									<?php echo $se['medical-name']?>
								</p>*/?>
							</td>
							<td>
								<?php echo $se['group0']?>
							</td>
                            <?php if(isset($aCurrentTestgroups[1]['name'])){?>
							<td>
								<?php echo @$se['group1']?>
							</td>
							<td style="width:230px">
								<?php echo $se['comparison']?>
							</td>
                            <?php }?>
						</tr>
					<?php }?>						
					</table>
					<!-- // end table-2se -->
				</td>
			</tr>
			<?php }?>
            <?php if(!empty($aCurrentSideEffects['other'])){?>
            <tr class="yellow-f3e96e">
                <td>
					<div class="other_label">Other possible side effects</div>
                    <div class="other_switcher"><a href="#">Show&nbsp;<i class="fa fa-angle-down" aria-hidden="true"></i></a></div>
				</td>
                <td></td>
                <td colspan="4">
                    <!-- table-2se -->
					<table class="table-2se other_section" style="display:none">
					<?php foreach($aCurrentSideEffects['other'] as $se){?>
						<tr>
							<td>
								<div class="title"><?php echo $se['name']?></div>
								<?/*<p>
									<?php echo $se['medical-name']?>
								</p>*/?>
							</td>
							<td>
								<?php echo $se['group0']?>
							</td>
                            <?php if(isset($aCurrentTestgroups[1]['name'])){?>
							<td>
								<?php echo @$se['group1']?>
							</td>
							<td style="width:230px">
								<?php echo $se['comparison']?>
							</td>
                            <?php }?>
						</tr>
					<?php }?>						
					</table>
					<!-- // end table-2se -->
                </td>
            </tr>
            <?php }?>
		</table>
        <?php }?>
		<!-- // end table-1se -->
        <?php
                        $seSource = isset($meta['sideeffects_source'][0]) ? @unserialize($meta['sideeffects_source'][0]) : '';
                        $aSES = array('name'=>'N/A');
                        if(!empty($seSource)){
                            foreach($seSource as $ses_cond){
                                if($ses_cond['condition'] === $condition->post_name and $ses_cond['box'] === $box['infy-name']){
                                    $aSES = $ses_cond;
                                }
                            }
                        }
        ?>
         <?php if(!empty($aSES)){?>
		<div class="sourse">
			<p><?php echo $aSES['name']?></p>
		</div>
		<?php }?>

	</div>
</div>
    <?php }?>
<?php }?>
<!-- // end Slide Effects -->

<!--  -->
<?php
$trialDescription = isset($meta['trial_description'][0]) ? @unserialize($meta['trial_description'][0]) : '';
$aTD = array();
if(!empty($trialDescription)){
    foreach($trialDescription as $td_cond){
        if($td_cond['condition'] === $condition->post_name and (!empty($td_cond['name']) or !empty($td_cond['description']))){
            $aTD = $td_cond;
        }
    }
}
?>
<?php if(!empty($aTD)){?>
<div class="container content-block grey">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Story of FDA approval</h3>
			</div>
			<div class="td2">
                <div class="title-body"><?php echo $aTD['name'];?></div>
				<p>
					<?php echo $aTD['description']?>
				</p>
			</div>
		</div>
		
	</div>
</div>
<?php }?>
<!-- // end  -->

<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo $condition->post_name?>/side-effects">NEXT: SIDE EFFECTS</a>
	</div>
</div>

<?php }else{?>
<?php get_template_part("drug-not-allowed"); ?> 
<?php }?>
<?php }else{?>
<br /><br />
<?php }?>