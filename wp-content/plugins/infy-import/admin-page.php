<?php
/**
 *  Admin Page 
 *  For configuring the plugin
 */
 

function infy_import_save_meta($post_id, $name, $data){
    update_post_meta($post_id, $name, $data); 
    if (!empty($data)){
        foreach($data as $key => $cfc_block){
            foreach ($cfc_block as $cfc_name => $cfc_value){
                update_post_meta($post_id, $name . '_' . $cfc_name . '_' . ($key+1), $cfc_value);
            }
        }
    }

}


function import_drug($data){
    if (is_object($data)){
        $post = get_page_by_title($data->drug_name,'OBJECT','drug');

        if (empty($post) || empty($post->ID)){
            //create drug
            $post_id = wp_insert_post(
                array(
                    'comment_status'  => 'closed',
                    'ping_status'   => 'closed',
                    'post_name'   => $data->drug_name,
                    'post_title'    => $data->drug_name,
                    'post_status'   => 'publish',
                    'post_type'   => 'drug'
                )
            );
        } else {
            $post_id = $post->ID;
        }
        
        
        infy_import_update_data($data, 0, $post_id);

        if (count($data->box) > 1) {

            $args = array(
                'parent' => $post_id,
                'post_type' => 'drug',
                'post_status' => 'publish'
            ); 
            $_sub_drugs = get_pages($args); 
            $sub_drugs = array();
            if (!empty($_sub_drugs)){
                //load sub drugs
                foreach ($_sub_drugs as $key => $value){
                    $infy_name = get_cfc_field('maininfo', 'infy-name', $value->ID);
                    $sub_drugs[$infy_name] = $value->ID;
                }
            }

            $actual_subdrugs = array();
            //create sub drugs for each box
            for ($i=1; $i < count($data->box); $i++){
                $box = $data->box[$i];
                if (empty($sub_drugs[$box->infy_name])){
                    $subpost_id = wp_insert_post(
                        array(
                            'comment_status'  => 'closed',
                            'ping_status'   => 'closed',
                            'post_name'   => $data->drug_name,
                            'post_title'    => $data->drug_name,
                            'post_status'   => 'publish',
                            'post_type'   => 'drug',
                            'post_parent' => $post_id,
                        )
                    );
                } else {
                    $subpost_id = $sub_drugs[$box->infy_name];
                }
                $actual_subdrugs[] = $box->infy_name;
                infy_import_update_data($data, $i, $subpost_id);
            }

            foreach($sub_drugs as $key => $id){
                if (!in_array($key, $actual_subdrugs)){
                     wp_delete_post($id, true);
                }
            
            }
        
        }

        return true;
    }
    return false;
}

