 <?php
global $loggedUser;
$loggedUser = wp_get_current_user();
if(is_user_logged_in() and is_unc() and $post->post_name != 'member'){
    if(!strpos($_SERVER['REQUEST_URI'], 'all-drugs') and !strpos($_SERVER['REQUEST_URI'], 'drug/') and !strpos($_SERVER['REQUEST_URI'], 'condition/')){
        header("Location: ".esc_url( home_url( '/all-drugs/' ) ));
    }
}
//allowed conditions
$aGrantedConditions = array();
if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
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
    if(isset($aGrantedConditions)){
        $aGrantedConditions = array_values(array_unique($aGrantedConditions));
    }
}
?>
<?php if(!strpos($_SERVER['REQUEST_URI'], 'download-pdf')){?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php bloginfo( 'name' ); ?> <?php wp_title( '|', true, 'left' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!-- Web Fonts -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,300&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Dosis:400,200,300,500,600,700,800' rel='stylesheet' type='text/css'>
    
    <link rel='stylesheet' id='main-style'  href='<?php echo get_stylesheet_uri(); ?>' type='text/css' media='all' />
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon" />
    <!--[if IE]><script src="<?php echo get_template_directory_uri(); ?>/js/excanvas.compiled.js" type="text/javascript"></script><![endif]-->
	<?php wp_head(); ?>  
    <script src="<?=get_template_directory_uri()?>/js/jquery.formstyler.min.js"></script>
    <script>
		(function($) {
		$(function() {
            $('select').styler();      
			
            $('#accTypeTab').find('li').find('a').each(function(){
                $(this).click(function(){
                    $('.tab-1t a[href="'+$(this).attr('href')+'"]').tab('show');
                    return false;
                });
            });
		});
		})(jQuery)
	</script>
    <script>
    jQuery(document).ready(function() {        
        
    var iOS = !!navigator.userAgent.match(/iPad/i) || !!navigator.userAgent.match(/iPod/i) || !!navigator.userAgent.match(/iPhone/i);
    if (iOS) {
      var screenTop;
        jQuery(window).scroll(function(){
            screenTop = jQuery(window).scrollTop();
            jQuery('.fixfixed').find('.navbar').css('top',screenTop);
        });
      jQuery(document)
        .on('focus', 'input, select', function(e) {
            jQuery('body').addClass('fixfixed');
            jQuery(window).scrollTop(screenTop);
            jQuery('.fixfixed').find('.navbar').css('top',screenTop);
        })
        .on('blur', 'input, select', function(e) {
            jQuery('body').removeClass('fixfixed');
            jQuery('.navbar').css('top',0);        
        });
        //if(isMobile.iPhone()){   
            jQuery('.jq-selectbox').css('overflow', 'hidden');
            jQuery('.jq-selectbox').find('.dropdown').css('visibility','hidden');
        //}
    }
    
    <?php if(isset($_GET['login']) and $_GET['login']=='failed'){?>
    jQuery('#sign-in-switcher').click();  
        <?php if(isset($_GET['tab']) and $_GET['tab'] == 'enterprise'){?> 
    jQuery('#enterpriseTabLbel').click();  
        <?php }?>    
    <?php }?>    

});
    </script>
    <style>
    .fixfixed .navbar {
  position: absolute!important;  
}
input.failed{border: 3px solid #f00!important;}
.failed_text{color: #f00;
    font-weight:bold;
    left: 16px;
    margin: 0 0 10px;
    position: relative;
}
    </style>

    <script type="text/javascript">
      window.heap=window.heap||[],heap.load=function(e,t){window.heap.appid=e,window.heap.config=t=t||{};var r=t.forceSSL||"https:"===document.location.protocol,a=document.createElement("script");a.type="text/javascript",a.async=!0,a.src=(r?"https:":"http:")+"//cdn.heapanalytics.com/js/heap-"+e+".js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n);for(var o=function(e){return function(){heap.push([e].concat(Array.prototype.slice.call(arguments,0)))}},p=["addEventProperties","addUserProperties","clearEventProperties","identify","removeEventProperty","setEventProperties","track","unsetEventProperty"],c=0;c<p.length;c++)heap[p[c]]=o(p[c])};
        heap.load("2533091710");
  </script>

</head>
<body <?php body_class(); ?>>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KKN7NX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KKN7NX');</script>
<!-- End Google Tag Manager -->
<?php

$mainPost = $post;
$args = array(  
        'showposts'=>-1,
        'post_type' => 'condition',        
        'orderby' => 'post_title',
        'order'   => 'ASC',
    ); 
if(is_user_logged_in() and (is_unc() or current_user_is("s2member_level1") or current_user_is("s2member_level2"))){
    if(!empty($aGrantedConditions)){
        $args['post__in'] = $aGrantedConditions;
    }else{
        if(is_unc()){
            $args['post__in'] = array('no');
        }
    }
}
$query = new WP_Query( $args );
global $aGlobalConditions;
$aGlobalConditions = array();
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $aCond['id'] = $post->ID;
        $aCond['name'] = $post->post_title;
        $aCond['link'] = get_permalink($post->ID);
        $aGlobalConditions[] = $aCond;
    }
}

?>


<?php wp_reset_postdata();?>   

<!-- logo + nav -->
	<div class="container-fluid">
		<div class="row">
			<nav class="navbar navbar-default navbar-fixed-top">
                <?php if(@$mainPost->post_type === 'drug' or @$mainPost->post_type === 'condition'){?>
                <div class="navbar-header notvisible-span">
                    
                    <h2 class="navbar-brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>#content"><i class="fa fa-bars"></i><span><?php echo $mainPost->post_title?></span><?php if(!is_user_logged_in() or !is_unc()){?><img class="for-desktop" src="<?=get_template_directory_uri()?>/images/logo2.png" alt="" /><img class="for-mobile" src="<?=get_template_directory_uri()?>/images/logo2-mobile.png" alt="" /><?php }?></a></h2>
                    
                </div>
                <?php }else{?>
				<div class="navbar-header">
                    
                    <h2 class="navbar-brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>#content"><?php if(!is_user_logged_in() or !is_unc()){?><img class="for-desktop" src="<?=get_template_directory_uri()?>/images/logo2.png" alt="" /><img class="for-mobile" src="<?=get_template_directory_uri()?>/images/logo2-mobile.png" alt="" /><?php }?></a></h2>
                    
                </div>
                <?php }?>
                <div class="top-menu-block">
				  <ul class="top-menu">
					<li<?php if($post->post_name == 'all-drugs') echo " class='active'"?>>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>all-drugs/">
                            <img src="<?=get_template_directory_uri()?>/images/icon-nav1.png" alt="" />
                            <span>DRUG FACTS BOXES</span>
                        </a>
                    </li>                   
                    <li class="dropdown<?php if($post->post_type == 'condition') echo " active"?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" id="conditionsMenuItem">
							<img src="<?=get_template_directory_uri()?>/images/icon-nav2.png" alt="" />
							<span>CONDITIONS</span>
						</a>
                        <div class="dropdown-menu links-m">
							<div class="container">
								<div class="row">
									
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="row">
										
										<h3>Conditions</h3>
										<?php
                                        $numGroups = 4;
                                        $numConditions = !empty($aGlobalConditions) ? count($aGlobalConditions) : 0; 
                                        $itemsPerGroup = ceil($numConditions/$numGroups); 
                                        $conditionGroups = array();
                                        for($n=0; $n < $numGroups; $n++){ 
                                            $groupContent = array();
                                            for($i=$itemsPerGroup*$n; $i<$itemsPerGroup*($n+1); $i++){//dump($i);
                                                if(isset($aGlobalConditions[$i])){
                                                    $groupContent[] = $aGlobalConditions[$i];       
                                                }
                                            }
                                            $conditionGroups[$n] = $groupContent;
                                        }
                                        foreach($conditionGroups as $group){?>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <?php 
                                            foreach($group as $cond){
                                                if(!empty($cond)){?>
											<a href="<?php echo $cond['link']?>" title=""><?php echo $cond['name']?></a>
                                            <?php } }?>
										</div>
										<?php }?>									
										
										</div>
									</div>

								</div>
							</div>
						</div>		
				    </li>
                     <?php if(!is_user_logged_in() or !is_unc()){?>
                    <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" id="searchMenuItem">
							<img src="<?=get_template_directory_uri()?>/images/icon-nav3.png" alt="" />
							<span>SEARCH</span>
						</a>
						<div class="dropdown-menu">
							<div class="container">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 search-div search-div">
										<form id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
											 <div class="search-input">
												<input id="s" class="form-control ui-autocomplete-input" type="text" name="s" value="<?php echo @$_GET['s']?>" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" placeholder="Search" />
											 </div>
											 <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
										 </form>
									</div>
								</div>
							</div>
						</div>
					</li>
                    <li class="dropdown">
                        <?php if ( is_user_logged_in() ) {?>
                            <?php if(current_user_is("s2member_level2")){?>
                         <a href=" <?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>" class="dropdown-toggle">
							<img src="<?=get_template_directory_uri()?>/images/icon-nav4.png" alt="" />
							<span>SIGN OUT</span>
						</a>
                            <?php }else{?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="<?=get_template_directory_uri()?>/images/icon-nav4.png" alt="" />
							<span>USER</span>
						</a>
                        <div class="dropdown-menu">	
								<!-- start cago -->
								<div class="container cago">
									<div class="row">
									    <?php 
                                        if ( is_user_logged_in() ) {
                                            $currentUser = wp_get_current_user();
                                        }
                                        ?>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<h3><?php echo $currentUser->data->display_name?></h3>
											</div>
										</div>
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<a href="<?php echo esc_url( home_url( '/' ) ); ?>account/" title="">My Account</a>
											</div>
										</div>
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<a href="<?php echo esc_url( home_url( '/' ) ); ?>profile/" title="">My Profile</a>
											</div>
										</div>
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<a class="so" href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>" title="">Sign Out</a>
											</div>
										</div>
										
									</div>
								</div>
								<!-- stop cago -->
								
							</div>
                            <?php }?>                        
                        <?php }else{?>
						<a href="#" class="dropdown-toggle" id="sign-in-switcher" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							<img src="<?=get_template_directory_uri()?>/images/icon-nav4.png" alt="" />
							<span>SIGN IN</span>
						</a>
                        <div class="dropdown-menu" id="loginDropdown">
                        
                        	<ul class="tab-1t" role="tablist" id="accTypeTab">
                                <li role="presentation" class="active"><a href="#r-a" aria-controls="r-a" role="tab" data-toggle="tab">INDIVIDUAL ACCOUNT</a></li>
                                <li role="presentation"><a href="#e-a" aria-controls="e-a" role="tab" data-toggle="tab" id="enterpriseTabLbel">ENTERPRISE ACCOUNT</a></li>
                            </ul>
                            
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="r-a">
                                    <div class="container signin-block">
								    <div class="row">
                                    
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<form method="post" name="loginform" id="loginform" action="<?php echo esc_url( home_url( '/' ) ); ?>wp-login.php">
                                            <input type="hidden" name="redirect_to" value="" />
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<h3>User Sign In
														<div class="close-x">
															<a class="dropdown-toggle" title="" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																<img alt="" src="<?=get_template_directory_uri()?>/images/close-f.png">
															</a>
														</div>													
													</h3>
													<div class="signin-block-table1">
														<span><img src="<?=get_template_directory_uri()?>/images/icon-4.png" alt="" /></span>
														<a href="<?php echo esc_url( home_url( '/' ) ); ?>sign-up/" title="">Create new account</a>
													</div>
												</div>
											</div>
                                            <?php if(isset($_GET['login']) and $_GET['login']=='failed'){?>
                                            <div class="failed_text">Login information is invalid. Please try again.</div>
                                            <?php }?>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<input type="text" name="log" class="form-control<?php if(isset($_GET['login']) and $_GET['login']=='failed'){?> failed<?php }?>" id="" placeholder="Email" />
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<input type="password" name="pwd" class="form-control<?php if(isset($_GET['login']) and $_GET['login']=='failed'){?> failed<?php }?>" id="" placeholder="Password" />
													<a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#individualRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<button type="submit" name="wp-submit" class="btn btn-default">Sign in</button>
													<?/*<a class="for-mobile" href="<?php echo wp_lostpassword_url( $_SERVER['REQUEST_URI'] ); ?>" title="">Forgot password?</a>*/?>
												</div>
											</div>
										</form>	
									</div>
								    </div>
							        </div>
                                    
                                </div>
                                
                                <div role="tabpanel" class="tab-pane" id="e-a">
                                    <div class="container signin-block">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <form action="<?php echo esc_url( home_url( '/' ) ); ?>wp-login.php" method="post" name="">
                                                <input type="hidden" name="redirect_to" value="" />
                                                <input type="hidden" name="enterprise" value="1" />
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <div class="form-group">
                                                            <h3>User Sign In
                                                                <div class="close-x">
                                                                    <a title="" href="#" data-toggle="modal2" data-target="#myModal">
                                                                        <img alt="" src="<?=get_template_directory_uri()?>/images/close-f.png">
                                                                    </a>
                                                                </div>													
                                                            </h3>
                                                        </div>
                                                    </div>
                                                    <?php if(isset($_GET['login']) and $_GET['login']=='failed'){?>
                                            <div class="failed_text">Login information is invalid. Please try again.</div>
                                            <?php }?>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <div class="form-group">
                                                        <input type="text" name="log" class="form-control<?php if(isset($_GET['login']) and $_GET['login']=='failed'){?> failed<?php }?>" placeholder="Username" />
                                                        <a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestNameModal"  aria-haspopup="true" aria-expanded="true" title="" id="forgotEntUserNameLink">Forgot username?</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <div class="form-group">
                                                            <input type="password" name="pwd" class="form-control<?php if(isset($_GET['login']) and $_GET['login']=='failed'){?> failed<?php }?>" id="" placeholder="Password" />
                                                            <a class="for-desktop dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <div class="form-group">
                                                            <button type="submit" name="wp-submit" class="btn btn-default">Sign in</button>
                                                            <?/*
                                                            <a class="for-mobile dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestNameModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot User Name?</a>
                                                            <a class="for-mobile dropdown-toggle" href="#" data-toggle="modal" data-target="#enterpriseRestModal"  aria-haspopup="true" aria-expanded="true" title="">Forgot password?</a>  
                                                            */?>
                                                        </div>
                                                    </div>
                                                </form>	
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            </div>							 
							 
						</div>
						<?php }?>
					</li>	
                    <?php }?>                    
				  </ul>
				  </div>
			</nav>
		</div>
	</div>
    <?php //get_search_form( true ); ?>
<!-- // end logo + nav -->  
    <!-- individual client -->
	<div class="modal fade pop-up-2" id="individualRestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<a style="position:absolute; top:20px; right:20px; z-index:2;" href="#" title="#" data-dismiss="modal"><img src="<?=get_template_directory_uri()?>/images/close-1.png" alt="" /></a>
			<h2 class="modal-title" id="myModalLabel">Forgot Password?</h2>
		  </div>
          <form id="lostpasswordform" method="post" action="<?php echo wp_lostpassword_url( $_SERVER['REQUEST_URI'] ); ?>" name="lostpasswordform">
		  <div class="modal-body" style="text-align:center">
				To receive password via email, please enter your account's e-mail address:
				<div class="clearfix"></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div style="margin-top:30px;">
						<input type="text" class="form-control" id="" placeholder="Email" name="user_login"/>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="clearfix"></div>
		  </div>
		  <div class="modal-footer">
			<a class="ok" href="#" title="#" data-dismiss="modal" onclick="document.lostpasswordform.submit()">SUBMIT</a>
		  </div>
          </form>
		</div>
	  </div>
	</div>
	<!-- // end individual client -->
    
    <!-- enterprise client -->
	<div class="modal fade pop-up-2" id="enterpriseRestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<a style="position:absolute; top:20px; right:20px; z-index:2;" href="#" title="#" data-dismiss="modal"><img src="<?=get_template_directory_uri()?>/images/close-1.png" alt="" /></a>
			<h2 class="modal-title" id="myModalLabel">Forgot Password?</h2>
		  </div>
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" name="enterpriseform">
          <input type="hidden" name="restore_pass_sent" value="1"/>
		  <div class="modal-body" style="text-align:center">
				No problem. Let us know your company's username and your email, and we will send you the password:
				<div class="clearfix"></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div style="margin-top:30px;">
						<div style="margin-top:30px;">
                                <input type="text" class="form-control" id="" placeholder="Enterprise user name" name="username"/>
                        </div>
                        <div style="margin-top:30px;">
                            <input type="text" class="form-control" id="" placeholder="Your email address" name="email_address"/>
                        </div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="clearfix"></div>
		  </div>
		  <div class="modal-footer">
			<a class="ok" href="#" title="#" data-dismiss="modal" onclick="document.enterpriseform.submit()">SUBMIT</a>
		  </div>
          </form>
		</div>
	  </div>
	</div>
    
    <div class="modal fade pop-up-2" id="enterpriseRestNameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a style="position:absolute; top:20px; right:20px; z-index:2;" href="#" title="#" data-dismiss="modal"><img src="<?=get_template_directory_uri()?>/images/close-1.png" alt="" /></a>
                    <h2 class="modal-title" id="myModalLabel">Forgot Username?</h2>
                </div>
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" name="enterprisenameform">
                    <input type="hidden" name="restore_name_sent" value="1"/>
                    <div class="modal-body" style="text-align:center">
                        To recover enterprise user name, please enter the following information: 
                        <div class="clearfix"></div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <div style="margin-top:30px;">
                                <input type="text" class="form-control" id="" placeholder="Company Name" name="company_name"/>
                            </div>
                            <div style="margin-top:30px;">
                                <input type="text" class="form-control" id="" placeholder="Your Name" name="user_name"/>
                            </div>
                            <div style="margin-top:30px;">
                                <input type="text" class="form-control" id="" placeholder="Email Address" name="email_address"/>
                            </div>
                            
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        <a class="ok" href="#" title="#" data-dismiss="modal" onclick="document.enterprisenameform.submit()">SUBMIT</a>
                    </div>
                </form>
            </div>           
        </div>
    </div>
	<!-- // end individual client -->
    <?php
    if(isset($_POST['restore_pass_sent'])){        
        $recoverEmail = $_POST['email_address'];
        if(!empty($recoverEmail) and preg_match("/^[a-z_0-9!#*=.-]+@([a-z0-9-]+\.)+[a-z]{2,6}$/i", $recoverEmail) and !empty($_POST['username'])){
            $enterpriseUser = get_user_by( 'login', $_POST['username'] );
            if(!empty($enterpriseUser)){               
                $pass_to_resore = get_the_author_meta('password_res', $enterpriseUser->ID);
                if(!empty($pass_to_resore)){
                    $headers = "From: DrugFactsBox <customers@informulary.com> \r\n";
                    $subj = 'Your Drugfactsbox.co account password';
                    $message.= "Use this password to login to http://drugfactsbox.co : ".$pass_to_resore. "\r\n";           
                    wp_mail( $recoverEmail, $subj, $message, $headers);
                }
            }
        }?>
                <!-- enterprise client -->
    <a href="#" id="hiddenEmailModal" class="dropdown-toggle" data-toggle="modal" data-target="#emailModal"  aria-haspopup="true" aria-expanded="true" title=""></a>
    <script>
    jQuery(document).ready(function(){
        jQuery('#hiddenEmailModal').click();
        jQuery('#reopenRestoreEntUsername').click(function(){
            jQuery('#closeForgotEntPass').click();
            jQuery('#forgotEntUserNameLink').click();
            return false;
        });
    });
    </script>
	<div class="modal fade pop-up-2" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<a style="position:absolute; top:20px; right:20px; z-index:2;" href="#" title="#" data-dismiss="modal"><img src="<?=get_template_directory_uri()?>/images/close-1.png" alt="" id="closeForgotEntPass"/></a>
			<h2 class="modal-title" id="myModalLabel"><?php if(!empty($enterpriseUser) and preg_match("/^[a-z_0-9!#*=.-]+@([a-z0-9-]+\.)+[a-z]{2,6}$/i", $recoverEmail)){?>Success!<?php }else{?>Hmmm<?}?></h2>
		  </div>
		  <div class="modal-body" style="text-align:center">
				<div class="clearfix"></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div style="margin-top:0px;">
                    <?php if(preg_match("/^[a-z_0-9!#*=.-]+@([a-z0-9-]+\.)+[a-z]{2,6}$/i", $recoverEmail)){?>
                        <?php if(!empty($enterpriseUser)){?>
                        Check your email to find your company's password. If it's not in your inbox, check your spam folder. If you still can't find it, please email us at customersuccess@informulary.com and we'll help you out.
                        <?php }else{?>
						We didn't recognize the enterprise user name you have entered. <a href="#" id="reopenRestoreEntUsername">Forgot your username?</a>
                        <?php }?>  
                    <?php }else{?>
                        We didn't recognize the email you have entered as a valid email address. Please check entered data and try again
                    <?php }?>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
				<div class="clearfix"></div>
		  </div>
		</div>
	  </div>
	</div>
	<!-- // end individual client -->
        <?php } ?>
    <?php
    if(isset($_POST['restore_name_sent'])){
        $adminUser = get_user_by( 'login', 'admin' );
        $adminEmail = $adminUser->data->user_email.', customers@informulary.com';
        if(!empty($_POST['email_address']) and preg_match("/^[a-z_0-9!#*=.-]+@([a-z0-9-]+\.)+[a-z]{2,6}$/i", $_POST['email_address'])){
            $headers = 'From: '.htmlspecialchars($_POST['user_name']).' - '.htmlspecialchars($_POST['company_name']).' <'.$_POST['email_address'].'>' . "\r\n";
            $subj = 'Drugfactsbox.co Forget User Name Request';
            $message.= "Company name: ".htmlspecialchars($_POST['company_name']). "\r\n";
            $message.= "User name: ".htmlspecialchars($_POST['user_name']). "\r\n";
            $message.= "Email address: ".htmlspecialchars($_POST['email_address']). "\r\n";            
            $result = wp_mail( $adminEmail, $subj, $message, $headers);
            if($result){
                $respHeader = 'Success!';
                $respText  = 'A customer success representative from Informulary will follow up with you directly via email.';
            }else{
                $respHeader = 'Sorry!';
                $respText = 'We cant process your request at the moment. Please try again later or contact us via '.$adminEmail;
            }
        }else{
            $respHeader = 'Hmmm';
            $respText = 'We didn\'t recognize the email you have entered as a valid email address. Please check entered data and try again';
        }
        ?>
        <a href="#" id="hiddenEmailNameModal" class="dropdown-toggle" data-toggle="modal" data-target="#emailNameModal"  aria-haspopup="true" aria-expanded="true" title=""></a>
        <script>
        jQuery(document).ready(function(){
            jQuery('#hiddenEmailNameModal').click();
        });
        </script>
        <div class="modal fade pop-up-2" id="emailNameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <a style="position:absolute; top:20px; right:20px; z-index:2;" href="#" title="#" data-dismiss="modal"><img src="<?=get_template_directory_uri()?>/images/close-1.png" alt="" /></a>
                        <h2 class="modal-title" id="myModalLabel"><?php echo $respHeader?></h2>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        <div class="clearfix"></div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <div style="margin-top:0px;">
                                <?php echo $respText?>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <?
        
    }
    ?>
<?php }else{//pdf ?>

<?}?>