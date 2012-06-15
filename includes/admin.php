<?php

// Add Admin Options Page
add_action('admin_menu', 'register_spam_free_wordpress_options_page');

// Register Admin Options Page
function register_spam_free_wordpress_options_page() {
	add_options_page(
		'Spam Free Wordpress Configuration',
		'Spam Free Wordpress',
		'manage_options',
		'spam-free-wordpress-admin-page',
		'spam_free_wordpress_options_page'
		);
}

// Admin Settings Options Page function
function spam_free_wordpress_options_page() {

	// Check to see if user has adequate permission to access this page
	if (!current_user_can('manage_options')){
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	global $spam_free_wordpress_version;

?>
<div class="wrap">
	<?php screen_icon( 'edit-comments' );?>
    <h2>Spam Free Wordpress <?php echo SFW_VERSION; ?> Settings</h2>

	<form method="POST" action="">
<?php
		if (isset( $_POST['options'] ) ) {
			update_option('spam_free_wordpress', $_POST['spam_free_wordpress_options']);
			// Display saved message when options are updated.
			$spam_free_wordpress_options = get_option('spam_free_wordpress');
			if( $spam_free_wordpress_options['ping_status'] == 'open' ) {
				sfw_open_pingbacks();
			} elseif( $spam_free_wordpress_options['ping_status'] == 'closed' ) {
				sfw_close_pingbacks();
			}
			_e('<div id="message" class="updated"><p>Spam Free Wordpress settings saved.</p></div>');
		}
		
		$spam_free_wordpress_options = get_option('spam_free_wordpress');
?>

<table class="form-table">
	<tr>
		<td colspan="2" valign="top">
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Comment Form Spam Stats</span></h3>
			<p><h3>Turn on your stats to say thank you.</h3> This will also display a link to the plugin page on your comment form.</p>
			<fieldset>
				<p>On <input type="radio" name="spam_free_wordpress_options[toggle_stats_update]" <?php echo (($spam_free_wordpress_options['toggle_stats_update'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[toggle_stats_update]" <?php echo (($spam_free_wordpress_options['toggle_stats_update'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" /></p>
			</fieldset>
			
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Installation Instructions</span></h3>
			<p>If the Spam Free Wordpress password field is not visible on the comment form then review the instructions available at <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank">Spam Free Wordpress</a>.</p>
			<p>If you still need help getting the password field to show on the comment form:</p>
			</p>Post all of the code from your original comments.php file to <a href="http://pastebin.com/" target="_blank">Pastbin</a>, paste a link to that code <a href="http://www.toddlahman.com/spam-free-wordpress/" target="_blank">here</a>, and I will reply with a link to pastebin with the new code for your comments.php file.</p>
			
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Remove HTML from Comments</span></h3>
			<p>Strips the HTML from comments to render spam links as plain text. Also removes the allowed HTML tags message from below the comment box.</p>			
				<fieldset>
					<p>On <input type="radio" name="spam_free_wordpress_options[toggle_html]" <?php echo (($spam_free_wordpress_options['toggle_html'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[toggle_html]" <?php echo (($spam_free_wordpress_options['toggle_html'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" /></p>
				</fieldset>
				
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Remove URL Form Field</span></h3>
			<p>Removes the URL (web site address) comment form field, so it cannot be used as a spam link.</p>			
				<fieldset>
					<p>On <input type="radio" name="spam_free_wordpress_options[remove_author_url_field]" <?php echo (($spam_free_wordpress_options['remove_author_url_field'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[remove_author_url_field]" <?php echo (($spam_free_wordpress_options['remove_author_url_field'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" /></p>
				</fieldset>
				
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Remove Comment Author Clickable Link</span></h3>
			<p>Disables the comment author clickable link if the comment author fills in the URL (web site) comment form field.</p>			
				<fieldset>
					<p>On <input type="radio" name="spam_free_wordpress_options[remove_author_url]" <?php echo (($spam_free_wordpress_options['remove_author_url'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[remove_author_url]" <?php echo (($spam_free_wordpress_options['remove_author_url'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" /></p>
				</fieldset>
				
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Password Form Customization</span></h3>
				<fieldset>
					<p>
					<input type="text" name="spam_free_wordpress_options[pw_field_size]" size="2" value="<?php echo $spam_free_wordpress_options['pw_field_size']; ?>" style="color: #000000; background-color: #fffbcc" />&nbsp;&nbsp; Password Field Size. Default is 12.
					&nbsp;&nbsp;&nbsp;<input type="text" name="spam_free_wordpress_options[tab_index]" size="2" value="<?php echo $spam_free_wordpress_options['tab_index']; ?>" style="color: #000000; background-color: #fffbcc" />&nbsp;&nbsp; Tab Index
					</p>
				</fieldset>

			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Remote Comment Blocklist</span></h3>
			<p>The Remote Comment Blocklist accesses a text file list of IP addresses on a remote server to block comment spam. This allows a global IP address blocklist to be shared with multiple blogs. The Remote Comment Blocklist and the Local Comment Blocklist can be used at the same time. The Remote Comment Blocklist works exactly the same way as the Local Comment Blocklist, except it is on a remote server. The URL to the remote text file could be for example: <code>http://www.example.com/mybl/bl.txt</code></p>
			<p><code>#</code> can be used to comment out an IP address.</p>
				<fieldset>
					<p>On <input type="radio" name="spam_free_wordpress_options[rbl_enable_disable]" <?php echo (($spam_free_wordpress_options['rbl_enable_disable'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[rbl_enable_disable]" <?php echo (($spam_free_wordpress_options['rbl_enable_disable'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="60" name="spam_free_wordpress_options[remote_blocked_list]" value="<?php echo esc_url($spam_free_wordpress_options['remote_blocked_list']); ?>" style="color: #000000; background-color: #fffbcc" />&nbsp;&nbsp; Enter URL to remote text file.</p>
				</fieldset>
				
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Share Link Custom Message</span></h3>
			<p>Customize a URL link to the Spam Free Wordpress plugin page below if you want to share it with others somewhere else on your blog other than the comment form.</p>
				<fieldset>
					<p>Link Message &nbsp;&nbsp;<input type="text" size="60" name="spam_free_wordpress_options[affiliate_msg]" value="<?php echo $spam_free_wordpress_options['affiliate_msg']; ?>" style="color: #000000; background-color: #fffbcc" /> &nbsp;&nbsp;<?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?></p>
				</fieldset>
			<p>Copy and paste the line of code below into a template file to display the custom share link.</p>
			<code>&lt;?php if(function_exists('custom_affiliate_link')) { custom_affiliate_link(); } ?&gt;</code>
			
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Pingbacks and Trackbacks</span></h3>
			<p><strong>It is highly recommended to keep pingbacks CLOSED to eliminate that form of spam entirely.</strong></p>
			<p>Pingbacks can cause a downgrade in SEO ranking, and are almost entirely spam. Pingbacks are not worth the trouble they bring, but if you still want them it is your choice.</p>
				<fieldset>
					<p>Open <input type="radio" name="spam_free_wordpress_options[ping_status]" <?php echo (($spam_free_wordpress_options['ping_status'] == "open") ? 'checked="checked"' : '') ;  ?> value="open" />&nbsp;&nbsp; Closed <input type="radio" name="spam_free_wordpress_options[ping_status]" <?php echo (($spam_free_wordpress_options['ping_status'] == "closed") ? 'checked="checked"' : '') ;  ?> value="closed" /></p>
				</fieldset>
			
			<td valign="top">
				<div align="center"><h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Blocked Spam Comments</span></h3>
				</div>
					<p align="center"><b><span style="font-size:200%;"><?php echo number_format_i18n(get_option('sfw_spam_hits')); ?></span></big></b></p>
				<div align="center" style="margin-left:5px;">
					<iframe width="355" height="900" frameborder="0" src="http://www.toddlahman.com/plugin-news/sfw/spw-plugin-news.php"></iframe>
				</div>
			</td>
		</td>
			
	<tr>
		<td valign="middle">		
			<h3><span style="border-bottom: 2px solid #99ccff; padding: 3px;">Local Comment Blocklist</span></h3>
			<p>The Local Blocklist is a list of blocked IP addresses stored in the blog database. When a comment comes from an IP address matching the Blocklist it will be blocked, which means you will never see it as waiting for approval or marked as spam. Blocked commenters will be able to view your blog, but any comments they submit will be blocked, which means not saved to the database, and they will see the message &#8220;Spam Blocked.&#8221;</p>
			<p>Enter one IP address per line. Wildcards like 192.168.1.* will not work.</p>
			<p><code>#</code> can be used to comment out an IP address.</p>
				<fieldset>
					<p>On <input type="radio" name="spam_free_wordpress_options[lbl_enable_disable]" <?php echo (($spam_free_wordpress_options['lbl_enable_disable'] == "enable") ? 'checked="checked"' : '') ;  ?> value="enable" />&nbsp;&nbsp; Off <input type="radio" name="spam_free_wordpress_options[lbl_enable_disable]" <?php echo (($spam_free_wordpress_options['lbl_enable_disable'] == "disable") ? 'checked="checked"' : '') ;  ?> value="disable" />
				</fieldset>
		</td>
		<td valign="middle">
					<p align="center"><b>IP Addresses Go Here</b></p>
				<fieldset>
					<textarea name="spam_free_wordpress_options[blocklist_keys]" cols='20' rows='9' style="color: #000000; background-color: #fffbcc"><?php echo esc_textarea($spam_free_wordpress_options['blocklist_keys']); ?></textarea>
				</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			
			<?php submit_button( 'Save Changes', 'primary', 'options' ); ?>
			
		</td>
	</tr>
	</tr>
</table>
</form>
</div>

<?php

}

?>