function infy_import_update_data($data, $box_num, $post_id){ //error_reporting(E_ALL);ini_set('display_errors', 'On');
    
    $aDrugDetails = $data->drug_details;    
    

    $maininfo = array();
    $maininfo[0] = array();
    $maininfo[0]['chem-name'] = $data->chem_name;
    $maininfo[0]['generic'] = $data->generic;
    $maininfo[0]['class'] = $data->class;
    $maininfo[0]['alt-name'] = $data->alt_names;
    $maininfo[0]['atc-code'] = $data->atc_code;
    $maininfo[0]['legal-status'] = $data->legal_status;
    $maininfo[0]['legal-status-descriptions'] = $data->legal_status_description;    
    $maininfo[0]['box-code'] = $data->box[$box_num]->code;
    $maininfo[0]['qualifiers'] = $data->box[$box_num]->qualifiers;
    $maininfo[0]['source-doctors-taught'] = $data->box[$box_num]->source_doctors_taught;
    $maininfo[0]['try-first'] = $data->box[$box_num]->try_first;
    $maininfo[0]['prescription'] = $data->prescription;
    $maininfo[0]['otc'] = $data->OTC;
    $maininfo[0]['drug-fits'] = $data->box[$box_num]->drug_fits;
    infy_import_save_meta($post_id, 'maininfo', $maininfo);

        
    $dashboard = array();
    if (!empty($data->drug_details[$box_num]->fda_indications)){
        $dashboard[0]['who-take'] = $data->drug_details[$box_num]->fda_indications->who_for->name;//fixed
        $dashboard[0]['who-not-take'] = $data->box[$box_num]->dashboard->who_not_take;
        $dashboard[0]['black-box'] = $data->box[$box_num]->dashboard->black_box;
        $dashboard[0]['compared-to'] = $data->box[$box_num]->dashboard->compared_to;
        $dashboard[0]['summary'] = $data->box[$box_num]->dashboard->summary;
        $dashboard[0]['formulation'] = $data->box[$box_num]->dashboard->formulation;
        $dashboard[0]['bottom-line'] = $data->drug_details[$box_num]->fda_indications->bottom_line->name;//added
    }
    infy_import_save_meta($post_id, 'dashboard', $dashboard);
        
    $dashboard_outcome = array('0'=>array());
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->dashboard->outcome)){
        foreach($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->dashboard->outcome as $key => $outcome){
            $dashboard_outcome[$key]['name'] = $outcome->name;
            $dashboard_outcome[$key]['value'] = $outcome->value;
            $dashboard_outcome[$key]['abs-diff'] = $outcome->abs_diff;
        }
    }
    infy_import_save_meta($post_id, 'dashboard_outcome', $dashboard_outcome);
    
    //added
    $sideeffects = array();    
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type == 'tell_doctor' or $v->type == 'call_doctor' or $v->type == 'bbw'){
            $sideeffects[$i]['name'] = $v->name;
            $sideeffects[$i]['type'] = $v->type;
            $sideeffects[$i]['description'] = $v->description;
            $i++;
        }
    }
    infy_import_save_meta($post_id, 'drug_side_effects', $sideeffects);
        
    
    //fixed    
    $contraindications = array();
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type == 'contraindication'){
            $contraindications[$i]['name'] = $v->name;
            $contraindications[$i]['type'] = $v->type;
            $contraindications[$i]['description'] = $v->description;
            $i++;
        }
    }
    infy_import_save_meta($post_id, 'contraindications', $contraindications);
        
    $contraindications_cond = array();
    if (!empty($data->contraindications->conditions)){
        foreach ($data->contraindications->conditions as $key => $condition){
            $contraindications_cond[$key]['condition'] = $condition->condition;
            $contraindications_cond[$key]['code'] = $condition->code;
            $contraindications_cond[$key]['interaction'] = $condition->interaction;
            $contraindications_cond[$key]['explanation'] = $condition->explanation;
        }
    }
    infy_import_save_meta($post_id, 'contraindications_cond', $contraindications_cond);
    
    //fixed
    $notrecommended = array();
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type == 'not_rec'){
            $notrecommended[$i]['name'] = $v->name;
            $notrecommended[$i]['type'] = $v->type;
            $notrecommended[$i]['description'] = $v->description;
            $i++;
        }
    }
    infy_import_save_meta($post_id, 'notrecommended', $notrecommended);
        
        
    $notrecommended_cond = array(0=>array());
    if (!empty($data->not_recommended_for->conditions)){
        foreach ($data->not_recommended_for->conditions as $key => $condition){
            $notrecommended_cond[$key]['condition'] = $condition->condition;
            $notrecommended_cond[$key]['code'] = $condition->code;
            $notrecommended_cond[$key]['interaction'] = $condition->interaction;
            $notrecommended_cond[$key]['explanation'] = $condition->explanation;
        }
    }
    infy_import_save_meta($post_id, 'notrecommended_cond', $notrecommended_cond);
    
    $dosage_forms = array();
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type == 'form'){
            $dosage_forms[$i]['name'] = $v->name;
            $dosage_forms[$i]['type'] = $v->type;
            $dosage_forms[$i]['description'] = $v->description;
            $i++;
        }
    }
    infy_import_save_meta($post_id, 'dosage_forms', $dosage_forms);
        
    $pregnant_breastfeeding = array(0=>array());
    $pregnant_breastfeeding[0]['text'] = $data->pregnant_breastfeeding->text;
    $pregnant_breastfeeding[0]['pregnancy-category'] = $data->pregnant_breastfeeding->pregnancy_category;
    $pregnant_breastfeeding[0]['pregnancy-description'] = $data->pregnant_breastfeeding->pregnancy_description;
    $pregnant_breastfeeding[0]['breastfeeding-category'] = $data->pregnant_breastfeeding->breastfeeding_category;
    $pregnant_breastfeeding[0]['breastfeeding-description'] = $data->pregnant_breastfeeding->breastfeeding_description;
    infy_import_save_meta($post_id, 'pregnant_breastfeeding', $pregnant_breastfeeding);

    //fixed    
    $precautions = array();
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type === 'testing' or $v->type === 'to_avoid'){
            $precautions[$i]['type'] = $v->type;
            $precautions[$i]['name'] = $v->name;
            $precautions[$i]['description'] = $v->description;
            $i++;
        }
    }  
    infy_import_save_meta($post_id, 'precautions', $precautions);
    
    //added
    $interactions = array();    
    $i=0;
    foreach($aDrugDetails as $k=>$v){
        if($v->type == 'interaction'){
            $interactions[$i]['name'] = $v->name;
            $interactions[$i]['type'] = $v->type;
            $interactions[$i]['description'] = $v->description;
            $i++;
        }
    }
    infy_import_save_meta($post_id, 'drug_interactions', $interactions);
        
        
    $recomended = array(0=>array());
    $recomended[0]['text'] = $data->box[$box_num]->recommended_for->text;
    $recomended[0]['who'] = $data->box[$box_num]->recommended_for->who;
    $recomended[0]['with'] = $data->box[$box_num]->recommended_for->with;
    $recomended[0]['limitation'] = $data->box[$box_num]->recommended_for->limitation;
    infy_import_save_meta($post_id, 'recomended', $recomended);


    //removed
    /*$tested = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box)){
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box as $key => $t){
            $tested[$key]['number'] = $t->who_was_tested->number;
            $tested[$key]['age'] = $t->who_was_tested->age;
            $tested[$key]['age-range'] = $t->who_was_tested->age_range;
            $tested[$key]['age-average'] = $t->who_was_tested->age_average;
            $tested[$key]['study-length'] = $t->who_was_tested->study_length;
            $tested[$key]['sex'] = $t->who_was_tested->sex;
            $tested[$key]['pregnancy'] = $t->who_was_tested->pregnancy;
            $tested[$key]['source'] = $t->who_was_tested->source;
            $tested[$key]['description'] = $t->who_was_tested->description;
        }
    }
    infy_import_save_meta($post_id, 'tested', $tested);*/
    
    //removed   
    /*$test_cond = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->who_was_tested->conditions)){
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->who_was_tested->conditions as $key => $condition){
            $test_cond[$key]['condition'] = $condition->condition;
            $test_cond[$key]['code'] = $condition->code;
            $test_cond[$key]['explanation'] = $condition->explanation;
            $test_cond[$key]['severity'] = $condition->severity;
            $test_cond[$key]['scale'] = $condition->scale;
        }
    }
    infy_import_save_meta($post_id, 'test_cond', $test_cond);*/
    
    //removed
    /*$test_box = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box)){
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box as $key => $box_item){
            $test_box[$key]['infy-name'] = $box_item->infy_name;
            $test_box[$key]['fda-id'] = $box_item->fda_id;
            $test_box[$key]['infy-id'] = $box_item->infy_id;
            $test_box[$key]['trial-description'] = $box_item->who_was_tested->trial_description;
        }
    }
    infy_import_save_meta($post_id, 'box', $test_box);*/

    //removed
    /*$test_groups = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->who_was_tested->group_description)){
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->who_was_tested->group_description as $key => $group){
            $test_groups[$key]['name'] = $group->name;
            $test_groups[$key]['dosing'] = $group->dosing;
            $test_groups[$key]['background'] = $group->background;
        }
    }
    infy_import_save_meta($post_id, 'test_groups', $test_groups);*/
        
        
    $how_drug_helps = array(0=>array());
    $how_drug_helps[0]['what-drug-for'] = $data->box[$box_num]->how_drug_helps->what_drug_for;
    $how_drug_helps[0]['time-to-results'] = $data->box[$box_num]->how_drug_helps->time_to_results;
    infy_import_save_meta($post_id, 'help', $how_drug_helps);
        
    //removed
    /*$study_results = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->how_drug_helps->study_results)){ 
        $i=0;
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->how_drug_helps->study_results as $key => $result){
            if (!empty($result->outcome_details)){
                $study_results[$i]['outcome'] = $result->outcome;
                foreach($result->outcome_details as $details){
                    $study_results[$i]['outcome'] = $result->outcome;
                    $study_results[$i]['description'] = $details->description;
                    $study_results[$i]['scale'] = $details->scale;
                    $study_results[$i]['abs-difference'] = $details->abs_difference;
                    $study_results[$i]['ruler'] = $details->ruler;
                    $study_results[$i]['code'] = $details->code;
                    $study_results[$i]['priority'] = $details->priority;
                    if (!empty($details->groups)){
                        $j = 1;
                        foreach ($details->groups as $key_group => $value){
                            $study_results[$key]['group' . $j . '-result'] = $value;
                            $j++;
                        }
                    }
                    $i++;
                }
            }
        }
    }
    infy_import_save_meta($post_id, 'study_results', $study_results);*/
        
    //fixed    
    /*$side_effects = array();
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->side_effects)){
        foreach ($data->drug_details[$box_num]->how_to_use[$box_num]->box[$box_num]->side_effects as $key => $effect){
            $side_effects[$key]['name'] = $effect->name;
            $side_effects[$key]['medical-name'] = $effect->medical_name;
            $side_effects[$key]['description'] = $effect->description;
            $side_effects[$key]['abs-difference'] = $effect->abs_difference;
            $side_effects[$key]['priority'] = $effect->priority;
            $side_effects[$key]['dashboard'] = $effect->dashboard;
            if (!empty($effect->groups)){
                $i = 1;
                foreach ($effect->groups as $key_group => $value){
                    $side_effects[$key]['group' . $i . '-result'] = $value;
                    $i++;
                }
            }
        }
    }
    infy_import_save_meta($post_id, 'side_effects', $side_effects);*/


    $uncertainty = array(0=>array());
    $uncertainty[0]['date-approved'] = $data->box[$box_num]->uncertainty->date_approved;
    $uncertainty[0]['date-approved-for-indication'] = $data->box[$box_num]->uncertainty->date_approved_for_indication;
    $uncertainty[0]['years-on-the-market'] = $data->box[$box_num]->uncertainty->years_on_the_market;
    $uncertainty[0]['track-record'] = $data->box[$box_num]->uncertainty->track_record;
    $uncertainty[0]['track-record-description'] = $data->box[$box_num]->uncertainty->track_record_description;
    $uncertainty[0]['post-marketing'] = $data->box[$box_num]->uncertainty->post_marketing;
    $uncertainty[0]['post-marketing-description'] = $data->box[$box_num]->uncertainty->post_marketing_description;
    $uncertainty[0]['studies'] = $data->box[$box_num]->uncertainty->studies;
    $uncertainty[0]['positive-studies'] = $data->box[$box_num]->uncertainty->positive_studies;
    $uncertainty[0]['description'] = $data->box[$box_num]->uncertainty->description;
    infy_import_save_meta($post_id, 'uncertainty', $uncertainty);

    $other_drug = array();
    if (!empty($data->box[$box_num]->other_choices->drug)){
        foreach ($data->box[$box_num]->other_choices->drug as $key => $other){
            $other_drug[$key]['name'] = $other->drug_names;
            $other_drug[$key]['class'] = $other->drug_class;
            $other_drug[$key]['type'] = $other->type;
            $other_drug[$key]['code'] = $other->atc_code;
            $other_drug[$key]['description'] = $other->description;
                
        }
    }
    infy_import_save_meta($post_id, 'other_drug', $other_drug);


    $other_non_drug = array();
    if (!empty($data->box[$box_num]->other_choices->non_drug)){
        foreach ($data->box[$box_num]->other_choices->non_drug as $key => $other){
            $other_non_drug[$key]['name'] = $other->name;
            $other_non_drug[$key]['code'] = $other->code;
            $other_non_drug[$key]['type'] = $other->type;
            $other_non_drug[$key]['description'] = $other->description;
            $other_non_drug[$key]['strength-of-evidence'] = $other->strength_of_evidence;
        }
    }
    infy_import_save_meta($post_id, 'other_non_drug', $other_non_drug);

    //removed
    /*$take = array(0=>array());
    if (!empty($data->drug_details[$box_num]->how_to_use[$box_num])){
        $take[0]['starting-dose'] = $data->drug_details[$box_num]->how_to_use[$box_num]->starting_dose;
        $take[0]['maximum-dose'] = $data->drug_details[$box_num]->how_to_use[$box_num]->maximum_dose;
        $take[0]['approved-dose'] = $data->drug_details[$box_num]->how_to_use[$box_num]->approved_dose;
        $take[0]['titration-instructions'] = $data->drug_details[$box_num]->how_to_use[$box_num]->titration_instructions;
        $take[0]['missed-dose'] = $data->drug_details[$box_num]->how_to_use[$box_num]->missed_dose;
        $take[0]['stopping-instructions'] = $data->drug_details[$box_num]->how_to_use[$box_num]->stopping_instructions;
        $take[0]['special-populations'] = $data->drug_details[$box_num]->how_to_use[$box_num]->special_populations;
        $take[0]['other'] = $data->drug_details[$box_num]->how_to_use[$box_num]->other;
    }
    infy_import_save_meta($post_id, 'take', $take);*/
    
    
    //FDA Indications
    $aFDAIndication = $data->fda_indications;
    if(!empty($aFDAIndication)){
        $aCond = array();
        $aYearApproved = array();
        $aDrugFor = array();
        $aWhoFor = array();
        $aLimitations = array();
        $aTrackRecord = array();
        $aOpenQuestions = array();
        $aBottomLine = array();
        $aTrialDescription = array();
        $aHowToUse = array();
        $aTimeResults = array();
        $aBoxes = array();
        $aTested = array();
        $aTestCond = array();
        $aTestGroups = array();
        $aBenefits = array();
        $aBenefitsSource = array();
        $aBenefitsDetails = array();
        $aSideEffects = array();
        $aSideEffectsSource = array();
        
        foreach($aFDAIndication as $FDAInd){           
            $details = $FDAInd->fda_indication_details;
            $condition = $details->condition_id;
            $existingCondition = get_page_by_title( $condition, 'OBJECT', 'condition' );
            if(empty($existingCondition)){ 
                $newPost = array(
                                   'post_type' =>  'condition',
                                   'post_title' => $condition,
                                   'post_status' => 'publish',
                                   'post_name' => sanitize_title($condition)
                                );
                wp_insert_post($newPost);                
                $conditionSlug = sanitize_title($condition);
            }else{
                $aCond[] = $existingCondition->ID;  
                $conditionSlug = $existingCondition->post_name ? $existingCondition->post_name : sanitize_title($existingCondition->post_title);
            }
            //year approved
            $yearApwd = $details->year_approved;
            $ya=array();
            $ya['condition'] = $conditionSlug;
            $ya['name'] = $yearApwd;
            $aYearApproved[] = $ya;
            
            //drug for
            $drugFor = $details->drug_for;
            $df=array();
            $df['condition'] = $conditionSlug;
            $df['name'] = $drugFor->name;
            $df['description'] = $drugFor->description;
            $aDrugFor[] = $df;
            
            //who for
            $whoFor = $details->who_for;
            $wf=array();
            $wf['condition'] = $conditionSlug;
            $wf['name'] = $whoFor->name;
            $wf['description'] = $whoFor->description;
            $aWhoFor[] = $wf;
            
            //limitations
            $limitations = $details->limitations;
            if(is_array($limitations)) foreach($limitations as $l){
                $lim=array();
                $lim['condition'] = $conditionSlug;
                $lim['name'] = $l->name;
                $lim['description'] = $l->description;
                $aLimitations[] = $lim;
            }
            
            //track record
            $trackRecord = $details->track_record;
            $tr=array();
            $tr['condition'] = $conditionSlug;
            $tr['name'] = $trackRecord->name;
            $tr['description'] = $trackRecord->description;
            $aTrackRecord[] = $tr;
            
            //open questions
            $openQuestions = $details->open_questions;
            $oq=array();
            $oq['condition'] = $conditionSlug;
            $oq['name'] = $openQuestions->name;
            $oq['description'] = $openQuestions->description;
            $aOpenQuestions[] = $oq;
            
            //bottom line
            $bottomLine= $details->bottom_line;
            if(!empty($bottomLine)) foreach($bottomLine as $b){
                $bl=array();
                $bl['condition'] = $conditionSlug;
                $bl['name'] = $b->name;
                $bl['description'] = $b->description;
                $aBottomLine[] = $bl;
            }
            
            //trial description
            $trialDescription = $details->trial_description;
            $td=array();
            $td['condition'] = $conditionSlug;
            $td['name'] = $trialDescription->name;
            $td['description'] = $trialDescription->description;
            $aTrialDescription[] = $td;
            
            //how to use
            $howToUse = $details->how_to_use;
            $htu=array();
            $htu['condition'] = $conditionSlug;
            $htu['starting-dose'] = $howToUse->starting_dose;
            $htu['maximum-dose'] = $howToUse->maximum_dose;
            $htu['approved-dose'] = $howToUse->approved_dose;
            $htu['rec-dose'] = $howToUse->rec_dose;
            $htu['titration-instructions'] = $howToUse->titration_instructions;
            $htu['missed-dose'] = $howToUse->missed_dose;
            $htu['stopping-instructions'] = $howToUse->stopping_instructions;
            $htu['special-populations'] = $howToUse->special_populations;
            $htu['other'] = $howToUse->other;
            $aHowToUse[] = $htu;
            
            //time to results
            $timeResults = $details->time_to_results;
            $ttr=array();
            $ttr['condition'] = $conditionSlug;
            $ttr['name'] = $timeResults;
            $aTimeResults[] = $ttr;
            
            //boxes
            $boxes = $details->box;
            if(!empty($boxes)){
                foreach($boxes as $box){
                    $test_box = array();
                    $test_box['condition'] = $conditionSlug;
                    $test_box['infy-name'] = $box->infy_name;
                    $test_box['fda-id'] = $box->fda_id;
                    $test_box['infy-id'] = $box->infy_id;
                    $test_box['source-side-effects'] = $box->source_side_effects;
                    $aBoxes[] = $test_box;
                    
                    //who was tested
                    $tested = $box->who_was_tested;
                    if(!empty($tested)){
                        $who_tested = array();
                        $who_tested['condition'] = $conditionSlug;
                        $who_tested['box'] = $box->infy_name;
                        $who_tested['number'] = $tested->number;
                        $who_tested['age-range'] = $tested->age_range;
                        $who_tested['age-average'] = $tested->age_average;
                        $who_tested['study-length'] = $tested->study_length;
                        $who_tested['sex'] = $tested->sex;
                        $who_tested['description'] = $tested->trial_description;
                        $aTested[] =$who_tested;
                    }
                    
                    //test conditions
                    $testCondition = $tested->conditions;
                    if(!empty($testCondition)){
                        foreach($testCondition as $tc){
                            $test_cond = array();
                            $test_cond['condition'] = $conditionSlug;
                            $test_cond['box'] = $box->infy_name;
                            $test_cond['condition-name'] = $tc->condition;
                            $test_cond['code'] = $tc->code;
                            $test_cond['explanation'] = $tc->explanation;
                            $test_cond['severity'] = $tc->severity;
                            $test_cond['scale'] = $tc->scale;
                            $aTestCond[] = $test_cond;
                        }
                    }
                    
                    //test groups
                    $testGroups = $tested->group_description;
                    if(!empty($testGroups)){
                        foreach($testGroups as $tg){
                            $test_group = array();
                            $test_group['condition'] = $conditionSlug;
                            $test_group['box'] = $box->infy_name;
                            $test_group['name'] = $tg->name;
                            $test_group['dosing'] = $tg->dosing;
                            $test_group['background'] = $tg->background;
                            $aTestGroups[] = $test_group;
                        }
                    }
                    
                    //benefits
                    $benefits = $box->how_drug_helps->study_results;
                    if(!empty($benefits)){
                        foreach($benefits as $bnf){
                            $benefit = array();
                            $benefit['condition'] = $conditionSlug;
                            $benefit['box'] = $box->infy_name;
                            $benefit['name'] = $bnf->outcome;
                            $aBenefits[] = $benefit;
                            
                            //benefits details
                            $benefitsDetails = $bnf->outcome_details;
                            if(!empty($benefitsDetails)){
                                foreach($benefitsDetails as $bnfdet){
                                    $benefit_det = array();
                                    $benefit_det['condition'] = $conditionSlug;
                                    $benefit_det['box'] = $box->infy_name;
                                    $benefit_det['benefit'] = $bnf->outcome;
                                    $benefit_det['comparison'] = $bnfdet->comparison;
                                    $benefit_det['description'] = $bnfdet->description;
                                    $benefit_det['scale'] = $bnfdet->scale;
                                    $benefit_det['abs-difference'] = $bnfdet->abs_difference;
                                    $benefit_det['ruler'] = $bnfdet->ruler;
                                    $benefit_det['code'] = $bnfdet->code;
                                    $benefit_det['priority'] = $bnfdet->priority;
                                    $aGroups = array();
                                    foreach($bnfdet->groups as $k=>$v){
                                        $aGroups[] = $v;
                                    }
                                    $benefit_det['group0'] = $aGroups[0]; 
                                    if(isset($aGroups[1])){
                                        $benefit_det['group1'] = $aGroups[1];
                                    }
                                    $aBenefitsDetails[] = $benefit_det;
                                }
                            }
                        }
                    }
                    
                    //benefits source
                    $bnfsources = $box->how_drug_helps->source_benefit;
                    $bnfsource=array();
                    $bnfsource['condition'] = $conditionSlug;
                    $bnfsource['box'] = $box->infy_name;
                    $bnfsource['name'] = $bnfsources;
                    $aBenefitsSource[] = $bnfsource;
                    
                    //side effects
                    $sideEffects = $box->side_effects;
                    if(!empty($sideEffects)){
                        if(!empty($sideEffects)){
                            foreach($sideEffects as $sde){
                                $sideeffect = array();
                                $sideeffect['condition'] = $conditionSlug;
                                $sideeffect['box'] = $box->infy_name;
                                $sideeffect['name'] = $sde->name;
                                $sideeffect['medical-name'] = $sde->medical_name;
                                $sideeffect['description'] = $sde->description;
                                $sideeffect['code'] = $sde->code;
                                $sideeffect['abs-difference'] = $sde->abs_difference;
                                $sideeffect['priority'] = $sde->priority;
                                $sideeffect['comparison'] = $sde->comparison;
                                $aGroups = array();
                                foreach($sde->groups as $k=>$v){
                                    $aGroups[] = $v;
                                }
                                $sideeffect['group0'] = $aGroups[0];
                                if(isset($aGroups[1])){
                                    $sideeffect['group1'] = $aGroups[1];
                                }
                                $aSideEffects[] = $sideeffect;
                            }
                        }
                    }
                    
                    //side effects source
                    $sesources = $box->source_side_effects;
                    $sesource=array();
                    $sesource['condition'] = $conditionSlug;
                    $sesource['box'] = $box->infy_name;
                    $sesource['name'] = $sesources;
                    $aSideEffectsSource[] = $sesource;
                    
                }                
            }
            
        }
        infy_import_save_meta($post_id, 'conditions', $aCond);
        infy_import_save_meta($post_id, 'year_approved', $aYearApproved);
        infy_import_save_meta($post_id, 'drug_for', $aDrugFor);
        infy_import_save_meta($post_id, 'who_for', $aWhoFor);
        infy_import_save_meta($post_id, 'limitations', $aLimitations);
        infy_import_save_meta($post_id, 'track_record', $aTrackRecord);
        infy_import_save_meta($post_id, 'open_questions', $aOpenQuestions);
        infy_import_save_meta($post_id, 'bottom_line', $aBottomLine);
        infy_import_save_meta($post_id, 'trial_description', $aTrialDescription);
        infy_import_save_meta($post_id, 'take', $aHowToUse);
        infy_import_save_meta($post_id, 'time_to_results', $aTimeResults);
        infy_import_save_meta($post_id, 'box', $aBoxes);
        infy_import_save_meta($post_id, 'tested', $aTested);
        infy_import_save_meta($post_id, 'test_cond', $aTestCond);
        infy_import_save_meta($post_id, 'test_groups', $aTestGroups);
        infy_import_save_meta($post_id, 'benefits',$aBenefits);
        infy_import_save_meta($post_id, 'benefits_source',$aBenefitsSource);
        infy_import_save_meta($post_id, 'benefits_details',$aBenefitsDetails);
        infy_import_save_meta($post_id, 'box_side_effects', $aSideEffects);
        infy_import_save_meta($post_id, 'sideeffects_source', $aSideEffectsSource);
    }
    
    
    if (function_exists('infyso_update_post_index')){
        infyso_update_post_index($post_id);
    }
}

$saved = false;
$error = false;

if (!empty($_POST['submit'])){
    if(!empty($_POST['import_data'])){
        $data = @json_decode(stripslashes($_POST['import_data']));
    }
    
    if(!empty($_FILES['import_file']['tmp_name'])){
        $data = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = @json_decode($data);
    }
    
    if (!empty($data) && import_drug($data)){
        $saved = true;
    } else {
        $error = true;
    }
}

?>

<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Informulary json import</h2>
    <?php 
    if ($saved) {
        echo '<div id="message" class="updated fade"><p><strong>' . $data->drug_name . '. Import done.</strong></p></div>';
    }
    if ($error) {
        echo '<div id="message" class="error fade"><p><strong>Error input data has broken or empty.</strong></p></div>';
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tr><th colspan="2">Past json</th></tr>
            <tr>
                <td>
                    <label for="import_data">JSON for 1 drug</label>
                </td>
                <td>
                    <textarea name="import_data"></textarea>
                </td>
            </tr>
            <tr><th colspan="2">Or upload file</th></tr>
            <tr>
                <td>
                    <label for="import_data">File with json for 1 drug</label>
                </td>
                <td>
                    <input type="file" name="import_file"></textarea>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" value="Import" class="button button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
