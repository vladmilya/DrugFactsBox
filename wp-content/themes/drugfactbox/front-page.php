<?php
get_header(); 
?>
<?php
$cover_img =wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
$frontpage_meta = get_post_meta($post->ID); 
$downloadfile = wp_get_attachment_url($frontpage_meta['download_button'][0]);
?>

<!-- intro -->
	<div class="container-fluid intro" style="background-image:url(<?php echo $cover_img?>);">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="intro-table">
					<div class="intro-td">
                        <div class="intro-content">
                            
                            <h3><?php echo str_replace(array('\n', '&lt;', '&gt;'), array('<br />','<','>'), get_bloginfo( 'description')); ?></h3>
                            <a href="<?php echo esc_url( home_url( '/all-drugs/' ) ); ?>" class="drugs-button">FIND A DRUG »</a>
                            <a href="#" class="signin-button" id="coverSignInBtn">SIGN IN »</a>
                            <h1><img src="<?=get_template_directory_uri()?>/images/slice-1.png" alt="" /></h1>
                            
                        </div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- // end intro -->

	<!-- what-drugfactbox -->
	<div class="container what-drugfactbox" style="margin-top:60px;">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<h2>What Is The DrugFactsBox<sup>TM</sup>?</h2>
			</div>
            <?php //slides
            $args = array(   
                'showposts'=>4,
                'category_name' => 'home-page',
                'orderby' => 'published',
                'order'   => 'ASC',
                ); 
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $img =wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
                ?>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="icon-o-table">
					<div class="icon-o-td">
						<img src="<?php echo $img?>" alt="" />
					</div>
				</div>
				<h3><?php the_title()?></h3>
				<?php the_content()?>
			</div>
            
            <?php } }?>
            <?php if(!empty($downloadfile)){?>
            <div style="clear:both"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="row">			
					<div class="details-a">
						<a title="" href="<?php echo $downloadfile?>">DOWNLOAD SAMPLE DRUG FACTS BOX »</a>
					</div>
				</div>
			</div>
            <?php }?>

		</div>
	</div>
	<!-- // end what-drugfactbox -->
    
    <?php
    //slides
    $args = array(  
        'showposts'=>10,
        'post_type' => 'drug',
        'meta_query' => array(
            array(
                'key' => 'onhomepage_display_1', 
                'value' => 'yes'
            )
        ), 
        'orderby' => 'published',
        'order'   => 'ASC',
    ); 
    $query = new WP_Query( $args );
    $query2 = $query;
    if ( $query->have_posts() ) {?> 
    
    <!-- carousel-1 -->
    <script>
		jQuery(window).load(function() {
	
			jQuery('#allinone_carousel_charming').allinone_carousel({
				skin: 'charming',
				width: 1170,
				height: 740,
				responsive:true,
				autoPlay: 3,
				resizeImages:true,
				autoHideBottomNav:false,
				showElementTitle:false,
				verticalAdjustment:50,
				showPreviewThumbs:false,
				//easing:'easeOutBounce',
				numberOfVisibleItems:5,
				nextPrevMarginTop:23,
				playMovieMarginTop:0,
				bottomNavMarginBottom:-10
			});		
			
			jQuery('.left-pag').click(function(){ 
                jQuery('.bannerControls').find('.leftNav').mousedown();
            });
            jQuery('.right-pag').click(function(){ 
                jQuery('.bannerControls').find('.rightNav').mousedown();
            });
		});
	</script>
    
    
	<div class="container-fluid position-relative carousel-1-block">
		<div class="row">
        
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<h2><?php echo $frontpage_meta['carousel_header'][0]?></h2>
            </div>
    
			<div id="allinone_carousel_charming">
            
				<div class="myloader"></div>
				
				<ul class="allinone_carousel_list">
				<?php 
                $sPopUps = '';
                $sPopUps2 = '';
                while ( $query->have_posts() ) {
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
                                $aConditions[] = $aCond;
                            }
                        }  
                        
                ?>
					<li>						
						<div class="drug-block-1">
							<div class="drug-pic"><img src="<?=get_template_directory_uri()?>/images/slide-pic2.png" alt="" /></div>
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
                                    <?php 
                                    $sPopUps.='<!-- pop-up-1 -->
                                    <div class="modal fade pop-up-1" id="conditionModal'.$post->ID.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>			</button>
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take '.$post->post_title.'</h2>
                                                </div>
                                                <div class="modal-body">';
                                                foreach($aConditions as $c){
                                                    $sPopUps.='<a href="'.$c['link'].'" title="">'.$c['name'].'</a>';
                                                }
                                                $sPopUps.='</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- // end pop-up-1 -->';                                    
                                    }?>
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
                                    <?php 
                                    $sPopUps2.='<!-- pop-up-1 -->
                                    <div class="modal fade pop-up-1" id="condition2Modal'.$post->ID.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>			</button>
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take '.$post->post_title.'</h2>
                                                </div>
                                                <div class="modal-body">';
                                                foreach($aConditions as $c){
                                                    $sPopUps2.='<a href="'.get_permalink($post->ID).''.$c['slug'].'" title="">'.$c['name'].'</a>';
                                                }
                                                $sPopUps2.='</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- // end pop-up-1 -->';  
                                    ?>                                    
                                    <?php }?>
                                <?php }else{?>
                                    <a href="<?php echo get_permalink($post->ID)?>" title="">DETAILS »</a>
                                <?php }?>
                                </div>								
							</div>
						</div>                        
					</li>
                <?php }?>
					
				</ul>    
												   
			</div>
            
            <div class="carousel-pagination">
				<div class="left-pag"><i class="fa fa-angle-left"></i></div>
				<div class="right-pag"><i class="fa fa-angle-right"></i></div>
			</div>

			<div class="container see-all-drugs">
				<div class="row">			
					<div class="details-a">
						<a title="" href="<?php echo esc_url( home_url( '/' ) ); ?>all-drugs/">SEE ALL DRUGS »</a>
					</div>
				</div>
			</div>

		</div>
	</div>
    <?php echo $sPopUps?>
    <?php echo $sPopUps2?>
	<!-- // end carousel-1 -->
    
    
    <!-- carousel-1-mobile -->

