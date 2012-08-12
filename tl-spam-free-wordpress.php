<?php
/*
Plugin Name: Spam Free Wordpress
Plugin URI: http://www.toddlahman.com/spam-free-wordpress/
Description: Comment spam blocking plugin that uses anonymous password authentication to achieve 100% automated spam blocking with zero false positives, plus a few more features.
Version: 1.7.5
Author: Todd Lahman, LLC
Author URI: http://www.toddlahman.com/
License: GPLv3

	Intellectual Property rights reserved byTodd Lahman, LLC as allowed by law incude,
	but are not limited to, the working concept, function, and behavior of this plugin,
	the logical code structure and expression as written. All WordPress functions, objects, and
	related items, remain the property of WordPress under GPLv3 license, and any WordPress core
	functions and objects in this plugin operate under the GPLv3 license.
*/


// Plugin version
if ( !defined('SFW_VERSION') )
	define( 'SFW_VERSION', '1.7.5' );

// Ready for translation
load_plugin_textdomain( 'spam-free-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );

if ( !defined('SFW_WP_REQUIRED') )
	define( 'SFW_WP_REQUIRED', '3.1' );

if (!defined('SFW_WP_REQUIRED_MSG'))
	define( 'SFW_WP_REQUIRED_MSG', 'Spam Free Wordpress' . __( ' requires at least WordPress 3.1. Sorry! Click back button to continue.', 'spam-free-wordpress' ) );

if (!defined('SFW_URL') )
	define( 'SFW_URL', plugin_dir_url(__FILE__) );
if (!defined('SFW_PATH') )
	define( 'SFW_PATH', plugin_dir_path(__FILE__) );
if (!defined('SFW_BASENAME') )
	define( 'SFW_BASENAME', plugin_basename( __FILE__ ) );
if(!defined( 'SFW_IS_ADMIN' ) )
    define( 'SFW_IS_ADMIN',  is_admin() );
	
if ( !get_option('sfw_version') ) {
	update_option( 'sfw_version', SFW_VERSION );
}

if ( get_option('sfw_version') && version_compare( get_option('sfw_version'), SFW_VERSION, '<' ) ) {
	update_option( 'sfw_version', SFW_VERSION );
}

// Set the default settings if not already set
if( !get_option( 'spam_free_wordpress' ) ) {
	sfw_add_default_data();
}

// Add default database settings on plugin activation
function sfw_add_default_data() {

	if( !get_option( 'spam_free_wordpress' ) ) {
		if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die( SFW_WP_REQUIRED_MSG );
		} else {
			$sfw_options = array(
			'blocklist_keys' => '',
			'lbl_enable_disable' => 'disable',
			'remote_blocked_list' => '',
			'rbl_enable_disable' => 'disable',
			'pw_field_size' => '20',
			'tab_index' => '',
			'affiliate_msg' => '',
			'toggle_stats_update' => 'disable',
			'toggle_html' => 'disable',
			'remove_author_url_field' => 'disable',
			'remove_author_url' => 'disable',
			'ping_status' => 'closed',
			'comment_form' => 'on',
			'special_msg' => ''
			);
			update_option( 'spam_free_wordpress', $sfw_options );
		
			// Close pingback default settings
			update_option( 'default_ping_status', 'closed' );
			update_option( 'default_pingback_flag', '' );
		}
		
		if( !get_option( 'sfw_spam_hits' ) ) {
			update_option( 'sfw_spam_hits', '1' );
		}
	}
}

// Runs add_default_data function above when plugin activated
register_activation_hook( __FILE__, 'sfw_add_default_data' );

// variable used as global to retrieve option array for functions
$spam_free_wordpress_options = get_option('spam_free_wordpress');

require_once( dirname( __FILE__ ) . '/includes/functions.php' );

if ( is_admin() ) {
	require_once( dirname( __FILE__ ) . '/includes/admin.php' );
}

/**
* Upgrade
* http://wpdevel.wordpress.com/2010/10/27/plugin-activation-hooks/#comment-11989
* http://wpdevel.wordpress.com/2010/10/27/plugin-activation-hooks-no-longer-fire-for-updates/
*/
$sfw_run_once = get_option( 'sfw_run_once' );

if( !$sfw_run_once && $spam_free_wordpress_options['blocklist_keys'] && !$spam_free_wordpress_options['remove_author_url_field'] ) {
	sfw_upgrade_to_new_version();
}

function sfw_upgrade_to_new_version() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$spam_free_wordpress_options = get_option('spam_free_wordpress');
	
		$oldver = $spam_free_wordpress_options;
		$newver = array(
			'remove_author_url_field' => 'disable',
			'remove_author_url' => 'disable',
			'ping_status' => 'closed'
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
		
		update_option('sfw_run_once',true);
		
		// Close pingback default settings
		update_option( 'default_ping_status', 'closed' );
		update_option( 'default_pingback_flag', '' );
	}
}

if( !$spam_free_wordpress_options['ping_status'] ) {
	sfw_add_default_ping_status();
}

