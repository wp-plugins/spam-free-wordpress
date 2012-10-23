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
		
		// Set referrer check to FALSE since it fails too often
		wp_nonce_field('sfw_nonce','sfw_comment_nonce', FALSE, TRUE);
				
		if( $sfw_options['legacy_pwd'] == 'off' ) {
			echo "\n<p><noscript>JavaScript must be ond to leave a comment.</noscript></p>";
			echo "\n<input type='hidden' name='pwdfield' class='pwddefault' value='' />";
			echo "\n<input type='hidden' name='comment_ip' id='comment_ip' value='' />";
		} else {
			// Added 1.9 Invisible legacy password fields.
			$pwd_ip = sfw_pwd_ip_legacy();
			echo "\n<input type='hidden' name='comment_ip' id='comment_ip' value='".$pwd_ip['ip']."' />";
			echo "\n<input type='hidden' name='pwdfield' id='pwdfield' value='".$pwd_ip['pwd']."' />\n";
		}
		
		if( $sfw_options['cf_spam_stats'] == 'on' ) {
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
				wp_die( __( 'Spam Free Wordpress nonce security check failed.', 'spam-free-wordpress' ) . sfw_spam_counter(), 'Spam Free Wordpress rejected your comment', array( 'response' => 200, 'back_link' => true ) );
					
			// Compares current comment form password with current password for post
			if( empty( $_POST['pwdfield'] ) || $_POST['pwdfield'] != $sfw_comment_script )
				wp_die( __( 'Spam Free Wordpress could not retrieve the password from the server. Contact support.', 'spam-free-wordpress' ) . sfw_spam_counter(), 'Spam Free Wordpress rejected your comment', array( 'response' => 200, 'back_link' => true ) );
		
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
*/
function sfw_load_pwd() {
	// for testing multiple versions of jQuery
	/*
	 * 	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', SFW_VERSION, true );
	 *	wp_enqueue_script( 'jquery' );
	 */
	if( is_singular() ) {
		$js_path =  SFW_URL . 'js/sfw-ipwd.js';

		wp_enqueue_script( 'sfw_ipwd', $js_path, array( 'jquery' ), SFW_VERSION, true );
		// Added 1.9 check_ajax_referer nonce data
		wp_localize_script( 'sfw_ipwd', 'sfw_ipwd_script', array( 'sfw_ajaxurl' => admin_url( 'admin-ajax.php' ), 'sfw_ajax_nonce_sec' => wp_create_nonce( 'sfw-ajax-nonce' ) ) );
		
		//wp_register_script( 'CryptoJS', 'http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', null, SFW_VERSION, true );
		//wp_enqueue_script( 'CryptoJS' );
		// wp_check_password( $password, $hash, $user_id );
		// wp_hash_password( $password );
		// wp_salt( $scheme );
	}
}

/****************************************************
* Combined password and IP address retrieval for AJAX
* Temporary passwords stored in transient
****************************************************/
function sfw_pwd_ip() {
	// Added 1.9 to secure AJAX
	check_ajax_referer( 'sfw-ajax-nonce', 'sfw_nonce_for_ajax' );
	
	// Get post_id from AJAX
	$postid = $_POST['post_id'];
	
	// Get password
	$pwd = wp_generate_password(12, false);
	set_transient( $postid. '-' .$pwd, $pwd, 60 * 20 ); // expire password after 20 minutes
	$pwd_key = get_transient( $postid. '-' .$pwd );

	// Get IP address
	if(!empty( $_SERVER['HTTP_CLIENT_IP']) ) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( !empty($_SERVER['REMOTE_ADDR']) ) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip_address = '';
	}

	// Send info back to AJAX
	echo json_encode( array( 'pwd'=>$pwd_key, 'ip'=>$ip_address ) );
	
	// die() for AJAX
	die();
}

// Added 1.8.6 to process legacy dual password fields
function sfw_pwd_ip_legacy() {
	global $post;
	$postid = $post->ID;
	
	// Get password
	$pwd = wp_generate_password(12, false);
	set_transient( $postid. '-' .$pwd, $pwd, 60 * 20 ); // expire password after 20 minutes
	$pwd_key = get_transient( $postid. '-' .$pwd );

	// Get IP address
	if(!empty( $_SERVER['HTTP_CLIENT_IP']) ) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( !empty($_SERVER['REMOTE_ADDR']) ) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip_address = '';
	}

	$results = array( 'pwd'=>$pwd_key, 'ip'=>$ip_address );

	return $results;
}

// Added 1.9 Enqueue style file
function sfw_load_styles() {
	$css = SFW_URL . 'css/sfw-comment-style.css?' . filemtime( SFW_PATH . 'css/sfw-comment-style.css' );

	if ( !is_admin() ) {
		wp_register_style( 'sfw-comment-style', $css, array(), NULL, 'all' );
		wp_enqueue_style( 'sfw-comment-style' );
	}
}

?>