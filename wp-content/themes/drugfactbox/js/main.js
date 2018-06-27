var coverHeight;
var limitedForm;
var unlimitedForm;
jQuery(document).ready(function(){
    
    //cover feature
    /*var body = jQuery('body');
    var bodyHeight = jQuery('.backsheet').height();
    body.css('min-height', bodyHeight+'px');*/
       
    jQuery('.intro-td').height(jQuery(window).height());
    
    jQuery('.scroller').each(function(){
        jQuery(this).click(function(){
                var anchor = jQuery(this).attr('href');
				var offset = jQuery(anchor).offset().top-130;
				jQuery('body, html').animate({ scrollTop: offset}, 600);
				return false;
	    });
    });
    
    jQuery('#sectionSelector').change(function(){        
        var anchor = jQuery(this).val();
		var offset = jQuery(anchor).offset().top-130;
		jQuery('body, html').animate({ scrollTop: offset}, 600);
		return false;	    
    });
    
    //fixed menu on
    jQuery(window).scroll(function(){
        var scrollTop = jQuery(window).scrollTop();
        
               
        //mobile menu
        if(jQuery('body').hasClass('single-drug') || jQuery('body').hasClass('single-condition')){
            if(jQuery(window).scrollTop() > (jQuery('.container-fluid.hp').height() + jQuery('.container-fluid.header-about').height() + jQuery('.title-h1').outerHeight(true) + jQuery('.navbar-fixed-top').height())){
                jQuery('body').css('padding-top', jQuery('.in1-fixed').height());
                jQuery('.navbar-header').addClass('there-menu');
                jQuery('.in1').addClass('dekstop-left-black');
                jQuery('.in1-bars').css('visibility', 'hidden');
            }else{
                jQuery('body').css('padding-top', 0);
                jQuery('.navbar-header').removeClass('there-menu');
                jQuery('.in1').removeClass('dekstop-left-black');
                jQuery('.in1-bars').css('visibility', 'visible');
            }
        } 
        
    });
    
    //conditions filters on
    jQuery('.dropdown-custom').click(function(){ 
        if(jQuery('.dropdown-filter').css('display') == 'block'){
            jQuery('.dropdown-filter').css('display', 'none');
            jQuery(this).removeClass('active');
        }else{
            jQuery('.dropdown-filter').css('display', 'block');
            jQuery(this).addClass('active');
        }
        return false;
    });

    
    
    
    //condition filters select
    var selectedConditions = new Array();
    jQuery('#filterValues').find('input').each(function(){
        jQuery('#cond'+jQuery(this).attr('id').replace('filter','')).attr('checked', 'true');  
        if(jQuery('#cond'+jQuery(this).attr('id').replace('filter','')).is(':checked')){
            selectedConditions[jQuery(this).attr('id').replace('filter','')] = jQuery(this).val();
        }else{
            selectedConditions[jQuery(this).attr('id').replace('filter','')] = '';
        }
    });
    
    jQuery('.chek').find('input').click(function(){
        if(jQuery(this).is(':checked')){
            selectedConditions[jQuery(this).attr('id').replace('cond','')] = jQuery(this).val();
        }else{
            selectedConditions[jQuery(this).attr('id').replace('cond','')] = '';
        }
    });
    
    jQuery('#searchMenuItem').click(function(){
        setTimeout(function(){jQuery('#s').focus();}, 10);       
    });
    jQuery('#conditionsMenuItem').click(function(){
        setTimeout(function(){
            if(jQuery(window).height() < jQuery('.links-m').find('.container').height()){
                jQuery('.links-m').css('overflow', 'auto');
                jQuery('.links-m').height(jQuery(window).height()-125+'px');
            }else{
                jQuery('.links-m').height(jQuery('.links-m').find('.container').height());
            }
        }, 10);              
    });

    //conditions filters off
    jQuery('.close-x').find('a').click(function(){
        jQuery('#dropdownToggle').click();
    });
    jQuery('.filterGoLink').click(function(){
        jQuery('.dropdown-filter').css('display', 'none');
        jQuery('.dropdown-custom').removeClass('active');
        var valContainer = jQuery('#filterValues');
        var conditions = ''; 
        if(selectedConditions.length){      
            jQuery.each(selectedConditions, function(index, value){     
                if(value){
                    conditions+= '<div class="filterItem"><div class="pseudoBox"><input type="checkbox" name="condition'+index+'" id="filter'+index+'" value="'+value+'" checked/><label for="filter'+index+'" class="icon-ok-circled"></label></div>'+value+'</div>';
                }
            });
        }
        valContainer.html(conditions);
        jQuery('#conditionForm').submit();
        return false;
    });
    
    //remove selected filter value
    jQuery('#filterValues').on('click','label', function(){
        var checker = jQuery(this).prev();
        checker.click(function(){ 
            var id = jQuery(this).attr('id').replace('filter','');
            jQuery(this).parent().parent().remove();
            jQuery('#cond'+id).click();
            jQuery('#conditionForm').submit();
            return false;
        });
    });
    
    //generic filter
    jQuery('.genericCheck').click(function(){
        jQuery('#genericForm').submit();
    });
    
    //drugs display mode
    if(jQuery('#drugContainer').hasClass('list-block-1')){
        jQuery('#listMode').addClass('active');
        jQuery('#barMode').removeClass('active');
    }else{
        jQuery('#barMode').addClass('active');
        jQuery('#listMode').removeClass('active');
    }
    jQuery('#barMode').click(function(){
        jQuery('#drugContainer').removeClass('list-block-1');
        jQuery('#barMode').addClass('active');
        jQuery('#listMode').removeClass('active');
        eraseCookie("list_mode");
    });
    jQuery('#listMode').click(function(){
        jQuery('#drugContainer').addClass('list-block-1');
        jQuery('#listMode').addClass('active');
        jQuery('#barMode').removeClass('active');
        createCookie("list_mode", 1, { expires : 30 });
    });
    
    if(jQuery(window).width() <= 760){
        jQuery('#drugContainer').addClass('list-block-1');
    }else{
        if(!readCookie('list_mode')){
            jQuery('#drugContainer').removeClass('list-block-1');
        }
    }
    
    //mobile drug menu 
    if(jQuery(window).width() <= '767'){
        if(jQuery('body').hasClass('single-drug') || jQuery('body').hasClass('single-condition')){
            if(jQuery(window).scrollTop() > (jQuery('.header-product').height() + jQuery('.in1').height())){               
                jQuery('.navbar-header').addClass('there-menu');
                jQuery('.in1').addClass('dekstop-left-black');
            }else{                
                jQuery('.navbar-header').removeClass('there-menu');
                jQuery('.in1').removeClass('dekstop-left-black');
            }
        }
    }else{
        jQuery('.navbar-header').removeClass('there-menu');
        jQuery('.in1').removeClass('dekstop-left-black');
    }
    jQuery('.navbar').on('click', '.there-menu', function(){
        if(jQuery('.in1-fixed').hasClass('vis')){
            jQuery('.in1-fixed').removeClass('vis');
        }else{
            jQuery('.in1-fixed').addClass('vis');
        }
        return false;
    });
    jQuery('.in1-bars').click(function(){
        if(jQuery('.in1-fixed').hasClass('vis')){
            jQuery('.in1-fixed').removeClass('vis');
        }else{
            jQuery('.in1-fixed').addClass('vis');
        }
        return false;
    });
    jQuery('.in1-fixed').find('.close-x').find('a').click(function(){
        jQuery('.in1-fixed').removeClass('vis');
        return false;
    });
    
    
    //sign up form close
    jQuery('.signin-block').find('.close-x').find('a').click(function(){
        jQuery('#sign-in-switcher').click();
    });
    
    //trials show result
    jQuery('.resultsOn').each(function(){
        var btn = jQuery(this);
        
        btn.click(function(){
            btnId = jQuery(this).attr('id');
            if(btn.hasClass('on')){
                jQuery('.'+btnId).css('display', 'none');
                btn.removeClass('on');
                btn.html('Show<br />Results');
            }else{
                jQuery('.'+btnId).css('display', 'block');
                btn.addClass('on');
                btn.html('Hide<br />Results');
            }
        return false;
        });
    });
    
    //registration forms
    limitedForm = jQuery("#limitedForm");
    unlimitedForm = jQuery("#unlimitedForm");
    if(jQuery(window).width() <= 599){
        jQuery("#limitedForm").remove();
        limitedForm.insertAfter('#limPlan');        
        jQuery("#unlimitedForm").remove();
        unlimitedForm.insertAfter('#unlimPlan');
    }else{
        if(typeof pageName !== "undefined"){
            if(pageName == 'sign-up'){ 
                jQuery("#limitedForm").remove();
                limitedForm.insertAfter('#planForm');
                jQuery("#unlimitedForm").remove();
                unlimitedForm.insertAfter('#planForm');
            }else{
                
            }
        }
    }
    
    //home cover sign-in click   
    jQuery('#coverSignInBtn').click(function(){
        jQuery('#loginDropdown').toggle();
    });
    jQuery('#sign-in-switcher').click(function(){
        jQuery('#loginDropdown').toggle();
    });
    jQuery(document).on('click', function (e) {
        if (jQuery(e.target).closest("#loginDropdown").length === 0 && jQuery(e.target).closest("#coverSignInBtn").length === 0) {
            jQuery("#loginDropdown").hide();
        }
    });
    /*jQuery("body").click(function(e){
        if(e.target.getAttribute('id') !== "loginDropdown" && e.target.getAttribute('id') !== "sign-in-switcher" && e.target.className !== "signin-button"){
            jQuery('#loginDropdown').hide();
        }
    });*/
    
    
});  

