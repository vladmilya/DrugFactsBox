<?php 
get_header();
?>
<style type="text/css">
.non-link-list-item{
    padding: 7px 0;   
}
</style>
<?php
if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){//allowed conditions

    if(is_unc()){
        $uncUser = get_user_by('login', 'unc');
        $userMeta = get_user_meta( $uncUser->data->ID );
    }else{
        $userMeta = get_user_meta( $loggedUser->ID );
    }
    
    $aGrantedConditions = array();
    
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
    if(is_unc()){
        if(!in_array($post->ID, $aGrantedConditions)){
            header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
        }
    }else{
        if(!empty($aGrantedConditions)){
            if(!in_array($post->ID, $aGrantedConditions)){
                header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
            }
        }
    }
}

$is_treatment = isset($wp->query_vars['treatment']) ? true : false;

$meta = get_post_meta($post->ID); //dump($meta);

$aProperties = isset($meta['condition_properties'][0]) ? @unserialize($meta['condition_properties'][0]) : '';

?>

<?php if(!$is_treatment){
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
$thumb_url = $thumb_url_array[0];
    ?>

<!-- header about -->
<div class="container-fluid header-about" style="background-image:url(<?php echo $thumb_url?>);">
	<div class="row">
		<div class="container">
			<div class="row">
				<div class="header-table">
					<div class="td-1">
						<h2><?php the_title()?></h2>
					</div>
					<div class="td-2">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- header about -->
<?php }else{?>
<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php the_title()?></h1>
		</div>
	</div>
</div>
<!-- // end title-h1 -->
<?php }?>


<!-- in1 -->
<div class="in1">
	<div class="in1-top">
		<div class="in1-bars">
			<i class="fa fa-bars"></i><span>TABLE OF CONTENTS</span>
		</div>
		<div class="in1-fixed">
			<div class="title-bars">
				<h2><span><i class="fa fa-bars"></i></span><font><?php the_title()?></font></h2>
				<div class="close-x">
					<a title="" href="#">
						<img alt="" src="<?=get_template_directory_uri()?>/images/close-f.png">
					</a>
				</div>	
			</div>
			<ul>
				<li><a <?php if(!$is_treatment) echo 'class="active" '?>href="<?php echo get_permalink($post->ID)?>" title="">ABOUT</a></li>
				<li><a <?php if($is_treatment) echo 'class="active" '?>href="<?php echo get_permalink($post->ID)?>/treatment" title="">TREATMENT</a></li>
			</ul>
		</div>
	</div>
	
</div>
<!-- // end in1 -->


<?php if(!$is_treatment){   
    $symptoms = isset($meta['symptoms'][0]) ? @unserialize($meta['symptoms'][0]) : '';
?>

<div class="container title-2-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1>About <?php the_title()?></h1>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="powered-by">	
				<p class="small">Powered by</p>
				<img src="<?=get_template_directory_uri()?>/images/logo.png" alt="" />
			</div>
		</div>
	</div>
</div>

<!-- content-table-block -->
<div class="container content-block">
	<div class="row">
		
		<div class="content-table">
			<div class="td1">
				<h3>Overview</h3>
			</div>
			<div class="td2">
				<?php the_content()?>
			</div>
		</div>
	</div>
</div>
<!-- // end content-table -->

<?php
$aAboutProperties = array();
if(!empty($aProperties)){
    foreach($aProperties as $k=>$v){
        if($v['section'] === 'About'){
            $aAboutProperties[] = $v;
        }
    }
    $aAboutProperties = sort_array($aAboutProperties,'order','asc');
}
?>

<?php foreach($aAboutProperties as $num=>$prop){?>
<div class="container content-block<?php if(($num+1)%2) echo ' grey'?>">
	<div class="row">
		<div class="content-table">
			<div class="td1">
				<h3><?php echo $prop['title']?></h3>
			</div>
			<div class="td2">
            <?php echo $prop['content-text']?>
			</div>
		</div>
			
		<div class="separator-grey"></div>
			
	</div>
</div>
<?php }?>


<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink($post->ID)?>treatment">NEXT: TREATMENTS</a>
	</div>
</div>

<?php }else{
    $conditionID = $post->ID;
    $conditionSlug = $post->post_name;
    $nondrugMethods = isset($meta['nondrug'][0]) ? @unserialize($meta['nondrug'][0]) : '';
    
    //Prescripted drugs
    $aPrescriptedDrugs = array();
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
            $aDrug = array();
            $query->the_post();
            $meta_drug = get_post_meta($query->post->ID);
            $chemName = ucfirst($meta_drug['maininfo_chem-name_1'][0]);
            $aDrug['name'] = $query->post->post_title;
            $aDrug['chem_name'] = $chemName;
            $aDrug['link'] = get_permalink($query->post->post_ID).''.$conditionSlug;            
            $aPrescriptedDrugs[] = $aDrug;
        }
    }
    
    $nonDFBPrescriptedDrugs = isset($meta['nondfbprescription'][0]) ? @unserialize($meta['nondfbprescription'][0]) : '';
    if(!empty($nonDFBPrescriptedDrugs)){
        foreach($nonDFBPrescriptedDrugs as $d){
            $aDrug = array();
            $aDrug['name'] = $d['drug'];
            $aDrug['link'] = '';
            $aPrescriptedDrugs[] = $aDrug;
        }
    }
    $aPrescriptedDrugs = sort_array($aPrescriptedDrugs,'name','asc');
    
    //OTC Drugs
    //dump($meta);
    $aDFBOTCDrugsIDs = isset($meta['drug'][0]) ? @unserialize($meta['drug'][0]) : '';
    $aOTCDrugs = array();
    if($aDFBOTCDrugsIDs){
        foreach($aDFBOTCDrugsIDs as $d){
            $aDrug = array();
            $aDrug['name'] = get_the_title($d);
            $aDrug['link'] = get_permalink($d).''.$conditionSlug;
            $aOTCDrugs[] = $aDrug;
        }
    }
    $nonDFBOTCDrugs = isset($meta['nondfbotc'][0]) ? @unserialize($meta['nondfbotc'][0]) : '';
    if(!empty($nonDFBOTCDrugs)){
        foreach($nonDFBOTCDrugs as $d){
            $aDrug = array();
            $aDrug['name'] = $d['otc-drug'];
            $aDrug['link'] = '';
            $aOTCDrugs[] = $aDrug;
        }
    }
    $aOTCDrugs = sort_array($aOTCDrugs,'name','asc');
    

    $aTreatmentsProperties = array();
    if(!empty($aProperties)){
        foreach($aProperties as $k=>$v){
            if($v['section'] === 'Treatment'){
                $aTreatmentsProperties[] = $v;
            }
        }
        $aTreatmentsProperties = sort_array($aTreatmentsProperties,'order','asc');
    }
