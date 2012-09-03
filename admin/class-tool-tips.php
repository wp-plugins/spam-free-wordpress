<?php

if( !class_exists('SFW_TOOL_TIPS' ) ) {
	
	class SFW_TOOL_TIPS {
	
		function tips( $tip ) {
			switch ( $tip ) {
				case 'stats':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'The number of blocked comment spam will display on your comment form, along with a link to the plugin homepage.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'comment_form':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'If the plugin is not working properly, your theme may not be using the comment_form function, so you will need to turn this on. The plugin can generate the comment form, and display the comments, automatically. Depending on your theme, the comments and comment form may require CSS styling that you will need to provide, or you can hire me.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'html':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Spammers use HTML in comments to leave spam links on your blog. This options strips out all the HTML tags, so the links are just plain text, and not links. Spam links are bad for SEO, even when they are nofollow links.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'url':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'The default comment form has a Website Address form field that spammers use to leave a spam link on your blog. Spam links are bad for SEO, even when they are nofollow links', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'link':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Spammers use the link embedded in the comment author name to leave a spam link on your blog. Spam links are bad for SEO, even when they are nofollow links', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'pingbacks':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Pingbacks, or trackbacks, can be placed on your blog without your knowledge or permission from another blog that may not provide a reciprocal link. 99% of pingbacks are spam links. Having a page with more than 100 links on it, or more links going out than coming in, will hurt the SEO for that page, and for the entire domain name.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'jquery_compat':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'If you have WordPress 3.3 or greater, and you see a Spam Free Wordpress error when leaving a test comment, it could mean you have a theme that is loading a version of jQuery older than 1.7. Check this box to use the older jQuery script to try to fix the problem. If the problem is not fixed, ask for help on the Support Forum on toddlahman.com, and consider getting your theme fixed, or hiring me to fix it. If your theme is more than one year old, it probably needs to be updated.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'comment_msg':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'This option displays a message above the comment text area on the comment form. Plane text, or HTML tags, can be used.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'blocklist':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'This option is to block manually submitted spam. The blocklist allows spammer IP addresses to be blocked, so the spammer can no longer leave a comment. Comments that are blocked are not saved to the database, so you will not see anything in your spam folder. This option can also be used to ban a reader from leaving comments. Enter one IP address per line. Wildcards like 192.168.1.* will not work.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'clean_spam':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Automatically deletes comment spam from the spam folder. Note: Spam Free Wordpress does not save blocked comments in the spam folder, or anywhere else. Any comments in the Spam folder were put there by another plugin, or by the Comment Blacklist built into WordPress under Settings > Discussion.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'clean_trackbacks':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Automatically deletes trackbacks/pingbacks.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
				case 'clean_unapproved':
					?>
					<span class='icon-pos'><a href='' class='tool-tip' title='<?php _e( 'Comments that are marked as unapproved, because the discussion settings are set to hold a comment for moderation (aka the Comment Moderation queue), will be deleted automatically, so there will be no opportunity to review and approve the comment.', 'spam-free-wordpress' ); ?>'><img src='<?php echo SFW_URL; ?>images/icon-question.png' title=''' /></a></span>
					<?php
					break;
			}
		}
	
	}

	$sfw_tool_tips = new SFW_TOOL_TIPS();

}

?>