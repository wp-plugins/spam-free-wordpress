<?php
/*
Plugin Name: Spam Free Wordpress
Plugin URI: http://www.toddlahman.com/spam-free-wordpress/
Description: Comment spam blocking plugin that uses anonymous password authentication to achieve 100% automated spam blocking with zero false positives, plus a few more features.
Version: 1.6.7
Author: Todd Lahman, LLC
Author URI: http://www.toddlahman.com/
License: GPLv2

	Intellectual Property rights reserved byTodd Lahman, LLC as allowed by law incude,
	but are not limited to, the working concept, function, and behavior of Spam Free Wordpress,
	the logical code structure and expression as written. All WordPress functions, objects, and
	related items, remain the property of WordPress under GPLv2 license, and any WordPress core
	functions and objects in this plugin operate under the GPLv2 license.
*/


// Plugin version
define( 'SFW_VERSION', '1.6.7' );
// Required WordPress version
define( 'SFW_WP_REQUIRED', '3.1' );
define( 'SFW_PINGBACKS_CLOSED', '<div id="message" class="error"><p><strong>A pingbacks setting was allowing pingback spam on your blog. Spam Free WordPress closed this security hole. Have a nice day. :)</strong></p></div>' );

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
		// Checks to make sure WordPress version is at least 3.1 or above, if not deactivates plugin
		if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die( 'Spam Free Wordpress requires at least WordPress' . SFW_WP_REQUIRED . '. Sorry! Click back to continue.' );
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
			'ping_status' => 'closed'
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
	// Checks to make sure WordPress version is at least 3.0 or above, if not deactivates plugin
	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( 'Spam Free Wordpress requires at least WordPress' . SFW_WP_REQUIRED . '. Sorry! Click back to continue.' );
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
	// Checks to make sure WordPress version is at least 3.0 or above, if not deactivates plugin
	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( 'Spam Free Wordpress requires at least WordPress' . SFW_WP_REQUIRED . '. Sorry! Click back to continue.' );
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

// Calls the Wordpress 3.x code for admin settings page
add_action('after_setup_theme', 'do_spam_free_wordpress_automation', 1);

// Calls the wp-comments-post.php authentication
add_action('pre_comment_on_post', 'tl_spam_free_wordpress_comments_post', 1);

// Checks that the comment form password exists on single page load
add_action('loop_start', 'sfw_comment_pass_exist_check', 1);

// Call the function to change password in custom fields when after each new comment is saved in the database.
add_action('comment_post', 'sfw_new_comment_pass', 1);

// settings action link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sfw_settings_link', 10, 1);
// "network_admin_plugin_action_links_{$plugin_file}"

// plugin row links
add_filter('plugin_row_meta', 'sfw_donate_link', 10, 2);

function sfw_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$links[] = '<a href="'.admin_url('options-general.php?page=spam-free-wordpress-admin-page').'">Settings</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SFVH6PCCC6TLG">Donate</a>';
	}
	return $links;
}


/**
* Close pingbacks and trackback automatically if enabled.
* If ping_status is already closed, and if some posts still have ping enabled, then manual button on settings page needs to be used.
*/
if( !$spam_free_wordpress_options['ping_status'] == 'closed' ) {
	if ( get_option( 'default_ping_status' ) == 'open' || get_option( 'default_pingback_flag' ) == '1' ) {
		sfw_close_pingbacks();
		echo SFW_PINGBACKS_CLOSED;
	}
}


/**
* Pingbacks and trackbacks are closed automatically if they are open
*/
if( !$spam_free_wordpress_options['ping_status'] == 'closed' ) {
	$sfw_close_pings_once = get_option( 'sfw_close_pings_once' );

	if ( !$sfw_close_pings_once ) {
		global $pagenow;
	
		if ( $pagenow == 'options-discussion.php' || $pagenow == 'edit.php' || $pagenow == 'post.php' ) {
			sfw_close_pingbacks();
			echo SFW_PINGBACKS_CLOSED;
			update_option( 'sfw_close_pings_once', true );
		}
	}
}

// For testing only
function sfw_delete() {
	delete_option( 'spam_free_wordpress' );
}

// register_deactivation_hook( __FILE__, 'sfw_delete' );

?>