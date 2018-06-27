<?php
$aResult = array();
$currentUser = wp_get_current_user();
if(!empty($currentUser->ID)){   
    $userMeta = get_user_meta( $currentUser->ID );
    
    $aOutputDrugs = array();
    
    //drugs by conditions
    if(isset($userMeta['allowed_conditions'])){
        $allowedConditions = $userMeta['allowed_conditions'][0];
        $aAllowedConditions = explode('::', $allowedConditions);
        if(is_array($aAllowedConditions)) foreach($aAllowedConditions as $conditionID){
            $condition_result = $wpdb->get_row("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE ID = '".intval($conditionID)."'");
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
                    $aDrug = array();
                    $query->the_post();
                    $meta_drug = get_post_meta($query->post->ID);
                    $chemName = ucfirst($meta_drug['maininfo_chem-name_1'][0]);
                    $aDrug['id'] = $query->post->ID;
                    $aDrug['name'] = $query->post->post_name;
                    $aDrug['title'] = $query->post->post_title;
                    $aDrug['chem_name'] = $chemName;     
                    $aDrug['condition_id'] = $conditionID; 
                    $aDrug['condition_name'] =$condition_result->post_name; 
                    $aDrug['condition_title'] =$condition_result->post_title;                
                    $aOutputDrugs[] = $aDrug;
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
            $chemName = ucfirst($meta_drug['maininfo_chem-name_1'][0]);
            $conditions = isset($meta_drug['conditions'][0]) ? @unserialize($meta_drug['conditions'][0]) : '';
            foreach($conditions as $conditionID){
                $condition_result = $wpdb->get_row("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE ID = '".intval($conditionID)."'");
                $aDrug = array();
                $aDrug['id'] = $drug_result->ID;
                $aDrug['name'] = $drug_result->post_name;
                $aDrug['title'] = $drug_result->post_title;
                $aDrug['chem_name'] = $chemName;
                $aDrug['condition_id'] = $conditionID; 
                $aDrug['condition_name'] =$condition_result->post_name; 
                $aDrug['condition_title'] =$condition_result->post_title;  
                $aOutputDrugs[] = $aDrug;
            }
        }    
    }
    
    $aResult['data'] = array_values(array_unique($aOutputDrugs, SORT_REGULAR));
    
    @wp_logout(); 
}else{
    $aResult['error'] = 'Authorization failed';
    $aResult['data'] = array();
}

$jsonOutput = json_encode($aResult);
echo $jsonOutput;