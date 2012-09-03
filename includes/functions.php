<?php

// Adds password field to comment form and options to filter HTML from comments
function sfw_comment_form_additions() {
	global $sfw_options;

		// Calls the password form for comments.php if the comment_form function is outputting comment form fields
		add_action('comment_form_after_fields', 'sfw_comment_form_extra_fields', 1);
		
		// Strips out html from comment form when ond
		if( $sfw_options['cf_html'] == "on" ) {
			// Removes all HTML from comments and leaves it only as text
			remove_filter('comment_text', 'make_clickable', 9);
			// line above doesn't make everything unclickable
			add_filter('comment_text', 'wp_filter_nohtml_kses');
			add_filter('comment_text_rss', 'wp_filter_nohtml_kses');
			add_filter('comment_excerpt', 'wp_filter_nohtml_kses');
			// remove tags from below comment form
			//add_filter('comment_form_defaults','sfw_remove_allowed_tags_field');
			// replaced line above to add no html notice
			add_filter('comment_form_defaults','sfw_no_html_notice');
		
			/*-----------------------
			Custom Theme Support
			------------------------*/
			if( function_exists( 'wp_get_theme' ) ) {
				$sfw_wp_get_theme = wp_get_theme();
			} else {
				$sfw_wp_get_theme = get_current_theme();
			}
		
			// Suffusion
			if( $sfw_wp_get_theme == 'Suffusion' ) {
				add_filter('suffusion_comment_form_fields','sfw_no_html_notice');
			}
			
			// Genesis
			if( $sfw_wp_get_theme == 'Genesis/genesis' ) {
				add_action('genesis_after_comment_form','sfw_no_html_notice_action');
			}
			
			// Graphene
			if( $sfw_wp_get_theme == 'Graphene' ) {
				add_filter('graphene_comment_form_args','sfw_no_html_notice');
			}
			
			// Thesis
			if( $sfw_wp_get_theme == 'Thesis' ) {
				add_action('thesis_hook_comment_field', 'sfw_comment_form_extra_fields');
				add_action('thesis_hook_after_comment_box','sfw_no_html_notice_action');
			}
			
			// Thematic
			if( $sfw_wp_get_theme == 'Thematic' ) {
				define('THEMATIC_COMPATIBLE_COMMENT_FORM', true);
				add_filter('thematic_comment_form_args','sfw_no_html_notice');
			}
	
		}

		if( $sfw_options['website_url'] == "on" ) {
			add_filter('comment_form_field_url', 'sfw_remove_url_field_off');
		}

		if( $sfw_options['author_link'] == "on" ) {
			add_filter('get_comment_author_url', 'strip_author_url');
		}
}

/*--------------------------------
Strip HTML out of comments
----------------------------------*/

/* Replaced with html message below
// Remove note after comment box that says which HTML tags can be used in comment
function sfw_remove_allowed_tags_field($no_allowed_tags) {
    unset($no_allowed_tags['comment_notes_after']);
    return $no_allowed_tags;
}
*/

// Friendly no HTML allowed notice under comment form
function sfw_no_html_notice($nohtml) {
	$sfw_tag_msg = __( 'HTML tags are not allowed.', 'spam-free-wordpress' );
	$nohtml['comment_notes_after'] = '<p><b>'. $sfw_tag_msg .'</b></p>';
	return $nohtml;
}

function sfw_no_html_notice_action() {
	$sfw_tag_msg = __( 'HTML tags are not allowed.', 'spam-free-wordpress' );
	echo '<p><b>'. $sfw_tag_msg .'</b></p>';
}

// Remove url field from comment form, but only if the comment form uses the comment_form function
function sfw_remove_url_field_off($no_url) {
    return '';
}

// Remove comment author link
function strip_author_url($content = "") {
  return "";
}

// AJAX function to obtain client IP address
function get_remote_ip_address_ajax() {
	if(!empty( $_SERVER['HTTP_CLIENT_IP']) ) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( !empty($_SERVER['REMOTE_ADDR']) ) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip_address = '';
	}
	echo $ip_address;
	
	// die() for AJAX
	die();
}

