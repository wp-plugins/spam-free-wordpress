<?php

if( !class_exists('SFW_MENU' ) ) {
	
	class SFW_MENU {

		// Load admin menu
		function __construct() {
			add_action( 'admin_menu', array( $this, 'add_sfw_menu' ) );
			add_action( 'admin_init', array( $this, 'load_settings' ) );
		}
		
		
		// Add option page menu
		function add_sfw_menu() {					
			$page = add_options_page( 'Spam Free Wordpress Settings', 'Spam Free Wordpress',
							'manage_options', 'sfw_dashboard', array( $this, 'config_page')
			);
			add_action( 'admin_print_styles-' . $page, array( $this, 'sfw_admin_css' ) );
			add_action( "admin_print_scripts-$page", array( $this, 'sfw_tooltip_scripts' ) );
		}
		
		
		// Draw option page
		function config_page() {
			?>
			<div class='wrap'>
				<?php screen_icon(); ?>
				<h2><?php _e( 'Spam Free Wordpress Settings', 'spam-free-wordpress' ); ?></h2>
				<form action='options.php' method='post'>
				<?php settings_fields( 'spam_free_wordpress' ); ?>
			
				<script type="text/javascript">
					(function($) {
						$(document).ready(function() {
							$(".tool-tip").tipTip({maxWidth: "310px", edgeOffset: 15});
						});
					})(jQuery);
				</script>
			
				<div class="main">
					<?php
					// Part of settings api requirements
					do_settings_sections( 'sfw_dashboard' );
					?>
				

					<?php
					$sfw_save_changes = __( 'Save Changes', 'spam-free-wordpress' );
					submit_button( $sfw_save_changes );
					?>
				</div>
				<div class="sidebar">
					<?php $this->sfw_sidebar(); ?>
				</div>
				</form>
			</div>
			<?php
		}
		
		
		// Register settings
		function load_settings() {									
			register_setting( 'spam_free_wordpress', 'spam_free_wordpress', array( $this, 'validate_options' ) );
			
			add_settings_section( 'api_key', 'License Key', array( $this, 'api_key_section_text' ), 'sfw_dashboard' );
			add_settings_field( 'api_key', 'License Key', array( $this, 'api_key_field' ), 'sfw_dashboard', 'api_key' );
			
			add_settings_section( 'comment_form', 'Comment Form', array( $this, 'checkbox_text' ), 'sfw_dashboard' );
			
			$checkboxes = array(
									'Spam Stats',
									'Generate Comment Form',
									'Remove Comment HTML',
									'Remove URL Field',
									'Remove Author Link',
									'Close Pingbacks'
								);
				foreach( $checkboxes as $box ) {
					add_settings_field(
						$box, $box, array( $this, 'checkboxes' ), 'sfw_dashboard', 'comment_form', $box
					);
				}
			
			add_settings_section( 'cf_msg', 'Comment Form Message', array( $this, 'cf_msg_text' ), 'sfw_dashboard' );
			add_settings_field( 'cf_msg', '', array( $this, 'cf_msg_textarea' ), 'sfw_dashboard', 'cf_msg' );
			
			add_settings_section( 'bl_keys', 'Spam IP Address Blocklist', array( $this, 'bl_keys_text' ), 'sfw_dashboard' );
			add_settings_field( 'bl_keys', '', array( $this, 'bl_keys_textarea' ), 'sfw_dashboard', 'bl_keys' );
				
			add_settings_section( 'tech_support_info', 'Tech Support Information', array( $this, 'tech_support_info_text' ), 'sfw_dashboard' );
			
			$tech_support = array(
									'PHP Version',
									'Database Version',
									'WordPress Version',
									'Spam Free Wordpress Version'
								);
				foreach( $tech_support as $ts ) {
					add_settings_field(
						$ts, $ts, array( $this, 'tech_support' ), 'sfw_dashboard', 'tech_support_info', $ts
					);
				}

		}
		
		
		// Provides text for api key section
		function api_key_section_text() {
			if( SFW_KEY::get_key() == '' ) {
				echo '<p class="alert">';
				_e( 'PLUGIN SUPPORT, AND ADVANCED PLUGIN FEATURES, REQUIRE A FREE LICENSE KEY.', 'spam-free-wordpress' );
				echo '</p>';
			}
		}
		
		
		function cf_msg_text() {
			if( SFW_KEY::get_key() == '' ) {
				echo '<p class="alert">';
				_e( 'THIS SECTION REQUIRES A FREE LICENSE KEY.', 'spam-free-wordpress' );
				echo '</p>';
			} else {
				_e( 'Text and HTML are acceptable.', 'spam-free-wordpress' );
				SFW_TOOL_TIPS::tips( 'comment_msg' );
			}
		}
		
		
		function bl_keys_text() {
			if( SFW_KEY::get_key() == '' ) {
				echo '<p class="alert">';
				_e( 'THIS SECTION REQUIRES A FREE LICENSE KEY.', 'spam-free-wordpress' );
				echo '</p>';
			} else {
				_e( 'IP addresses only. One IP address per line.', 'spam-free-wordpress' );
				SFW_TOOL_TIPS::tips( 'blocklist' );
			}
		}
	
				
		// Outputs API License text field
		function api_key_field() {
			$options = get_option( 'spam_free_wordpress' );
			$api_key = $options['api_key'];
			echo "<input id='api_key' name='spam_free_wordpress[api_key]' size='25' type='text' value='{$options['api_key']}' />";
			if ( !empty( $options['api_key'] ) ) {
				echo "<span class='icon-pos'><img src='".SFW_URL."images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
			} else {
				echo "<span class='icon-pos'><img src='".SFW_URL."images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span><strong><a href='".SFW_API_KEY_URL."' target='_blank'>Get a free license key.</a></strong>";
			}
		}
		
		

		function cf_msg_textarea() {
			$options = get_option( 'spam_free_wordpress' );
			if( SFW_KEY::get_key() == '' ) {
				echo '<div class="hidden">';
			}
			echo '<textarea id="cf_msg" rows="10" name="spam_free_wordpress[cf_msg]">' . $options[ 'cf_msg' ] . '</textarea>';
			if( SFW_KEY::get_key() == '' ) {
				echo '</div>';
			}
		}
		
		
		function bl_keys_textarea() {
			$options = get_option( 'spam_free_wordpress' );
			if( SFW_KEY::get_key() == '' ) {
				echo '<div class="hidden">';
			}
			echo '<textarea id="bl_keys" rows="8" name="spam_free_wordpress[bl_keys]">' . $options[ 'bl_keys' ] . '</textarea>';
			if( SFW_KEY::get_key() == '' ) {
				echo '</div>';
			}
		}
		
		
		// Provides text for search engine section
		function checkbox_text() {
			//
		}
		
		
		function tech_support_info_text() {
			//
		}
		
		
		// Sanitizes and validates all input and output
		function validate_options( $input ) {
			// Load existing options, validate, and update with changes from input before returning
			$options = get_option( 'spam_free_wordpress' );
			/**
			* API/License Key
			*/

			$options['api_key'] = trim( $input['api_key'] );
			
			$api_key_msg = __( 'is not a valid license key.', 'spam-free-wordpress' );
			
			if ( !preg_match( '/^(sfw-)+[A-Za-z0-9-]+$/', $options['api_key'] ) || $options['api_key'] == '' ) {
				add_settings_error( 'api_key_text', 'api_key_error', "{$options['api_key']} $api_key_msg", 'error' );
				$options['api_key'] = '';
			}
			
			$options['cf_spam_stats'] = ( $input['cf_spam_stats'] == 'on' ? 'on' : 'off' );
			$options['comment_form'] = ( $input['comment_form'] == 'on' ? 'on' : 'off' );
			$options['cf_html'] = ( $input['cf_html'] == 'on' ? 'on' : 'off' );
			$options['website_url'] = ( $input['website_url'] == 'on' ? 'on' : 'off' );
			$options['author_link'] = ( $input['author_link'] == 'on' ? 'on' : 'off' );
			$options['ping_status'] = ( $input['ping_status'] == 'on' ? 'on' : 'off' );
			
			if( $input['cf_msg'] == '' ) {
				$options['cf_msg'] = ' ';
			} else {
				$options['cf_msg'] = stripslashes( $input['cf_msg'] );
			}
			
			if( $input['bl_keys'] == '' ) {
				$options['bl_keys'] = ' ';
			} else {
				$options['bl_keys'] = esc_textarea( trim( $input['bl_keys'] ) );
			}
			
			return $options;
		}
		
		
		// Outputs checkboxes
		function checkboxes( $checkboxes ) {
			$options = get_option( 'spam_free_wordpress' );

			switch ( $checkboxes ) {
				case 'Spam Stats':
					?>
					<input type="checkbox" id="cf_spam_stats" name="spam_free_wordpress[cf_spam_stats]" value="on" <?php checked( $options['cf_spam_stats'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'stats' ) ?>
					<?php
					if ( $options['cf_spam_stats'] != 'on' ) {
						echo "<span class='icon-pos'><img src='".SFW_URL."images/warn.png' title='' /></span><strong>Needs attention!</strong>";
					}
					?>
					<?php
					break;
				case 'Generate Comment Form':
					?>
					<input type="checkbox" id="comment_form" name="spam_free_wordpress[comment_form]" value="on" <?php checked( $options['comment_form'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'comment_form' ) ?>
					<?php
					break;
				case 'Remove Comment HTML':
					?>
					<input type="checkbox" id="cf_html" name="spam_free_wordpress[cf_html]" value="on" <?php checked( $options['cf_html'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'html' ) ?>
					<?php
					break;
				case 'Remove URL Field':
					?>
					<input type="checkbox" id="website_url" name="spam_free_wordpress[website_url]" value="on" <?php checked( $options['website_url'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'url' ) ?>
					<?php
					break;
				case 'Remove Author Link':
					?>
					<input type="checkbox" id="author_link" name="spam_free_wordpress[author_link]" value="on" <?php checked( $options['author_link'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'link' ) ?>
					<?php
					break;
				case 'Close Pingbacks':
					?>
					<input type="checkbox" id="ping_status" name="spam_free_wordpress[ping_status]" value="on" <?php checked( $options['ping_status'], 'on' ); ?> /><?php SFW_TOOL_TIPS::tips( 'pingbacks' ) ?>
					<?php
			}
		}
		
		
		// Tech Support Information
		function tech_support( $ts ) {
			global $wpdb;

			$php_version = phpversion();
			$db_version = $wpdb->db_version();
			$wordpress_version = get_bloginfo("version");
			$sfw_version = SFW_VERSION;
			
			switch ( $ts ) {
				case 'PHP Version':
					_e( $php_version, 'spam-free-wordpress' );
					break;
				case 'Database Version':
					_e( $db_version, 'spam-free-wordpress' );
					break;
				case 'WordPress Version':
					_e( $wordpress_version, 'spam-free-wordpress' );
					break;
				case 'Spam Free Wordpress Version':
					_e( $sfw_version, 'spam-free-wordpress' );
					break;
			}
		}
		
		// Loads admin style sheets
		function sfw_admin_css() {
			wp_register_style( 'sfw-tool-tips', SFW_URL . '/css/tool-tips.css', array(), '1.0.0', 'all');
			wp_enqueue_style( 'sfw-tool-tips' );
			wp_register_style( 'sfw-admin-css', SFW_URL . '/css/style-admin.css', array(), '1.0.0', 'all');
			wp_enqueue_style( 'sfw-admin-css' );
		}
		
		// Tooltip script
		function sfw_tooltip_scripts(){
			$js_path =  SFW_URL . 'js/jquery.tipTip.minified.js';
	
			wp_register_script( 'tool-tip' , $js_path, null, SFW_VERSION );
			wp_enqueue_script( 'tool-tip', $js_path, array( 'jquery' ), SFW_VERSION );
		}
	
		// displays sidebar
		function sfw_sidebar() {
			?>
			<h3><?php _e( 'Hire Todd Lahman', 'spam-free-wordpress' ); ?></h3>
			<ul class="celist">
				<p><a href="http://www.toddlahman.com/hire-todd-lahman-search-engine-optimization/" target="_blank"><?php _e( 'Hire Todd Lahman', 'spam-free-wordpress' ); ?></a></p>
				<p><strong><?php _e( "Todd's services include:", 'spam-free-wordpress' ); ?></strong></p>
				<li><?php _e( "WordPress Search Engine Optimization.", 'spam-free-wordpress' ); ?></li>
				<li><?php _e( "Incredibly fast website tuning to handle high loads of traffic for less money.", 'spam-free-wordpress' ); ?></li>
				<li><?php _e( "Theme and plugin development.", 'spam-free-wordpress' ); ?></li>
				<li><?php _e( "Custom programming in PHP, jQuery, HTML 4 or 5, MySQL, CSS 2 and 3, and other languages.", 'spam-free-wordpress' ); ?></li>
				<li><?php _e( "Linux server administration.", 'spam-free-wordpress' ); ?></li>
				<li><a href="http://www.toddlahman.com/spam-free-wordpress-support/" target="_blank"><?php _e( 'Comment Form CSS Styling', 'spam-free-wordpress' ); ?></a></li>
				<li><a href="http://www.toddlahman.com/spam-free-wordpress-support/" target="_blank"><?php _e( 'Advanced SFW Support', 'spam-free-wordpress' ); ?></a></li>
			</ul>
			<h3><?php _e( 'Other Plugins', 'spam-free-wordpress' ); ?></h3>
			<ul class="celist">
				<li><a href="http://www.toddlahman.com/shop/search-engine-ping/" target="_blank"><?php _e( 'Search Engine Ping', 'spam-free-wordpress' ); ?></a></li>
				<li><a href="http://www.toddlahman.com/shop/cachengin-wordpress-cache-plugin-for-nginx/" target="_blank"><?php _e( 'CacheNgin', 'spam-free-wordpress' ); ?></a></li>
				<li><a href="http://www.toddlahman.com/shop/translation-cache/" target="_blank"><?php _e( 'Translation Cache', 'spam-free-wordpress' ); ?></a></li>
			</ul>
			<h3><?php _e( 'Buy Todd a Coffee', 'spam-free-wordpress' ); ?></h3>
			<ul class="celist">
				<p><?php _e( "Given enough coffee I could rule the world.", 'spam-free-wordpress' ); ?></p>
				<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SFVH6PCCC6TLG" target="_blank"><?php _e( 'Buy Todd a Coffee', 'spam-free-wordpress' ); ?></a></li>
			</ul>
			<h3><?php _e( 'Support Forum', 'spam-free-wordpress' ); ?></h3>
			<ul class="celist">
				<p><?php _e( "Be sure to login to use the support forum.", 'spam-free-wordpress' ); ?></p>
				<li><a href="http://www.toddlahman.com/forums/forum/spam-free-wordpress/" target="_blank"><?php _e( 'Support Forum', 'spam-free-wordpress' ); ?></a></li>
			</ul>
			<?php
		}
		
	}

	$sfw_menu = new SFW_MENU();

}

?>