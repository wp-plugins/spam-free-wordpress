<?php

if( !class_exists('SFW_MENU' ) ) {

	class SFW_MENU {

		// Load admin menu
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_sfw_menu' ) );
			add_action( 'admin_init', array( $this, 'load_settings' ) );
		}


		// Add option page menu
		public function add_sfw_menu() {
			$page = add_options_page( 'Spam Free Wordpress', 'Spam Free Wordpress',
							'manage_options', 'sfw_dashboard', array( $this, 'config_page')
			);
			add_action( 'admin_print_styles-' . $page, array( $this, 'sfw_admin_css' ) );
		}


		// Draw option page
		public function config_page() {
			?>
			<div class='wrap'>
				<?php screen_icon(); ?>
				<h2><?php _e( 'Spam Free Wordpress', 'spam-free-wordpress' ); ?></h2>
				<form action='options.php' method='post'>
				<?php settings_fields( 'spam_free_wp' ); ?>

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
				/**
				 * Special offer notice
				 */
				if ( isset( $_GET['notice'] ) ) {
					if ( $_GET['notice'] == 1 ) {
						update_option( 'sfwp_july_coupon', 1 );
					}
				}
		}


		// Register settings
		public function load_settings() {
			register_setting( 'spam_free_wp', 'spam_free_wp', array( $this, 'validate_options' ) );

			// Comment Form
			add_settings_section( 'comment_form', 'Comment Form', array( $this, 'checkbox_text' ), 'sfw_dashboard' );
			$checkboxes = array(
									'Spam Stats',
									'Generate Comment Form'
								);
			foreach( $checkboxes as $box ) {
				add_settings_field( $box, $box, array( $this, 'checkboxes' ), 'sfw_dashboard', 'comment_form', $box );
			}

		}


		public function checkbox_text() {
			//
		}


		// Sanitizes and validates all input and output
		public function validate_options( $input ) {

			// Load existing options, validate, and update with changes from input before returning
			$options = get_option( 'spam_free_wp' );

			if ( isset( $input['spam_stats'] ) && $input['spam_stats'] == 'on' ) {
				$options['spam_stats'] = 'on';
			} else {
				$options['spam_stats'] = 'off';
			}

			if ( isset( $input['comment_form'] ) && $input['comment_form'] == 'on' ) {
				$options['comment_form'] = 'on';
			} else {
				$options['comment_form'] = 'off';
			}

			return $options;
		}

		// Outputs Comment Form checkboxes
		public function checkboxes( $box ) {
			global $sfw_tool_tips;
			$options = get_option( 'spam_free_wp' );

			switch ( $box ) {
				case 'Spam Stats':
					?>
					<input type="checkbox" id="spam_stats" name="spam_free_wp[spam_stats]" value="on" <?php checked( esc_attr( $options['spam_stats'] ), 'on' ); ?> />
					<span class="description" style="padding-left:10px;"><?php _e( 'See how much spam has been blocked. Stats appear on the comment form.', 'spam-free-wordpress' ); ?></span>
					<?php
					break;
				case 'Generate Comment Form':
					?>
					<input type="checkbox" id="comment_form" name="spam_free_wp[comment_form]" value="on" <?php checked( esc_attr( $options['comment_form'] ), 'on' ); ?> />
					<span class="description" style="padding-left:10px;"><?php _e( 'Generates a comment form if theme is not compatible with SFW.', 'spam-free-wordpress' ); ?></span>
					<?php
					break;
			}
		}

		// Loads admin style sheets
		public function sfw_admin_css() {
			wp_register_style( 'sfw-admin-css', SFW_URL . 'css/style-admin.css', array(), SFW_VERSION, 'all');
			wp_enqueue_style( 'sfw-admin-css' );
		}

		// displays sidebar
		public function sfw_sidebar() {
			?>
			<h3 class="sb_green"><?php _e( 'Use This Coupon', 'simple-comments' ); ?></h3>
			<ul class="celist">
				<li class="promo"><a href="https://www.toddlahman.com/shop/simple-comments/" target="_blank"><?php _e( 'Upgrade to Simple Comments', 'simple-comments' ); ?></a></li>
				<li class="promo"><?php _e( '10% Off with Coupon Code WPSFW', 'simple-comments' ); ?></li>
			</ul>
			<h3 class="sb_yellow"><?php _e( 'Simple Comments Features', 'simple-comments' ); ?></h3>
			<ul class="celist">
				<li class="promo"><strong><u><?php esc_html_e( '99.9% - 100% Spambot and Hackbot protection for the following forms:', 'spam-free-wordpress' ); ?></u></strong></li>
				<li class="promo"><?php esc_html_e( 'WordPress Comment form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'Gravity Forms', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'Contact Form 7', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WordPress login form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WordPress user registration form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WooCommerce Product Review form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WooCommerce Product Enquiry Form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WooCommerce 2.1 and above Login Form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'WooCommerce 2.1 and above Registration Form', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'AffiliateWP', 'spam-free-wordpress' ); ?></li>
				<li class="promo"></li>
				<li class="promo"><strong><u><?php esc_html_e( 'Security Features:', 'spam-free-wordpress' ); ?></u></strong></li>
				<li class="promo"><?php esc_html_e( 'SEO Hacking Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'Redirection Hacking Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'DDOS Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'XSS Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'CSRF Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'Brute Force Attack Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"><?php esc_html_e( 'XML RPC Service Attack Protection', 'spam-free-wordpress' ); ?></li>
				<li class="promo"></li>
				<li class="promo"><a href="https://www.toddlahman.com/shop/simple-comments/" target="_blank"><?php _e( 'Read more', 'simple-comments' ); ?></a></li>
			</ul>
			<?php
		}

	}

	$sfw_menu = new SFW_MENU();

}
