<?php

// Enqueue Styles
add_action( 'wp_enqueue_scripts', 'goonlaze_enqueue_styles' );
function goonlaze_enqueue_styles() {
	wp_enqueue_style( 'parent-theme', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-theme', get_stylesheet_directory_uri() .'/style.css', array( 'parent-theme' ) );
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800' );
}

/*** Add library includes */
// require_once get_stylesheet_directory_uri().'/lib/inc/gravity.php'; 

/*** Custom Login Screen */
function custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/lib/css/login.css" />';
}
add_action('login_head', 'custom_login');

/* *** Change the login logo URL */
function login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'login_logo_url' );

/* *** Change the login logo URL */
function login_logo_url_title() {
	return 'Goonlaze Livery Yard';
}
add_filter( 'login_headertitle', 'login_logo_url_title' );


/* *** Redirect Non Logged in Users to Login Page */
add_action('template_redirect','non_logged_redirect');
function non_logged_redirect() {
  if( !is_user_logged_in() && !is_page( 'Register' ) ) :
    wp_redirect( home_url( '/login/' ) );
    die();
	endif;
}
    
/* *** Custom Registration URL */
function custom_register_url( $register_url ) {
  $register_url = get_permalink( $register_page_id = 90 );
  return $register_url;
}
add_filter( 'register_url', 'custom_register_url' );
add_filter( 'login_display_language_dropdown' , '__return_false' ); 

add_filter( 'gettext', 'my_custom_text_changes', 20, 3 );
function my_custom_text_changes( $translated_text, $text, $domain ) {
  switch ( $translated_text ) {
    case 'Username or Email Address' :
      $translated_text = 'Email Address';
    break;
    case 'Error: The username field is empty.' :
      $translated_text = 'Error: The email address field is empty.';
    break;
  }
  return $translated_text;
}


/* function wpdocs_authenticate_user( $user, $username, $password ) {
	if ( empty( $username ) || empty( $password ) ) {
		$error = new WP_Error();
		$user  = new WP_Error( 'authentication_failed', __( 'ERROR: Invalid username or incorrect password.' ) );
		return $error;
	}

	return $user;
}
add_filter( 'authenticate', 'wpdocs_authenticate_user', 20, 3 ); */
 
add_action( 'gform_user_registered', 'gravity_registration_autologin',  10, 4 );
/**
 * Auto login after registration.
 */
function gravity_registration_autologin( $user_id, $user_config, $entry, $password ) {
	$user = get_userdata( $user_id );
	$user_login = $user->user_login;
	$user_password = $password;
  $user->set_role(get_option('default_role', 'subscriber'));

  wp_signon( array(
		'user_login' => $user_login,
		'user_password' =>  $user_password,
		'remember' => false

  ) );
}

/*** Enqueue custom jquery */
function custom_jquery() {

	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array(), null, true);

}
add_action('wp_enqueue_scripts', 'custom_jquery');

// READ ONLY FIELD
add_filter( 'gform_pre_render_2', 'add_readonly_script' );
function add_readonly_script( $form ) {
    ?>
    <script type="text/javascript">
        jQuery(document).on('gform_post_render', function(){
            /* apply only to a input with a class of gf_readonly */
            jQuery(".gf_readonly input").attr("readonly","readonly");
        });
    </script>
    <?php
    return $form;
}

// Dynmaic population
add_filter( 'gform_field_value_user_phone', 'phone_population_function' );
function phone_population_function( $value ) {
	$current_user = get_currentuserinfo();	
	$phone = get_user_meta( $current_user->ID, 'booked_phone', true );

  return $phone;
}

/* Remove booking plugin auto update */
function booked_plugin_updates( $value ) {
  if( isset( $value->response['booked/booked.php'] ) ) {        
     unset( $value->response['booked/booked.php'] );
   }
   return $value;
}
add_filter( 'site_transient_update_plugins', 'booked_plugin_updates' );

/* Disable theme and plugin editor */
define( 'DISALLOW_FILE_EDIT', true );