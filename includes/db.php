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
			'bl_keys' => '',
			'cf_spam_stats' => 'off',
			'cf_html' => 'on',
			'website_url' => 'on',
			'author_link' => 'on',
			'ping_status' => 'on',
			'comment_form' => 'on',
			'cf_msg' => '',
			'api_key' => '',
			'clean_spam' => 'off',
			'clean_trackbacks' => 'off',
			'clean_unapproved' => 'off',
			'legacy_pwd' => 'off',
			'nonce' => 'off'
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
		

// Since 1.8
function sfw_upgrade_db() {
	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$sfw_options = get_option('spam_free_wordpress');
				
		if( isset( $sfw_options['blocklist_keys'] ) ) {
			$sfw_options['bl_keys'] = $sfw_options['blocklist_keys'];
		} else {
			$sfw_options['bl_keys'] = '';
		}
				
		if( isset( $sfw_options['toggle_stats_update'] ) ) {
			$sfw_options['cf_spam_stats'] = $sfw_options['toggle_stats_update'];
				if( $sfw_options['cf_spam_stats'] == 'disable' ) {
					$sfw_options['cf_spam_stats'] = 'off';
				} elseif( $sfw_options['cf_spam_stats'] == 'enable' ) {
					$sfw_options['cf_spam_stats'] = 'on';
				}
		} else {
			$sfw_options['cf_spam_stats'] = 'off';
		}
				
		if( isset( $sfw_options['toggle_html'] ) ) {
			$sfw_options['cf_html'] = $sfw_options['toggle_html'];
				if( $sfw_options['cf_html'] == 'disable' ) {
					$sfw_options['cf_html'] = 'off';
				} elseif( $sfw_options['cf_html'] == 'enable' ) {
					$sfw_options['cf_html'] = 'on';
				}
		} else {
			$sfw_options['cf_html'] = 'on';
		}
				
		if( isset( $sfw_options['remove_author_url_field'] ) ) {
			$sfw_options['website_url'] = $sfw_options['remove_author_url_field'];
			if( $sfw_options['website_url'] == 'disable' ) {
					$sfw_options['website_url'] = 'off';
				} elseif( $sfw_options['website_url'] == 'enable' ) {
					$sfw_options['website_url'] = 'on';
				}
		} else {
			$sfw_options['website_url'] = 'on';
		}
				
		if( isset( $sfw_options['remove_author_url'] ) ) {
			$sfw_options['author_link'] = $sfw_options['remove_author_url'];
			if( $sfw_options['author_link'] == 'disable' ) {
					$sfw_options['author_link'] = 'off';
				} elseif( $sfw_options['author_link'] == 'enable' ) {
					$sfw_options['author_link'] = 'on';
				}
		} else {
			$sfw_options['author_link'] = 'on';
		}
				
		if( isset( $sfw_options['ping_status'] ) ) {
			$sfw_options['ping_status'] = $sfw_options['ping_status'];
				if( $sfw_options['ping_status'] == 'closed' ) {
					$sfw_options['ping_status'] = 'off';
				} elseif( $sfw_options['ping_status'] == 'open' ) {
					$sfw_options['ping_status'] = 'on';
				}
		} else {
			$sfw_options['ping_status'] = 'on';
		}
				
		if( isset( $sfw_options['comment_form'] ) ) {
			$sfw_options['comment_form'] = $sfw_options['comment_form'];
		} else {
			$sfw_options['comment_form'] = 'on';
		}
				
		if( isset( $sfw_options['comment_form'] ) ) {
			$sfw_options['comment_form'] = $sfw_options['comment_form'];
		} else {
			$sfw_options['comment_form'] = 'on';
		}
				
		if( isset( $sfw_options['special_msg'] ) ) {
			$sfw_options['cf_msg'] = $sfw_options['special_msg'];
		} else {
			$sfw_options['cf_msg'] = '';
		}
				
		if( isset( $sfw_options['api_key'] ) ) {
			$sfw_options['api_key'] = $sfw_options['api_key'];
		} else {
			$sfw_options['api_key'] = '';
		}
					
		update_option( 'spam_free_wordpress', $sfw_options );
	}
}

function sfw_upgrade_db_clean_spam() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$sfw_options = get_option('spam_free_wordpress');
	
		$oldver = $sfw_options;
		$newver = array(
			'clean_spam' => 'off',
			'clean_trackbacks' => 'off',
			'clean_unapproved' => 'off'
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
		
	}
}

// Added 1.8.6
function sfw_upgrade_db_legacy_pwd() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$sfw_options = get_option('spam_free_wordpress');
	
		$oldver = $sfw_options;
		$newver = array(
			'legacy_pwd' => 'off'
			);
		$mergever = array_merge( $oldver, $newver );
	
		update_option( 'spam_free_wordpress', $mergever );
		
	}
}

// Added 1.9.1
function sfw_upgrade_db_nonce() {

	if ( version_compare( get_bloginfo( 'version' ), SFW_WP_REQUIRED, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( SFW_WP_REQUIRED_MSG );
	} else {
		$sfw_options = get_option('spam_free_wordpress');

		$oldver = $sfw_options;
		$newver = array(
				'nonce' => 'off'
		);
		$mergever = array_merge( $oldver, $newver );

		update_option( 'spam_free_wordpress', $mergever );

	}
}

/*******************************************
* Database default and upgrade END
********************************************/

// http://xmouse.ithium.net/2004/removing-values-from-a-php-array

?>