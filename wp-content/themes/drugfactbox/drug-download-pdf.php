<?php
global $page_tab, $meta, $condition, $conditions, $aConditions, $allowedDrug, $how_to_use_display, $precautions_display, $interactions_display;
if ( !is_user_logged_in() or !$allowedDrug){
    header('HTTP/1.0 404 Not Found');
    header("Location: ".esc_url( home_url( '/404/' ) ));
}

$cd = getdate();
$year = $cd['year'];

function tableBlock($title, $data, $divider=true){
    if(!empty($data['name']) or !empty($data['description'])){
        $html_add= '<div class="table3col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
            <td align="right" valign="top" width="150" style="width: 150px;border-right:1px solid #d7d7d7;">
					<h2>'.$title.'&nbsp;</h2>
		    </td>
			<td align="left" valign="top">
            <h3 style="font-size:10px">'.@strip_tags($data['name']).'</h3><p style="font-size:8px">'.@strip_tags($data['description']).'</p>
            </td>
		</tr>	
		</table></div>';
        if($divider){
            $html_add.='<hr />';
        }
        return $html_add;
    }
}

function tableBlockMultiRow($title, $data, $divider=true){
    if(!empty($data)){
        $html_add= '<div class="table3col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>			
			<td align="right" valign="top" width="150" style="width: 150px;border-right:1px solid #d7d7d7;">
					<h2>'.$title.'&nbsp;</h2>
		    </td>
			<td>';            
            foreach($data as $itm){
				$html_add.= '<h3 style="font-size:10px">'.@strip_tags($itm['name']).'</h3>
				<p style="font-size:8px;padding-bottom:10px;">'.@strip_tags($itm['description']).'</p>';
            }
        $html_add.= '</td>
		</tr>	
		</table></div>';
        if($divider){
            $html_add.='<hr />';
        }
        return $html_add;
    }
}

require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';

Dompdf\Autoloader::register();
use Dompdf\Dompdf;
$dompdf = new Dompdf();

/*
html, body {margin: 50px 25px 30px 25px; font-size:12px;}

    #header { position: fixed; left: 0px; top: -50px; right: 0px; height: 40px; background-color: orange; text-align: center; text-align:center}
    #footer { position: fixed; left: 0px; bottom: -20px; right: 0px; height: 60px; background-color: lightblue;text-align:center; font-size:8px}
    #footer .copy{float:left; width: 200px}
    #footer .pnumber{float:right; width: 200px}
    #footer .pnumber span:after {display:inline; content: counter(page);}
    .page{position:relative;top: -50px}
    .tableBlock td div{width:100%;text-align:left}
    .tableBlock td{padding: 5px 10px; text-align:left; vertical-align:top;}
    .tableBlock td.blockTitle{width:200px;text-align:right;}
    
    .descriptionCell{width:300px}
    
    
        @import url(http://fonts.googleapis.com/css?family=Oswald:400,300,700);

@import url(http://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900);

@font-face {
  font-family: "Tahoma", sans-serif;
  src: url("fonts/Tahoma.eot?#iefix") format("embedded-opentype"),  
  url"fonts/Tahoma.woff") format("woff"), 
  url("fonts/Tahoma.ttf")  format("truetype"), 
  url("fonts/Tahoma.svg#Tahoma") format("svg");
  font-weight: normal;
  font-style: normal;
}

@font-face {
  font-family: "Tahoma-Bold", sans-serif;
  src: url("fonts/Tahoma-Bold.eot?#iefix") format("embedded-opentype"),  
  url("fonts/Tahoma-Bold.woff") format("woff"), 
  url("fonts/Tahoma-Bold.ttf")  format("truetype"), 
  url("fonts/Tahoma-Bold.svg#Tahoma-Bold") format("svg");
  font-weight: normal;
  font-style: normal;
}
*{
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
*:before,
*:after {
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
*/

$html = '
<html>
<head>
  <style>

html, body {margin: 130px 0 20px 0}
 
