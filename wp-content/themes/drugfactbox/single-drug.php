<?php 
get_header();
?>

<?php 
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $sideeffects, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;
$page_tab = isset($wp->query_vars['page']) ? $wp->query_vars['page'] : '';

$meta = get_post_meta($post->ID); //dump($meta);

$drugImages = array();
if(!empty($meta['images'][0])){
    $aImages = unserialize($meta['images'][0]);
    if(!empty($aImages)){
        foreach($aImages as $img){
            $image = wp_get_attachment_image_src( $img['image'], 'full');
            $drugImages[] = $image;
        }
    }
}


if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
    if(is_unc()){
        $uncUser = get_user_by('login', 'unc');
        $userMeta = get_user_meta( $uncUser->data->ID );
    }else{
        $userMeta = get_user_meta( $loggedUser->ID );
    }
    
    $aOutputDrugs = array();
    
    //drugs by conditions
    if(isset($userMeta['allowed_conditions'])){
        $allowedConditions = $userMeta['allowed_conditions'][0];
        $aAllowedConditions = explode('::', $allowedConditions);
        if(is_array($aAllowedConditions)) foreach($aAllowedConditions as $conditionID){
            //Prescripted drugs        
            $args = array(  
                'showposts'=>-1,
                'post_type' => 'drug',                    
                'orderby' => 'published',
                'order'   => 'DESC', 
                'meta_query' => array(
                    'relation'=>'OR',
                        array(
                            'key' => 'conditions',
                            'value' => sprintf(':"%s";', intval($conditionID)),
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key' => 'conditions',
                            'value' => sprintf(':%s;', intval($conditionID)),
                            'compare' => 'LIKE'
                        )
                )
            ); 
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();           
                    $aOutputDrugs[] = $query->post->ID;;
                }
            }
            wp_reset_query();
        }
    }
    
    //direct drugs
    if(isset($userMeta['allowed_drugs'])){
        $allowedDrugs = $userMeta['allowed_drugs'][0];
        $aAllowedDrugs = explode('::', $allowedDrugs);
        if(is_array($aAllowedDrugs))foreach($aAllowedDrugs as $drugID){
            $drug_result = $wpdb->get_row("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE ID = '".intval($drugID)."'"); 
            $meta_drug = get_post_meta($drug_result->ID);
            $conditions = isset($meta_drug['conditions'][0]) ? @unserialize($meta_drug['conditions'][0]) : '';
            foreach($conditions as $conditionID){
                $condition_result = $wpdb->get_row("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE ID = '".intval($conditionID)."'"); 
                $aOutputDrugs[] = $drug_result->ID;
            }
        }    
    }
    
    $accessibleDrugs = array_values(array_unique($aOutputDrugs, SORT_REGULAR));
    if(is_unc()){
        if(!in_array($post->ID, $accessibleDrugs)){
            header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
        }
    }else{
        if(!empty($accessibleDrugs)){
            if(!in_array($post->ID, $accessibleDrugs)){
                header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
            }
        }
    }

    //allowed conditions
    $aGrantedConditions = array();
    if(is_unc()){
        $uncUser = get_user_by('login', 'unc');
        $userMeta = get_user_meta( $uncUser->data->ID );
    }else{
        $userMeta = get_user_meta( $loggedUser->ID );
    }
    
    $aOutputDrugs = array();
    
    //direct conditions
    if(isset($userMeta['allowed_conditions'])){
        $allowedConditions = $userMeta['allowed_conditions'][0];
        $aGrantedConditions = explode('::', $allowedConditions);
    }
     
    //conditions by drugs
    if(isset($userMeta['allowed_drugs'])){
        $allowedDrugs = $userMeta['allowed_drugs'][0];
        $aAllowedDrugs = explode('::', $allowedDrugs);
        if(is_array($aAllowedDrugs)) foreach($aAllowedDrugs as $drugID){
            $drug_result = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE ID = '".intval($drugID)."'"); 
            $meta_drug = get_post_meta($drug_result->ID);
            $conditions = isset($meta_drug['conditions'][0]) ? @unserialize($meta_drug['conditions'][0]) : '';            
            foreach($conditions as $conditionID){                  
                $aGrantedConditions[] = $conditionID;
            }
        }          
    }
    $aGrantedConditions = array_values(array_unique($aGrantedConditions));
}

