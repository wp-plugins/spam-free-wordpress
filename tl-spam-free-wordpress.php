<?php
/*
Plugin Name: Spam Free Wordpress
Plugin URI: http://www.toddlahman.com/spam-free-wordpress/
Description: Comment spam blocking plugin that uses anonymous password authentication to achieve 100% automated spam blocking with zero false positives, plus a few more features.
Version: 1.8.2
Author: Todd Lahman, LLC
Author URI: http://www.toddlahman.com/
License: GPLv3

	Intellectual Property rights reserved byTodd Lahman, LLC as allowed by law incude,
	but are not limited to, the working concept, function, and behavior of this plugin,
	the logical code structure and expression as written. All WordPress functions, objects, and
	related items, remain the property of WordPress under GPLv3 license, and any WordPress core
	functions and objects in this plugin operate under the GPLv3 license.
*/


if ( !defined('SFW_VERSION') )
	define( 'SFW_VERSION', '1.8.2' );
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
if(!defined( 'SFW_HOME_URL' ) )
    define( 'SFW_HOME_URL',  'http://www.toddlahman.com/spam-free-wordpress/' );
if(!defined( 'SFW_API_KEY_URL' ) )
    define( 'SFW_API_KEY_URL',  'http://www.toddlahman.com/shop/spam-free-wordpress/' );

// Ready for translation
load_plugin_textdomain( 'spam-free-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );


if ( SFW_IS_ADMIN ) {
	require_once( SFW_PATH . 'admin/class-db-sfw.php' );
	require_once( SFW_PATH . 'admin/class-menu-sfw.php' );
	require_once( SFW_PATH . 'admin/class-tool-tips-sfw.php' );
}

// Update version
if ( !get_option('sfw_version') ) {
	update_option( 'sfw_version', SFW_VERSION );
}

if ( get_option('sfw_version') && version_compare( get_option('sfw_version'), SFW_VERSION, '<' ) ) {
	update_option( 'sfw_version', SFW_VERSION );
}

// Set the default settings if not already set
if( !get_option( 'spam_free_wordpress' ) ) {
	SFW_DB::sfw_default();
}

// Runs add_default_data function above when plugin activated
register_activation_hook( __FILE__, array('SFW_DB', 'sfw_default') );

/*
* Since 1.8
* Upgrade database
*/
if( get_option('spam_free_wordpress') ) {
	$sfw_options = get_option('spam_free_wordpress');
	
	if ( !$sfw_options['bl_keys'] ) {
		SFW_DB::upgrade_db();
	}
}

require_once( SFW_PATH . 'includes/class-key-sfw.php' );
require_once( SFW_PATH . 'includes/functions.php' );
require_once( SFW_PATH . 'includes/class-comment-form.php' );
require_once( SFW_PATH . 'includes/legacy.php' );

//Pingbacks and trackbacks are closed automatically one time only
if( get_option( 'sfw_close_pings_once' ) ) {
	$sfw_close_pings_once = get_option( 'sfw_close_pings_once' );
}

// Close or open pingbacks depending on settings
global $pagenow;
if( !isset( $sfw_close_pings_once ) ) {
	if ( $pagenow == 'options-discussion.php' || $pagenow == 'edit.php' || $pagenow == 'post.php' ) {
		sfw_close_pingbacks();
		update_option( 'sfw_close_pings_once', true );
	}
}

// Close or open pingbacks depending on settings
if( $sfw_options['ping_status'] == 'on' && get_option( 'default_ping_status') == 'open' ) {
	sfw_close_pingbacks();
} elseif( $sfw_options['ping_status'] == 'on' && get_option( 'default_pingback_flag') == '1' ) {
	sfw_close_pingbacks();
} elseif( $sfw_options['ping_status'] == 'off' && get_option( 'default_ping_status') == 'closed' ) {
	sfw_open_pingbacks();
} elseif( $sfw_options['ping_status'] == 'off' && get_option( 'default_pingback_flag') == '' ) {
	sfw_open_pingbacks();
}

// settings action link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sfw_settings_link', 10, 1);
// "network_admin_plugin_action_links_{$plugin_file}"

// plugin row links
add_filter('plugin_row_meta', 'sfw_donate_link', 10, 2);

function sfw_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$links[] = '<a href="'.admin_url('options-general.php?page=sfw_dashboard').'">'.__('Settings', 'spam-free-wordpress').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SFVH6PCCC6TLG">'.__('Donate', 'spam-free-wordpress').'</a>';
	}
	return $links;
}


