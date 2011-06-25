<?php
/*
Plugin Name: Spam Free Wordpress
Plugin URI: http://www.toddlahman.com/spam-free-wordpress/
Description: Comment spam blocking plugin that uses anonymous password authentication to achieve 100% automated spam blocking with zero false positives, plus a few more features.
Version: 1.4.8
Author: Todd Lahman, LLC
Author URI: http://www.toddlahman.com/
*/

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
function add_default_data() {
	$sfw_options = array(
	'blocklist_keys' => '',
	//'remote_blocked_list' => '',
	'pw_field_size' => '20',
	'tab_index' => '',
	'affiliate_msg' => '',
	'toggle_stats_update' => 'disable',
	'toggle_html' => 'disable'
	);
	add_option('spam_free_wordpress', $sfw_options);
	add_option('sfw_spam_hits', '');
}

// Runs add_default_data function above when plugin activated
register_activation_hook( __FILE__, 'add_default_data' );

// Delete the default options from database when plugin deactivated,
// The post comment passwords can be deleted also using the following SQL statement.
// DELETE from wp_postmeta WHERE meta_key = "sfw_comment_form_password" ;

/*
// Runs remove_default_data function above when plugin deactivated
// Uncomment this code block if you want the all options to be deleted when plugin is deactivated, this includes number of spam blocked
function remove_default_data() {
delete_option('spam_free_wordpress');
delete_option('sfw_spam_hits');
}

// register_deactivation_hook( __FILE__, 'remove_default_data' );
*/

// Plugin version
$spam_free_wordpress_version = "1.4.8";

// variable used as global to retrieve option array for functions
$wp_sfw_options = get_option('spam_free_wordpress');

// Displays message on Admin settings page if using a Wordpress version older than 3.x
function older_wp_notice() {
	global $wp_version;

	if ( version_compare($wp_version, '3.0', '<' ) ) {
		echo '<table class="form-table"><tr><td><fieldset><label><hr /><h3>WARNING</h3><p>Your version of Wordpress is '.$wp_version.', which is too old to take advantage of the automated features built into Spam Free Wordpress. Please upgrade Wordpress.</p><hr /></label><br /></fieldset></td></tr></table>';
	}
}


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
function wp_blocklist_check() {
	global $wp_sfw_options;

	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();
	// do_action('wp_blocklist_check', $comment_author_ip);

	$local_blocklist_keys = trim( $wp_sfw_options['blocklist_keys'] );
	if ( '' == $local_blocklist_keys )
		return false; // If blocklist keys are empty
	$local_key = explode("\n", $local_blocklist_keys );

	foreach ( (array) $local_key as $lkey ) {
		$lkey = trim($lkey);

		// Skip empty lines
		if ( empty($lkey) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$lkey = preg_quote($lkey, '#');

		$pattern = "#$lkey#i";
		if (
			   preg_match($pattern, $comment_author_ip)
		 )
			return true;
	}
	return false;
}
/*
// Returns Remote Realtime Comment Blocklist
function wp_realtime_blocklist_check() {
	global $wp_sfw_options;
	
	// Gets IP address of commenter
	$comment_author_ip = get_remote_ip_address();

	$remote_blocklist_keys = trim( wp_remote_get($wp_sfw_options['remote_blocked_list']) );
	if ( '' == $remote_blocklist_keys )
		return false; // If blocklist keys are empty
	$remote_key = explode("\n", $remote_blocklist_keys );

	foreach ( (array) $remote_key as $rkey ) {
		$rkey = trim($rkey);

		// Skip empty lines
		if ( empty($rkey) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$rkey = preg_quote($rkey, '#');

		$pattern = "#$rkey#i";
		if (
			   preg_match($pattern, $comment_author_ip)
		 )
			return true;
	}
	return false;
}
*/

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
	global $wp_sfw_options;
	global $post;
	global $wp_version;
	
	$sfw_comment_form_password_var = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	// Checks to see if comment form password exists and if not creates one in custom fields
	$new_sfw_comment_form_pwd = wp_generate_password(12, false);
	if( empty($sfw_comment_form_password_var) || !$sfw_comment_form_password_var  && comments_open() ) {
	update_post_meta($post->ID, 'sfw_comment_form_password', $new_sfw_comment_form_pwd);
	}
	
	$sfw_pw_field_size = $wp_sfw_options['pw_field_size'];
	$sfw_tab_index = $wp_sfw_options['tab_index'];

	// If the reader is logged in don't require password for comments.php
	if ( !is_user_logged_in() ) {
		// extra hidden passwords
		echo '<!-- Comment Spam Protection provided by Spam Free Wordpress located at http://www.toddlahman.com/spam-free-wordpress/ -->';
		// Commenter IP address
		echo "<input type='hidden' name='comment_ip' id='comment_ip' value='".get_remote_ip_address()."' />";
		// Reader must enter this password manually on the comment form
		echo "<p><label for='pwd_text'>* Copy this password:</label>
		<input type='text' value='".$sfw_comment_form_password_var."' onclick='this.select()' size='20' /></p>";
		echo "<p><label for='passthis'>* Type or paste password here:</label>
		<input type='text' name='passthis' id='passthis' value='".$comment_passthis."' size='".$sfw_pw_field_size."' tabindex='".$sfw_tab_index."' /></p>";
		// Shows how many comment spam have been killed on the comment form
		if ($wp_sfw_options['toggle_stats_update'] == "enable") {
				echo '<p>'.number_format(display_spam_hits()).' Spam Comments Blocked so far by <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank" rel="nofollow">Spam Free Wordpress</a></p>';
		} else {
				echo "";
		}
	}
}

