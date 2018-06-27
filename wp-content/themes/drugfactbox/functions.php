<?php 
add_filter('xmlrpc_enabled', '__return_false');
remove_action( 'wp_head', 'rsd_link' );

function custom_page_rewrite(){    
    
    add_rewrite_rule(
        '^drug/([a-zA-Z0-9_\-]+)/([a-zA-Z0-9_\-]+)/([a-zA-Z0-9_\-]+)/?',
        'index.php?post_type=drug&drug=$matches[1]&cond=$matches[2]&page=$matches[3]',
        'top'
    );
    
    add_rewrite_rule(
        '^drug/([a-zA-Z0-9_\-]+)/([a-zA-Z0-9_\-]+)/?',
        'index.php?post_type=drug&drug=$matches[1]&cond=$matches[2]',
        'top'
    );
    
    add_rewrite_rule(
        '^drug/([a-zA-Z0-9_\-]+)/?',
        'index.php?post_type=drug&drug=$matches[1]',
        'top'
    );
    
    add_rewrite_rule(
        '^condition/([a-zA-Z0-9_\-]+)/treatment/?',
        'index.php?post_type=condition&condition=$matches[1]&treatment=1',
        'top'
    );
    
    add_rewrite_rule(
        '^member/([a-zA-Z0-9_\-]+)/([a-zA-Z0-9_\-]+)/?',
        'index.php?pagename=member&level=$matches[1]&usr=$matches[2]',
        'top'
    );
}
add_action( 'init', 'custom_page_rewrite' );

function query_vars($public_query_vars) {
    $public_query_vars[] = "cond";
    $public_query_vars[] = "page";
    $public_query_vars[] = "treatment";
    $public_query_vars[] = "level";
    $public_query_vars[] = "usr";
    return $public_query_vars;
}
add_filter('query_vars', 'query_vars');

function dfb_style() {
	// Add Genericons, used in the main stylesheet.
    wp_register_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
    wp_enqueue_style( 'bootstrap');
    wp_register_style( 'font-awesome', get_template_directory_uri() . '/font-awesome-4.3.0/css/font-awesome.min.css');
    wp_enqueue_style( 'font-awesome');
    wp_register_style( 'carousel', get_template_directory_uri() . '/css/carousel-1.css');
    wp_enqueue_style( 'carousel');    
    wp_register_style( 'icons', get_template_directory_uri() . '/css/icons.css');
    wp_enqueue_style( 'icons');    
    wp_register_style( 'style', get_template_directory_uri() . '/css/style.css');
    wp_enqueue_style( 'style');
}
add_action( 'wp_enqueue_scripts', 'dfb_style' );

function dfb_scripts() {
    wp_register_script( 'jquery', get_template_directory_uri() . '/js/jquery.min.js', array(), false, true );
    wp_enqueue_script( 'jquery');
    wp_register_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array(), false, true );
    wp_enqueue_script( 'bootstrap');
    wp_register_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui-1.8.16.custom.min.js', array(), false, true );
    wp_enqueue_script( 'jquery-ui');    
    wp_register_script( 'touch-punch', get_template_directory_uri() . '/js/jquery.ui.touch-punch.min.js', array(), false, true );
    wp_enqueue_script( 'touch-punch');    
    wp_register_script( 'allinone-carousel', get_template_directory_uri() . '/js/allinone_carousel.js', array(), false, true );
    wp_enqueue_script( 'allinone-carousel');
    
    wp_register_script( 'main', get_template_directory_uri() . '/js/main.js', array(), false, true );
    wp_enqueue_script( 'main');    
    
    wp_enqueue_script('password-strength-meter');
}
add_action( 'wp_enqueue_scripts', 'dfb_scripts' );

function auto_login() {   
    if(!empty($_GET['login']) and !empty($_GET['token'])) {     
        $user_login = @$_GET['login'];         
        $secret_token = $_GET['token'];
        $user = get_user_by('login', $user_login);
        if($user){
            $user_id = $user->ID; 
            $stored_token = get_the_author_meta('access_token', $user_id);         
            if($stored_token == $secret_token){         
                //login                 
                wp_set_current_user($user_id, $user_login);
                wp_set_auth_cookie($user_id);                
                do_action('wp_login', $user_login);
                if(is_unc()){
                    wp_redirect(home_url( '/' ).'/member/unc/'.$user_login);exit;
                }
            }
        }      
    }
} 
add_action('init', 'auto_login');