body {
	font-family: Arial, sans-serif;
	font-size: 18px;
    font-weight: 400;
	color:#424242;
}
h1 {
    font-family: Arial Narrow, Arial, sans-serif;
    font-size: 40px;
    font-weight: 700;
    margin:0 0 40px 0;
	padding:20px 0 0 0;
    text-align: center;
}
h2 {
    font-family: Arial Narrow, Arial, sans-serif;
    font-size: 18px;
    font-weight: 900;
    margin:0;
	padding:0;
}
h3{
    font-family: Arial Narrow, Arial, sans-serif;
    font-size: 12px;
    font-weight: 900;
    margin:0 0 5px 0;
	padding:0;
}
h5{
    font-family: Arial Narrow, Arial, sans-serif;
	font-size: 12px;
    font-weight: 400;
    margin:0;
	padding:0;
}
p{
    margin:0 0 20px 0;
	padding:0;
    font-size: 12px;
}
hr{
  height: 0;
  border:0;
  border-top:2px solid #d7d7d7;
  margin:0;
  padding:0;
}
div{
  margin:0;
  padding:0;
}
.border-top td{
  border-top:2px solid #d7d7d7;
}
table {
  border-spacing: 0;
  border-collapse: collapse;
}
td,
th{
  padding:0;
}
/* start #header
------------------------------------------------- */
#header{
	position:fixed;
	top:-100;
	left:0;
	right:0;
	width:100%;
    height: 100px;
}
.field-header{
	border-bottom:2px solid #333;
	margin:60px 80px 0 80px;
	padding-bottom:20px;
}
/* stop #header
------------------------------------------------- */

/* start page
------------------------------------------------- */
.page{
	width:100%;
	float:left;
	padding:0px 80px 120px 80px;
    position:relative;
    top:-120px;
}
.page table{
	margin:40px 0 45px 0;
}
.page table td{
	padding:0 20px;
}
.table3col-1 td p{
	margin-bottom:0;
}
.table2col-1 td p{
	margin-bottom:30px;
}
.table3col-2 table td{
	padding-top:20px;
	padding-bottom:20px;
}
.table3col-2 td p{
	margin:0;
    font-size: 12px;
}
.page .table4col-1 table td{
	padding:0 10px;
}
.page .table4col-1 table tr.border-top td{
	padding-top:15px;
}
.table4col-1 span{
	display:block;
    padding:0;
    margin-top:0px;
	margin-bottom:10px;
	font-size:12px;
	color:#666;
}
/* stop page
------------------------------------------------- */

/* start #footer
------------------------------------------------- */
#footer{
    height:160px;
	position:fixed;
	bottom:0;
	left:0;
	right:0;
	width:100%;
}
.field-footer{
	/*border-top:2px solid #d7d7d7;*/
	margin:0 80px 60px 80px;
	padding:30px 0 0 0;
    font-size: 10px;
    color: #666;
}
.field-footer table{
	margin-bottom:30px;
}
#footer span{color:#000}
#footer span:after{
    color:#000;
	display:inline; 
	content: counter(page);
}
#footer a{color:#337ab7; position:relative; bottom:2px;}
/* stop #footer
------------------------------------------------- */


  </style>
</head>
<body >
<div id="header">
	<div class="field-header">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="left" valign="top">
					<h2>'.$post->post_title.' ('.$meta['maininfo_chem-name_1'][0].')</h2>
				</td>
				<td align="right" valign="top">
					<h2>For '.$condition->post_title.'</h2>
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="footer">
	<div class="field-footer">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="left" valign="top">
					This information is meant to enhance communication with 
					your health care, not replace it. The content here in is 
					meant to educate consumers on health care and medical issues 
					that may affect their daily lives. Nothing in this content 
					should be considered or used as a subsstitute for medical 
					advice, diagnosis or treatmeny, or constitute the practice 
					of any medical or other professional health care advice. 
					You should always talk to your health care provider for 
					diagnosis and treatment, including your specific medical 
					needs. This product does not represent or warrant any 
					particular service or product is safe, appropriate or 
					effective for you. See terms for more details.
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="left" valign="top">
					<div>&copy;'.$year.' <a href="http://drugfactsbox.co">Informulary, Inc.</a> All Rights Reserved</div>
				</td>
				<td align="right" valign="top">
					<span>Page </span>
				</td>
			</tr>
		</table>
	</div>
</div>';
  
//Overview  
$html.='    <div class="page">
    <h1>Overview</h1><hr />';
    
