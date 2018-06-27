<?php status_header( 404 );
  get_template_part( 404 ); exit();
//dump($post);
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $overview_display, $how_to_use_display, $precautions_display, $interactions_display;

$theme_folder = get_template_directory_uri();
?>
<style>
.poweredLogo{
    text-align: center;
}
.text-1p.centered{
    text-align:center!important;
}
.pull-right.centered{
    float:none!important;
    text-align:center!important;
}
.jcarousel-control-prev .fa{
    margin-left:16px;
}
.jcarousel-control-next .fa{
    margin-left:-16px;
}
.jq-selectbox .select .text{
    line-height:32px;
}
</style>
	<!-- || start js jcarousel 2 -->
	<script type="text/javascript" src="<?php echo $theme_folder?>/js/jquery.jcarousel.min.js"></script>
	<script type="text/javascript" src="<?php echo $theme_folder?>/js/jcarousel.responsive.js"></script>
	<!-- // stop js jcarousel 2 -->
    <script>
    jQuery(document).ready(function(){
        jQuery('.dosages').css('display','none');
        jQuery('.dosages').eq(0).css('display','block');
        jQuery('#dosages').change(function(){
            jQuery('.dosages').css('display','none');
            var selected = jQuery(this).val();
            jQuery('.'+selected).css('display','block');
        });
    });
    </script>
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

