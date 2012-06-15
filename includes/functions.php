<?php

// Adds password field to comment form and options to filter HTML from comments
function do_spam_free_wordpress_automation() {
	global $spam_free_wordpress_options;

	// Calls the password form for comments.php if the comment_form function is outputting comment form fields
	add_action('comment_form_after_fields', 'tl_spam_free_wordpress_comments_form', 1);
	
	// Strips out html from comment form when enabled
	if ( $spam_free_wordpress_options['toggle_html'] == "enable" ) {
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
		
		$sfw_get_current_theme = get_current_theme();
		
		// Suffusion
		if ( $sfw_get_current_theme == 'Suffusion' ) {
			add_filter('suffusion_comment_form_fields','sfw_no_html_notice');
			}
			
		// Genesis
		if ( $sfw_get_current_theme == 'Genesis/genesis' ) {
			add_action('genesis_after_comment_form','sfw_no_html_notice_action');
			}
			
		// Graphene
		if ( $sfw_get_current_theme == 'Graphene' ) {
			add_filter('graphene_comment_form_args','sfw_no_html_notice');
			}
			
		// Thesis
		if ( $sfw_get_current_theme == 'Thesis' ) {
			add_action('thesis_hook_comment_field', 'tl_spam_free_wordpress_comments_form');
			add_action('thesis_hook_after_comment_box','sfw_no_html_notice_action');
			}
			
		// Thematic
		if ( $sfw_get_current_theme == 'Thematic' ) {
			add_filter('thematic_comment_form_args','sfw_no_html_notice');
			}
	
	}

	if ( $spam_free_wordpress_options['remove_author_url_field'] == "enable" ) {
		add_filter('comment_form_field_url', 'sfw_remove_url_field_off');
	}

	if ( $spam_free_wordpress_options['remove_author_url'] == "enable" ) {
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
	$nohtml['comment_notes_after'] = '<p><b>HTML tags and attributes are not allowed.</b></p>';
	return $nohtml;
}

function sfw_no_html_notice_action() {
	echo '<p><b>HTML tags and attributes are not allowed.</b></p>';
}

// Remove url field from comment form, but only if the comment form uses the comment_form function
function sfw_remove_url_field_off($no_url) {
    return '';
}

// Remove comment author link
function strip_author_url($content = "") {
  return "";
}

/*---------------------------------------------------------------------------------------------
Checks to see if comment form password exists and if not creates one in custom fields
-----------------------------------------------------------------------------------------------*/

function sfw_comment_pass_exist_check() {
	global $post;
	$new_post_comment_pwd = wp_generate_password(12, false);
	$sfw_pwd_exists_check = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	if( empty($sfw_pwd_exists_check) || !$sfw_pwd_exists_check  && is_singular() && comments_open() ) {
		update_post_meta($post->ID, 'sfw_comment_form_password', $new_post_comment_pwd);
	}
}

// Creates a new comment form password each time a comment is saved in the database
function sfw_new_comment_pass() {
	global $post;
	$new_comment_pwd = wp_generate_password(12, false);
	$old_password = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	update_post_meta($post->ID, 'sfw_comment_form_password', $new_comment_pwd, $old_password);
}

// Gets the remote IP address even if behind a proxy
function get_remote_ip_address() {
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if(!empty($_SERVER['REMOTE_ADDR'])) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip_address = '';
	}
	return $ip_address;
}

// Returns Local Blocklist
function sfw_local_blocklist_check() {
	global $spam_free_wordpress_options;

	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();

	$local_blocklist_keys = trim( $spam_free_wordpress_options['blocklist_keys'] );
	if ( '' == $local_blocklist_keys )
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
			   preg_match($pattern, $comment_author_ip)
		 )
			return true;
	}
	return false;
}

// Returns Remote Blocklist
function sfw_remote_blocklist_check() {
	global $spam_free_wordpress_options;
	
	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();
	// Retrieves remote blocklist url from database
	$rbl_url = $spam_free_wordpress_options['remote_blocked_list'];
	// Uses a URL to retrieve a list of IP address in an array
	$get_remote_blocklist = wp_remote_get($rbl_url);
	
	if ( '' == $rbl_url )
		return false; // If blocklist keys are empty or url is not in the database
	$remote_key = explode("\n", $get_remote_blocklist['body'] ); // Turns blocklist array into string and lists each IP address on new line

	foreach ( (array) $remote_key as $rkey ) {
		$rkey = trim($rkey);

		// Skip empty lines
		if ( empty($rkey) ) { continue; }

		// Can use '#' to comment out line in blocklist
		$rkey = preg_quote($rkey, '#');

		$pattern = "#$rkey#i";
		if (
			   preg_match($pattern, $comment_author_ip)
		 )
			return true;
	}
	return false;
}