$availability = array();
if($meta['maininfo_prescription_1'][0] == 'Y' or $meta['maininfo_prescription_1'][0] == 'y' or $meta['maininfo_prescription_1'][0] == 'Yes' or $meta['maininfo_prescription_1'][0] == 'yes'){ 
    $availability[] ='Prescription';
}   
if($meta['maininfo_otc_1'][0] == 'Y' or $meta['maininfo_otc_1'][0] == 'y' or $meta['maininfo_otc_1'][0] == 'Yes' or $meta['maininfo_otc_1'][0] == 'yes'){
    $availability[] ='Over the counter';
}
if($meta['maininfo_generic_1'][0] == 'Y' or $meta['maininfo_generic_1'][0] == 'y' or $meta['maininfo_generic_1'][0] == 'Yes' or $meta['maininfo_generic_1'][0] == 'yes'){
    $availability[] ='Generic';
}
$sAvailability = implode(', ',$availability);


$yearApproved = isset($meta['year_approved'][0]) ? @unserialize($meta['year_approved'][0]) : '';
$aYA = array('name'=>'N/A');
if(!empty($yearApproved)){
    foreach($yearApproved as $ya_cond){
        if($ya_cond['condition'] === @$condition->post_name){
            $aYA = $ya_cond;
        }
    }
}

$sideeffects = isset($meta['drug_side_effects'][0]) ? @unserialize($meta['drug_side_effects'][0]) : ''; 
$bbw = 0;    
if(!empty($sideeffects)){
    foreach($sideeffects as $se){
        if($se['type'] === 'bbw' and (!empty($se['name']) or !empty($se['description']))){
            $bbw++;
        }
    }
}

$html.='<div class="table3col-1">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="center" valign="top" width="33.33333333%">
					<h3>AVAILABILITY</h3>
					<p>'.$sAvailability.'</p>
				</td>
				<td align="center" valign="top" style="border-left:1px solid #d7d7d7; border-right:1px solid #d7d7d7;" width="33.33333333%">
					<h3>TRACK RECORD</h3>
					<p>Approved in '.$aYA['name'].'</p>
				</td>
				<td align="center" valign="top" width="33.33333333%">
					<h3>NOTABLE SIDE EFFECTS</h3>
					<p>'.($bbw ? 'Black Box Warning' : 'None').'</p>
				</td>
			</tr>
		</table>
	</div>
    <hr />';


$bottomLine = isset($meta['bottom_line'][0]) ? @unserialize($meta['bottom_line'][0]) : '';
$aBL = array(0=>array('name'=>'N/A','description'=>''));
if(!empty($bottomLine)){
    $aBL = array();
    foreach($bottomLine as $bl_cond){
        if($bl_cond['condition'] === @$condition->post_name){
            if(!empty($bl_cond['name']) or !empty($bl_cond['description'])){
                $aBL[] = $bl_cond;
            }
        }
    }
}
$drugFor = isset($meta['drug_for'][0]) ? @unserialize($meta['drug_for'][0]) : '';
$aDF = array('name'=>'N/A','description'=>'');
if(!empty($drugFor)){
    foreach($drugFor as $df_cond){
        if($df_cond['condition'] === @$condition->post_name and (!empty($df_cond['name']) or !empty($df_cond['description']))){
            $aDF = $df_cond;
        }
    }
}

$whoFor = isset($meta['who_for'][0]) ? @unserialize($meta['who_for'][0]) : '';
$aWF = array('name'=>'N/A','description'=>'');
if(!empty($whoFor)){
    foreach($whoFor as $wf_cond){
        if($wf_cond['condition'] === @$condition->post_name and (!empty($wf_cond['name']) or !empty($wf_cond['description']))){
            $aWF = $wf_cond;
        }
    }
}

$limitations = isset($meta['limitations'][0]) ? @unserialize($meta['limitations'][0]) : '';
$aLim = array(0=>array('name'=>'N/A','description'=>''));
if(!empty($limitations)){
    $aLim = array();
    foreach($limitations as $lim_cond){
        if(@$lim_cond['condition'] === @$condition->post_name){
            if(!empty($lim_cond['name']) or !empty($lim_cond['description'])){
                $aLim[] = $lim_cond;
            }
        }
    }
}