// Function for wp-comments-post.php file located in the root Wordpress directory. The same directory as the wp-config.php file.
function tl_spam_free_wordpress_comments_post() {
	global $post;
	
	$sfw_comment_script = get_post_meta( $post->ID, 'sfw_comment_form_password', true );
	// Checks to see if comment form password exists and if not creates one in custom fields
	$new_sfw_comment_script_pwd = wp_generate_password(12, false);
	if( empty($sfw_comment_script) || !$sfw_comment_script  && comments_open() ) {
	update_post_meta($post->ID, 'sfw_comment_form_password', $new_sfw_comment_script_pwd);
	}
	
	// If the reader is logged in don't require password for wp-comments-post.php
	if ( !is_user_logged_in() ) {

		// Comment form manual password (key 2)
		if ($_POST['passthis'] == '' || $_POST['passthis'] != $sfw_comment_script)
			wp_die( __('Error 1: Click back and type in the password.', spam_counter()) );
		
		// Compares commenter IP address to blocked list and realtime blocked list
		if ($_POST['comment_ip'] == '' || $_POST['comment_ip'] == wp_blocklist_check() )
			wp_die( __('Spam Blocked by Spam Free Wordpress (local blocklist)', spam_counter()) );
		
		//if ($_POST['comment_ip'] == '' || $_POST['comment_ip'] == wp_realtime_blocklist_check() )
		//	wp_die( __('Spam Blocked by Spam Free Wordpress (remote blocklist)', spam_counter()) );

	}
}

/*
May add hashed password check with hidden form field using function:
wp_hash_password( $password );

example:

$wp_hasher = new PasswordHash(8, TRUE);
	
$password_hashed = '$P$B55D6LjfHDkINU5wF.v2BuuzO0/XPk/';
$plain_password = 'test';
	
if($wp_hasher->CheckPassword($plain_password, $password_hashed)) {
   echo "YES, Matched";
}
else {
   echo "No, Wrong Password";
}

*/

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
	add_options_page('Spam Free Wordpress Configuration', 'Spam Free Wordpress', 'manage_options', __FILE__, 'spam_free_wordpress_options_page');

}