$conditions = isset($meta['conditions'][0]) ? @unserialize($meta['conditions'][0]) : '';
$aConditions = array();
if(!empty($conditions)){
    foreach($conditions as $cond){
        $condPost = get_post($cond);
        $aCond['id'] = $condPost->ID;   
        $aCond['slug'] = $condPost->post_name;
        $aCond['name'] = $condPost->post_title;
        $aCond['link'] = get_permalink($condPost->ID);
        if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
            if($loggedUser->data->user_login === 'unc'){
                if(!in_array($condPost->ID, $aGrantedConditions)){
                    continue;
                }
            }else{
                if(!empty($aGrantedConditions)){
                    if(!in_array($condPost->ID, $aGrantedConditions)){
                        continue;
                    }
                }
            }
        }   
        $aConditions[] = $aCond;
    }    
}  

$sideeffects = isset($meta['drug_side_effects'][0]) ? @unserialize($meta['drug_side_effects'][0]) : ''; 
$bbw = 0;    
if(!empty($sideeffects)){
    foreach($sideeffects as $se){
        if($se['type'] === 'bbw' and (!empty($se['name']) or !empty($se['description']))){
            $bbw++;
        }
    }
}

$otherdrugs = isset($meta['other_drug'][0]) ? @unserialize($meta['other_drug'][0]) : '';      

if(!empty($wp->query_vars['cond'])){
    $condition = get_page_by_path($wp->query_vars['cond'],OBJECT,'condition');
    if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
        if($loggedUser->data->user_login === 'unc'){
            if(!in_array($condition->ID, $aGrantedConditions)){
                header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
            }
        }else{
            if(!empty($aGrantedConditions)){
                if(!in_array($condition->ID, $aGrantedConditions)){
                    header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
                }
            }
        }
    }
    //$condition = get_post(intval($_GET['cond']));;
    
    if(isset($condition->ID)){
        $condition_meta = get_post_meta(intval($condition->ID));
    }else{
        wp_redirect( home_url( '404' ), 302 );
        exit();
    }
}else{
    $condition = '';
    $condition_meta = '';
}
global $current_user;
if(is_user_logged_in()){
    if(current_user_is("s2member_level0")){
        $allowedDrug = false;       
        
        $metaCustomFields = get_user_meta($current_user->ID, 'wp_s2member_custom_fields');
        $selectedDrugs = isset($metaCustomFields[0]['selected_drugs']) ? $metaCustomFields[0]['selected_drugs'] : '';
        $aSelectedDrugs = explode('||', $selectedDrugs); 
        if(!empty($aSelectedDrugs)){
            $aDI = array();
            foreach($aSelectedDrugs as $sd){
                $aDrugInfo = explode('::', $sd);                
                if(!empty($sd)){
                    $aDI[] = $aDrugInfo[0];
                }
            }
            if(in_array($post->ID, $aDI)){
                $allowedDrug = true;
            }
        }        
    }elseif(current_user_is("s2member_level1") or current_user_is("s2member_level2") or $current_user->roles[0]=='administrator'){
        $allowedDrug = true;
    }
}

?>


