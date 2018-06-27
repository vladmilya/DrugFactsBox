<?php
get_header();
?>
<style>
.drug-block-1c{
    border-top: 2px solid #d8d8d8;
    border-bottom: none!important;
    margin-top: 5px!important;
    margin-bottom: 25px!important;
    padding: 35px 125px 0 0!important;
}
.notfound{
    text-align:center;
    margin: 30px auto;
}
</style>


 <?php if ( have_posts() ) : ?>
<!-- conditions-page -->
	<div class="conditions-page">
	
		<h1>Search Results</h1>
        
        <div class="clearfix"></div>
		
		<div class="container list-block-1">
            <div class="row">
            
           
            
                <?php while ( have_posts() ) : the_post(); ?>
                
                <?php 
                $meta = get_post_meta($post->ID);//dump( $meta);
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
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="row">
                        <div class="drug-block-1">
                            <div class="drug-block-1c">
                             <h3><?php the_title()?></h3>
                             <p><span><?php echo ucfirst($post->post_type)?></span></p>
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
                                                    <h2 class="modal-title" id="myModalLabel">Why I Take <?php the_title()?></h2>
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
                
                <?php endwhile; ?>
                
            
            
            <?php
$args = array(

    'type'               => 'array',
	
	'prev_next'          => true,
	'prev_text'          => __('<span aria-hidden="true" class="arrows"><i class="fa fa-angle-left"></i></span>'),
	'next_text'          => __('<span aria-hidden="true" class="arrows"><i class="fa fa-angle-right"></i></span>')
    );
$pager = paginate_links($args);
?>
            <?php if(!empty($pager)){?>
            <!-- pagination -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pagination-1">
					<div class="row">                  
						<nav>
                        


                        
						  <ul class="pagination">
                          
                          <?php foreach($pager as $k=>$p){?>
                          <li><?php echo $p?></li>
                          <?}?>
						  </ul>
						</nav>
					</div>
				</div>
			<?php }?>	<!-- // end pagination -->
                        
            </div>
        </div>
        
    </div>
<?php else : ?>
        <?php
$notfound_post = get_page_by_path('not-found',OBJECT,'post');
?>
<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php echo isset($notfound_post->post_title) ? $notfound_post->post_title : 'Not Found'?></h1>	
		</div>
	</div>
</div>
<!-- // end title-h1 -->
 
<div class="page-content">	
	<hr />
    <div class="container">
<?php echo isset($notfound_post->post_content) ? $notfound_post->post_content : 'Sorry. Page you are looking for doesn\'t exist'?>
    </div>
</div>
<?php endif; ?>    
    

<?php
get_footer();
?>