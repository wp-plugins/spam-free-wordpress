<?php

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

// Added version 1.7.6
function sfw_add_default_pwd_style() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$spam_free_wordpress_options = get_option('spam_free_wordpress');
	
		$oldver = $spam_free_wordpress_options;
		$newver = array(
			'pwd_style' => 'invisible_password',
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
		
	}
}


?>