jQuery(window).resize(function(){   
    
    //cover feature
    /*var body = jQuery('body');
    var bodyHeight = jQuery('.backsheet').height();*/
    /*body.css('min-height', bodyHeight+'px');*/
    
   
    jQuery('.intro-td').height(jQuery(window).height());

    
    if(jQuery(window).width() <= 760){
        jQuery('#drugContainer').addClass('list-block-1');
    }else{
        if(!readCookie('list_mode')){
            jQuery('#drugContainer').removeClass('list-block-1');
        }
    }
    
    //mobile menu
        if(jQuery('body').hasClass('single-drug') || jQuery('body').hasClass('single-condition')){
            if(jQuery(window).scrollTop() > (jQuery('.container-fluid').height() + jQuery('.in1').height())){
                jQuery('.navbar-header').addClass('there-menu');
                jQuery('.in1').addClass('dekstop-left-black');
            }else{
                jQuery('.navbar-header').removeClass('there-menu');
                jQuery('.in1').removeClass('dekstop-left-black');
            }
        } 
        
    //registration forms relocation
    if(jQuery(window).width() <= 599){
        if(jQuery('#limPlan').next().attr('id') != 'limitedForm'){
            jQuery("#limitedForm").remove();
            limitedForm.insertAfter('#limPlan');   
        }
        if(jQuery('#unlimPlan').next().attr('id') != 'unlimitedForm'){
            jQuery("#unlimitedForm").remove();
            unlimitedForm.insertAfter('#unlimPlan');
        }
    }else{
        if(typeof pageName !== "undefined"){
            if(pageName == 'sign-up'){ 
                if(jQuery('#planForm').next().attr('id') != 'limitedForm'){
                    jQuery("#limitedForm").remove();
                    limitedForm.insertAfter('#planForm');
                }
                if(jQuery('#planForm').next().attr('id') != 'unlimitedForm'){
                    jQuery("#unlimitedForm").remove();
                    unlimitedForm.insertAfter('#planForm');
                }
            }else{
                if(jQuery('.signup-table').next().attr('id') != 'limitedForm'){
                    jQuery("#limitedForm").remove();
                    limitedForm.insertAfter('.signup-table');
                }
                if(jQuery('.signup-table').next().attr('id') != 'unlimitedForm'){
                    jQuery("#unlimitedForm").remove();
                    unlimitedForm.insertAfter('.signup-table');
                }
            }
        }
    }
    
});   

function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

 var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    iPhone: function() {
        return navigator.userAgent.match(/iPhone|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};