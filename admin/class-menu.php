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
			$page = add_options_page( 'Spam Free Wordpress', 'Spam Free Wordpress',
							'manage_options', 'sfw_dashboard', array( $this, 'config_page')
			);
			add_action( 'admin_print_styles-' . $page, array( $this, 'sfw_admin_css' ) );
		}


		// Draw option page
		function config_page() {
			?>
			<div class='wrap'>
				<?php screen_icon(); ?>
				<h2><?php _e( 'Spam Free Wordpress', 'spam-free-wordpress' ); ?></h2>
				<form action='options.php' method='post'>
				<?php settings_fields( 'spam_free_wordpress' ); ?>

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
				// Special offer notice
				if ( isset( $_GET['notice'] ) ) {
					if ( $_GET['notice'] == 1 ) {
						update_option('sfwp_july_coupon', 1);
					}
				}
		}


		// Register settings
		function load_settings() {
			register_setting( 'spam_free_wordpress', 'spam_free_wordpress', array( $this, 'validate_options' ) );

			// Comment Form
			add_settings_section( 'comment_form', 'Comment Form', array( $this, 'checkbox_text' ), 'sfw_dashboard' );
			$checkboxes = array(
									'Generate Comment Form'
								);
			foreach( $checkboxes as $box ) {
				add_settings_field( $box, $box, array( $this, 'checkboxes' ), 'sfw_dashboard', 'comment_form', $box );
			}

		}


		function checkbox_text() {
			//
		}


		// Sanitizes and validates all input and output
		function validate_options( $input ) {

			// Load existing options, validate, and update with changes from input before returning
			$options = get_option( 'spam_free_wordpress' );

			$options['comment_form'] = ( $input['comment_form'] == 'on' ? 'on' : 'off' );

			return $options;
		}


		// Outputs Comment Form checkboxes
		function checkboxes( $box ) {
			global $sfw_tool_tips;
			$options = get_option( 'spam_free_wordpress' );

			switch ( $box ) {
				case 'Generate Comment Form':
					?>
					<input type="checkbox" id="comment_form" name="spam_free_wordpress[comment_form]" value="on" <?php checked( $options['comment_form'], 'on' ); ?> />
					<span class="description" style="padding-left:10px;"><?php _e( 'Generates a comment form if theme is not compatible with SFW.', 'spam-free-wordpress' ); ?></span>
					<?php
					break;
			}
		}

		// Loads admin style sheets
		function sfw_admin_css() {
			wp_register_style( 'sfw-admin-css', SFW_URL . 'css/style-admin.css', array(), SFW_VERSION, 'all');
			wp_enqueue_style( 'sfw-admin-css' );
		}

		// displays sidebar
		function sfw_sidebar() {
			?>
			<h2><?php _e( 'Simple Comments', 'spam-free-wordpress' ); ?></h2>
			<ul class="celist">
				<h3>Get bullet proof spam protection, a lot more features, and support with Simple Comments.</h3>
				<?php if ( SFW_COUPON_TIME > time() ) { ?>
					<h4>Until July 31 get 20% OFF Simple Comments. Use COUPON CODE <span style="padding:1px; background-color: #e5f3ff">SFWPJUL</span>. <a href="http://www.toddlahman.com/shop/simple-comments/" target="_blank">Learn more</a>.</h4>
				<?php } ?>
				<li><a href="http://www.toddlahman.com/shop/simple-comments/" target="_blank"><?php _e( 'Simple Comments', 'spam-free-wordpress' ); ?></a></li>
			</ul>
			<?php
		}

	}

	$sfw_menu = new SFW_MENU();

}