// Returns Local Blocklist
function sfw_local_blocklist_check( $cip ) {
	global $sfw_options;

	$local_blocklist_keys = trim( $sfw_options['bl_keys'] );
	if( '' == $local_blocklist_keys )
		return false; // If blocklist keys are empty
	$local_key = explode("\n", $local_blocklist_keys );

	foreach ( (array) $local_key as $lkey ) {
		$lkey = trim($lkey);

		// Skip empty lines
		if ( empty($lkey) ) { continue; }

		// Can use '#' to comment out line in blocklist
		$lkey = preg_quote($lkey, '#');

		$pattern = "#$lkey#i";
		if (
			   preg_match($pattern, $cip)
		 )
			return true;
	}
	return false;
}

// Function for comments.php file
function sfw_comment_form_extra_fields() {
	global $post, $sfw_options;

	// If the reader is logged in don't require password for comments.php
	if( !is_user_logged_in() ) {
		
		echo '<!-- ' . number_format_i18n( get_option( 'sfw_spam_hits' ) );
		_e( ' Spam Comments Blocked so far by ', 'spam-free-wordpress' );
		echo 'Spam Free Wordpress';
		_e( ' version ', 'spam-free-wordpress' );
		echo SFW_VERSION;
		_e( ' located at ', 'spam-free-wordpress' );
		echo "http://www.toddlahman.com/spam-free-wordpress/ -->\n";
			
		echo stripslashes( $sfw_options['cf_msg'] );
		
		wp_nonce_field('sfw_nonce','sfw_comment_nonce');
		echo '<p><noscript>JavaScript must be ond to leave a comment.</noscript></p>';
		echo "<input type='hidden' name='pwdfield' class='pwddefault' value='' />\n";
		echo "<input type='hidden' name='comment_ip' id='comment_ip' value='' />\n";
		
		if($sfw_options['cf_spam_stats'] == "on") {
				echo '<p>' . number_format_i18n( get_option('sfw_spam_hits' ) );
				_e( ' Spam Comments Blocked so far by ', 'spam-free-wordpress' );
				echo '<a href="http://www.toddlahman.com/spam-free-wordpress/" title="Spam Free Wordpress" target="_blank">Spam Free Wordpress</a></p>'."\n";
		} else {
				echo "";
		}
		
		// Automatically cleanup Post Meta Custom Fields since transients are now used
		delete_post_meta( $post->ID, 'sfw_comment_form_password' );
		
	}
}

// Function for wp-comments-post.php file located in the root Wordpress directory. The same directory as the wp-config.php file.
function sfw_comment_post_authentication() {
	global $post, $sfw_options;
	
	//$sfw_comment_script = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	$sfw_comment_script = get_transient( $post->ID. '-' .$_POST['pwdfield'] );
	
	$cip = $_POST['comment_ip'];
	
	// If the reader is logged in don't require password for wp-comments-post.php
		if( !is_user_logged_in() ) {
			// Nonce check
			if( empty( $_POST['sfw_comment_nonce'] ) || !wp_verify_nonce( $_POST['sfw_comment_nonce'],'sfw_nonce' ) )
				wp_die( __( 'Spam Free Wordpress rejected your comment because you failed a critical security check.', 'spam-free-wordpress' ) . sfw_spam_counter(), 'Spam Free Wordpress rejected your comment', array( 'response' => 200, 'back_link' => true ) );
		
			// Compares current comment form password with current password for post
			if( empty( $_POST['pwdfield'] ) || $_POST['pwdfield'] != $sfw_comment_script )
				wp_die( __( 'Spam Free Wordpress rejected your comment because you did not enter the correct password or it was empty.', 'spam-free-wordpress' ) . sfw_spam_counter(), 'Spam Free Wordpress rejected your comment', array( 'response' => 200, 'back_link' => true ) );
		
			// Compares commenter IP address to local blocklist
			if( empty( $_POST['comment_ip'] ) || $_POST['comment_ip'] == sfw_local_blocklist_check( $cip ) )
				wp_die( __( 'Comment blocked by Spam Free Wordpress because your IP address is in the local blocklist, or you forgot to type a comment.', 'spam-free-wordpress' ) . sfw_spam_counter(), 'Spam Blocked by Spam Free Wordpress local blocklist', array( 'response' => 200, 'back_link' => true ) );

		}
}

// Counts number of comment spam hits and stores in options database table
function sfw_spam_counter() {
	$s_hits = get_option('sfw_spam_hits');
	update_option('sfw_spam_hits', $s_hits+1);
}

/*------------------------------------------------------------------------
Pingbacks and trackbacks are closed automatically if they are open
--------------------------------------------------------------------------*/