<div class="carousel-1-mobile">
<h2><?php echo $frontpage_meta['carousel_header'][0]?></h2>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    
    <?php
    $i = 0;    
    while ( $query2->have_posts() ) {                    
                    $query2->the_post();
                    $meta = get_post_meta($query2->post->ID); 
                    $conditions = isset($meta['conditions'][0]) ? @unserialize($meta['conditions'][0]) : '';
                        $aConditions = array();
                        if(!empty($conditions)){
                            foreach($conditions as $cond){
                                $condPost = get_post($cond);
                                $aCond['id'] = $condPost->ID;  
                                $aCond['slug'] = $condPost->post_name;          
                                $aCond['name'] = $condPost->post_title;
                                $aCond['link'] = get_permalink($condPost->ID);
                                $aConditions[] = $aCond;
                            }
                        }  
                        
                ?>
    <div class="item<?php if($i==0) echo ' active'?>">

		<div class="drug-block-1">
			<div class="drug-pic"><img src="<?=get_template_directory_uri()?>/images/slide-pic2.png" alt="" /></div>
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
                                    <?php }?>
                                <?php }else{?>
                                    <a href="<?php echo get_permalink($post->ID)?>" title="">DETAILS »</a>
                                <?php }?>
				</div>								
			</div>
		</div>
			
    </div>
     <?php $i++; }?>
     
  </div>

  <!-- Controls -->

    <a class="pag-mob left-pag" href="#carousel-example-generic" role="button" data-slide="prev"><i class="fa fa-angle-left"></i></a>
    <a class="pag-mob right-pag" href="#carousel-example-generic" role="button" data-slide="next"><i class="fa fa-angle-right"></i></a>

</div>

<div class="container see-all-drugs">
	<div class="row">
		<div class="details-a">
			<a href="http://get-dev.com/DrugFactBox/all-drugs/" title="">SEE ALL DRUGS »</a>
		</div>
        
	</div>
</div>

</div>
<!-- // end carousel-1-mobile -->
    
    
    <?}?>
    
    <?php wp_reset_postdata();?>    
    
    
    <?php if ( !is_user_logged_in()) {?>
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