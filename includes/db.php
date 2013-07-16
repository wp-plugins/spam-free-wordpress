<?php

/*******************************************
* Database default and upgrade
********************************************/
// Add default database settings on plugin activation
function sfw_default() {
	if( !get_option( 'spam_free_wordpress' ) ) {
		if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die( SFW_WP_REQUIRED_MSG );
		} else {
			$sfw_options = array(
			'comment_form' => 'on',
			'legacy_pwd' => 'on'
			);
			update_option( 'spam_free_wordpress', $sfw_options );

			}

		if( !get_option( 'sfw_spam_hits' ) ) {
			update_option( 'sfw_spam_hits', '1' );
		}
	}
}