function is_unc(){
    $loggedUser = wp_get_current_user();
    if(isset($loggedUser->data->user_email)){
        if($loggedUser->data->user_email === 'medigist@unc.edu'){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function dbf_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer Bar', 'drugfactbox' ),
		'id'            => 'footer',
		'description'   => __( 'Add widgets here to appear in website footer.', 'drugfactbox' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '"',
		'after_title'   => '"',
	) );
}
add_action( 'widgets_init', 'dbf_widgets_init' );

//Menu areas registration
register_nav_menus( array(
		'secondary' => __( 'Footer', 'drugfactbox' ),
	) );

//Featured images on
add_theme_support( 'post-thumbnails' ); 

remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

remove_filter( 'the_excerpt', 'wpautop' );
add_filter( 'the_excerpt', 'wpautop' , 12);

add_filter('widget_text', 'do_shortcode');

add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

/*add_action('wp_logout','go_home');
function go_home(){
  wp_redirect( home_url() );
  exit();
}*/

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
//---------------restore password process updates--------------------------
add_filter( 'authenticate', 'dfb_authenticate_username_password', 30, 3);
function dfb_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new WP_Error();
        $user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        return $error;
    }
}
add_action( 'wp_login_failed', 'dfb_front_end_login_fail' );  // hook failed login
function dfb_front_end_login_fail( $username ) {
   $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      $referrer = str_replace('?login=failed', '', $referrer);
      $referrer = str_replace('&tab=enterprise', '', $referrer);
      if(isset($_POST['enterprise'])){
            $tab = '&tab=enterprise';
      }else{
            $tab = ''; 
      }
      wp_redirect( $referrer . '?login=failed'.$tab );
      exit;
   }
}

function dfb_add_custom_user_profile_fields( $user ) {
?>
    <h3><?php _e('API Settings', 'drugfactbox'); ?></h3>
    
    <table class="form-table">
		<tr>
			<th>
				<label><?php _e('Allowed Conditions', 'drugfactbox'); ?></label>
            </th>
			<td>
                <div style="width: 500px;height:305px;overflow:auto; margin-bottom:10px;">
<?php 
$sAllowedConds = get_the_author_meta( 'allowed_conditions', $user->ID );
if(!empty($sAllowedConds)){
    $aAllowedConds = explode("::",$sAllowedConds);
}else{
    $aAllowedConds = array();
}
$args = array(  
        'showposts'=>-1,
        'post_type' => 'condition',        
        'orderby' => 'post_title',
        'order'   => 'ASC',
    ); 
$query = new WP_Query( $args );
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        ?>
        <input type="checkbox" name="allowed_conditions[<?php echo $query->post->ID?>]" value="<?php echo $query->post->ID?>" <?php if(in_array($query->post->ID, $aAllowedConds)) echo "checked"?>/> <?php echo $query->post->post_title?><br />
        <?php
    }    
}?>
                </div>
                <span class="description"><?php _e('Drugs related to these conditions will be available.', 'drugfactbox'); ?></span>
			</td>
		</tr>
	</table>
    
    <table class="form-table">
        <tr>
            <th>
				<label><?php _e('Allowed Drugs', 'drugfactbox'); ?></label>
            </th>
            <td>
                <div style="width: 500px;height:305px;overflow:auto; margin-bottom:10px;">
                    <?php
