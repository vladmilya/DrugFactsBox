<?php 
get_header();
?>
<?php 
if(isset($_POST['conditionSent'])){ 
    unset($_SESSION['condition']);
    $data = array(); 
    foreach($_POST as $k=>$v){
        $key = str_replace('condition', '', $k);
        if($key != 'Sent'){
            $data[$key] = $v;
        }
    }
    $_SESSION['condition'] = $data;
}

if(!isset($_SESSION['generic'])){
    $_SESSION['generic'][0] = 1;
    $_SESSION['generic'][1] = 1;
}
if(isset($_POST['genericSent'])){
    unset($_SESSION['generic']);
    $_SESSION['generic'] = $_POST['generic'];
}

global $loggedUser;
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
}

$aGrantedConditions = array();//allowed conditions
if(is_user_logged_in() and is_unc()){    
    
    $uncUser = get_user_by('login', 'unc');
    $userMeta = get_user_meta( $uncUser->data->ID );
    
    
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
?>
<!-- conditions-page -->
	<div class="conditions-page">
	
		<h1>Drug Facts Boxes</h1>
		
		<div class="container-fluid position-relative filter-top">
			<div class="row">
				<div class="container">
					<div class="row">
                    
                        <?php if(!is_user_logged_in() or !is_unc()){?>
			
						<a class="dropdown-toggle dropdown-custom" href="#" id="dropdownToggle">CONDITIONS <i class="fa fa-angle-down"></i></a>
                        
                        
						
						<ul class="dropdown-filter dropdown-menu" style="">
						
							<div class="close-x"><a href="#" title="">
                            <?php /*FILTER*/?>
                            <img src="<?=get_template_directory_uri()?>/images/close-f.png" alt="" />                            
                            </a></div>
							
							<li class="container">
							
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="row">
										<h3>Conditions</h3>
									</div>
								</div>
								<?php
                                $numGroups = 4;
                                $numConditions = count($aGlobalConditions);
                                $itemsPerGroup = ceil($numConditions/$numGroups); 
                                $conditionGroups = array();
                                for($n=0; $n < $numGroups; $n++){ 
                                    $groupContent = array();
                                    for($i=$itemsPerGroup*$n; $i<$itemsPerGroup*($n+1); $i++){
                                        if(isset($aGlobalConditions[$i])){
                                            $groupContent[] = $aGlobalConditions[$i];       
                                        }
                                    }
                                    $conditionGroups[$n] = $groupContent;
                                }
                                //dump($conditionGroups);
                                foreach($conditionGroups as $group){?>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="row">
                                        <?php
                                        
                                        foreach($group as $cond){
                                            if(!empty($cond)){
                                                if(!empty($cond['name'])){?>
										<div class="chek">
											<div class="pseudoBox">
												<input type="checkbox" name="c[<?php echo $cond['id']?>]" id="cond<?php echo $cond['id']?>" value="<?php echo $cond['name']?>"/>
												<label for="cond<?php echo $cond['id']?>" class="icon-ok-circled"></label>
											</div>
										    <span><?php echo $cond['name']?></span>										
                                        </div>
                                                <?php }}}?>
									</div>
								</div>
								<?php }?>	
                                <div class="clearfix"></div>
								<a href="#" class="filterGoLink">GO »</a>
							</li>
						</ul>
						
						
						<span class="filter-span">FILTER:</span>
						
                        <form action="<?php echo esc_url( home_url( '/' ) ); ?>all-drugs/" method="post" id="genericForm" style="display:inline">
                        <input type="hidden" name="genericSent" value="1" />
                        
						<div class="pseudoBox">
							<input type="checkbox" name="generic[0]" id="brand" value="1" class="genericCheck" <?php if(isset($_SESSION['generic'][0])) echo 'checked'?>/>
							<label for="brand" class="icon-ok-circled"></label>
						</div>
						<span class="brand-drags">BRAND DRUGS</span>
                        
                        
						<div class="pseudoBox">
							<input type="checkbox" name="generic[1]" id="generic" value="1" class="genericCheck" <?php if(isset($_SESSION['generic'][1])) echo 'checked'?>/>
							<label for="generic" class="icon-ok-circled"></label>
						</div>
						<span class="generik">GENERIC</span>
                       
						</form>                        
                        <?php }?>
						<div class="pull-right">
							<a class="active" href="#" title="" id="barMode"><i class="fa fa-th-large"></i></a>
							<a href="#" title="" id="listMode"><i class="fa fa-bars"></i></a>						
						</div>
						
						
					</div>
				</div>
			</div>
		</div>
		
		<div class="container inde">
            <form action="<?php echo esc_url( home_url( '/' ) ); ?>all-drugs/" method="post" id="conditionForm">
            <input type="hidden" value="1" name="conditionSent" />
			<div class="row" id="filterValues">
				<?php if(!empty($_SESSION['condition'])){?>
                <?php foreach($_SESSION['condition'] as $k=>$v){?>
                <div class="filterItem"><div class="pseudoBox"><input type="checkbox" name="condition<?=$k?>" id="filter<?=$k?>" value="<?=$v?>" checked/><label for="filter<?=$k?>" class="icon-ok-circled"></label></div><?=stripslashes($v)?></div>
                <?php }?>
                <?php }?>
			</div>
            </form>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="container<?php if(isset($_COOKIE['list_mode'])) echo ' list-block-1';?>" id="drugContainer">
			<div class="row">
            <?php                
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                if(is_unc()){
                    $num_page = 16;
                }else{
                    $num_page = 12;
                }
                $args = array(  
                    'posts_per_page' => $num_page,
                    'post_type' => 'drug',                    
                    'orderby' => 'post_title',
                    'order'   => 'ASC',
                    'paged' => $paged 
                ); 
                if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
                    if(is_unc()){
                        if(isset($accessibleDrugs) and !empty($accessibleDrugs)){
                            $args['post__in'] = $accessibleDrugs;
                        }else{
                            $args['post__in'] = array('no');
                        }
                    }else{
                        if(!empty($accessibleDrugs)){
                            $args['post__in'] = $accessibleDrugs;
                        }
                    }
                }
                //generic filter
                if(isset($_SESSION['generic'][0]) and !isset($_SESSION['generic'][1])){
                    $args['meta_query'][] = array(
                                'relation' => 'OR',
                                array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'N'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'n'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'No'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'no'
                                )
                            ); 
                }elseif(isset($_SESSION['generic'][1]) and !isset($_SESSION['generic'][0])){
                    $args['meta_query'][] = array(
                                'relation' => 'OR',
                                array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'Y'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'y'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'Yes'
                                ),
                                 array(
                                'key' => 'maininfo_generic_1', 
                                'value' => 'yes'
                                )
                            );
                }elseif(!isset($_SESSION['generic'][1]) and !isset($_SESSION['generic'][0])){
                    $args['meta_query'][] = array(
                        array(
                                'key' => 'maininfo_generic_1', 
                                'value' => ''
                                )
                    );
                }
                //condition filter
                if(!empty($_SESSION['condition'])){
                    $condsArray = array();
                    $condsArray['relation']= 'OR';
                    foreach($_SESSION['condition'] as $cond_id => $cond_name){                        
                        $condsArray[] = array(
                            'key' => 'conditions',
                            'value' => sprintf(':"%s";', $cond_id),
                            'compare' => 'LIKE'
                        );   
                        $condsArray[] = array(
                            'key' => 'conditions',
                            'value' => sprintf(':%s;', $cond_id),
                            'compare' => 'LIKE'
                        );
                    }
                    $args['meta_query'][] = array(
                                $condsArray
                            );
                }
                //dump($args);
                
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $aDrugsAllowedConds = array();
                        $query->the_post();
                        $meta = get_post_meta($query->post->ID);
                        $conditions = isset($meta['conditions'][0]) ? @unserialize($meta['conditions'][0]) : '';
                        $aConditions = array();
                        if(!empty($conditions)){
                            foreach($conditions as $cond){
                                $condPost = get_post($cond);                                                           
                                $aCond['id'] = $condPost->ID;          
                                $aCond['slug'] = $condPost->post_name;             
                                $aCond['name'] = $condPost->post_title;
                                $aCond['link'] = get_permalink($condPost->ID);
                                if(is_user_logged_in() and is_unc()){
                                    if(!in_array($condPost->ID, $aGrantedConditions)){
                                        continue;
                                    }
                                }                                
                                $aConditions[] = $aCond;
                            }
                                
                        }          
                ?>
			
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="row">
						<div class="drug-block-1">
							<div class="drug-pic"><img src="<?=get_template_directory_uri()?>/images/slide-pic1.png" alt="" /></div>
							<div class="drug-block-1c">
								<h3>
                                <?if(!empty($aConditions)){?>
                                    <?php if(count($aConditions) == 1){?>
                                    <a href="<?php echo get_permalink($post->ID)?><?php echo $aConditions[0]['slug']?>" title=""><?php echo $post->post_title?></a>
                                    <?php }else{?>
                                    <a href="#" title="" data-toggle="modal" data-target="#condition2Modal<?php echo $post->ID?>"><?php echo $post->post_title?></a>                                                                    
                                    <?php }?>
                                <?php }else{?>
                                    <a href="<?php echo get_permalink($post->ID)?>" title=""><?php echo $post->post_title?></a>
                                <?php }?>
                                </h3>
								<p><span>Generic:</span> <?php if($meta['maininfo_generic_1'][0] == 'Y' or $meta['maininfo_generic_1'][0] == 'y' or $meta['maininfo_generic_1'][0] == 'Yes' or $meta['maininfo_generic_1'][0] == 'yes'){echo 'Yes';}elseif($meta['maininfo_generic_1'][0] == 'N' or $meta['maininfo_generic_1'][0] == 'n' or $meta['maininfo_generic_1'][0] == 'No' or $meta['maininfo_generic_1'][0] == 'no'){echo 'Not Available';}else{echo 'Not Available';}?></p>
								<p>
                                    <span>Condition:</span> 
                                    <?if(!empty($aConditions)){?>
                                    <?php if(count($aConditions) == 1){?>
                                    <a href="<?php echo $aConditions[0]['link']?>" title="<?php echo $aConditions[0]['name']?>"><?php echo $aConditions[0]['name']?></a>
                                    <?php }else{?>
                                    <a href="#" title="<?php echo $aConditions[0]['name']?>" data-toggle="modal" data-target="#conditionModal<?php echo $post->ID?>"><?php echo $aConditions[0]['name']?></a>
                                    <!-- pop-up-1 -->
                                    <div class="modal fade pop-up-1" id="conditionModal<?php echo $post->ID?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>			</button>
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take <?php echo $post->post_title?></h2>
                                                </div>
                                                <div class="modal-body">
                                                <?php foreach($aConditions as $c){?>
                                                    <a href="<?php echo $c['link']?>" title=""><?php echo $c['name']?></a>
                                                <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- // end pop-up-1 -->
                                    <?php }?>
                                    <?php }else{?>
                                    in progress
                                    <?php }?>
                                </p>
								<p><span>Class:</span> <?php echo $meta['maininfo_class_1'][0]?></p>
								<div class="details-a">
                                <?if(!empty($aConditions)){?>
                                    <?php if(count($aConditions) == 1){?>
                                    <a href="<?php echo get_permalink($post->ID)?><?php echo $aConditions[0]['slug']?>" title="">DETAILS »</a>
                                    <?php }else{?>
                                    <a href="#" title="" data-toggle="modal" data-target="#condition2Modal<?php echo $post->ID?>">DETAILS »</a>
                                    <div class="modal fade pop-up-1" id="condition2Modal<?php echo $post->ID?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>			</button>
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take <?php echo $post->post_title?></h2>
                                                </div>
                                                <div class="modal-body">
                                                <?php foreach($aConditions as $c){?>
                                                    <a href="<?php echo get_permalink($post->ID)?><?php echo $c['slug']?>" title=""><?php echo $c['name']?></a>
                                                <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }?>
                                <?php }else{?>
                                    <a href="<?php echo get_permalink($post->ID)?>" title="">DETAILS »</a>
                                <?php }?>
                                </div>
							</div>
						</div>
					</div>
				</div>                
                
                    <?php } } ?>
				
				
				<!-- pagination -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pagination-1">
					<div class="row">                  
						<nav>
                        
<?php
$args = array(
	'base'               => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
	'format'             => '?page=%#%',
    'type'               => 'array',
	'total'              => $query->max_num_pages,
	'current'            => max( 1, get_query_var('paged') ),
	'prev_next'          => true,
	'prev_text'          => __('<span aria-hidden="true" class="arrows"><i class="fa fa-angle-left"></i></span>'),
	'next_text'          => __('<span aria-hidden="true" class="arrows"><i class="fa fa-angle-right"></i></span>')
    );
$pager = paginate_links($args);
?>

                        
						  <ul class="pagination">
                          
                          <?php if(!empty($pager) and is_array($pager)) foreach($pager as $k=>$p){?>
                          <li><?php echo $p?></li>
                          <?}?>
						  </ul>
						</nav>
					</div>
				</div>
				<!-- // end pagination -->
	<?php wp_reset_query(); ?>
			</div>
		</div>
	
	</div>
	<!-- // end conditions-page -->

	
<?php
get_footer();
?>