function sfw_add_default_ping_status() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$spam_free_wordpress_options = get_option('spam_free_wordpress');
	
		$oldver = $spam_free_wordpress_options;
		$newver = array(
			'ping_status' => 'closed'
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
	}
}

// Added version 1.7
if( !$spam_free_wordpress_options['comment_form'] ) {
	sfw_add_default_comment_form();
}

function sfw_add_default_comment_form() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$spam_free_wordpress_options = get_option('spam_free_wordpress');
	
		$oldver = $spam_free_wordpress_options;
		$newver = array(
			'comment_form' => 'on',
			'special_msg' => ''
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
		
	}
}


// Calls the Wordpress 3.x code for admin settings page
add_action('after_setup_theme', 'do_spam_free_wordpress_automation', 1);

// Calls the wp-comments-post.php authentication
add_action('pre_comment_on_post', 'tl_spam_free_wordpress_comments_post', 1);

// settings action link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sfw_settings_link', 10, 1);
// "network_admin_plugin_action_links_{$plugin_file}"

// plugin row links
add_filter('plugin_row_meta', 'sfw_donate_link', 10, 2);

function sfw_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$links[] = '<a href="'.admin_url('options-general.php?page=spam-free-wordpress-admin-page').'">'.__('Settings', 'spam-free-wordpress').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SFVH6PCCC6TLG">'.__('Donate', 'spam-free-wordpress').'</a>';
	}
	return $links;
}

/**
* Pingbacks and trackbacks are closed automatically one time only
*/
$sfw_close_pings_once = get_option( 'sfw_close_pings_once' );

if ( !$sfw_close_pings_once ) {
	global $pagenow;
	
	if ( $pagenow == 'options-discussion.php' || $pagenow == 'edit.php' || $pagenow == 'post.php' ) {
		sfw_close_pingbacks();
		update_option( 'sfw_close_pings_once', true );
		$sfw_pingback_msg = __( 'Pingbacks were closed this one time to stop pingback and trackback spam. To reopen go to Settings -> ' , 'wspam-free-wordpress' ) . 'Spam Free Wordpress.';
		echo '<div id="message" class="error"><p><strong>'. $sfw_pingback_msg .'</strong></p></div>';
	}
}

// For testing only
function sfw_delete() {
	delete_option( 'spam_free_wordpress' );
	delete_option( 'sfw_close_pings_once' );
}

//register_deactivation_hook( __FILE__, 'sfw_delete' );


/*-----------------------------------------------------------------------------------------------------------------------
* Before the comment form can be automatically generated, make sure JetPack Comments module is not active
-------------------------------------------------------------------------------------------------------------------------*/

if ( class_exists( 'Jetpack' ) ) {
	if ( 1 == Jetpack::get_option( 'activated' ) ) {
		if ( in_array( 'comments', Jetpack::get_active_modules() ) ) {
			Jetpack::deactivate_module( 'comments' );
		}
	}
}

// automatically generate comment form
function sfw_comment_form_init() {
	return dirname( plugin_basename( __FILE__ ) ) . '/comments.php';
}

if ( $spam_free_wordpress_options['comment_form'] == 'on' ) {
	add_filter( 'comments_template', 'sfw_comment_form_init' );
}

// Load AJAX for password field
function sfw_load_pwd() {

		$js_path =  SFW_URL . 'js/sfw-load-pwd.js?' . filemtime( SFW_PATH . 'js/sfw-load-pwd.js' );
		
		wp_enqueue_script( 'sfw_pwd', $js_path, array( 'jquery' ) );
		wp_localize_script( 'sfw_pwd', 'sfw_pwd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'sfw_pwd', 'sfw_client_ip', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
	
//register with hook 'wp_enqueue_scripts' which can be used for front end CSS and JavaScript
add_action('wp_enqueue_scripts', 'sfw_load_pwd');

add_action( 'wp_ajax_nopriv_sfw_ajax_hook', 'sfw_get_pwd' );
add_action( 'wp_ajax_sfw_ajax_hook', 'sfw_get_pwd' );

add_action( 'wp_ajax_nopriv_sfw_ajax_client_ip_hook', 'get_remote_ip_address_ajax' );
add_action( 'wp_ajax_sfw_ajax_client_ip_hook', 'get_remote_ip_address_ajax' );

/*
// Censor comments
// use preg_match or preg_match_all to match profanity in submitted comment and replacing the word before saving in database.
// then replaced word is a read only from the database
// user chooses their own list of words to replace, and the censored version of the word to replace it with
function sfw_censor_comments($content) {
    $profanities = array('badword','alsobad','...'); // make array within options array, multidimensional array
    $content=str_ireplace($profanities,'{censored}',$content);
    return $content;
}
add_filter('comment_text','sfw_censor_comments');
// Can use this line to get comment data to run through filter, match words in the censor list, then write it to the database with the new censored words
$comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;
*/
// Match word from $comment to key in database, and replace with key => value before saving in database.
// Use form similar to editing tags to list and allow each word to be clicked to edit word to filter, and replacement censor word wit example like bitch replaced by b!tch or b*tch
// If no matching censored word exists use something the user chooses as the default like {censored}

?>