/*-----------------------------------------------------------------------------------------------------------------------
* Before the comment form can be automatically generated, make sure JetPack Comments module is not active
-------------------------------------------------------------------------------------------------------------------------*/
// Added 1.7.3
if ( class_exists( 'Jetpack' ) ) {
	if ( in_array( 'comments', Jetpack::get_active_modules() ) ) {
		Jetpack::deactivate_module( 'comments' );
	}
}

/**
* added 1.7.8.6
 * SFW requires jQuery 1.7 since it uses functions like .on() for events.
 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
 * using the version in core) we need to deregister it and register the 
 * core version of the file.
 */
add_action( 'wp_print_scripts', 'sfw_check_jquery', 25 );

/**
* Load SFW authentication AJAX JavaScript. Requires jQuery 1.7 or above since it uses .on()
*/
add_action('wp_enqueue_scripts', 'sfw_load_pwd');


// Actions for password AJAX
add_action( 'wp_ajax_nopriv_sfw_i_pwd', 'sfw_get_pwd' );
add_action( 'wp_ajax_sfw_i_pwd', 'sfw_get_pwd' );
add_action( 'wp_ajax_nopriv_sfw_cip', 'get_remote_ip_address_ajax' );
add_action( 'wp_ajax_sfw_cip', 'get_remote_ip_address_ajax' );

// automatically generate comment form - fixed in 1.7.8.1
function sfw_comment_form_init() {
	return dirname(__FILE__) . '/comments.php';
}

if ( $sfw_options['comment_form'] == 'on' ) {
	add_filter( 'comments_template', 'sfw_comment_form_init' );
}

// Calls the comment security, messages, and features
add_action('after_setup_theme', 'sfw_comment_form_additions', 1);

// Calls the wp-comments-post.php authentication
add_action('pre_comment_on_post', 'sfw_comment_post_authentication', 1);


/*
* Reminder that API Key is required for activation
*/
function sfw_admin_head() {
	if ( !current_user_can( 'manage_options' ) )
		return;
	if( SFW_KEY::get_key() == '' ) {
		add_action( 'admin_notices', 'sfw_license_nag' );
	}
}

function sfw_license_nag() {
	if ( isset( $_GET['page'] ) && 'sfw_dashboard' == $_GET['page'] )
		return;

		$message = sprintf(
			__( '<strong><a href="%1$s">Enter your Free License Key</a></strong> to activate the free plugin support and advanced features, or <strong><a href="'.SFW_API_KEY_URL.'" target="_blank">get one from Todd Lahman LLC</a></strong>.', 'spam-free-wordpress' ),
				admin_url( 'options-general.php?page=sfw_dashboard' )
			);
		$heading = __( 'Spam Free Wordpress needs your attention!', 'spam-free-wordpress' );
?>
		<div id="sep-notice" class="sep-notice updated">
			<div class="sep-message">
				<h3><?php echo $heading; ?></h3>
				<p><?php echo $message; ?></p>
			</div>
		</div>
<?php
}

if( get_option( 'sfw_lic_nag' ) ) {
	$sfw_lic_nag = get_option( 'sfw_lic_nag' );
}

if( !isset( $sfw_lic_nag ) || get_option( 'sfw_lic_nag' ) < '2' ) {
	update_option( 'sfw_lic_nag', $sfw_lic_nag +1 );
	add_action( 'admin_head', 'sfw_admin_head' );
}


// For testing only
function sfw_delete() {
	delete_option( 'spam_free_wordpress' );
	delete_option( 'sfw_close_pings_once' );
}

register_deactivation_hook( __FILE__, 'sfw_delete' );

/*
* For troubleshooting Unexpect Output errors
* http://www.toddlahman.com/the-plugin-generated-x-characters-of-unexpected-output-during-activation/
* Add the following code to a page that displays in the Admin dashboard, like the plugin settings page
* <p><?php echo get_option( 'plugin_error' ); ?></p>
*/
function sfw_save_error() {
    update_option( 'plugin_error',  ob_get_contents() );
}

//add_action( 'activated_plugin', 'sfw_save_error' );

?>