$trackRecord = isset($meta['track_record'][0]) ? @unserialize($meta['track_record'][0]) : '';
$aTR = array('name'=>'N/A','description'=>'');
if(!empty($trackRecord)){
    foreach($trackRecord as $tr_cond){
        if($tr_cond['condition'] === @$condition->post_name and (!empty($tr_cond['name']) or !empty($tr_cond['description']))){
            $aTR = $tr_cond;
        }
    }
}

$openQuestions = isset($meta['open_questions'][0]) ? @unserialize($meta['open_questions'][0]) : '';
$aOQ = array('name'=>'N/A','description'=>'');
if(!empty($openQuestions)){
    foreach($openQuestions as $oq_cond){
        if($oq_cond['condition'] === @$condition->post_name and (!empty($oq_cond['name']) or !empty($oq_cond['description']))){
            $aOQ = $oq_cond;
        }
    }
}

$contraindications = isset($meta['contraindications'][0]) ? @unserialize($meta['contraindications'][0]) : '';
$aContraIndications = array();
if(!empty($contraindications)){
    foreach($contraindications as $ci){
        if(@$ci['type'] === 'contraindication'){
            $aContraIndications['contraindication'][] = $ci;
        }
    }
}

$notrecommended = isset($meta['notrecommended'][0]) ? @unserialize($meta['notrecommended'][0]) : '';
$aNotRecommended = array();
if(!empty($notrecommended)){
    foreach($notrecommended as $nr){
        if($nr['type'] === 'not_rec'){
            $aNotRecommended['not_rec'][] = $nr;
        }
    }
}

$html.=tableBlockMultiRow('Bottom Line', $aBL);

$html.=tableBlock('FDA-approved use', $aDF);

$html.=tableBlock('Who might consider taking it?', $aWF);

$html.=tableBlockMultiRow('What is not known', $aLim);

$html.=tableBlock('Track record', $aTR);

$html.=tableBlock('Open Questions', $aOQ);

$html.=tableBlockMultiRow('Do not take if you...', $aContraIndications['contraindication']);

$html.=tableBlockMultiRow('Not recommended if you...', $aNotRecommended['not_rec']);

if(!empty($meta['pregnant_breastfeeding_pregnancy-description_1'][0])){
    $data[]=array('name'=>'Pregnant', 'description'=>$meta['pregnant_breastfeeding_pregnancy-description_1'][0]);
}
if(!empty($meta['pregnant_breastfeeding_breastfeeding-description_1'][0])){
    $data[]=array('name'=>'Breastfeeding', 'description'=>$meta['pregnant_breastfeeding_breastfeeding-description_1'][0]);
}
$html.=tableBlockMultiRow("Safe if pregnant or breastfeeding?", $data, false);

$html.='</div>';

//Trials    
$html.='<div class="page" style="page-break-before: always;">';
$html.='<h1>FDA Clinical Trials</h1>';
$trialDescription = isset($meta['trial_description'][0]) ? @unserialize($meta['trial_description'][0]) : '';
$aTD = array();
if(!empty($trialDescription)){
    foreach($trialDescription as $td_cond){
        if($td_cond['condition'] === $condition->post_name and (!empty($td_cond['name']) or !empty($td_cond['description']))){
            $aTD = $td_cond;
        }
    }
}

$html.=tableBlock('Story of FDA approval', $aTD);

$html.=tableBlockMultiRow('Bottom Line', $aBL, false);

$html.='</div>';