?>
<?php wp_reset_query(); ?>
<div class="container content-block">
	<div class="row">
	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Treatments</h2>
		</div>
		
			<div class="trea-1">
			    <?php if(!empty($nondrugMethods)){?>
				<div class="td">
					<div class="trea-1-header">
						<h3>Non-drug</h3>
						<div class="icon">
							<img src="<?=get_template_directory_uri()?>/images/icon-a91.png" alt="" />
						</div>
					</div>
					<div class="trea-1-content">
                    <?php if(!empty($nondrugMethods)){?>
                        <?php foreach($nondrugMethods as $m){?>
						<div class="non-link-list-item"><?php echo $m['treatment-method']?></div>
                        <?php }?>
                    <?php }else{?>
                        <div class="non-link-list-item">None</div>
                    <?php }?>                       
					</div>
				</div>
			    <?php }?> 
                
                <?php if(!empty($aPrescriptedDrugs)){?>
				<div class="td">
					<div class="trea-1-header">
						<h3>Prescription Drugs</h3>
						<div class="icon">
							<img src="<?=get_template_directory_uri()?>/images/icon-a92.png" alt="" />
						</div>
					</div>
					<div class="trea-1-content">
                     <?php if(!empty($aPrescriptedDrugs)){?>
                        <?php foreach($aPrescriptedDrugs as $d){?>
                        <?php if($d['link']){?>
						<a href="<?php echo $d['link']?>" title=""><?php echo $d['name']?><?if(!empty($d['chem_name'])) echo ' ('.$d['chem_name'].')'?></a>
                        <?php }else{?>
                        <div class="non-link-list-item"><?php echo $d['name']?></div>
                        <?php }?>
                        <?php }?>
                    <?php }else{?>
                        <div class="non-link-list-item">None</div>
                    <?php }?>
					</div>
				</div>
			    <?php }?>
                
				<div class="td">
					<div class="trea-1-header">
						<h3>Over-the-counter Drugs</h3>
						<div class="icon">
							<img src="<?=get_template_directory_uri()?>/images/icon-a93.png" alt="" />
						</div>
					</div>
					<div class="trea-1-content">
                        <?php if(!empty($aOTCDrugs)){?>
						 <?php foreach($aOTCDrugs as $d){?>
                        <?php if($d['link']){?>
						<a href="<?php echo $d['link']?>" title=""><?php echo $d['name']?></a>
                        <?php }else{?>
                        <div class="non-link-list-item"><?php echo $d['name']?></div>
                        <?php }?>
                        <?php }?>
                        <?php }else{?>
                        <div class="non-link-list-item">None</div>
                        <?php }?>
					</div>
				</div>
				
			</div>
			
		</div>
	</div>
    
    
    <?php if(!empty($aTreatmentsProperties)) foreach($aTreatmentsProperties as $num=>$prop){?>
    <div class="container content-block<?php if(($num)%2) echo ' grey'?>">
        <div class="row">
            <div class="content-table">
                <div class="td1">
                    <h3><?php echo $prop['title']?></h3>
                </div>
                <div class="td2">
                <?php echo $prop['content-text']?>
                </div>
            </div>
			
            <div class="separator-grey"></div>
			
        </div>
    </div>
    <?php }?>

<?php }?>

<?php
get_footer();
?>