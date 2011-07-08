<?php
/*
Plugin Name: Spam Free Wordpress
Plugin URI: http://www.toddlahman.com/spam-free-wordpress/
Description: Comment spam blocking plugin that uses anonymous password authentication to achieve 100% automated spam blocking with zero false positives, plus a few more features.
Version: 1.5.1
Author: Todd Lahman, LLC
Author URI: http://www.toddlahman.com/
*/

// Plugin version
$spam_free_wordpress_version = "1.5.1";

/*
	Copyright 2007 - 2011 by Todd Lahman, LLC.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add default database settings on plugin activation
function sfw_add_default_data() {
	$sfw_options = array(
		'blocklist_keys' => '',
		'lbl_enable_disable' => 'disable',
		'remote_blocked_list' => '',
		'rbl_enable_disable' => 'disable',
		'pw_field_size' => '30',
		'tab_index' => '',
		'affiliate_msg' => '',
		'toggle_stats_update' => 'disable',
		'toggle_html' => 'disable'
		// 'sfw_version' => '1.5.1'
		);
	add_option('spam_free_wordpress', $sfw_options);
	add_option('sfw_spam_hits', '1');
}

// variable used as global to retrieve option array for functions
$wp_sfw_options = get_option('spam_free_wordpress');
// Gets Spam Blocked Count
$sfw_count = number_format_i18n(get_option('sfw_spam_hits'));

// Runs add_default_data function above when plugin activated
register_activation_hook( __FILE__, 'sfw_add_default_data' );

// Delete the default options from database when plugin deactivated,
// The post comment passwords can be deleted also using the following SQL statement.
// DELETE from wp_postmeta WHERE meta_key = "sfw_comment_form_password" ;


// Deletes all options listed in remove_default_data when plugin deactivated
// Remove // to enable an option to be deleted
function sfw_remove_default_data() {
// delete_option('spam_free_wordpress');
// delete_option('sfw_spam_hits');
}

// Deletes all options listed in remove_default_data when plugin deactivated
register_deactivation_hook( __FILE__, 'sfw_remove_default_data' );

// Checks to see if comment form password exists and if not creates one in custom fields
function sfw_comment_pass_exist_check() {
	global $post;
	$new_post_comment_pwd = wp_generate_password(12, false);
	$sfw_pwd_exists_check = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	if( empty($sfw_pwd_exists_check) || !$sfw_pwd_exists_check  && comments_open() ) {
		update_post_meta($post->ID, 'sfw_comment_form_password', $new_post_comment_pwd);
	}
}
add_action('loop_start', 'sfw_comment_pass_exist_check', 1);

// Creates a new comment form password each time a comment is saved in the database
function sfw_new_comment_pass() {
	global $post;
	$new_comment_pwd = wp_generate_password(12, false);
	$old_password = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	update_post_meta($post->ID, 'sfw_comment_form_password', $new_comment_pwd, $old_password);
}

// Call the function to change key 2 password to custom fields when after each new comment is saved in the database.
add_action('comment_post', 'sfw_new_comment_pass', 1);

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
	global $wp_sfw_options;

	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();

	$local_blocklist_keys = trim( $wp_sfw_options['blocklist_keys'] );
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
	global $wp_sfw_options;
	
	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();
	// Retrieves remote blocklist url from database
	$rbl_url = $wp_sfw_options['remote_blocked_list'];
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
	global $wp_sfw_options;

	$aff_msg = $wp_sfw_options['affiliate_msg'];
	
	if ($wp_sfw_options['affiliate_msg'] =='') {
		echo "<a href='http://www.toddlahman.com/spam-free-wordpress/' rel='nofollow'>Make Your Blog Spam Free</a>";
	} else {
		echo "<a href='http://www.toddlahman.com/spam-free-wordpress/' rel='nofollow'>".$aff_msg."</a>";
	}
}

// Function for comments.php file
function tl_spam_free_wordpress_comments_form() {
	global $wp_sfw_options, $post, $spam_free_wordpress_version, $wp_version, $sfw_count;
	
	$sfw_comment_form_password_var = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	$sfw_pw_field_size = $wp_sfw_options['pw_field_size'];
	$sfw_tab_index = $wp_sfw_options['tab_index'];

	// If the reader is logged in don't require password for comments.php
	if ( !is_user_logged_in() ) {
		// Hidden credit
		echo '<!-- ' . $sfw_count . ' Spam Comments Blocked so far by Spam Free Wordpress version '.$spam_free_wordpress_version.' located at http://www.toddlahman.com/spam-free-wordpress/ -->';
		// Commenter IP address
		echo "<input type='hidden' name='comment_ip' id='comment_ip' value='".get_remote_ip_address()."' />";
		// Reader must enter this password manually on the comment form
		echo "<p>* Copy this password:
		<input type='text' value='".$sfw_comment_form_password_var."' onclick='this.select()' size='".$sfw_pw_field_size."' /></p>";
		echo "<p>* Type or paste password here:
		<input type='text' name='passthis' id='passthis' value='".$comment_passthis."' size='".$sfw_pw_field_size."' tabindex='".$sfw_tab_index."' /></p>";
		// Shows how many comment spam have been killed on the comment form
		if ($wp_sfw_options['toggle_stats_update'] == "enable") {
				// number_format will cause errors in other locales, so Wordpress created the undocumented number_format_i18n function that properly localizes the number
				// Examples:
				// http://cleverwp.com/date_i18n-reference-and-usage/
				// more here http://wpcodesnippets.info/blog/7-cool-undocumented-wordpress-functions.html
				// http://wpengineer.com/1918/24th-door-the-wpe-quit-smoking-widget/
				// http://hitchhackerguide.com/2011/02/12/number_format_i18n/
				// http://hitchhackerguide.com/2011/02/12/number_format_i18n-2/
				echo '<p>' . $sfw_count . ' Spam Comments Blocked so far by <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank" rel="nofollow">Spam Free Wordpress</a></p>';
		} else {
				echo "";
		}
	}
}

// Function for wp-comments-post.php file located in the root Wordpress directory. The same directory as the wp-config.php file.
function tl_spam_free_wordpress_comments_post() {
	global $post, $wp_sfw_options;
	
	$sfw_comment_script = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	
	// If the reader is logged in don't require password for wp-comments-post.php
	if ( !is_user_logged_in() ) {

		// Compares current comment form password with current password for post
		if ($_POST['passthis'] == '' || $_POST['passthis'] != $sfw_comment_script)
			wp_die( __('Error 1: Click back and type in the password.', spam_counter()) );
		
		// Compares commenter IP address to local blocklist
		if ($wp_sfw_options['lbl_enable_disable'] == 'enable') {
			if ($_POST['comment_ip'] == '' || $_POST['comment_ip'] == sfw_local_blocklist_check() )
				wp_die( __('Spam Blocked by Spam Free Wordpress (local blocklist)', spam_counter()) );
		}
		
		// Compares commenter IP address to remote blocklist
		if ($wp_sfw_options['rbl_enable_disable'] == 'enable') {
			if ($_POST['comment_ip'] == '' || $_POST['comment_ip'] == sfw_remote_blocklist_check() )
				wp_die( __('Spam Blocked by Spam Free Wordpress (remote blocklist)', spam_counter()) );
		}

	}
}

// Counts number of comment spam hits and stores in options database table
function spam_counter() {
	$s_hits = get_option('sfw_spam_hits');
	update_option('sfw_spam_hits', $s_hits+1);
}

// displays comment spam hits wherever it is called
function display_spam_hits() {
	$s_hits = get_option('sfw_spam_hits');
	return $s_hits;
}

// Register Admin Options Page
function register_spam_free_wordpress_options_page() {
	add_options_page('Spam Free Wordpress Configuration', 'Spam Free Wordpress', 'manage_options', 'spam-free-wordpress-admin-page', 'spam_free_wordpress_options_page');
}

// Admin Settings Options Page function
function spam_free_wordpress_options_page() {

	// Check to see if user has adequate permission to access this page
	if (!current_user_can('manage_options')){
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	global $spam_free_wordpress_version, $wp_sfw_options, $sfw_count;

?>
<div class="wrap">
    <h2>Spam Free Wordpress <?php echo $spam_free_wordpress_version; ?> Settings</h2>

	<form method="post" action="">
<?php

	echo '<table class="form-table">';
		if ($_POST['options']) {
		
		$new_wp_sfw_options = $_POST['wp_sfw_options'];
		update_option('spam_free_wordpress', $new_wp_sfw_options);

		// Display saved message when options are updated.
		$msg_status = 'Spam Free Wordpress settings saved.';
		_e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
		}
?>

<table class="form-table">
	<tr>
		<td valign="top">		
			<h3>Local Comment Blocklist</h3>
			<p>The Local Blocklist is a list of blocked IP addresses stored in the blog database. When a comment comes from an IP address matching the Blocklist it will be blocked, which means you will never see it as waiting for approval or marked as spam. Blocked commenters will be able to view your blog, but any comments they submit will be blocked, which means not saved to the database, and they will see the message &#8220;Spam Blocked.&#8221;</p>
			<p>Enter one IP address (for example 192.168.1.1) per line. Wildcards like 192.168.1.* will not work.</p>
			<p><code>#</code> can be used to comment out an IP address.</p>
				<fieldset>
					<p>On <input type="radio" name="wp_sfw_options[lbl_enable_disable]" <?php echo (($wp_sfw_options['lbl_enable_disable'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="wp_sfw_options[lbl_enable_disable]" <?php echo (($wp_sfw_options['lbl_enable_disable'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" />
				</fieldset>
				<fieldset>
					<textarea name="wp_sfw_options[blocklist_keys]" cols='20' rows='12' ><?php echo $wp_sfw_options['blocklist_keys']; ?></textarea>
				</fieldset>

			<h3>Remote Comment Blocklist</h3>
			<p>The Remote Comment Blocklist accesses a text file list of IP addresses on a remote server to block comment spam. This allows a global IP address blocklist to be shared with multiple blogs. It is also possible to use the Local Comment Blocklist for blog specific blocking, and the Remote Comment Blocklist for global blocking used by mutliple blogs at the same time. Remote Comment Blocklist works exactly the same way as the Local Comment Blocklist, except it is on a remote server. The URL to the remote text file could be for example: <code>http://www.example.com/mybl/bl.txt</code></p>
			<p><code>#</code> can be used to comment out an IP address.</p>
				<fieldset>
					<p>On <input type="radio" name="wp_sfw_options[rbl_enable_disable]" <?php echo (($wp_sfw_options['rbl_enable_disable'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="wp_sfw_options[rbl_enable_disable]" <?php echo (($wp_sfw_options['rbl_enable_disable'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="60" name="wp_sfw_options[remote_blocked_list]" value="<?php echo $wp_sfw_options['remote_blocked_list']; ?>" />&nbsp;&nbsp; Enter URL to remote text file.</p>
				</fieldset>
				
			<h3>Password Form Customization</h3>
				<fieldset>
					<p>
					<input type="text" name="wp_sfw_options[pw_field_size]" size="4" value="<?php echo $wp_sfw_options['pw_field_size']; ?>" />&nbsp;&nbsp; Password Field Size. Default is 30.
					&nbsp;&nbsp;&nbsp;<input type="text" name="wp_sfw_options[tab_index]" size="4" value="<?php echo $wp_sfw_options['tab_index']; ?>" />&nbsp;&nbsp; Tab Index
					</p>
				</fieldset>
				
			<h3>Comment Form Spam Stats</h3>
			<p>When spam comment stats are ON they will be shown alongside a nofollow link to spamfreewordpress.com, and if an affiliate ID is entered below the link to spamfreewordpress.com will be transformed into the affiliate link.</p>
				<fieldset>
					<p>On <input type="radio" name="wp_sfw_options[toggle_stats_update]" <?php echo (($wp_sfw_options['toggle_stats_update'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="wp_sfw_options[toggle_stats_update]" <?php echo (($wp_sfw_options['toggle_stats_update'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" />&nbsp;&nbsp; Leave on to make others aware of Spam Free Wordpress.</p>
				</fieldset>
				
			<h3>Remove HTML from Comments</h3>
			<p>It is very common for manual and automated comment spam to include a URL that links to a web site. This feature will automatically strip out HTML from comments so that links will show up as plain text, and it removes the allowed HTML tags from below the comment text box.</p>			
				<fieldset>
					<p>On <input type="radio" name="wp_sfw_options[toggle_html]" <?php echo (($wp_sfw_options['toggle_html'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="wp_sfw_options[toggle_html]" <?php echo (($wp_sfw_options['toggle_html'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" /></p>
				</fieldset>
				
			<h3>Pingbacks and Trackbacks</h3>
			<p>The plugin below will close pingbacks and trackbacks on all posts and pages on a blog.</p>
			<p>Download the Auto Close Pings and Trackbacks plugin from the <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank">Spam Free Wordpress</a> homepage.</p>
			<p>To make sure pingbacks and trackbacks are closed on future posts and pages, go to <code>Settings -> Discussion</code> and uncheck the box next to <code>Allow link notifications from other blogs (pingbacks and trackbacks)</code>.</p>

			<h3>Share Link Custom Message</h3>
			<p>Customize a URL link to the Spam Free Wordpress plugin page below if you want to share it with others somewhere else on your blog other than the comment form.</p>
				<fieldset>
					<p>Link Message &nbsp;&nbsp;<input type="text" size="60" name="wp_sfw_options[affiliate_msg]" value="<?php echo $wp_sfw_options['affiliate_msg']; ?>" /> &nbsp;&nbsp;<?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?></p>
				</fieldset>
			<p>Copy and paste the line of code below into a template file to display the custom share link.</p>
			<code>&lt;?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?&gt;</code>
			
			<p class="submit">
			<input type="submit" name="options" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
</form>
			<h2><font style="BACKGROUND-COLOR: #ffffff">What Am I Worth?</font></h2>
			<p><font style="BACKGROUND-COLOR: #ffffff"><h2><tt>Is a reliable spam fighting plugin worth a dollar?</tt></h2></font></p>
			<p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="SFVH6PCCC6TLG">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			</p>
			<br />
			
			<h3>Installation Instructions</h3>
			<p>Complete installation instructions are available at <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank">Spam Free Wordpress</a>.

			<hr />
			<h4><a href="http://www.toddlahman.com/" target="_blank">Spam Free Wordpress</a> was built and created by Todd Lahman.</h4>
			
		</td>
			
		<td valign="top" bgcolor="#FFFFFF">
						<div align="center"><h3>Blocked Comment Spam</h3></div>
						<p align="center"><b><big><?php echo $sfw_count; ?></big></b></p>
						<br />
		
			<div id="sideblock" style="float:right;width:275px;margin-left:10px;">
					<iframe width="275" height="1070" frameborder="0" src="http://www.toddlahman.com/plugin-news/sfw/spw-plugin-news.html?utm_source=sfw-plugin&utm_medium=sfw-plugin&utm_campaign=sfw-plugin"></iframe>
			</div>
		</td>
	</tr>
</table>

<?php

}

// Remove note after comment box that says which HTML tags can be used in comment
function sfw_remove_allowed_tags_field($no_allowed_tags) {
    unset($no_allowed_tags['comment_notes_after']);
    return $no_allowed_tags;
}

// Strips out html from comment form when enabled
if ($wp_sfw_options['toggle_html'] == "enable" && version_compare($wp_version, '3.0', '>=' )) {
	// Removes all HTML from comments and leaves it only as text
	add_filter('comment_text', 'wp_filter_nohtml_kses');
	add_filter('comment_text_rss', 'wp_filter_nohtml_kses');
	add_filter('comment_excerpt', 'wp_filter_nohtml_kses');
	// remove tags from below comment form
	add_filter('comment_form_defaults','sfw_remove_allowed_tags_field');
}

// Adds password field to comment form is > Wordpress 3.0 and using comment_form function to generate comment form
function do_spam_free_wordpress_automation() {
	global $wp_version;
	
	// run the following code only if using Wordpress 3.x or greater
	if ( version_compare($wp_version, '3.0', '>=' ) ) {

		// Calls the password form for comments.php if the comment_form function is outputting comment form fields
		add_filter('comment_form_after_fields', 'tl_spam_free_wordpress_comments_form', 1);
	}
}

// Calls the Wordpress 3.x code for admin settings page
add_action('init', 'do_spam_free_wordpress_automation', 1);
// Calls the wp-comments-post.php authentication
add_action('pre_comment_on_post', 'tl_spam_free_wordpress_comments_post', 1);
// Add Admin Options Page
add_action('admin_menu', 'register_spam_free_wordpress_options_page');

?>