// Admin Settings Options Page function
function spam_free_wordpress_options_page() {

	// Check to see if user has adequate permission to access this page
	if (!current_user_can('manage_options')){
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	global $spam_free_wordpress_version;
	global $wp_sfw_options;

?>
<div class="wrap">
    <h2>Spam Free Wordpress <?php echo $spam_free_wordpress_version; ?> Settings</h2>

	<form method="post" action="">
<?php

	echo '<table class="form-table">';
		if ($_POST['action'] == 'update') {
		
		$new_wp_sfw_options = $_POST['wp_sfw_options'];
		update_option('spam_free_wordpress', $new_wp_sfw_options);

		// Display saved message when options are updated.
		$msg_status = 'Spam Free Wordpress settings saved.';
		_e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
		}

		older_wp_notice ();
?>

<table class="form-table">
	<tr>
		<td valign="top">
			<h3>How Much Comment Spam Has Been Blocked?</h3>
					<p>Comment Spam Blocked: <b><?php echo number_format(get_option('sfw_spam_hits')); ?></b></p>
				
			<h3>Local Comment Blocklist</h3>
			<p>The Local Blocklist is a list of blocked IP addresses stored in the blog database. When a comment comes from an IP address matching the Blocklist it will be blocked, which means you will never see it as waiting for approval or marked as spam. Blocked commenters will be able to view your blog, but any comments they submit will be blocked, which means not saved to the database, and they will see the message &#8220;Spam Blocked.&#8221; Enter one IP address (for example 192.168.1.1) per line. Wildcards like 192.168.1.* will not work.</p>
				<fieldset>
					<label><textarea name="wp_sfw_options[blocklist_keys]" cols='40' rows='12' ><?php echo $wp_sfw_options['blocklist_keys']; ?></textarea></label><br />
				</fieldset>
				

				
			<h3>Password Form Customization</h3>
				<fieldset>
					<label><p>Password Field Size <input type="text" name="wp_sfw_options[pw_field_size]" value="<?php echo $wp_sfw_options['pw_field_size']; ?>" /> Default is 20.</label>
				</fieldset>
				<fieldset>
					<label>Tab Index </font><input type="text" name="wp_sfw_options[tab_index]" value="<?php echo $wp_sfw_options['tab_index']; ?>" /></p></label>
				</fieldset>
				
			<h3>Comment Form Spam Stats</h3>
			<p>When spam comment stats are ON they will be shown alongside a nofollow link to spamfreewordpress.com, and if an affiliate ID is entered below the link to spamfreewordpress.com will be transformed into the affiliate link.</p>
				<label><p>Turn Spam Stats On or Off
					<select name="wp_sfw_options[toggle_stats_update]" class="toggle_stats">
						<option value="disable" <?php selected( 'disable', $wp_sfw_options['toggle_stats_update'] ); ?> ><?php _e( 'Spam Stats OFF' ) ?></option>
						<option value="enable" <?php selected( 'enable', $wp_sfw_options['toggle_stats_update'] ); ?> ><?php _e( 'Spam Stats ON' ) ?></option>
					</select>
				Leave on to make others aware of Spam Free Wordpress.</p></label>
				
			<h3>Remove HTML from Comments</h3>
			<p>It is very common for manual and automated comment spam to include a URL that links to a web site. This feature will automatically strip out HTML from comments so that links will show up as plain text, and it removes the allowed HTML tags from below the comment text box.</p>
				<label><p>Turn HTML Filter On or Off
					<select name="wp_sfw_options[toggle_html]" class="toggle_html">
						<option value="disable" <?php selected( 'disable', $wp_sfw_options['toggle_html'] ); ?> ><?php _e( 'Strip HTML OFF' ) ?></option>
						<option value="enable" <?php selected( 'enable', $wp_sfw_options['toggle_html'] ); ?> ><?php _e( 'Strip HTML ON' ) ?></option>
					</select>
				</p></label>
				
			<h3>Share Link Custom Message</h3>
			<p>Customize a URL link to the Spam Free Wordpress plugin page below if you want to share it with others somewhere else on your blog other than the comment form.</p>
				<fieldset>
					<label><p>Link Message <input type="text" size="60" name="wp_sfw_options[affiliate_msg]" value="<?php echo $wp_sfw_options['affiliate_msg']; ?>" /> <?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?></p></label>
				</fieldset>
			<p>Copy and paste the line of code below into a template file to display the custom share link.</p>
			<code>&lt;?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?&gt;</code>
			
			<input type="hidden" name="page_options" value="<?php echo $wp_sfw_options ?>" />
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
</form>
			
		</td>
			
		<td valign="top">
			<div id="sideblock" style="float:right;width:275px;margin-left:10px;"> 		 
				<iframe width="275" height="800" frameborder="0" src="http://www.toddlahman.com/plugin-news/sfw/spw-plugin-news.html?utm_source=sfw-plugin&utm_medium=sfw-plugin&utm_campaign=sfw-plugin"></iframe>
			</div>
		</td>
	</tr>
</table>

<table class="form-table">
	<tr>
		<td>
		<h3>Instructions</h3>
			<p>The password comment form field is required for <strong>Spam Free Wordpress</strong> to block comment spam. Below are instructions to make sure the comment form password field is working properly.</p>
			<p>If you are running Wordpress 3.x and up, and your comments.php file is using the comment_form() function to output the comment form fields, then <strong>Spam Free Wordpress</strong> will automatically add the password field to your comment form.</p>
			<p>If you don't see a password field in your comment form when you visit a post page then follow the instructions below.</p>
			<p>If you are running Wordpress 3.x and up, and your comments.php file IS NOT using the comment_form() function to output the comment form fields, then you will need to copy and paste the following line of code to your comments.php file just below the website address, or email address field.<br /><code>&lt;?php if(function_exists('tl_spam_free_wordpress_comments_form')) { tl_spam_free_wordpress_comments_form(); } ?&gt;</code></p>
			<p>If you are using Wordpress 2.8 or 2.9 then you will need to copy and paste the following line of code to your comments.php file just below the website address, or email address field.<br /><code>&lt;?php if(function_exists('tl_spam_free_wordpress_comments_form')) { tl_spam_free_wordpress_comments_form(); } ?&gt;</code></p>
			<p>Refer to the readme.txt file for more detailed information.</p>
			<p><strong>Thesis Theme</strong></p>
			<p>Go to <a href="http://ngurl.me/thesis" target="_blank">Thesis</a> -> Custom File Editor, choose custom_functions.php, then click Edit selected file. Add the following line of code to that file.</p>
			<p><code>add_action('thesis_hook_comment_field', 'tl_spam_free_wordpress_comments_form');</code></p>
			<p>Save changes.</p>
		</td>
	</tr>
</table>

<table class="form-table">
	<tr>
		<td>
			<hr />
			<h4><a href="http://www.toddlahman.com/" target="_blank">Spam Free Wordpress</a> was built and created by Todd Lahman.</h4>
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