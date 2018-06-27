<?php 
get_header();
?>
	<script>
     var pageName = 'account';
		(function($) {
		$(function() {             
             if(isMobile.iPhone()){   
                $('.jq-selectbox').css('overflow', 'hidden');
                $('.jq-selectbox').find('.dropdown').css('visibility','hidden');
             }
		})
		})(jQuery)
	</script>
<style>
#unsubscribe #s2member-pro-stripe-cancellation-submit,
#unsubscribe #s2member-pro-stripe-cancellation-form-submission-section-title{
    display:none;
}
#unsubscribe form.s2member-pro-stripe-form {
    min-height: 20px!important;
}
#s2member-pro-stripe-checkout-form-source-token-summary {
    margin-top: 20px;
} 
div.s2member-pro-stripe-form-section > div.s2member-pro-stripe-form-div button.s2member-pro-stripe-form-source-token-button::before{
    float:none!important;
}
</style>
<?php
    $plan = addslashes(htmlspecialchars(@$_GET['plan']));
    if(current_user_is("s2member_level0")){
        $current_plan = 'limited';
    }
    if(current_user_is("s2member_level1")){
        $current_plan = 'unlimited';
    }
    
    if(!empty($_POST['selected_drugs'])){
        $custom_fields = get_user_option('s2member_custom_fields');
        $custom_fields['selected_drugs'] = $_POST['selected_drugs'];
        update_user_meta( $current_user->data->ID, 'wp_s2member_custom_fields', $custom_fields);
    }
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

<div class="signup-1">
    <hr />
    
    <div class="container">
		<div class="row">	
			<h2>Change Membership Plan</h2>
		</div>
	</div>
    <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
            <?php else : ?>
    <div class="container">
		<div class="row">
        
            <!-- signup-table -->
			<div class="signup-table">
<?php
$limited_slug = 'limited';
$args = array(
  'name'        => $limited_slug,
  'post_type'   => 'post',
  'post_status' => 'publish',
  'numberposts' => 1
);
$my_posts = get_posts($args);
$limitedPost = $my_posts[0];
$limitedPostMeta = get_post_meta($limitedPost->ID);

$s2Meta = get_user_meta($current_user->data->ID, 'wp_s2member_custom_fields', true);
$selectedDrugs = @$s2Meta['selected_drugs']; //dump($selectedDrugs);
$aSelectedDrugs = array();
if(!empty($selectedDrugs)){
    $aLines = explode('||', $selectedDrugs);
    if(!empty($aLines)){
        foreach($aLines as $line){
            $aDrugs = explode('::', $line);
            $drug=array();
            $drug['id'] = $aDrugs[0];
            $drug['name'] = $aDrugs[1];
            $aSelectedDrugs[] = $drug;
        }
    }
}

                    $drug_result = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='drug' ORDER BY post_title ASC");
                    $aDrugs = array();
                    if ( !empty($drug_result) ) {
                        foreach( $drug_result as $drug) {
                            $aDrug = array();
                            $aDrug['name'] = $drug->post_title;
                            $aDrug['id'] = $drug->ID;
                            $aDrugs[$drug->ID] = $aDrug;
                        }
                    }