<!-- content-product -->
<div class="container content-block">
	<div class="row">
	
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>Where to get the best price?</h2>            
		</div>
		
		<div class="clearfix"></div>
		
		<div class="content" style="text-align:center">
		<?php //echo do_shortcode('[gmw_current_location elements="address" user_message="" guest_message="" title="" address_fields="street, city, state, zipcode, country, address" address_as_text=1]'); ?>
        <?php //dump(get_user_location());?>

        <?php
            //drug info
            $title = get_the_title();
            $first_letter=strtolower($title[0]);
            $title[0] = $first_letter;
            $url = 'https://api.goodrx.com/drug-info';
            $params = 'name='.urlencode($title).'&api_key=68c15f7811';
            $sig = hash_hmac('sha256', $params, 'fwAh6winBhk2IERjJdnF0A==', true);
            $sig = base64_encode($sig);
            $sig = str_replace('+', '_', $sig);
            $sig = str_replace('/', '_', $sig);
            $link = $url.'?'.$params.'&sig='.$sig;
            $result = @file_get_contents($link);
            $aResultInfo = json_decode($result, true);
            $aDataInfo = $aResultInfo['data'];
            //dump($link);
            //dump($aDataInfo);
            
            $aOptions = array();
            
            $aDrugs = array();
            if(!empty($aDataInfo)){
                $aDrug = $aDataInfo['quantities'];
                $num = 0;
                $aDetails = array();
                foreach($aDrug as $form=>$aDosage){
                    
                    if(!empty($aDosage)){
                        foreach($aDosage as $dose=>$aQuantity){ 
                        
                            $aOptions[] = $form.' '.$dose;
                        
                            $info = array();
                            $info['name'] = $title;
                            $info['form'] = $form;
                            $info['dosage'] = $dose;
                            $aPrices = array();
                            if(!empty($aQuantity)){ //dump($aQuantity);
                                $qty = $aQuantity[0];
                                foreach($aQuantity as $drugqty){
                                    if($drugqty == 30){
                                        $qty = 30;
                                    }
                                }
                                //foreach($aQuantity as $qty){
                                    //lowest price
                                    
                                    
                                    if($num == 0){
                                        $url2 = 'https://api.goodrx.com/low-price';
                                        $params2 = 'name='.urlencode($title).'&form='.urlencode($form).'&dosage='.urlencode($dose).'&quantity='.$qty.'&api_key=68c15f7811';
                                        $sig2 = hash_hmac('sha256', $params2, 'fwAh6winBhk2IERjJdnF0A==', true);
                                        $sig2 = base64_encode($sig2);
                                        $sig2 = str_replace('+', '_', $sig2);
                                        $sig2 = str_replace('/', '_', $sig2);
                                        $link2 = $url2.'?'.$params2.'&sig='.$sig2;
                                        $result2 = @file_get_contents($link2);
                                        $aDetails = json_decode($result2, true); //dump($aDetails);
                                    }                                    
                                                                        
                                    //if(!empty($aResult2)){
                                        $aResult2['data']['url'] = $aDataInfo['drugs'][$form][$dose];
                                        $aPrices[] = $aResult2['data'];                                        
                                    //}
                                    
                                //}
                            }                           
                            $info['display'] = $aDetails['data']['display'] ? $aDetails['data']['display'] : $title;
                            $info['manufacturer'] = @$aDetails['data']['manufacturer'] ? $aDetails['data']['manufacturer'] : '';
                            $signature = $title.'_'.$form.'_'.str_replace('/', ' ', $dose).'_'.$info['manufacturer'];
                            $aImages = array();
                            if ($folder = @opendir(get_template_directory().'/images/pill_images/'.$signature)) {
                                while (false !== ($entry = readdir($folder))) {
                                    if ($entry != "." && $entry != "..") {
                                        $aImages[] =  $theme_folder.'/images/pill_images/'.$signature.'/'.$entry;
                                    }
                                }
                                closedir($folder);
                            }
                            $info['images'] = $aImages;
                            $info['prices'] = $aPrices;
                            
                            $aDrugs[] = $info;
                            $num++;
                        }
                    }
                    
                }
            }
            //dump($aDrugs);
            
            
            /*$aDataDrugs = array();
            if(!empty($aDataInfo)){
                $tablets = $aDataInfo['quantities'];
                foreach($tablets as $form=>$aDosage){
                    if(!empty($aDosage)){                        
                        foreach($aDosage as $dose=>$aQuantity){ 
                            $aDrugInfo = array();
                            $aDrugInfo['dosage'] = $dose;
                            
                            //$aPacks = $aDataInfo['quantities']['tablet'][$dose];  
                            $imgUrl = '';
                            $drugPage = $aDataInfo['drugs'][$form][$dose];  //dump($drugPage); 
                            $drugPageContent = file_get_contents($drugPage);
                            $doc = new DOMDocument();
                            $doc->loadHTML($drugPageContent);
                            $xpath = new DOMXPath($doc);
                            $imageLink = $xpath->query("//a[@class='tiny-text text-center block drug-image-link pos-relative inverted']"); 
                            if(!empty($imageLink)){
                                foreach($imageLink as $link){
                                    $images = $link->getElementsByTagName('img'); 
                                    if(!empty($images)){
                                        foreach($images as $img){
                                            $imgUrl = $img->getAttribute('src');
                                        }
                                    }
                                }
                            }
                            unset($drugPageContent);
                            unset($doc);
                            unset($xpath);
                            
                            if(!empty($aQuantity)){
                                foreach($aQuantity as $qty){                                    
                                    //lowest price
                                    $url2 = 'https://api.goodrx.com/low-price';
                                    $params2 = 'name='.urlencode(get_the_title()).'&form='.urlencode($form).'&dosage='.$dose.'&quantity='.$qty.'&api_key=68c15f7811';
                                    $sig2 = hash_hmac('sha256', $params2, 'fwAh6winBhk2IERjJdnF0A==', true);
                                    $sig2 = base64_encode($sig2);
                                    $sig2 = str_replace('+', '_', $sig2);
                                    $sig2 = str_replace('/', '_', $sig2);
                                    $link2 = $url2.'?'.$params2.'&sig='.$sig2;
                                    $result2 = file_get_contents($link2);
                                    $aResult2 = json_decode($result2, true);
                                    if(!empty($aResult2['data'])){
                                        $aResult2['data']['img'] = $imgUrl;
                                        $aDataDrugs[] = $aResult2['data'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            dump($aDataDrugs);*/
        
            //lowest prices compare
            /*$url = 'https://api.goodrx.com/compare-price';
            $params = 'name='.get_the_title().'&api_key=68c15f7811';
            $sig = hash_hmac('sha256', $params, 'fwAh6winBhk2IERjJdnF0A==', true);
            $sig = base64_encode($sig);
            $sig = str_replace('+', '_', $sig);
            $sig = str_replace('/', '_', $sig);
            $link = $url.'?'.$params.'&sig='.$sig;
            
            $result = file_get_contents($link);
            $aResult = json_decode($result, true);
            $aData = $aResult['data'];*/
            //dump($aData);
            
            //lowest price
            /*$url2 = 'https://api.goodrx.com/low-price';
            $params2 = 'name='.get_the_title().'&quantity=30&api_key=68c15f7811';
            $sig2 = hash_hmac('sha256', $params2, 'fwAh6winBhk2IERjJdnF0A==', true);
            $sig2 = base64_encode($sig2);
            $sig2 = str_replace('+', '_', $sig2);
            $sig2 = str_replace('/', '_', $sig2);
            $link2 = $url2.'?'.$params2.'&sig='.$sig2;
            $result2 = file_get_contents($link2);
            $aResult2 = json_decode($result2, true);
            $aData2 = $aResult2['data'];*/
            //dump($aData2);
            
        ?>        
        
		
        <?php if(!empty($aDrugs)){?>
        
        <div class="select-drug-block" style="margin: 0 auto; float:none; width:260px!important;"> 
            <select name="dosage" id="dosages">                
                <?php foreach($aOptions as $k=>$dose){?>
                <option value="d<?php echo $k?>"><?php echo $dose?></option>
                <?php }?>
            </select>            
        </div>
        
                <?foreach($aDrugs as $k=>$item){ //dump($item);
                    $fourth = !(($k+1)%4);
                    
                    ?>
                
                
                <!-- || start price-block --> 
			<div class="dosages col-lg-3 col-md-3 col-sm-6 col-xs-12 d<?php echo $k?>" style="float:none;margin:0 auto">
                <?php 
                    $countImg = count($item['images']);
                ?>
				<div class="price-block">
				    <div class="h38px"><?php if($countImg > 1){?><div class="number-of-images"><?php echo $countImg?> Images</div><?php }?> </div>
					<div class="price-block-pic">
                        <?php 
                        if($countImg > 1){?>
						<!-- || Start jcarousel 2 -->
						<div class="jcarousel-wrapper">
							<div class="jcarousel">
								<ul>
                                <?php foreach($item['images'] as $img){?>
									<li><img src="<?php echo $img ?>" alt="<?php echo $item['display']?> <?php echo $item['dosage']?> <?php echo $item['form']?>" /></li>
                                <?php }?>
								</ul>
							</div>
							<a href="#" class="jcarousel-control-prev"><i class="fa fa-angle-left"></i></a>
							<a href="#" class="jcarousel-control-next"><i class="fa fa-angle-right"></i></a>
						</div>
						<!-- // Stop jcarousel 2 -->
                        <?php }elseif($countImg == 1){?>
                        <img src="<?php echo $item['images'][0]?>" alt="<?php echo $item['display']?> <?php echo $item['dosage']?> <?php echo $item['form']?>" />
                        <?php }else{?>
                        <img src="<?php echo $theme_folder?>/images/pill_images/no_image.jpg" alt="No image" />
                        <?php }?>
					</div>
					
					<div class="price-block-center">
						<div class="text-1p">
							Dose
						</div>
						<div class="text-2p">
							<?php echo $item['display']?> <?php echo str_replace('/', ' / ', $item['dosage'])?> <?php echo $item['form']?>
						</div>
						
						<div class="text-1p">
							Ingredient(s)
						</div>
						<div class="text-2p">
							<?php echo ucfirst(str_replace('/', ' / ', $meta['maininfo_chem-name_1'][0]))?>
						</div>
						
						<div class="text-1p">
							Brand or Generic?
						</div>
						<div class="text-2p">
                        <?php if($item['manufacturer'] === 'brand'){?>
                            Brand only
                        <?php }else{?>
							Generic available
                        <?php }?>
						</div>
					</div>
					<?php if(!empty($item['prices'])){?>
                    <?php foreach($item['prices'] as $price){?>
					<div class="price-block-bottom" style="height:50px;padding-top:15px">
						<div class="text-3p">
							<a href="<?php echo $price['url']?>" target="_blank">Click for best price in your area</a>
						</div>
					</div>
                    <?php }?>
                    <?php }?>			
					
					
				</div>
			</div>
			<!-- // stop price-block --> 
            
            <?php if($fourth){?>
            <div class="clearfix"></div>
            <?php }?>
			
            
            <?php } }?>
		
			
		</div>
		
	</div>
    
    <div class="poweredLogo"><a href="https://www.goodrx.com" target="_blank"><img src="https://d4fuqqd5l3dbz.cloudfront.net/static/images/powered-by-goodrx-black-xs.png" alt="Powered by GoodRx"/></a></div>
    
</div>

<div class="container next-treatments">
	<div class="details-a">
		<a title="" href="<?php echo get_permalink()?><?php echo $condition->post_name?>/trials">NEXT: TRIALS</a>
	</div>
</div>

<!-- // end content-product -->