$aBoxes = isset($meta['box'][0]) ? @unserialize($meta['box'][0]) : '';
$aTestBoxes = array();
if(!empty($aBoxes)){
    foreach($aBoxes as $box_cond){
        if($box_cond['condition'] === $condition->post_name){
            $aTestBoxes[] = $box_cond;
        }
    }
}
$aTestNumbers = isset($meta['tested'][0]) ? @unserialize($meta['tested'][0]) : '';
if(!empty($aTestBoxes)){
    foreach($aTestBoxes as $k=>$box){
        $aTested = isset($meta['tested'][0]) ? @unserialize($meta['tested'][0]) : '';
        $aCurrentTested = array();
        if(!empty($aTested)){
            foreach($aTested as $tested_cond){
                if($tested_cond['condition'] == $condition->post_name and $tested_cond['box'] == $box['infy-name']){
                    $aCurrentTested = $tested_cond;
                }
            }
        }
        $aTestConditions = isset($meta['test_cond'][0]) ? @unserialize($meta['test_cond'][0]) : '';
        if(!empty($aTestConditions)){
            $aCurrentTestConditions = array();
            foreach($aTestConditions as $tc_cond){
                if($tc_cond['condition'] == $condition->post_name and $tc_cond['box'] == $box['infy-name']){
                    $aCurrentTestConditions[] = $tc_cond;
                }
            }
        }
        $aTestGroups = isset($meta['test_groups'][0]) ? @unserialize($meta['test_groups'][0]) : '';
        if(!empty($aTestGroups)){
            $aCurrentTestgroups = array();
            foreach($aTestGroups as $tg_cond){
                if($tg_cond['condition'] == $condition->post_name and $tg_cond['box'] == $box['infy-name']){
                    $aCurrentTestgroups[] = $tg_cond;
                }
            }
        }
        $html.='<div class="page" style="page-break-before: always;">';
        $html.='<h2 style="margin-top:20px; text-align:center;">'.$box['infy-name'].'</h2>';
        $html.='<h1>Trial Summary</h1>';
        $html.='<p style="margin-bottom:50px;">'.$aCurrentTested['description'].'</p><hr />';
        $html.='<div class="table3col-2"><table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td align="center" valign="middle" width="33.33333333%" style="border-right:1px solid #d7d7d7; border-bottom:1px solid #d7d7d7;">
                    <h3>PATIENTS</h3><p>'.$aCurrentTested['number'].' ('.$aCurrentTested['sex'].')<p>
                    </td>
                    <td align="center" valign="middle" width="33.33333333%" style="border-right:1px solid #d7d7d7; border-bottom:1px solid #d7d7d7;">
                    <h3>AGE</h3><p>'.$aCurrentTested['age-range'].(!empty($aCurrentTested['age-average']) ? (' (average '.$aCurrentTested['age-average'].')') : '').'</p>
                    </td>
                    <td align="center" valign="middle" width="33.33333333%" style="border-bottom:1px solid #d7d7d7;">
                    <h3>CONDITIONS</h3><p>';
                    if(!empty($aCurrentTestConditions)){
                        foreach($aCurrentTestConditions as $ctc){
                            $html.='<div>
                                <p><b>'.$ctc['condition-name'].'<b/></p>
                                <p>'.$ctc['explanation'].'</p>';
                            if(!empty($ctc['severity'])){
                                $html.='<p>Severity: '.$ctc['severity'].'</p>';
                            }
                            $html.='</div>';
                        }
                    }
                    $html.='</p></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" width="33.33333333%" style="border-right:1px solid #d7d7d7;">
                    <h3>TYPE</h3><p>'.(count($aCurrentTestgroups) > 1 ? 'Randomized Trial' : 'Uncontrolled Trial').'</p>
                    </td>
                    <td align="center" valign="middle" width="33.33333333%" style="border-right:1px solid #d7d7d7;">
                    <h3>DRUGS</h3>
                    <p>'.$aCurrentTestgroups[0]['name'];
                    if(!empty($aCurrentTestgroups[0]['background'])){
                        $html.='<br />+'.$aCurrentTestgroups[0]['background'];
                    }
                    $html.='</p>';
                    if(!empty($aCurrentTestgroups[1]['name'])){
                        $html.='<p>'.$aCurrentTestgroups[1]['name'];
                        if(!empty($aCurrentTestgroups[1]['background'])){
                            $html.='<br />+'.$aCurrentTestgroups[1]['background'];
                        }
                    }
                    $html.='</td>
                    <td align="center" valign="middle" width="33.33333333%">
                    <h3>TRIAL DURATION</h3><p>'.@$aCurrentTested['study-length'].'</p>
                    </td>
                </tr>
            </table></div><hr />';
        $html.='</div>';
        
        $html.='<div class="page" style="page-break-before: always;">';
        $html.='<h2 style="margin-top:20px; text-align:center;">'.$box['infy-name'].'</h2>';
        $html.='<h1>Benefits</h1>';
        $aBenefits = isset($meta['benefits'][0]) ? @unserialize($meta['benefits'][0]) : '';
        if(!empty($aBenefits)){
            $aCurrentBenefits = array();
            foreach($aBenefits as $bnf_cond){
                if($bnf_cond['condition'] == $condition->post_name and $bnf_cond['box'] == $box['infy-name']){ 
                    $aCurrentBenefits[] = $bnf_cond;
                }
            }
        }
        if(!empty($aCurrentBenefits)){
            foreach($aCurrentBenefits as $bnf){
                $html.='<h2 style="text-align:center;">'.$bnf['name'].'</h2>';
                $aBenefitsDetails = isset($meta['benefits_details'][0]) ? @unserialize($meta['benefits_details'][0]) : ''; 
                if(!empty($aBenefitsDetails)){
                    $aCurrentBenefitsDetails = array();
                    foreach($aBenefitsDetails as $bnfdet_cond){
                        if($bnfdet_cond['condition'] === $condition->post_name and $bnfdet_cond['box'] === $box['infy-name'] and $bnfdet_cond['benefit'] === $bnf['name']){ 
                            $aCurrentBenefitsDetails[] = $bnfdet_cond;
                        }
                    }
                }
                if(!empty($aCurrentBenefitsDetails)){
                    $html.='<div class="table4col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
                    $html.='<thead><tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><h5>'.$aCurrentTestgroups[0]['name'].'</h5></td>';
                    if(isset($aCurrentTestgroups[1]['name'])){
                        $html.='<td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>
                                <td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).' vs. '.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>';
                    }
                    $html.='</tr>';
                    $html.='<tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[0]['dosing']).'</span></td>';
                    if(isset($aCurrentTestgroups[1]['name'])){
                        $html.='<td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[1]['dosing']).'</span></td>
                                <td align="center" valign="top" width="25%"></td>';
                    }
                    $html.='</tr></thead><tbody>';
                    foreach($aCurrentBenefitsDetails as $bnf_det){
                        $html.='<tr class="border-top">';
                        $html.='<td align="letf" valign="top" width="25%" style="padding-left:0;"><p><strong>'.htmlspecialchars($bnf_det['description']).'</strong></p></td>
                                <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($bnf_det['group0']).'</p></td>';
                        if(isset($aCurrentTestgroups[1]['name'])){
                            $html.='<td align="center" valign="top" width="25%"><p>'.@htmlspecialchars($bnf_det['group1']).'</p></td>
                            <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($bnf_det['comparison']).'</p>';
                            if(!empty($bnf_det['abs-difference'])){
                                $html.='<p>'.htmlspecialchars($bnf_det['abs-difference']).'</p></td>';
                            }
                        }
                        $html.='</tr>';
                    }
                    $html.='</tbody></table></div>';
                    
                        
                }
            }
            $benefitsSource = isset($meta['benefits_source'][0]) ? @unserialize($meta['benefits_source'][0]) : '';
            $aBS = array('name'=>'N/A');
            if(!empty($benefitsSource)){
                foreach($benefitsSource as $bs_cond){
                    if($bs_cond['condition'] === $condition->post_name and $bs_cond['box'] === $box['infy-name']){
                        $aBS = $bs_cond;
                    }
                }
            }
            if(!empty($aBS)){
                $html.='<p>'.htmlspecialchars($aBS['name']).'</p>';
            }
        }
        $html.='</div>';
        
        $aSideEffects = isset($meta['box_side_effects'][0]) ? @unserialize($meta['box_side_effects'][0]) : '';
        if(!empty($aSideEffects)){
            $aCurrentSideEffects = array();
            foreach($aSideEffects as $se_cond){
                if($se_cond['condition'] === $condition->post_name and $se_cond['box'] === $box['infy-name']){
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
        $html.='<div class="page" style="page-break-before: always;">';
        $html.='<h2 style="margin-top:20px; text-align:center;">'.$box['infy-name'].'</h2>';
        $html.='<h1>Side Effects</h1>';
        if(!empty($aCurrentSideEffects['bbw'])){
            $html.='<h2 style="margin-top:20px; text-align:center;">Black Box Warning - FDA\'s Most Serious Alert</h2>';
            $html.='<div class="table4col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html.='<thead><tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).'</h5></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>
                        <td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).' vs. '.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>';
            }
            $html.='</tr>';
            $html.='<tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[0]['dosing']).'</span></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[1]['dosing']).'</span></td>
                        <td align="center" valign="top" width="25%"></td>';
            }
            $html.='</tr></thead><tbody>';           
            foreach($aCurrentSideEffects['bbw'] as $se){
                $html.='<tr class="border-top">';
                $html.='<td align="letf" valign="top" width="25%" style="padding-left:0;"><p><strong>'.htmlspecialchars($se['name']).'</strong></p></td>
                        <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['group0']).'</p></td>';
                if(isset($aCurrentTestgroups[1]['name'])){
                    $html.='<td align="center" valign="top" width="25%"><p>'.@htmlspecialchars($se['group1']).'</p></td>
                            <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['comparison']).'</p></td>';
                }
                $html.='</tr>';
            }            
            $html.='</tbody></table></div>'; 
        }
        $html.='</div>'; 
        $html.='<div class="page" style="page-break-before: always;">';
        if(!empty($aCurrentSideEffects['serious'])){
            $html.='<h2 style="margin-top:20px; text-align:center;">Serious</h2>';
            $html.='<div class="table4col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html.='<thead><tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).'</h5></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>
                        <td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).' vs. '.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>';
            }
            $html.='</tr>';
            $html.='<tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[0]['dosing']).'</span></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[1]['dosing']).'</span></td>
                        <td align="center" valign="top" width="25%"></td>';
            }
            $html.='</tr></thead><tbody>';           
            foreach($aCurrentSideEffects['serious'] as $se){
                $html.='<tr class="border-top">';
                $html.='<td align="letf" valign="top" width="25%" style="padding-left:0;"><p><strong>'.htmlspecialchars($se['name']).'</strong></p></td>
                        <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['group0']).'</p></td>';
                if(isset($aCurrentTestgroups[1]['name'])){
                    $html.='<td align="center" valign="top" width="25%"><p>'.@htmlspecialchars($se['group1']).'</p></td>
                            <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['comparison']).'</p></td>';
                }
                $html.='</tr>';
            }            
            $html.='</tbody></table></div>'; 
        }        
        $html.='</div>'; 
        $html.='<div class="page" style="page-break-before: always;">';
        if(!empty($aCurrentSideEffects['symptoms'])){
            $html.='<h2 style="margin-top:20px; text-align:center;">Symptoms</h2>';
            $html.='<div class="table4col-1"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html.='<thead><tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).'</h5></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>
                        <td align="center" valign="top" width="25%"><h5>'.htmlspecialchars($aCurrentTestgroups[0]['name']).' vs. '.htmlspecialchars($aCurrentTestgroups[1]['name']).'</h5></td>';
            }
            $html.='</tr>';
            $html.='<tr><td align="letf" valign="top" width="25%">&nbsp;</td><td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[0]['dosing']).'</span></td>';
            if(isset($aCurrentTestgroups[1]['name'])){
                $html.='<td align="center" valign="top" width="25%"><span>'.htmlspecialchars($aCurrentTestgroups[1]['dosing']).'</span></td>
                        <td align="center" valign="top" width="25%"></td>';
            }
            $html.='</tr></thead><tbody>';           
            foreach($aCurrentSideEffects['symptoms'] as $se){
                $html.='<tr class="border-top">';
                $html.='<td align="letf" valign="top" width="25%" style="padding-left:0;"><p><strong>'.htmlspecialchars($se['name']).'</strong></p></td>
                        <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['group0']).'</p></td>';
                if(isset($aCurrentTestgroups[1]['name'])){
                    $html.='<td align="center" valign="top" width="25%"><p>'.@htmlspecialchars($se['group1']).'</p></td>
                            <td align="center" valign="top" width="25%"><p>'.htmlspecialchars($se['comparison']).'</p></td>';
                }
                $html.='</tr>';
            }            
            $html.='</tbody></table></div>'; 
        }
        $seSource = isset($meta['sideeffects_source'][0]) ? @unserialize($meta['sideeffects_source'][0]) : '';
        $aSES = array('name'=>'N/A');
        if(!empty($seSource)){
            foreach($seSource as $ses_cond){
                if($ses_cond['condition'] === $condition->post_name and $ses_cond['box'] === $box['infy-name']){
                    $aSES = $ses_cond;
                }
            }
        }
        if(!empty($aSES)){
            $html.='<p>'.htmlspecialchars($aSES['name']).'</p>';
        }
        $html.='</div>';
        
    }
}    