// Customizable Affiliate link
function custom_affiliate_link() {
	$spam_free_wordpress_options = get_option('spam_free_wordpress');

	$aff_msg = $spam_free_wordpress_options['affiliate_msg'];
	
	if ($spam_free_wordpress_options['affiliate_msg'] =='') {
		echo "<a href='http://www.toddlahman.com/spam-free-wordpress/'>Make Your Blog Spam Free</a>";
	} else {
		echo "<a href='http://www.toddlahman.com/spam-free-wordpress/'>".$aff_msg."</a>";
	}
}

// Function for comments.php file
function tl_spam_free_wordpress_comments_form() {
	global $post, $spam_free_wordpress_options;
	
	$sfw_comment_form_password_var = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	$sfw_pw_field_size = $spam_free_wordpress_options['pw_field_size'];
	$sfw_tab_index = $spam_free_wordpress_options['tab_index'];

	// If the reader is logged in don't require password for comments.php
	if ( !is_user_logged_in() ) {
		
		// Spam Count
		echo '<!-- ' . number_format_i18n(get_option('sfw_spam_hits')) . ' Spam Comments Blocked so far by Spam Free Wordpress version '.SFW_VERSION.' located at http://www.toddlahman.com/spam-free-wordpress/ -->';
		// Commenter IP address
		echo "<input type='hidden' name='comment_ip' id='comment_ip' value='".get_remote_ip_address()."' />";
		// Reader must enter this password manually on the comment form
		echo "<p><input type='text' value='".$sfw_comment_form_password_var."' onclick='this.select()' size='".$sfw_pw_field_size."' />
		<b>* Copy This Password *</b></p>";
		echo "<p><input type='text' name='passthis' id='passthis' value='' size='".$sfw_pw_field_size."' tabindex='".$sfw_tab_index."' />
		<b>* Type Or Paste Password Here *</b></p>";
		// Shows how many comment spam have been killed on the comment form
		if ($spam_free_wordpress_options['toggle_stats_update'] == "enable") {
				echo '<p>' . number_format_i18n(get_option('sfw_spam_hits')) . ' Spam Comments Blocked so far by <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank">Spam Free Wordpress</a></p>';
		} else {
				echo "";
		}
	}
}

// Function for wp-comments-post.php file located in the root Wordpress directory. The same directory as the wp-config.php file.
function tl_spam_free_wordpress_comments_post() {
	global $post, $spam_free_wordpress_options;
	
	$sfw_comment_script = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	// If the reader is logged in don't require password for wp-comments-post.php
	if ( !is_user_logged_in() ) {

		// Compares current comment form password with current password for post
		if ( empty( $_POST['passthis'] ) || $_POST['passthis'] != $sfw_comment_script)
			wp_die( __('Click back and type in the correct password. (Spam Free Wordpress)', spam_counter()) );
		
		// Compares commenter IP address to local blocklist
		if ($spam_free_wordpress_options['lbl_enable_disable'] == 'enable') {
			if ( empty( $_POST['comment_ip'] ) || $_POST['comment_ip'] == sfw_local_blocklist_check() )
				wp_die( __('Spam Blocked by Spam Free Wordpress (local blocklist)', spam_counter()) );
		}
		
		// Compares commenter IP address to remote blocklist
		if ($spam_free_wordpress_options['rbl_enable_disable'] == 'enable') {
			if ( empty( $_POST['comment_ip'] ) || $_POST['comment_ip'] == sfw_remote_blocklist_check() )
				wp_die( __('Spam Blocked by Spam Free Wordpress (remote blocklist)', spam_counter()) );
		}

	}
}

// Counts number of comment spam hits and stores in options database table
function spam_counter() {
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

/*-----------------------------------------
Closes user registration security hole
------------------------------------------*/

function sfw_close_auto_user_registration() {
	update_option( 'users_can_register', '0' );
}

// Adds settings link to plugin menu
function sfw_settings_link($links) {
	$links[] = '<a href="'.admin_url('options-general.php?page=spam-free-wordpress-admin-page').'">Settings</a>';
	return $links;
}

/**
* Corrects the Notice: Undefined index: checkbox error when the check box is not checked, and thus sends an empty value. This function gives it a value when using the WordPress checked function.
* Unfortunately a value is not stored in the database when unchecked. This problem can be corrected when using the Settings API which stores a value if checked or unchecked using a Ternary Operator
* http://wordpress.org/support/topic/problem-with-checked-function?replies=6
* Box starts out unchecked
*/
function sfw_unchecked( $checkoption ) {
	$spam_free_wordpress_options = get_option('spam_free_wordpress');
	if ( ! isset( $spam_free_wordpress_options[$checkoption] ) ) {
		$spam_free_wordpress_options[$checkoption] = 'enable';
	}
	echo '<input type="checkbox" name="spam_free_wordpress_options['. $checkoption .']" value="disable"'. checked( $spam_free_wordpress_options[$checkoption], 'disable', false ) .' />';
}

?>