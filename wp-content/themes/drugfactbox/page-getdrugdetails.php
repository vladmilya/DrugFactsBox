<?php
$aResult = array();
$currentUser = wp_get_current_user();
if(!empty($currentUser->ID)){ 
    $userMeta = get_user_meta( $currentUser->ID );
    
    $drug = isset($_GET['drug_name']) ? $_GET['drug_name'] : '';
    $condition = isset($_GET['condition_name']) ? $_GET['condition_name'] : '';    
    
    if(!empty($drug) and !empty($condition)){
        //get allowed drugs names
        $aAllowed = array();
    
        if(isset($userMeta['allowed_conditions'])){
            $allowedConditions = $userMeta['allowed_conditions'][0];
            $aAllowedConditions = explode('::', $allowedConditions);
            if(is_array($aAllowedConditions)) foreach($aAllowedConditions as $conditionID){
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
                    while ( $query->have_posts() ){  
                        $query->the_post();
                        $aAllowed[] = $query->post->post_name;
                    }
                }
            }
        }
    
        if(isset($userMeta['allowed_drugs'])){
            $allowedDrugs = $userMeta['allowed_drugs'][0];
            $aAllowedDrugs = explode('::', $allowedDrugs);
            if(is_array($aAllowedDrugs))foreach($aAllowedDrugs as $drugID){
                $drug_result = $wpdb->get_row("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE ID = '".intval($drugID)."'");  
                $aAllowed[] = $drug_result->post_name;
            }
        }
    
        $aAllowed = array_values(array_unique($aAllowed, SORT_REGULAR));
    
        if(in_array($drug, $aAllowed)){
            //get drug details
            $drugLevel = isset($userMeta['drug_level']) ? $userMeta['drug_level'][0] : 0;
            $fdaLevel = isset($userMeta['fda_level']) ? $userMeta['fda_level'][0] : 0;
            $boxLevel = isset($userMeta['box_level']) ? $userMeta['box_level'][0] : 0;
            
            $drugPost = get_page_by_path($drug,OBJECT,'drug');
            $meta = get_post_meta($drugPost->ID);
            $chemName = ucfirst($meta['maininfo_chem-name_1'][0]);
            $aDrug = array();
            $aDrug['overview']['title'] = $drugPost->post_title;
            $aDrug['overview']['chem_name'] = $chemName;
            $aDrug['overview']['class'] = $meta['maininfo_class_1'][0];
            $condition_result = $wpdb->get_row($wpdb->prepare("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE post_name = %s",$condition));
            if($condition_result){
                $aDrug['overview']['for'] = $condition_result->post_title;            
                //overview            

                $sideeffects = isset($meta['drug_side_effects'][0]) ? @unserialize($meta['drug_side_effects'][0]) : ''; 

                if($fdaLevel){                
                    //availability
                    if($meta['maininfo_prescription_1'][0] == 'Y' or $meta['maininfo_prescription_1'][0] == 'y' or $meta['maininfo_prescription_1'][0] == 'Yes' or $meta['maininfo_prescription_1'][0] == 'yes'){ 
                        $aDrug['overview']['availability']['prescrition'] = 1;
                    }else{
                        $aDrug['overview']['availability']['prescrition'] = 0;
                    }                
                    if($meta['maininfo_otc_1'][0] == 'Y' or $meta['maininfo_otc_1'][0] == 'y' or $meta['maininfo_otc_1'][0] == 'Yes' or $meta['maininfo_otc_1'][0] == 'yes'){ 
                        $aDrug['overview']['availability']['otc'] = 1;
                    }else{
                        $aDrug['overview']['availability']['otc'] = 0;
                    }
                    if($meta['maininfo_generic_1'][0] == 'Y' or $meta['maininfo_generic_1'][0] == 'y' or $meta['maininfo_generic_1'][0] == 'Yes' or $meta['maininfo_generic_1'][0] == 'yes'){ 
                        $aDrug['overview']['availability']['generic'] = 1;
                    }else{
                        $aDrug['overview']['availability']['generic'] = 0;
                    }   
                    //track
                    $yearApproved = isset($meta['year_approved'][0]) ? @unserialize($meta['year_approved'][0]) : '';
                    $aYA = array('name'=>'N/A');
                    if(!empty($yearApproved)){
                        foreach($yearApproved as $ya_cond){
                            if($ya_cond['condition'] === @$condition_result->post_name){
                                $aYA = $ya_cond;
                            }
                        }
                    }
                    $aDrug['overview']['approved'] = $aYA['name']; 

                    //bottom line
                    $bottomLine = isset($meta['bottom_line'][0]) ? @unserialize($meta['bottom_line'][0]) : ''; 
                    if(!empty($bottomLine)){
                        $i=0;
                        foreach($bottomLine as $bl_cond){
                            if($bl_cond['condition'] === @$condition_result->post_name){
                                if(!empty($bl_cond['name']) or !empty($bl_cond['description'])){
                                    $aDrug['overview']['bottom_line'][$i]['name'] = $bl_cond['name']; 
                                    $aDrug['overview']['bottom_line'][$i]['description'] = $bl_cond['description'];
                                    $i++;
                                }
                            }
                        }
                    }

                    //fda-approved use
                    $drugFor = isset($meta['drug_for'][0]) ? @unserialize($meta['drug_for'][0]) : '';                
                    if(!empty($drugFor)){
                        $i=0;
                        foreach($drugFor as $df_cond){
                            if($df_cond['condition'] === @$condition_result->post_name){
                                $aDrug['overview']['fda_approved_use'][$i]['name'] = $df_cond['name']; 
                                $aDrug['overview']['fda_approved_use'][$i]['description'] = $df_cond['description'];
                                $i++;
                            }
                        }                
                    }

                    //who for
                    $whoFor = isset($meta['who_for'][0]) ? @unserialize($meta['who_for'][0]) : '';
                    if(!empty($whoFor)){
                        $i=0;
                        foreach($whoFor as $wf_cond){
                            if($wf_cond['condition'] === @$condition_result->post_name){
                                $aDrug['overview']['who_for'][$i]['name'] = $wf_cond['name']; 
                                $aDrug['overview']['who_for'][$i]['description'] = $wf_cond['description'];
                                $i++;
                            }
                        }                
                    }

                    //what is not known
                    $limitations = isset($meta['limitations'][0]) ? @unserialize($meta['limitations'][0]) : '';
                    if(!empty($limitations)){
                        $i=0;
                        foreach($limitations as $lim_cond){
                            if(@$lim_cond['condition'] === @$condition_result->post_name){
                                if(!empty($lim_cond['name']) or !empty($lim_cond['description'])){
                                    $aDrug['overview']['what_is_not_known'][$i]['name'] = $lim_cond['name']; 
                                    $aDrug['overview']['what_is_not_known'][$i]['description'] = $lim_cond['description'];
                                    $i++;
                                }
                            }
                        }
                    }

                    //track records
                    $trackRecord = isset($meta['track_record'][0]) ? @unserialize($meta['track_record'][0]) : '';
                    if(!empty($trackRecord)){
                        $i=0;
                        foreach($trackRecord as $tr_cond){
                            if($tr_cond['condition'] === @$condition_result->post_name){
                                if(!empty($tr_cond['name']) or !empty($tr_cond['description'])){
                                    $aDrug['overview']['track_record'][$i]['name'] = $tr_cond['name']; 
                                    $aDrug['overview']['track_record'][$i]['description'] = $tr_cond['description'];
                                    $i++;
                                }
                            }
                        }
                    }

                    //open questions
                    $openQuestions = isset($meta['open_questions'][0]) ? @unserialize($meta['open_questions'][0]) : '';                
                    if(!empty($openQuestions)){
                        $i=0;
                        foreach($openQuestions as $oq_cond){
                            if($oq_cond['condition'] === @$condition_result->post_name){
                                if(!empty($oq_cond['name']) or !empty($oq_cond['description'])){
                                    $aDrug['overview']['open_questions'][$i]['name'] = $oq_cond['name']; 
                                    $aDrug['overview']['open_questions'][$i]['description'] = $oq_cond['description'];
                                    $i++;
                                }
                            }
                        }
                    }

                    $trialDescription = isset($meta['trial_description'][0]) ? @unserialize($meta['trial_description'][0]) : '';
                    if(!empty($trialDescription)){
                        $i=0;
                        foreach($trialDescription as $td_cond){
                            if($td_cond['condition'] === $condition_result->post_name){
                                if(!empty($td_cond['name']) or !empty($td_cond['description'])){
                                    $aDrug['overview']['fda_approval_story'][$i]['name'] = $td_cond['name']; 
                                    $aDrug['overview']['fda_approval_story'][$i]['description'] = $td_cond['description'];
                                    $i++;
                                }
                            }
                        }
                    }

                    //how to use
                    $doses = isset($meta['take'][0]) ? @unserialize($meta['take'][0]) : '';
                    if(!empty($doses)){
                        foreach($doses as $d_cond){
                            if($d_cond['condition'] === $condition_result->post_name){
                                $aDrug['how_to_use']['dose']['starting_dose'] = $d_cond['starting-dose']; 
                                $aDrug['how_to_use']['dose']['maximum_dose'] = $d_cond['maximum-dose']; 
                                $aDrug['how_to_use']['dose']['approved_dose'] = $d_cond['approved-dose']; 
                                $aDrug['how_to_use']['dose']['rec_dose'] = $d_cond['rec-dose'];                             
                                $aDrug['how_to_use']['dose']['missed_dose'] = $d_cond['missed-dose'];
                                $aDrug['how_to_use']['dose']['titration_instructions'] = $d_cond['titration-instructions'];
                                $aDrug['how_to_use']['dose']['stopping_instructions'] = $d_cond['stopping-instructions'];
                                $aDrug['how_to_use']['dose']['special_populations'] = $d_cond['special-populations'];
                                $aDrug['how_to_use']['dose']['other'] = $d_cond['other'];
                            }
                        }
                    }
                    $timeResults = isset($meta['time_to_results'][0]) ? @unserialize($meta['time_to_results'][0]) : ''; 
                    if(!empty($timeResults)){
                        foreach($timeResults as $tr_cond){
                            if($tr_cond['condition'] === $condition_result->post_name){
                                if(!empty($tr_cond['name']) or !empty($tr_cond['description'])){
                                    $aDrug['how_to_use']['time_to_results'] = $tr_cond['name']; 
                                }
                            }
                        }
                    }

                } 

                if($drugLevel){                
                    //do not take if
                    $contraindications = isset($meta['contraindications'][0]) ? @unserialize($meta['contraindications'][0]) : '';
                    if(!empty($contraindications)){
                        $i=0;
                        foreach($contraindications as $ci_cond){
                            if(@$ci_cond['type'] === 'contraindication'){
                                if(!empty($ci_cond['name']) or !empty($ci_cond['description'])){
                                    $aDrug['overview']['do_not_take_if'][$i]['name'] = $ci_cond['name'];
                                    $aDrug['overview']['do_not_take_if'][$i]['description'] = $ci_cond['description'];
                                    $i++;  
                                }
                            }
                        }
                    }

                    //not recommended if
                    $notrecommended = isset($meta['notrecommended'][0]) ? @unserialize($meta['notrecommended'][0]) : '';
                    if(!empty($notrecommended)){
                        $i=0;
                        foreach($notrecommended as $nr_cond){
                            if($nr_cond['type'] === 'not_rec'){
                                if(!empty($nr_cond['name']) or !empty($nr_cond['description'])){
                                    $aDrug['overview']['not_recommended_if'][$i]['name'] = $nr_cond['name'];
                                    $aDrug['overview']['not_recommended_if'][$i]['description'] = $nr_cond['description'];
                                    $i++; 
                                }
                            }
                        }
                    }

                    //if pregnant
                    if(!empty($meta['pregnant_breastfeeding_pregnancy-description_1'][0])){
                        $aDrug['overview']['pregnancy'] = $meta['pregnant_breastfeeding_pregnancy-description_1'][0];
                    }

                    //if breastfeeding
                    if(!empty($meta['pregnant_breastfeeding_breastfeeding-description_1'][0])){
                        $aDrug['overview']['breastfeeding'] = $meta['pregnant_breastfeeding_breastfeeding-description_1'][0];
                    }

                    //sideeffects
                    if(!empty($sideeffects)){ //dump($sideeffects);
                        foreach($sideeffects as $se){
                            if($se['type'] === 'bbw' and (!empty($se['name']) or !empty($se['description']))){
                                $aSE['bbw'][] = $se;
                            }
                            if($se['type'] === 'call_doctor' and (!empty($se['name']) or !empty($se['description']))){
                                $aSE['call_doctor'][] = $se;
                            }
                            if($se['type'] === 'tell_doctor' and (!empty($se['name']) or !empty($se['description']))){
                                $aSE['tell_doctor'][] = $se;
                            }
                        }
                    }
                    if(@$aSE['bbw']){
                        $i=0;
                        foreach($aSE['bbw'] as $se){
                            $aDrug['side_effects']['bbw'][$i]['name'] = $se['name']; 
                            $aDrug['side_effects']['bbw'][$i]['description'] = $se['description'];
                            $i++;                        
                        }
                    }
                    if(@$aSE['call_doctor']){
                        $i=0;
                        foreach($aSE['call_doctor'] as $se){
                            $aDrug['side_effects']['call_doctor'][$i]['name'] = $se['name']; 
                            $aDrug['side_effects']['call_doctor'][$i]['description'] = $se['description'];
                            $i++;                        
                        }
                    }
                    if(@$aSE['tell_doctor']){
                        $i=0;
                        foreach($aSE['tell_doctor'] as $se){
                            $aDrug['side_effects']['tell_doctor'][$i]['name'] = $se['name']; 
                            $aDrug['side_effects']['tell_doctor'][$i]['description'] = $se['description'];
                            $i++;                        
                        }
                    }

                    //precautions
                    $precautions = isset($meta['precautions'][0]) ? @unserialize($meta['precautions'][0]) : '';
                    $aPrecautions = array();
                    if(!empty($precautions)){
                        foreach($precautions as $pc){
                            if($pc['type'] === 'testing'){
                                $aPrecautions['testing'][] = $pc;
                            }
                            if($pc['type'] === 'to_avoid'){
                                $aPrecautions['to_avoid'][] = $pc;
                            }
                        }
                    }
                    if(@$aPrecautions['testing']){
                        $i=0;
                        foreach($aPrecautions['testing'] as $pc){
                            $aDrug['precautions']['testing'][$i]['name'] = $pc['name']; 
                            $aDrug['precautions']['testing'][$i]['description'] = $pc['description'];
                            $i++;                        
                        }
                    }
                    if(@$aPrecautions['to_avoid']){
                        $i=0;
                        foreach($aPrecautions['to_avoid'] as $pc){
                            $aDrug['precautions']['avoid'][$i]['name'] = $pc['name']; 
                            $aDrug['precautions']['avoid'][$i]['description'] = $pc['description'];
                            $i++;                        
                        }
                    }

                    //interactions
                    $interactions = isset($meta['drug_interactions'][0]) ? @unserialize($meta['drug_interactions'][0]) : '';
                    if(!empty($interactions)){
                        $i=0;
                        foreach($interactions as $ia){
                            $aDrug['interactions'][$i]['name'] = $ia['name']; 
                            $aDrug['interactions'][$i]['description'] = $ia['description'];
                            $i++;
                        }
                    }


                }

                if($boxLevel){
                    //trials
                    $aBoxes = isset($meta['box'][0]) ? @unserialize($meta['box'][0]) : '';
                    $aTestBoxes = array();
                    if(!empty($aBoxes)){
                        foreach($aBoxes as $box_cond){
                            if($box_cond['condition'] === $condition_result->post_name){
                                $aTestBoxes[] = $box_cond;
                            }
                        }
                    }            
                    if(!empty($aTestBoxes)){
                        $i=0;
                        foreach($aTestBoxes as $k=>$box){
                            $aDrug['trials'][$i]['trial_name'] = $box['infy-name'];
                            $aTested = isset($meta['tested'][0]) ? @unserialize($meta['tested'][0]) : ''; 
                            if(!empty($aTested)){
                                foreach($aTested as $tested_cond){
                                    if($tested_cond['condition'] == $condition_result->post_name and $tested_cond['box'] == $box['infy-name']){
                                        $aDrug['trials'][$i]['trial_description'] = $tested_cond['description'];
                                        $aDrug['trials'][$i]['patients_number'] = $tested_cond['number'];
                                        $aDrug['trials'][$i]['sex'] = $tested_cond['sex'];
                                        $aDrug['trials'][$i]['age_range'] = $tested_cond['age-range'];
                                        $aDrug['trials'][$i]['age_average'] = $tested_cond['age-average'];
                                    }
                                }
                            }
                            $aTestConditions = isset($meta['test_cond'][0]) ? @unserialize($meta['test_cond'][0]) : '';
                            if(!empty($aTestConditions)){
                                $j=0;
                                foreach($aTestConditions as $tc_cond){
                                    if($tc_cond['condition'] == $condition_result->post_name and $tc_cond['box'] == $box['infy-name']){
                                        $aDrug['trials'][$i]['conditions'][$j]['name'] = $tc_cond['condition-name'];
                                        $aDrug['trials'][$i]['conditions'][$j]['explanation'] = $tc_cond['explanation'];
                                        $aDrug['trials'][$i]['conditions'][$j]['severity'] = $tc_cond['severity'];
                                        $j++;
                                    }
                                }
                            }
                            $aTestGroups = isset($meta['test_groups'][0]) ? @unserialize($meta['test_groups'][0]) : '';
                            if(!empty($aTestGroups)){
                                $j=0;
                                foreach($aTestGroups as $tg_cond){
                                    if($tg_cond['condition'] == $condition_result->post_name and $tg_cond['box'] == $box['infy-name']){
                                        if(count($aTestGroups) > 1){
                                            $aDrug['trials'][$i]['randomized_trial'][$j] = $tg_cond['name'];
                                            $aDrug['trials'][$i]['randomized_trial']['study_length'] = $aTested[0]['study-length'];
                                        }else{
                                            $aDrug['trials'][$i]['uncontrolled_trial'][$j] = $tg_cond[0]['name'];
                                            $aDrug['trials'][$i]['uncontrolled_trial']['study_length'] = $aTested[0]['study-length'];
                                        }
                                        $j++;
                                    }
                                }
                            }

                            //benefits
                            $aBenefits = isset($meta['benefits'][0]) ? @unserialize($meta['benefits'][0]) : '';
                            if(!empty($aBenefits)){
                                $aCurrentBenefits = array();
                                foreach($aBenefits as $bnf_cond){
                                    if($bnf_cond['condition'] == $condition_result->post_name and $bnf_cond['box'] == $box['infy-name']){ 
                                        $aCurrentBenefits[] = $bnf_cond;
                                    }
                                }
                            }
                            if(!empty($aCurrentBenefits)){
                                $j=0;
                                foreach($aCurrentBenefits as $bnf){
                                    if(!empty($bnf['name'])){
                                        $aDrug['trials'][$i]['benefits'][$j]['name'] = $bnf['name'];
                                        $aBenefitsDetails = isset($meta['benefits_details'][0]) ? @unserialize($meta['benefits_details'][0]) : ''; 
                                        if(!empty($aBenefitsDetails)){
                                            $k=0;
                                            foreach($aBenefitsDetails as $bnfdet_cond){
                                                if($bnfdet_cond['condition'] === $condition_result->post_name and $bnfdet_cond['box'] === $box['infy-name'] and $bnfdet_cond['benefit'] === $bnf['name']){ 
                                                    //$aCurrentBenefitsDetails[] = $bnfdet_cond;
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['description'] = $bnfdet_cond['description'];
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][0]['name'] = $aTestGroups[0]['name'];
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][0]['dosing'] = $aTestGroups[0]['dosing'];
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][0]['result'] = $bnfdet_cond['group0'];
                                                    if(!empty($aTestGroups[1]['name'])){
                                                        $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][1]['name'] = $aTestGroups[1]['name'];
                                                        $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][1]['dosing'] = $aTestGroups[1]['dosing'];
                                                        $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['groups'][1]['result'] = $bnfdet_cond['group1'];
                                                    }
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['comparison']['result'] = $bnfdet_cond['comparison'];
                                                    $aDrug['trials'][$i]['benefits'][$j]['details'][$k]['comparison']['abs_difference'] = $bnfdet_cond['abs-difference'];
                                                    $k++;
                                                }
                                            }
                                            $j++;
                                        }   

                                    }
                                }
                                $benefitsSource = isset($meta['benefits_source'][0]) ? @unserialize($meta['benefits_source'][0]) : '';
                                if(!empty($benefitsSource)){
                                    foreach($benefitsSource as $bs_cond){
                                        if($bs_cond['condition'] === $condition_result->post_name and $bs_cond['box'] === $box['infy-name']){
                                            $aDrug['trials'][$i]['benefits_source'] = $bs_cond['name'];
                                        }
                                    }
                                }
                            }

                            //side effects
                            $aSideEffects = isset($meta['box_side_effects'][0]) ? @unserialize($meta['box_side_effects'][0]) : '';
                            if(!empty($aSideEffects)){
                                $aCurrentSideEffects = array();
                                foreach($aSideEffects as $se_cond){
                                    if($se_cond['condition'] === $condition_result->post_name and $se_cond['box'] === $box['infy-name']){
                                        if($se_cond['priority'] === 'black box warning'){
                                            $aCurrentSideEffects['bbw'][] = $se_cond;
                                        }elseif($se_cond['priority'] === 'serious' or $se_cond['priority'] === 'serious_uncommon'){
                                            $aCurrentSideEffects['serious'][] = $se_cond;
                                        }elseif($se_cond['priority'] === 'fda_symptom' or $se_cond['priority'] === 'other_symptom'){
                                            $aCurrentSideEffects['symptoms'][] = $se_cond;
                                        }
                                    }
                                }
                            }
                            if(!empty($aCurrentSideEffects)){
                                if(!empty($aCurrentSideEffects['bbw'])){
                                    $j=0;
                                    foreach($aCurrentSideEffects['bbw'] as $se){
                                        if(!empty($se['name'])){
                                            $aDrug['trials'][$i]['side_effects']['bbw'][$j]['name'] = $se['name'];
                                            $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][0]['name'] = $aTestGroups[0]['name']; 
                                            $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][0]['dosing'] = $aTestGroups[0]['dosing']; 
                                            $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][0]['result'] = $se['group0']; 
                                            if(!empty($aTestGroups[1]['name'])){
                                                $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][1]['name'] = $aTestGroups[1]['name']; 
                                                $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][1]['dosing'] = $aTestGroups[1]['dosing']; 
                                                $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['groups'][1]['result'] = $se['group1']; 
                                            }
                                            $aDrug['trials'][$i]['side_effects']['bbw'][$j]['details']['comparison'] = $se['comparison'];
                                            $j++; 
                                        }
                                    }
                                    $j=0;
                                    foreach($aCurrentSideEffects['serious'] as $se){
                                        if(!empty($se['name'])){
                                            $aDrug['trials'][$i]['side_effects']['serious'][$j]['name'] = $se['name'];
                                            $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][0]['name'] = $aTestGroups[0]['name']; 
                                            $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][0]['dosing'] = $aTestGroups[0]['dosing']; 
                                            $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][0]['result'] = $se['group0']; 
                                            if(!empty($aTestGroups[1]['name'])){
                                                $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][1]['name'] = $aTestGroups[1]['name']; 
                                                $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][1]['dosing'] = $aTestGroups[1]['dosing']; 
                                                $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['groups'][1]['result'] = $se['group1']; 
                                            }
                                            $aDrug['trials'][$i]['side_effects']['serious'][$j]['details']['comparison'] = $se['comparison'];
                                            $j++; 
                                        }
                                    }
                                    $j=0;
                                    foreach($aCurrentSideEffects['symptoms'] as $se){
                                        if(!empty($se['name'])){
                                            $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['name'] = $se['name'];
                                            $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][0]['name'] = $aTestGroups[0]['name']; 
                                            $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][0]['dosing'] = $aTestGroups[0]['dosing']; 
                                            $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][0]['result'] = $se['group0']; 
                                            if(!empty($aTestGroups[1]['name'])){
                                                $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][1]['name'] = $aTestGroups[1]['name']; 
                                                $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][1]['dosing'] = $aTestGroups[1]['dosing']; 
                                                $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['groups'][1]['result'] = $se['group1']; 
                                            }
                                            $aDrug['trials'][$i]['side_effects']['symptoms'][$j]['details']['comparison'] = $se['comparison'];
                                            $j++; 
                                        }
                                    }
                                }
                                $seSource = isset($meta['sideeffects_source'][0]) ? @unserialize($meta['sideeffects_source'][0]) : '';
                                if(!empty($seSource)){
                                    foreach($seSource as $ses_cond){
                                        if($ses_cond['condition'] === $condition_result->post_name and $ses_cond['box'] === $box['infy-name']){
                                            $aDrug['trials'][$i]['side_effects_source'] = $ses_cond['name'];
                                        }
                                    }
                                }
                            }

                            $i++;
                        }
                    }


                }

                $aResult['data'] = $aDrug;
            }else{
                $aResult['error'] = 'Unknown condition';
                $aResult['data'] = array();
            }
            
        }else{
            $aResult['error'] = 'You don\'t have permissions to request this information';
            $aResult['data'] = array();
        }
    }else{
        $aResult['error'] = 'Empty required fields';
        $aResult['data'] = array();
    }  
    @wp_logout();     
}else{
    $aResult['error'] = 'Authorization failed';
    $aResult['data'] = array();
}

$jsonOutput = json_encode($aResult);
echo $jsonOutput;