function sfw_close_pingbacks() {
	global $wpdb;

	$sql =
		"
		UPDATE $wpdb->posts
		SET ping_status = 'closed'
		WHERE ping_status = 'open'
		";
	
	$sfw_close_ping = $wpdb->query( $wpdb->prepare( $sql ) );

	update_option( 'default_ping_status', 'closed' );
	update_option( 'default_pingback_flag', '' );

}

function sfw_open_pingbacks() {
	global $wpdb;

	$sql =
		"
		UPDATE $wpdb->posts
		SET ping_status = 'open'
		WHERE ping_status = 'closed'
		";
	
	$sfw_open_ping = $wpdb->query( $wpdb->prepare( $sql ) );

	update_option( 'default_ping_status', 'open' );
	update_option( 'default_pingback_flag', '1' );

}


// Adds settings link to plugin menu
function sfw_settings_link($links) {
	$links[] = '<a href="'.admin_url('options-general.php?page=sfw_dashboard').'">Settings</a>';
	return $links;
}


/*-----------------------------------------
* Generates temporary passwords
------------------------------------------*/

function sfw_get_pwd() {
	$postid = $_POST['post_id'];
	
	$pwd = wp_generate_password(12, false);
	set_transient( $postid. '-' .$pwd, $pwd, 60 * 20 ); // expire password after 20 minutes
	$pwd_key = get_transient( $postid. '-' .$pwd );
	
	echo $pwd_key;
	
	// die() for AJAX
	die();
}

/*--------------------------------------------------------------------
* X-Autocomplete Comment Form Fields for Chrome 15 and above
* http://wiki.whatwg.org/wiki/Autocompletetype
* http://googlewebmastercentral.blogspot.com/2012/01/making-form-filling-faster-easier-and.html
---------------------------------------------------------------------*/

function sfw_add_x_autocompletetype( $fields ) {
	$fields['author'] = str_replace( '<input', '<input x-autocompletetype="name-full"', $fields['author'] );
	$fields['email'] = str_replace( '<input', '<input x-autocompletetype="email"', $fields['email'] );
	return $fields;
}

add_filter('comment_form_default_fields','sfw_add_x_autocompletetype');


/*
* Load JavaScript for comment form
* Requires jQuery 1.7 or Above
*/
function sfw_load_pwd() {
	$sfw_options = get_option('spam_free_wordpress');
	$js_path =  SFW_URL . 'js/sfw-ipwd.js';
	$js_path_old =  SFW_URL . 'js/before-wp-3_3/sfw-ipwd.js';
	
	// Make sure this is WordPress 3.3 and jQuery 1.7 or greater
	if( version_compare( get_bloginfo( 'version' ), '3.3', '>=' ) && $sfw_options['jquery_compat'] == 'on'  ) {
		wp_enqueue_script( 'sfw_ipwd', $js_path_old, array( 'jquery' ), SFW_VERSION, true );
		wp_localize_script( 'sfw_ipwd', 'sfw_ipwd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'sfw_ipwd', 'sfw_client_ip', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	} elseif ( version_compare( get_bloginfo( 'version' ), '3.3', '>=' )  && $sfw_options['jquery_compat'] == 'off'  ) {
		add_action( 'wp_print_scripts', 'sfw_check_jquery', 25 );
		wp_enqueue_script( 'sfw_ipwd', $js_path, array( 'jquery' ), SFW_VERSION, true );
		wp_localize_script( 'sfw_ipwd', 'sfw_ipwd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'sfw_ipwd', 'sfw_client_ip', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	} elseif( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
		wp_enqueue_script( 'sfw_ipwd', $js_path_old, array( 'jquery' ), SFW_VERSION, true );
		wp_localize_script( 'sfw_ipwd', 'sfw_ipwd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'sfw_ipwd', 'sfw_client_ip', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}

/**
* added 1.7.8.6
 * SFW requires jQuery 1.7 since it uses functions like .on() for events.
 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
 * using the version in core) we need to deregister it and register the 
 * core version of the file.
 */
function sfw_check_jquery() {
	global $wp_scripts;
	
	// Enforce minimum version of jQuery
	if( isset( $wp_scripts->registered['jquery']->ver ) && $wp_scripts->registered['jquery']->ver < '1.7' ) {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', array(), '1.7.2' );
		wp_enqueue_script( 'jquery' );
	}
}

?>