$sAllowedDrugs = get_the_author_meta( 'allowed_drugs', $user->ID );
if(!empty($sAllowedDrugs)){
    $aAllowedDrugs = explode("::",$sAllowedDrugs);
}else{
    $aAllowedDrugs = array();
}
global $wpdb;
$drug_result = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='drug' ORDER BY post_title ASC");
$aDrugs = array();
if ( !empty($drug_result) ) {
    foreach( $drug_result as $drug) {?>
                        <input type="checkbox" name="allowed_drugs[<?php echo $drug->ID?>]" value="<?php echo $drug->ID?>" <?php if(in_array($drug->ID, $aAllowedDrugs)) echo "checked"?>/> <?php echo $drug->post_title?><br />
    <?php }
}?>
                </div>
                <span class="description"><?php _e('Checked drugs will be available.', 'drugfactbox'); ?></span>
            </td>
        </tr>
    </table>
    
    <table class="form-table">
        <tr>
            <th>
				<label><?php _e('Drug Level', 'drugfactbox'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="drug_level" value="1" <?php if(get_the_author_meta( 'drug_level', $user->ID )) echo "checked"?>/>
                <span class="description"><?php _e('Allow user to see "drug" level information.', 'drugfactbox'); ?></span>
            </td>
        </tr>
    </table>
    
    <table class="form-table">
        <tr>
            <th>
				<label><?php _e('FDA Indication Level', 'drugfactbox'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="fda_level" value="1" <?php if(get_the_author_meta( 'fda_level', $user->ID )) echo "checked"?>/>
                <span class="description"><?php _e('Allow user to see "FDA Indication" level information.', 'drugfactbox'); ?></span>
            </td>
        </tr>
    </table>
    
    <table class="form-table">
        <tr>
            <th>
				<label><?php _e('Box Level', 'drugfactbox'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="box_level" value="1" <?php if(get_the_author_meta( 'box_level', $user->ID )) echo "checked"?>/>
                <span class="description"><?php _e('Allow user to see "box" level information.', 'drugfactbox'); ?></span>
            </td>
        </tr>
    </table>
    
    <h3><?php _e('Export options', 'drugfactbox'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th>
				<label><?php _e('PDF', 'drugfactbox'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="pdf_export" value="1" <?php if(get_the_author_meta( 'pdf_export', $user->ID )) echo "checked"?>/>
                <span class="description"><?php _e('Allow user to download or print all drug\'s information in pdf format', 'drugfactbox'); ?></span>
            </td>
        </tr>
    </table>
    

	<h3><?php _e('Password for restoring', 'drugfactbox'); ?></h3>
	
	<table class="form-table">
		<tr>
			<th>
				<label for="password_res"><?php _e('Password', 'drugfactbox'); ?>
			</label></th>
			<td>
				<input type="text" name="password_res" id="password_res" value="<?php echo esc_attr( get_the_author_meta( 'password_res', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('User\'s password.', 'drugfactbox'); ?></span>
			</td>
		</tr>
	</table>
    
    <h3><?php _e('Automatic authentication', 'drugfactbox'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th>
				<label for="access_token"><?php _e('Access Token', 'drugfactbox'); ?></label>
            </th>
            <td>
            <script>
            /*String.prototype.hashCode = function() {
                var hash = 0, i, chr, len;
                if (this.length === 0) return hash;
                for (i = 0, len = this.length; i < len; i++) {
                    chr   = this.charCodeAt(i);
                    hash  = ((hash << 5) - hash) + chr;
                    hash |= 0; // Convert to 32bit integer
                }
                return hash;
            };*/
            jQuery(document).ready(function(){
                jQuery('#hashGenerator').click(function(){                      
                    for(var c = ''; c.length < 32;) c += Math.random().toString(36).substr(2, 1)
                    jQuery(this).prev().val(c);
                    jQuery('#linktoken').text(c);
                    return false;
                });
            });
            </script>
                <input type="text" name="access_token" id="access_token" value="<?php echo esc_attr( get_the_author_meta( 'access_token', $user->ID ) ); ?>" class="regular-text" /> <a href="#" id="hashGenerator">generate</a><br />
                <span class="description"><?php _e('Token for access to the site via url', 'drugfactbox'); ?></span>
                <p>Authenticated URL : <?php echo esc_url( home_url( '/' ) )?>?login=<?php echo $user->user_login;?>&token=<span id="linktoken"><?php echo esc_attr( get_the_author_meta( 'access_token', $user->ID ) ); ?></span></p>
            </td>
        </tr>
    </table>
<?php }

function dfb_save_custom_user_profile_fields( $user_id ) {
	
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
    //API fields
	if(isset($_POST['allowed_conditions'])){
        $allCond = implode('::',$_POST['allowed_conditions']);
    }else{
        $allCond = '';
    }
    update_usermeta( $user_id, 'allowed_conditions', $allCond );
    
    if(isset($_POST['allowed_drugs'])){
        $allDrug = implode('::',$_POST['allowed_drugs']);
    }else{
        $allDrug = '';
    }
    update_usermeta( $user_id, 'allowed_drugs', $allDrug );    
    update_usermeta( $user_id, 'drug_level', isset($_POST['drug_level']) ? 1: 0);
    update_usermeta( $user_id, 'fda_level', isset($_POST['fda_level']) ? 1: 0);
    update_usermeta( $user_id, 'box_level', isset($_POST['box_level']) ? 1: 0);
    //Export
    update_usermeta( $user_id, 'pdf_export', isset($_POST['pdf_export']) ? 1: 0);
    //access fields
	update_usermeta( $user_id, 'password_res', $_POST['password_res'] );
    update_usermeta( $user_id, 'access_token', $_POST['access_token'] );
}

add_action( 'show_user_profile', 'dfb_add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'dfb_add_custom_user_profile_fields' );

add_action( 'personal_options_update', 'dfb_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'dfb_save_custom_user_profile_fields' );
//----------------------------------------------

function get_user_location(){
    $location = ( !empty( $_COOKIE['gmw_formatted_address'] ) ) ? urldecode( $_COOKIE['gmw_formatted_address'] ) : urldecode( $_COOKIE['gmw_address'] );
    return $location;
}

function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}

function sort_array($array,$key,$type='asc'){
    $sorted_array = array();
    if(@is_array($array) and count($array)>0){
        foreach($array as $k=>$row){
            @$key_values[$k] = $row[$key];
        }
        if($type == 'asc' ){
            asort($key_values);
        }
        else{
            arsort($key_values);
        }
        foreach($key_values as $k=>$v){
           $sorted_array[] = $array[$k];
        }
        return $sorted_array;
    }
    else{
        return false;
    }
}

function dump($var){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    echo "<hr />";
}
