<?php 
get_header();
?>

<?php
if ( is_user_logged_in() ) {
    header("Location: ".esc_url( home_url( '/' ) )."account/");
}
?>

<?php
    $plan = addslashes(htmlspecialchars(@$_GET['plan']));
?>
<style>
.selectedDrugs{visibility:hidden}
#s2member-pro-stripe-registration-form-custom-fields-section{
    /*visibility:hidden;
    height:0;
    overflow:hidden;*/
}
#s2member-pro-stripe-registration-form-custom-reg-field-selected-drugs-label{
    display:none!important;
}
#s2member-pro-stripe-checkout-form-custom-fields-section-title{
    display:none!important;
}
#s2member-pro-stripe-registration-form-custom-fields-section-title{
    display:none!important;
}
#s2member-pro-stripe-checkout-form-custom-fields-section{
    position:relative !important;
}
form.s2member-pro-stripe-form{
    min-height:600px;
}

#s2member-pro-stripe-registration-form-custom-reg-field-accept-div{
    position:relative !important;
}
#s2member-pro-stripe-checkout-form-source-token-summary{
    margin-top: 20px;
}
div.s2member-pro-stripe-form-section > div.s2member-pro-stripe-form-div button.s2member-pro-stripe-form-source-token-button::before{
    float:none!important;
}
</style>
<!-- JS FOR SELECT -->
	<script>    
        var pageName = 'sign-up';
		(function($) {
		$(function() {         
             if(isMobile.iPhone()){   
                $('.jq-selectbox').css('overflow', 'hidden');
                $('.jq-selectbox').find('.dropdown').css('visibility','hidden');
             }
		})
		})(jQuery)
	</script>
	<!-- / END JS FOR SELECT -->
    
<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php the_title()?></h1>	
		</div>
	</div>
</div>
<!-- // end title-h1 -->
    
<form action="#" method="post" id="planForm">
<!-- signup-1 -->
<div class="signup-1">
	
	<hr />
	
	<div class="container">
		<div class="row">	
			<h2>1. Select Membership Plan</h2>
			<p>All plans will give you full access to information on:</p>
		</div>
	</div>
	
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
?>			
				<div id="limPlan" class="td<?php if($plan == $limited_slug) echo ' td-active'?>">

					<div class="td-col-4">
						<h3><?php echo $limitedPost->post_title?></h3>
						<h2><?php echo $limitedPost->post_content?></h2>
						<p><?php echo $limitedPostMeta['comment'][0]?></p><a name="formLimited"></a> 
						<div class="details-a">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>sign-up/?plan=<?php echo $limited_slug?>#formLimited" title="">Select This Plan</a>
						</div>
					</div>
                    <?php if($plan == $limited_slug){?>
					<!-- Select Drug -->
                    <?php
                    $drug_result = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='drug' ORDER BY post_title ASC");
                    
                    /*$args = array(  
                    'showposts'=>-1,
                    'post_type' => 'drug',                    
                    'orderby' => 'post_title',
                    'order'   => 'ASC'
                    ); 
                    $query = new WP_Query( $args );*/
                    $aDrugs = array();
                    if ( !empty($drug_result) ) {
                        foreach( $drug_result as $drug) {
                            $aDrug = array();
                            $aDrug['name'] = $drug->post_title;
                            $aDrug['id'] = $drug->ID;
                            $aDrugs[$drug->ID] = $aDrug;
                        }
                    }
                    ?>
                    <script>
                    jQuery(document).ready(function(){                       
                        jQuery('.select-drug-block').find('select').each(function(){
                            var sel = jQuery(this);
                            sel.change(function(){ 
                                var selectedDrugs='';
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
					<div class="select-drug-block"> 
						<h4>Your DrugFactsBox Library</h4>
						<p>You can choose up to 5 drugs – one at a time or multiple at once. Choose now or at any time.
Once you click Submit, your choices are locked in. Upgrade to have unlimited access to the full library.</p>
                        <?php if(!empty($aDrugs)){?>
						<select name="drug1">
                            <option value="">--</option>
                        <?php foreach($aDrugs as $k=>$v){?>							
							<option value="<?php echo $k.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
						<select name="drug2">
							<option value="">--</option>
                        <?php foreach($aDrugs as $k=>$v){?>							
							<option value="<?php echo $k.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
						<select name="drug3">
							<option value="">--</option>
                        <?php foreach($aDrugs as $k=>$v){?>							
							<option value="<?php echo $k.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
						<select name="drug4">
							<option value="">--</option>
                        <?php foreach($aDrugs as $k=>$v){?>							
							<option value="<?php echo $k.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
						<select name="drug5">
							<option value="">--</option>
                        <?php foreach($aDrugs as $k=>$v){?>							
							<option value="<?php echo $k.'::'.$v['name']?>"><?php echo $v['name']?></option>
                        <?php }?>
						</select>
                        <?php }?>
					</div>
                    <?php wp_reset_query(); ?>
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
				<div id="unlimPlan" class="td<?php if($plan == $unlimited_slug) echo ' td-active'?>">
					<div class="td-col-4">
						<h3><?php echo $unlimitedPost->post_title?></h3>
						<h2><?php echo $unlimitedPost->post_content?></h2>
						<p><?php echo $unlimitedPostMeta['comment'][0]?></p><a name="formUnlimited"></a>
						<div class="details-a">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>sign-up/?plan=<?php echo $unlimited_slug?>#formUnlimited" title="">Select This Plan</a>
						</div>
                        
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
			<!-- // end signup-table -->
			
		</div>
	</div>
	
				
	

	
</div>
<!-- // end signup-1 -->    
</form> 

<?php if($plan == $limited_slug){?>
<div id="limitedForm">
    <?php echo do_shortcode($limitedPostMeta['payment_form'][0])?>
</div>
<?php }?>
<?php if($plan == $unlimited_slug){?>
<div id="unlimitedForm">
    <?php echo do_shortcode($unlimitedPostMeta['payment_form'][0])?>
</div>
<?php }?>
    
<?php
get_footer();
?>