<?php if($page_tab === 'download-pdf'){?>

<?php get_template_part("drug-download-pdf"); ?> 

<?php }else{?>

<style>
.bbw-icon img{position:relative;top:-10px;}
</style>

<?php
$dosageForms = isset($meta['dosage_forms'][0]) ? @unserialize($meta['dosage_forms'][0]) : '';
$aDosageForms = array();
if(!empty($dosageForms)){
    foreach($dosageForms as $df){
        if($df['type'] === 'form'){
            $aDosageForms[] = $df;
        }
    }
}

$bottomLine = isset($meta['bottom_line'][0]) ? @unserialize($meta['bottom_line'][0]) : '';
$aBL = array(0=>array('name'=>'N/A','description'=>''));
if(!empty($bottomLine)){
    $aBL = array();
    foreach($bottomLine as $bl_cond){
        if($bl_cond['condition'] === @$condition->post_name){
            if(!empty($bl_cond['name']) or !empty($bl_cond['description'])){
                $aBL[] = $bl_cond;
            }
        }
    }
}

$drugFor = isset($meta['drug_for'][0]) ? @unserialize($meta['drug_for'][0]) : '';
$aDF = array('name'=>'N/A','description'=>'');
if(!empty($drugFor)){
    foreach($drugFor as $df_cond){
        if($df_cond['condition'] === @$condition->post_name and (!empty($df_cond['name']) or !empty($df_cond['description']))){
            $aDF = $df_cond;
        }
    }
}

$whoFor = isset($meta['who_for'][0]) ? @unserialize($meta['who_for'][0]) : '';
$aWF = array('name'=>'N/A','description'=>'');
if(!empty($whoFor)){
    foreach($whoFor as $wf_cond){
        if($wf_cond['condition'] === @$condition->post_name and (!empty($wf_cond['name']) or !empty($wf_cond['description']))){
            $aWF = $wf_cond;
        }
    }
}

$limitations = isset($meta['limitations'][0]) ? @unserialize($meta['limitations'][0]) : '';
$aLim = array(0=>array('name'=>'N/A','description'=>''));
if(!empty($limitations)){
    $aLim = array();
    foreach($limitations as $lim_cond){
        if(@$lim_cond['condition'] === @$condition->post_name){
            if(!empty($lim_cond['name']) or !empty($lim_cond['description'])){
                $aLim[] = $lim_cond;
            }
        }
    }
}

$trackRecord = isset($meta['track_record'][0]) ? @unserialize($meta['track_record'][0]) : '';
$aTR = array('name'=>'N/A','description'=>'');
if(!empty($trackRecord)){
    foreach($trackRecord as $tr_cond){
        if($tr_cond['condition'] === @$condition->post_name and (!empty($tr_cond['name']) or !empty($tr_cond['description']))){
            $aTR = $tr_cond;
        }
    }
}

$openQuestions = isset($meta['open_questions'][0]) ? @unserialize($meta['open_questions'][0]) : '';
$aOQ = array('name'=>'N/A','description'=>'');
if(!empty($openQuestions)){
    foreach($openQuestions as $oq_cond){
        if($oq_cond['condition'] === @$condition->post_name and (!empty($oq_cond['name']) or !empty($oq_cond['description']))){
            $aOQ = $oq_cond;
        }
    }
}

$contraindications = isset($meta['contraindications'][0]) ? @unserialize($meta['contraindications'][0]) : '';
$aContraIndications = array();
if(!empty($contraindications)){
    foreach($contraindications as $ci){
        if(@$ci['type'] === 'contraindication'){
            $aContraIndications['contraindication'][] = $ci;
        }
    }
}

$notrecommended = isset($meta['notrecommended'][0]) ? @unserialize($meta['notrecommended'][0]) : '';
$aNotRecommended = array();
if(!empty($notrecommended)){
    foreach($notrecommended as $nr){
        if($nr['type'] === 'not_rec'){
            $aNotRecommended['not_rec'][] = $nr;
        }
    }
}

//overview visibility
if(empty($aBL) and $aDF['name'] ===  'N/A' 
               and $aWF['name'] ===  'N/A' 
               and empty($aLim) 
               and $aTR['name'] ===  'N/A' 
               and $aOQ['name'] ===  'N/A' 
               and empty($aContraIndications) 
               and empty($aNotRecommended)
               and empty($meta['pregnant_breastfeeding_pregnancy-description_1'][0])
               and empty($meta['pregnant_breastfeeding_breastfeeding-description_1'][0])){
    $overview_display = false;
    if($page_tab === ''){
        if($allowedDrug){
            header("Location: ".get_permalink(). @$condition->post_name."/trials/");
        }else{
            header("Location: ".get_permalink(). @$condition->post_name."/prices/");
        }
    }
}else{
    $overview_display = true;
}


//how to use menu visibility
$how_to_use_display = false;
$how_to_use_display1 = false;
$doses = isset($meta['take'][0]) ? @unserialize($meta['take'][0]) : '';
$aD = array();
if(!empty($doses)){
    foreach($doses as $d_cond){
        if($d_cond['condition'] === @$condition->post_name and (!empty($d_cond['starting-dose']) 
                                                               or !empty($d_cond['maximum-dose']) 
                                                               or !empty($d_cond['approved-dose']) 
                                                               or !empty($d_cond['rec-dose']) 
                                                               or !empty($d_cond['titration-instructions']) 
                                                               or !empty($d_cond['missed-dose'])
                                                               or !empty($d_cond['stopping-instructions'])
                                                               or !empty($d_cond['special-populations'])
                                                               or !empty($d_cond['other']))){
            $aD = $d_cond;
        }
    } 
    if(!empty($aD)){
        $how_to_use_display1 = true;
    }
}
$how_to_use_display2 = false;
$timeResults = isset($meta['time_to_results'][0]) ? @unserialize($meta['time_to_results'][0]) : ''; 
$aTTR = array('name'=>'N/A');
if(!empty($timeResults)){
    foreach($timeResults as $ttr_cond){
        if($ttr_cond['condition'] === @$condition->post_name){
            $aTTR = $ttr_cond;
        }
    }
    if(!empty($aTTR['name'])){
        $how_to_use_display2 = true;
    }
}
if($how_to_use_display1 or $how_to_use_display2){
    $how_to_use_display = true;
}

//precautions menu visibility
$precautions_display = false;
$precautions = isset($meta['precautions'][0]) ? @unserialize($meta['precautions'][0]) : '';
$aPrecautions = array();
if(!empty($precautions)){
    $precautions_display1 = false;
    $precautions_display2 = false;
    foreach($precautions as $pc){
        if($pc['type'] === 'testing'){
            $aPrecautions['testing'][] = $pc;
        }
        if($pc['type'] === 'to_avoid'){
            $aPrecautions['to_avoid'][] = $pc;
        }
    }
    if(@$aPrecautions['testing']){
        $precautions_display1  = true;
    }
    if(@$aPrecautions['to_avoid']){
        $precautions_display2  = true;
    }
    if($precautions_display1 or $precautions_display2){
        $precautions_display = true;
    }
}

//interactions menu visibility
$interactions_display = false;
$interactions = isset($meta['drug_interactions'][0]) ? @unserialize($meta['drug_interactions'][0]) : '';
$aInteractions = array();
if(!empty($interactions)){
    foreach($interactions as $ia){
        if($ia['type'] === 'interaction'){
            $aInteractions['interaction'][] = $ia;
        }
    }
    if($aInteractions['interaction']){
        $interactions_display = true;
    }
}
?>
<!-- pop-up-1 -->
                                    <div class="modal fade pop-up-1" id="conditionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>			</button>
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take <?php the_title()?></h2>
                                                </div>
                                                <div class="modal-body">
                                                <?php foreach($aConditions as $c){?>
                                                    <a href="<?php echo get_permalink($post->ID)?><?php echo $c['slug']?><?php if($page_tab) echo '/'.$page_tab?>" title=""><?php echo $c['name']?></a>
                                                <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- // end pop-up-1 -->
                                    <!-- pop-up-1 -->
                                    <div class="modal fade pop-up-1" id="conditionModalPermanent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h2 class="modal-title" id="myModalLabel">Why I Take <?php the_title()?></h2>
                                                </div>
                                                <div class="modal-body">
                                                <?php foreach($aConditions as $c){?>
                                                    <a href="<?php echo get_permalink($post->ID)?><?php echo $c['slug']?><?php if($page_tab) echo '/'.$page_tab?>" title=""><?php echo $c['name']?></a>
                                                <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- // end pop-up-1 -->
<?php if(!$page_tab){?>

<!-- header product -->
<div class="container-fluid hp">
    <div class="row">
        <div class="container">
            <div class="row">
                <div class="hp-table">
                
                    <div class="hp-td hp-td1">
                        <div class="hp-td1-content">
                            <h1><?php the_title()?></h1>
							<h2><?php echo ucfirst($meta['maininfo_chem-name_1'][0])?></h2>
                            <div class="hp-separator"></div>
                            <div class="hp-title clearfix">
                                <?php if(!empty($condition)){?>
								<h4>For <?=$condition->post_title?></h4>
                                    <?if(!empty($aConditions) and count($aConditions) > 1){?>
								<a href="#" title="" data-toggle="modal" data-target="#conditionModal">Change <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                    <?php }?>
                                <?php }?>
							</div>
                            <?php if(!empty($drugImages)){?>
                            <div id="carousel-example-generic" class="carousel slide" data-interval="false">
								<!-- Indicators -->
								<ol class="carousel-indicators">
                                <?php foreach($drugImages as $k=>$image){?>
									<li data-target="#carousel-example-generic" data-slide-to="<?php echo $k?>" <?php if($k === 0){?>class="active"<?php }?>></li>
                                <?php }?>
								</ol>
							  <!-- Wrapper for slides -->
							  <div class="carousel-inner" role="listbox">
                                <?php foreach($drugImages as $k=>$image){?>
								<div class="item<?php if($k === 0){?> active<?php }?>">
								 <?php /*<div class="item-image" style="background-image:url(<?php echo $image[0]?>);"></div>*/?>
                                 <div class="item-image" style="background:none;height:100%;">
                                 <img src="<?php echo $image[0]?>" width="270"/>
                                 </div>
								</div>
                                <?php }?>								
							  </div>
							  <!-- Controls -->
							  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
								<span><i class="fa fa-angle-left" aria-hidden="true"></i></span>
							  </a>
							  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
								<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							  </a>
							</div>
                            <?php }?>
                        </div>
                    </div>
                    
                    <div class="hp-td hp-td2">
                        <div class="hp-td2-content">
                            <?php if(!empty($dosageForms)){?>
                            <div class="hp-title clearfix">
								<h4>Drug available as</h4>
								
							</div>
                            <div class="hp-text">
                                <?php foreach($dosageForms as $form){?>
								<p><span><img src="<?=get_template_directory_uri()?>/images/<?php echo $form['description']?>.png" alt="<?php echo $form['description']?>" /></span><span><?php echo $form['name']?></span></p>
                                <?}?>
							</div>
							<div class="hp-separator"></div>
                            <?php }?>
                            <div class="hp-title clearfix">
                                <?php
                                $aTrackRecordName = explode(' ',$meta['track_record_name_1'][0]);
                                $trackRecordType = ucfirst(strtolower($aTrackRecordName[0]));
                                $yearApproved = isset($meta['year_approved'][0]) ? @unserialize($meta['year_approved'][0]) : '';
                                $aYA = array('name'=>'N/A');
                                if(!empty($yearApproved)){
                                    foreach($yearApproved as $ya_cond){
                                        if($ya_cond['condition'] === @$condition->post_name){
                                            $aYA = $ya_cond;
                                        }
                                    }
                                }
                                ?>
                                <h4>Track record</h4>
								<a href="#track-record" title="" class="scroller"><i class="fa fa-plus" aria-hidden="true"></i> More</a>
							</div>
							<div class="hp-text">
								<p><span><img src="<?=get_template_directory_uri()?>/images/a05.png" alt="" /></span><span><?php echo $trackRecordType?> (approved in <?php echo $aYA['name']?>)</span></p>
							</div>
							<div class="hp-separator"></div>
                            <?php if($bbw){?>
                            <div class="hp-title clearfix">
								<h4>Special FDA warning?</h4>
								<a href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/side-effects" title=""><i class="fa fa-plus" aria-hidden="true"></i> More</a>
							</div>
							<div class="hp-text">
								<p class="bbw-icon"><span><img src="<?=get_template_directory_uri()?>/images/a06.png" alt="" /></span><span>Yes - FDA requires black box warning</span></p>
							</div>
							<div class="hp-separator"></div>
                            <?php }?>
                            <div class="hp-title clearfix">
								<h4>Bottom line</h4>
								<a href="<?php if(!empty($aBL)){?>#bottom-line<?php }else{?><?php echo get_permalink()?><?php echo @$condition->post_name?>/trials<?php }?>" title="" <?php if(!empty($aBL)){?>class="scroller"<?php }?>><i class="fa fa-plus" aria-hidden="true"></i> More</a>
							</div>
							
                        </div>
                    </div>
                    
                    <div class="hp-td hp-td3">
                        <div class="hp-td3-content">
                        <?php if(!empty($condition)){?>
                            <h2><?=$condition->post_title?></h2>
                            <div class="hp-separator"></div>
                            <div class="hp-title clearfix">
								<h4>What is <?=$condition->post_title?>?</h4>
								<a href="<?php echo esc_url( home_url( '/' ) ).'condition/'.$condition->post_name?>" title=""><i class="fa fa-plus" aria-hidden="true"></i> More</a>
							</div>
                            <div class="hp-text">
								<p>
									<?=$condition->post_excerpt?>
								</p>
							</div>
							<div class="hp-separator"></div>
                            <div class="hp-title clearfix">
								<h4>Other treatments for <?=$condition->post_title?></h4>
								<a href="<?php echo esc_url( home_url( '/' ) ).'condition/'.$condition->post_name?>/treatment" title=""><i class="fa fa-plus" aria-hidden="true"></i> More</a>
							</div>
                            <?php
                            //non-drug
                            $nondrugMethods = isset($condition_meta['nondrug'][0]) ? @unserialize($condition_meta['nondrug'][0]) : '';    
                        
                            //prescription
                            $args = array(  
                                'showposts'=>-1,
                                'post_type' => 'drug',                    
                                'orderby' => 'published',
                                'order'   => 'DESC',
                                'meta_query' => array(
                                'relation'=>'OR',
                                    array(
                                    'key' => 'conditions',
                                    'value' => sprintf(':"%s";', intval($condition->ID)),
                                    'compare' => 'LIKE'
                                    ),
                                    array(
                                    'key' => 'conditions',
                                    'value' => sprintf(':%s;', intval($condition->ID)),
                                    'compare' => 'LIKE'
                                    )
                                )
                            ); 
                            $query = new WP_Query( $args );
                            $nonDFBPrescriptedDrugs = isset($condition_meta['nondfbprescription'][0]) ? @unserialize($condition_meta['nondfbprescription'][0]) : '';
                            if ( $query->have_posts() or $nonDFBPrescriptedDrugs){
                                $prescription = true; 
                            }else{
                                $prescription = false; 
                            }
                        
                            //otc
                            $aDFBOTCDrugsIDs = isset($condition_meta['drug'][0]) ? @unserialize($condition_meta['drug'][0]) : '';
                            $nonDFBOTCDrugs = isset($condition_meta['nondfbotc'][0]) ? @unserialize($condition_meta['nondfbotc'][0]) : '';
                            if($aDFBOTCDrugsIDs or $nonDFBOTCDrugs){
                                $otc = true;
                            }else{
                                $otc = false;
                            }
                            ?>
                            <div class="hp-text">
								<p><span><img src="<?=get_template_directory_uri()?>/images/<?php if($prescription){?>icon-pr-1.png<?php }else{?>icon-pr-2.png<?php }?>" alt="" /></span><span>Non-drug</span></p>
								<p><span><img src="<?=get_template_directory_uri()?>/images/<?php if($otc){?>icon-pr-1.png<?php }else{?>icon-pr-2.png<?php }?>" alt="" /></span><span>Over-the-counter</span></p>
								<p><span><img src="<?=get_template_directory_uri()?>/images/<?php if($prescription){?>icon-pr-1.png<?php }else{?>icon-pr-2.png<?php }?>" alt=""/></span><span>Prescription</span></p>
                                
							</div>
                        <?php }else{?>
                            <script>
                            jQuery(document).ready(function(){
                                //jQuery('#conditionClicker').click();
                                jQuery('#conditionModalPermanent').modal('show');
                                jQuery('#conditionModalPermanent').on('hidden.bs.modal', function (e) {
                                    jQuery('#conditionModalPermanent').modal('show');
                                })
                            });
                            </script>
                        <?php }?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- header product end-->

<!-- in1 -->
<?php get_template_part("drug-menu"); ?> 
<!-- // end in1 -->
<?php /*
<div class="clearfix"></div>
<div class="container-fluid submenu-container">
    <div class="row">
        <div class="container">
            <div class="sub-menu">
                <a href="#bottom-line" title="" class="scroller">Bottom Line</a>
                <a href="#fda-approved-use" title="" class="scroller">FDA-approved use</a>
                <a href="#who-taking" title="" class="scroller">Who might consider taking it?</a>
                <a href="#what-is-not-known" title="" class="scroller">What is not known</a>
                <a href="#track-record" title="" class="scroller">Track Record</a>
                <a href="#open-questions" title="" class="scroller">Open questions</a>
                <a href="#do_not_take_if" title="" class="scroller">Do not take if you...</a>
                <a href="#not_recommended_if" title="" class="scroller">Not recommended if you...</a>
                <a href="#pregnancy_or_breastfeeding" title="" class="scroller">Safe if pregnant or breastfeeding?</a>
            </div>
        </div>
    </div>
</div>*/?>

<!-- content-product -->
<div class="container content-block">
	<div class="row">
	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Overview</h2>           
		</div>
		
		<div class="clearfix"></div><div class="separator-grey-1"></div>
<?php
if(!empty($aBL)){
?>
		<div class="content-table" id="bottom-line">
		
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
<!-- // end content-product -->

<!-- content-product grey -->
<div class="container content-block grey" id="fda-approved-use">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>FDA-approved use</h3>
			</div>
			
			<div class="td2">
			
				<div class="title-body"><?php echo $aDF['name'];?></div>
				<p>
					<?php echo $aDF['description']?>
				</p>
			    
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product grey -->

<!-- content-product -->
<div class="container content-block" id="who-taking">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Who might consider taking it?</h3>
			</div>
			
			<div class="td2">			   
				<div class="title-body"><?php echo $aWF['name'];?></div>
				<p>
					<?php echo $aWF['description']?>
				</p>                
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product -->

<!-- content-product grey -->
<div class="container content-block grey" id="what-is-not-known">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>What is not known</h3>
			</div>
			
			<div class="td2">
            <?php if(count($aLim)){?>
			<?php foreach($aLim as $l){?>
				<div class="title-body"><?php echo $l['name'];?></div>
				<p>
					<?php echo $l['description'];?>
				</p>
			<?php }?> 
            <?php }else{?>
            <div class="title-body">No specific issues</div>
            <?php }?>
			</div>
			
		</div>
    </div>
</div>
<!-- // end content-product grey -->

<!-- content-product -->

<div class="container content-block" id="track-record">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Track record</h3>
			</div>
			
			<div class="td2">			   
				<div class="title-body"><?php echo $aTR['name'];?></div>
				<p>
					<?php echo $aTR['description']?>
				</p>               
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product -->

<!-- content-product grey -->
<div class="container content-block grey" id="open-questions">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Open questions</h3>
			</div>
			
			<div class="td2">
			
				<div class="title-body"><?php echo $aOQ['name'];?></div>
				<p>
					<?php echo $aOQ['description']?>
				</p>
			    
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product grey -->

<!-- content-product -->
<div class="container content-block" id="do_not_take_if">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Do not take if you...</h3>
			</div>
			
			<div class="td2">
			   <?php if(!empty($aContraIndications['contraindication'])){?>
                <?php foreach($aContraIndications['contraindication'] as $ci){?>
                <div class="title-body"><?php echo $ci['name'];?></div>
				<p>					
				    <?php echo $ci['description'];?>
				</p>
                <?php }?>
            <?php }else{?>
                <div class="title-body">None</div>
            <?php }?>
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product -->

<!-- content-product grey -->
<div class="container content-block grey" id="not_recommended_if">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Not recommended if you...</h3>
			</div>
			
			<div class="td2">				                
			<?php if(!empty($aNotRecommended['not_rec'])){?>
                <?php foreach($aNotRecommended['not_rec'] as $nr){?>
                <div class="title-body"><?php echo $nr['name'];?></div>
				<p>					
				    <?php echo $nr['description'];?>
				</p>
                <?php }?>
            <?php }else{?>
                <div class="title-body">None</div>
            <?php }?>			    
			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product grey -->

<!-- content-product -->
<div class="container content-block" id="pregnancy_or_breastfeeding">
	<div class="row">
		<div class="content-table">
		
			<div class="td1">
				<h3>Safe if pregnant or breastfeeding?</h3>
			</div>
			
			<div class="td2">
			   
				<?php if(!empty($meta['pregnant_breastfeeding_pregnancy-description_1'][0])){?>
				<div class="title-body">Pregnant</div>
				<p>
					<?php echo $meta['pregnant_breastfeeding_pregnancy-description_1'][0]?>
				</p>
                <?php }?>
			    
                <?php if(!empty($meta['pregnant_breastfeeding_breastfeeding-description_1'][0])){?>
				<div class="title-body">Breastfeeding</div>
				<p>
					<?php echo $meta['pregnant_breastfeeding_breastfeeding-description_1'][0]?>
				</p>
                <?php }?>               

			</div>
			
		</div>
	</div>
</div>
<!-- // end content-product -->
<?php if(function_exists('pf_show_link')){echo pf_show_link();} ?>
<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/trials">NEXT: TRIALS</a>
	</div>
</div>

<?php }?>


<?php if($page_tab == 'trials'){?>

<?php get_template_part("drug-trials"); ?> 

<?php }?>

<?php if($page_tab == 'prices'){?>

<?php get_template_part("drug-prices"); ?> 

<?php }?>

<?php if($page_tab == 'side-effects'){?>

<?php get_template_part("drug-side-effects"); ?> 

<?php }?>

<?php if($page_tab == 'how-to-use'){?>

<?php get_template_part("drug-how-to-use"); ?> 

<?php }?>

<?php if($page_tab == 'precautions'){?>

<?php get_template_part("drug-precautions"); ?> 

<?php }?>

<?php if($page_tab == 'interactions'){?>

<?php get_template_part("drug-interactions"); ?> 

<?php }?>

<?php if ( !is_user_logged_in() ) {?>
<!-- FORM Want to View More Content? -->
<div class="container want">
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h2>Want to View More Content?</h2>
	</div>
	
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
    
	<ul class="tab-1t" role="tablist" id="accTypeTab">
        <li role="presentation" class="active"><a href="#userPanel" aria-controls="userPanel" role="tab" data-toggle="tab">INDIVIDUAL ACCOUNT</a></li>
        <li role="presentation"><a href="#enterprisePanel" aria-controls="enterprisePanel" role="tab" data-toggle="tab">ENTERPRISE ACCOUNT</a></li>
    </ul>
	<div style="margin-bottom:30px"></div> 
    <div class="tab-content">
    
    <div role="tabpanel" class="tab-pane active" id="userPanel">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 signin-block">
		<div class="row">
        
        
			
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 margin-bottom-20px">
				<h3>Member Sign-in</h3>
				<div class="signin-block-table1">
					<span><img src="<?=get_template_directory_uri()?>/images/icon-4.png" alt="" /></span>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>sign-up/" title="">Create new account</a>
				</div>
			</div>
			
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 margin-bottom-20px">
				<div class="registrationComment">Not a member? Sign up today for a free Limited account and access up to 5 drugs during our beta trial.</div>
				<?/*<font color="#ffffff">30-day free access</font> 
				to our extensive data base on practically all medical drugs 
				in the U.S., including side effects, warnings, interactions, 
				clinical trial results, and more.*/?>
			</div>
			
			<div class="clearfix"></div>
			
			<form method="post" name="loginform" id="loginform" action="<?php echo esc_url( home_url( '/' ) ); ?>wp-login.php">
                <input type="hidden" name="redirect_to" value="" />
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<input type="text" name="log" class="form-control" id="" placeholder="Email" />
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<input type="password" name="pwd" class="form-control" id="" placeholder="Password" />
						<a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#individualRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<button type="submit" class="btn btn-default">Sign in</button>
						<a class="for-mobile" href="<?php echo wp_lostpassword_url( $_SERVER['REQUEST_URI'] ); ?>" title="">Forgot password?</a>
					</div>
				</div>
			</form>
				
		</div>
	</div>
    </div>
    
    <div role="tabpanel" class="tab-pane" id="enterprisePanel">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
         <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 signin-block">
		<div class="row">
        
        
			
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 margin-bottom-20px">
				<h3>Member Sign-in</h3>
			</div>
			
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 margin-bottom-20px">
				<?/*<font color="#ffffff">30-day free access</font> 
				to our extensive data base on practically all medical drugs 
				in the U.S., including side effects, warnings, interactions, 
				clinical trial results, and more.*/?>
			</div>
			
			<div class="clearfix"></div>
			
			<form method="post" name="loginform" id="loginform" action="<?php echo esc_url( home_url( '/' ) ); ?>wp-login.php">
                <input type="hidden" name="redirect_to" value="" />
                <input type="hidden" name="enterprise" value="1" />
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
                        <input type="text" name="log" class="form-control" placeholder="User Name" />
                        <a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestNameModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot username?</a>
                    </div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<input type="password" name="pwd" class="form-control" id="" placeholder="Password" />
						<a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<button type="submit" class="btn btn-default">Sign in</button>
                        <a class="for-mobile dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>						
					</div>
				</div>
			</form>
				
		</div>
	</div>
    </div>
    
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
		
</div>
<!-- // END FORM Want to View More Content? -->
<?php }?>


<?php
get_footer();
?>
<?php }?>