//Side Effects
$html.='<div class="page" style="page-break-before: always;">';
$html.='<h1>Side Effects</h1><hr />';
$aSE = array();
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
$html.=tableBlockMultiRow('Black Box Warning', @$aSE['bbw']);
$html.=tableBlockMultiRow('Call Your Doctor', @$aSE['call_doctor']);
$html.=tableBlockMultiRow('Tell Your Doctor', @$aSE['tell_doctor']);
$html.='</div>';

//How to use
$html.='<div class="page" style="page-break-before: always;">';
$html.='<h1>How To Use</h1><hr />';
$doses = isset($meta['take'][0]) ? @unserialize($meta['take'][0]) : '';
$aD = array();
if(!empty($doses)){
    foreach($doses as $d_cond){
        if($d_cond['condition'] === $condition->post_name){
            if(!empty($d_cond['starting-dose'])){
                $data[] = array('name'=>'Starting Dose','description'=>$d_cond['starting-dose']);
            }
            if(!empty($d_cond['maximum-dose'])){
                $data[] = array('name'=>'Maximum Dose','description'=>$d_cond['maximum-dose']);
            }
            if(!empty($d_cond['approved-dose'])){
                $data[] = array('name'=>'Approved Dose','description'=>$d_cond['approved-dose']);
            }
            if(!empty($d_cond['rec-dose'])){
                $data[] = array('name'=>'Recommended Dose','description'=>$d_cond['rec-dose']);
            }
            if(!empty($d_cond['titration-instructions'])){
                $data[] = array('name'=>'Titration Instructions','description'=>$d_cond['titration-instructions']);
            }
            if(!empty($d_cond['missed-dose'])){
                $data[] = array('name'=>'What to do if you miss a dose','description'=>$d_cond['missed-dose']);
            }
            if(!empty($d_cond['stopping-instructions'])){
                $data[] = array('name'=>'How to Safely Stop The Drug','description'=>$d_cond['stopping-instructions']);
            }
            if(!empty($d_cond['special-populations'])){
                $data[] = array('name'=>'Notes for Special Populations','description'=>$d_cond['special-populations']);
            }
            if(!empty($d_cond['other'])){
                $data[] = array('name'=>'Other','description'=>$d_cond['other']);
            }
        }
    }
}
$html.=tableBlockMultiRow("Dose", $data);
$timeResults = isset($meta['time_to_results'][0]) ? @unserialize($meta['time_to_results'][0]) : ''; 
$aTTR = array('name'=>'N/A');
if(!empty($timeResults)){
    foreach($timeResults as $ttr_cond){
        if($ttr_cond['condition'] === $condition->post_name){
            $aTTR = $ttr_cond;
        }
    }
}
$html.=tableBlock('When you might see results', $aTTR);
$html.='</div>';


//Precautions
$html.='<div class="page" style="page-break-before: always;">';
$html.='<h1>Precautions</h1><hr />';
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
$html.=tableBlockMultiRow("Testing", $aPrecautions['testing']);
$html.=tableBlockMultiRow("What To Avoid", $aPrecautions['to_avoid']);
$html.='</div>';


//Interactions
$html.='<div class="page" style="page-break-before: always;">';
$html.='<h1>Interactions</h1><hr />';
$interactions = isset($meta['drug_interactions'][0]) ? @unserialize($meta['drug_interactions'][0]) : '';
$aInteractions = array();
if(!empty($interactions)){
    foreach($interactions as $ia){
        if($ia['type'] === 'interaction'){
            $aInteractions['interaction'][] = $ia;
        }
    }
}
$html.=tableBlockMultiRow("Drug Interactions", $aInteractions['interaction']);
$html.='</div>';
  
$html.='</body></html>'; //dump($html);die;

$dompdf-> load_html($html);

$dompdf->set_paper('a4', 'album');

$dompdf->render();



$dompdf->stream($post->post_name);