//dump($aSelectedDrugs);
?>	
                <div id="limPlan" class="td">
                    <div class="td-col-4">
						<h3><?php echo $limitedPost->post_title?></h3>
						<h2><?php echo $limitedPost->post_content?></h2>
						<p><?php echo $limitedPostMeta['comment'][0]?></p>
                        
                        <?php if($current_plan == $limited_slug){?>
                        <div class="clearfix"></div>
						<div class="cp"><a href="<?php echo esc_url( home_url( '/' ) ); ?>account/?plan=<?php echo $limited_slug?>" title=""><img src="<?=get_template_directory_uri()?>/images/icon-g1.png" alt="" /><span>Current Plan</span></a></div>
                        <?php }else{?>
                        <div class="details-a">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>account/?plan=<?php echo $limited_slug?>#formLimited" title="">Select This Plan</a>
						</div>
                        <?php }?>
					</div>
                    <?php if($current_plan == $limited_slug){?>
                    <!-- Select Drug -->
                    <script>
                    jQuery(document).ready(function(){                       
                        jQuery('.select-drug-block').find('select').each(function(){
                            var sel = jQuery(this);
                            sel.change(function(){ 
                                var selectedDrugs='';
                                jQuery('.drug-block').find('input').each(function(){
                                    if(jQuery(this).val()){
                                        selectedDrugs+=jQuery(this).val()+'||';
                                    }
                                });
                                jQuery('.select-drug-block').find('select').each(function(){
                                    if(jQuery(this).val()){
                                        selectedDrugs+=jQuery(this).val()+'||';
                                    }
                                });
                                selectedDrugs = selectedDrugs.substring(0, selectedDrugs.length - 2);
                                jQuery('.selectedDrugs').val(selectedDrugs);
                            });                            
                        });
                    });
                    </script>
					<div class="select-drug-block" style="">
						<h4>Your DrugFactsBox Library</h4><a name="formLimited"></a> 
						<p>You can choose up to 5 drugs – one at a time or multiple at once. Choose now or at any time.
Once you click Submit, your choices are locked in. Upgrade to have unlimited access to the full library.</p>
						<form action="" method="post" name="updateLimitedForm">
                        <input type="hidden" name="selected_drugs" value="<?php echo $selectedDrugs?>" class="selectedDrugs">
						<div class="drug-block">
						<?php foreach($aSelectedDrugs as $k=>$v){?>
							<p>Drug <?php echo $k+1?>: 
                            <?php
                            $meta = get_post_meta($v['id']); 
                           $conditions = isset($meta['conditions'][0]) ? @unserialize($meta['conditions'][0]) : '';
                           $aConditions = array();
                        if(!empty($conditions)){
                            foreach($conditions as $cond){
                                $condPost = get_post($cond);
                                $aCond['id'] = $condPost->ID;                                
                                $aCond['name'] = $condPost->post_title;
                                $aCond['link'] = get_permalink($condPost->ID);
                                $aConditions[] = $aCond;
                            }
                        }  
                            ?>
                            
                            <?if(!empty($aConditions)){?>
                                    <?php if(count($aConditions) == 1){?>
                                    <a href="<?php echo get_permalink($v['id'])?>?cond=<?php echo $aConditions[0]['id']?>" title=""><?php echo $v['name']?></a>
                                    <?php }else{?>
                                    <a href="#" title="" data-toggle="modal" data-target="#condition2Modal<?php echo $post->ID?>"><?php echo $v['name']?></a>
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
                                                    <a href="<?php echo get_permalink($v['id'])?>?cond=<?php echo $c['id']?>" title=""><?php echo $c['name']?></a>
                                                <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }?>
                                <?php }else{?>
                                    <a href="<?php echo get_permalink($v['id'])?>" title=""><?php echo $v['name']?></a>
                                <?php }?>
                            </p>
                            <input type="hidden" name="drug<?php echo $k+1?>" value="<?php echo $v['id'].'::'.$v['name']?>"/>
						<?php }?>	
						<div class="clearfix"></div>
						</div>
                        <?php
                        $n = count($aSelectedDrugs);
                        if($n){
                            $startEmpty = $n+1;
                        }else{ 
                            $startEmpty=1;
                        }
                        for($i=$startEmpty; $i<=5; $i++){?>
						<select name="drug<?php echo $i?>">
							<option value="">--</option>
                        <?php foreach($aDrugs as $m=>$v){?>							
							<option value="<?php echo $m.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
                        <?php }?>	
                        </form>
					</div>
					<!-- // end Select Drug -->
                    <?php }?>
                </div>
                
<?php
$unlimited_slug = 'unlimited';
$args = array(
  'name'        => $unlimited_slug,
  'post_type'   => 'post',
  'post_status' => 'publish',
  'numberposts' => 1
);
$my_posts = get_posts($args);
$unlimitedPost = $my_posts[0];
$unlimitedPostMeta = get_post_meta($unlimitedPost->ID);
?>                
                <div id="unlimPlan" class="td">
                    <div class="td-col-4">
						<h3><?php echo $unlimitedPost->post_title?></h3>
						<h2><?php echo $unlimitedPost->post_content?></h2>
						<p><?php echo $unlimitedPostMeta['comment'][0]?></p><a name="formUnlimited"></a>
                        <?php if($current_plan == $unlimited_slug){?>
                        <div class="clearfix"></div>
						<div class="cp"><a href="<?php echo esc_url( home_url( '/' ) ); ?>account/?plan=<?php echo $unlimited_slug?>" title=""><img src="<?=get_template_directory_uri()?>/images/icon-g1.png" alt="" /><span>Current Plan</span></a></div>
                        <?php }else{?>
						<div class="details-a">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>account/?plan=<?php echo $unlimited_slug?>#formUnlimited" title="">Select This Plan</a>
						</div>
                        <?php }?>
					</div>
                </div>
                
                <div class="td">
                    					<div class="td-col-4">
						<h3>Enterprise</h3>
						<h2>ALL Drug Fact Boxes</h2>
						<p>Plus advanced features</p>
						<div class="details-a">
							<a href="#" title="" data-toggle="modal" data-target="#enterpriseModal">LEARN MORE »</a>
						</div>
					</div>
                    <!-- pop-up-2 -->
	<div class="modal fade pop-up-2" id="enterpriseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h2 class="modal-title" id="myModalLabel">Enterprise Membership</h2>
		  </div>
		  <div class="modal-body">
				You’re now being forwarded to Informulary.com’s contact 
				form where you will be able to tell us more about your 
				company and how we can assist you.
		  </div>
		  <div class="modal-footer">
			<a class="ok" href="http://www.informulary.com/enterprise" title="#">OK</a>
			<a class="cancel" href="#" title="#" data-dismiss="modal">CANCEL</a>
		  </div>
		</div>
	  </div>
	</div>
	<!-- // end pop-up-2 -->
                </div>                
                
            </div>
            <?php if($current_plan == $limited_slug and $plan != $unlimited_slug){?>
            <div id="limitedForm">
            <div class="clearfix"></div>
			<div class="details-a">
				<a title="" href="#" onclick="document.updateLimitedForm.submit();return false;">SUBMIT</a>
			</div>
            </div>
            <?}?>
             
             <?php if($plan == $limited_slug and $current_plan != $limited_slug){?>
                <div id="unsubscribe"><?php echo do_shortcode($limitedPostMeta['upgrade_form'][0])?></div>
                <div class="clearfix"></div>
			    <div class="details-a">
				    <a title="" href="#" onclick="jQuery('#s2member-pro-stripe-cancellation-form').submit();return false;">SUBMIT</a>
			    </div>
             <?php }?>
             
             <?php if($plan == $unlimited_slug){?>
             <div id="unlimitedForm">
                <?php echo do_shortcode($unlimitedPostMeta['upgrade_form'][0])?>
             </div>
             <?php }?>
        
        </div>
    </div>
    <?php endif;?>
    
</div>

<?php
get_footer();
?>