<?php 
get_header();
?>
<style>
.none{
 display:none;   
}
.error{
 color: #f00;   
}
</style>
<script>
jQuery(document).ready(function(){
      if(jQuery("#pass-strength-result").length > 0){
            jQuery("#pass1").bind("keyup", function(){
            var pass1 = jQuery("#pass1").val();
            var pass2 = jQuery("#pass2").val();
            var username = jQuery("#username").val();
            var strength = passwordStrength(pass1, username, pass2);
            if(pass1.length == 0){
                strength = 'n';
            }
            updateStrength(strength);
            });
            jQuery("#pass2").bind("keyup", function(){
            var pass1 = jQuery("#pass1").val();
            var pass2 = jQuery("#pass2").val();
            var username = jQuery("#username").val();
            var strength = passwordStrength(pass1, username, pass2);
            if(pass1.length == 0 && pass2.length == 0){
                strength = 'n';
            }
            updateStrength(strength);
            });
        }
    });

function updateStrength(strength){
    var status = new Array('weak', 'average', 'good','mismatch');
    var resultText = jQuery("#pass-strength-result");
    var resultIndicator = jQuery("#pass-strength-indicator");
    switch(strength){
    case 0:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[0]);
      resultText.val('Weak');
      break;
    case 1:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[0]);
      resultText.val('Weak');
      break;
    case 2:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[0]);
      resultText.val('Weak');
      break;
    case 3:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[1]);
      resultText.val('Average');
      break;
    case 4:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[2]);
      resultText.val('Good');
      break;
    case 5:
      resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass(status[0]);
      resultText.val('Mismatch');
      break;
    default:
        resultIndicator.removeClass('good').removeClass('average').removeClass('weak').removeClass('none').addClass('none');
        break;
    }
}
</script>
<?php
global $current_user, $wp_roles;
//get_currentuserinfo(); //deprecated since 3.1

/* Load the registration file. */
//require_once( ABSPATH . WPINC . '/registration.php' ); //deprecated since 3.1
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else{
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
        }
    }

    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
    

    /* Redirect so the page will show updated info.*/
  /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    /*if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink() );
        exit;
    }*/
}
?>


<!-- title-h1 -->
<div class="container title-h1">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1><?php the_title()?></h1>	
		</div>
	</div>
</div>
<!-- // end title-h1 -->

<!-- signup-1 -->
<div class="signup-1">
<hr />	

    <div class="container">
        <div class="row">
            
            <!-- Change Profile -->
			<div class="form-1 change-profile-forma">
            
                <h2>Change Profile</h2>
                
            <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
            <?php else : ?>
                 <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                <form action="<?php the_permalink(); ?>" method="post" name="editProfileForm">
				
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<p>*Required Information</p>
					</div>
					
					<div class="clearfix"></div>
					
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
					
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					<div class="form-group">
						<label>User Name * <span>(Can not be changed)</span></label>
						<input type="text"  class="form-control disabled" id="username" disabled="disabled" value="<?php the_author_meta( 'user_login', $current_user->ID ); ?>" />
					</div>
					<div class="form-group">
						<label>Email address *</label>
						<input type="email" name="email" class="form-control" id="" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
					</div>
				    <div class="form-group">
						<label>First Name *</label>
						<input type="text" name="first-name" class="form-control" id="" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
					</div>
						<div class="form-group">
						<label>Last Name *</label>
						<input type="text" name="last-name" class="form-control" id=""  value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
					</div>
	
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="form-group">
							<label>New Password</label>
							<input type="password" name="pass1" class="form-control" id="pass1" placeholder="" />
						</div>
						<div class="form-group">
							<label>Re-enter New Password</label>
							<input type="password" name="pass2" class="form-control" id="pass2" placeholder="" />
						</div>
						<div class="form-group none" id="pass-strength-indicator"><!-- меняем классы отображаюжие степень сложности пароля
							weak - слабый
							average - средний
							good - хороший 
						  -->
							<label>Password Strength Indicator</label>
							<input type="text" class="form-control psi" disabled="disabled" id="pass-strength-result" value="" />
						</div>

					</div>
					
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
								
					<div class="clearfix"></div>
					
					<div class="details-a">
						<a title="" href="#" onclick="document.editProfileForm.submit();return false;">SUBMIT</a>
                        <?php wp_nonce_field( 'update-user' ) ?>
                        <input name="action" type="hidden" id="action" value="update-user" />
					</div>
				</form>
            <?php endif; ?>  
            
            </div>
            
        </div>
    </div>
</div>

<?php
get_footer();
?>