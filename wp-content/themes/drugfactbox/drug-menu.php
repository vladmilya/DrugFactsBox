<?php 
global $page_tab, $condition, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;
$currentUser = wp_get_current_user();
if(!empty($currentUser->ID)){ 
    $userMeta = get_user_meta( $currentUser->ID );
    $pdfAllowed = isset($userMeta['pdf_export']) ? $userMeta['pdf_export'][0] : 0;
}else{
    $pdfAllowed = 0;
}
?>
    <!-- || Start go-back -->
<script type="text/javascript">jQuery(document).ready(function($){
$(window).scroll(function () {if ($(this).scrollTop() > (jQuery('.container-fluid.hp').height() + jQuery('.container-fluid.header-about').height() + jQuery('.title-h1').outerHeight(true) + jQuery('.navbar-fixed-top').height())) {$('#scrollerTopBtn').fadeIn();} else {$('#scrollerTopBtn').fadeOut();}});
$('#scrollerTopBtn').click(function () {$('body,html').animate({scrollTop: 0}, 400); return false;});
});</script>
<!-- // Stop back-block -->
<div class="in1">
	<div class="in1-top">
		<div class="in1-bars">
			<i class="fa fa-bars"></i><span>TABLE OF CONTENTS</span>            
		</div>
        <?php if($pdfAllowed){?>
        <div class="pdf-icon-mobile">
            <a href="<?php if($allowedDrug){?><?php echo get_permalink()?><?php echo @$condition->post_name?>/download-pdf<?php }else{?>#<?php }?>"><img src="<?=get_template_directory_uri()?>/images/PDF-<?php if(!$allowedDrug){?>off<?php }else{?>on<?php }?>.png" /></a>
        </div>
        <?php }?>
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
				<?php if($overview_display){?>
                <li><a <?php if($page_tab == '') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>" title="">OVERVIEW</a></li>
				<?php }?>
                <?php /*<li><a <?php if($page_tab === 'prices') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo $condition->post_name?>/prices" title="">PRICES</a></li>*/?>
				<li><a <?php if($page_tab === 'trials') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/trials" title="">TRIALS<?php if(!$allowedDrug){?><i class="fa fa-lock"></i><?php }?></a></li>
				<li><a <?php if($page_tab === 'side-effects') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/side-effects" title="">SIDE EFFECTS<?php if(!$allowedDrug){?><i class="fa fa-lock"></i><?php }?></a></li>
				<?php if($how_to_use_display){?>
                <li><a <?php if($page_tab === 'how-to-use') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/how-to-use" title="">HOW TO USE<?php if(!$allowedDrug){?><i class="fa fa-lock"></i><?php }?></a></li>
				<?php }?>
                <?php if($precautions_display){?>
                <li><a <?php if($page_tab === 'precautions') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/precautions" title="">LIFESTYLE CHANGES<?php if(!$allowedDrug){?><i class="fa fa-lock"></i><?php }?></a></li>
                <?php }?>
                <?php if($interactions_display){?>
				<li><a <?php if($page_tab === 'interactions') echo 'class="active" '?>href="<?php echo get_permalink()?><?php echo @$condition->post_name?>/interactions" title="">INTERACTIONS<?php if(!$allowedDrug){?><i class="fa fa-lock"></i><?php }?></a></li>
                <?php }?>
                <?php if($pdfAllowed){?>
                <li class="pdf-link">
                    <a href="<?php if($allowedDrug){?><?php echo get_permalink()?><?php echo @$condition->post_name?>/download-pdf<?php }else{?>#<?php }?>">
                        <span class="pdf-img"><img src="<?=get_template_directory_uri()?>/images/PDF-<?php if(!$allowedDrug){?>off<?php }else{?>on<?php }?>-1.png"></span>
                        <span class="pdf-text"><span class="pdf-get">GET PDF <span class="pdf-version">VERSION</span></span>
                    </a>
                </li>                
                <?php }?>
                 <li class="pdf-link rtt" id="scrollerTopBtn" style="display:none">
                    <a href="">
                        <span class="pdf-text"><span class="pdf-version">return <span class="pdf-get">to top</span></span></span>
                        <span class="pdf-img"><i class="fa fa-arrow-circle-o-up"></i></span>
                    </a>
                 </li>
            </ul>            
		</div>
	</div>	
</div>